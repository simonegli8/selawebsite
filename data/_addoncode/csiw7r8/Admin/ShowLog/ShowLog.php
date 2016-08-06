<?php defined('is_running') or die('Not an entry point...');

gpPlugin::incl('Common/AntiSpamSFS.php');

class AntiSpamSFS_Admin_ShowLog extends AntiSpamSFS {

	// Root url
	var $root_url; // string

	// Log
	var $log_search; // array

	// Perform user actions ?
	var $do_sort; // bool
	var $do_search; // bool

	// query string
	var $search_query; // string
	var $sort_query; // string

	// query string parameters
	var $page; // int
	var $search_key; // string
	var $search_val; // string
	var $sort_by; // string
	var $sort_dir; // string

	// Control html
	var $pagination; // array
	var $ordering; // array

	var $total; // int
	var $count_log; // int

	// Limit	
	var $limit; // int
	var $limit_redir_url; // string

	// Constructor
	function AntiSpamSFS_Admin_ShowLog() {

		global $langmessage;

		// Parent Class Constructor
		parent::AntiSpamSFS();

		// Load the addon config
		$this->_loadConfig();

		// Load the (raw) log data
		if ($this->_loadLog() === false) {
			return;
		}

		// Set the root url
		$this->_setRootUrl();

		// New limit param ?
		if ($this->_doSetLimit()) {
			$this->_setLimit(); 
			$this->_setLimitRedirUrl();
			common::Redirect($this->limit_redir_url, 302);
		}

		// Get the limit parameter
		$this->_getLimit();

		// Load the forms data
		$this->_loadForms();

		// Get the query string params
		$this->_getSearchParam();
		$this->_getSortParam();
		$this->_getPageParam();

		// Process the raw log data accordingly
		$this->_doSearch();		
		$this->_doSort();
		$this->_doPage();

		// Delete log ?
		if ($this->_doDeleteLog()) {
			if ($this->_canDeleteLog()) {
				$this->_deleteLog();
				common::Redirect($this->root_url, 302);
			} else {
				message($langmessage['not_permitted']);
			}
		}

		// Make the html control (urls)
		$this->_makeSearchHtml();		
		$this->_makeSortHtml();
		$this->_makePageHtml();

		// Load the html template
		$this->_showLog();

	}

	/////////////////////////////////////////////////////////////////////
	// PRIVATE METHODS - MAIN
	/////////////////////////////////////////////////////////////////////

	function _canDeleteLog() {

		global $gpAdmin;

		// Super Admin?
		if ($this->isSuperAdmin) {
			return true;
		}

		// Check user permission
		if ($this->config['p_delete_log']) {
			return true;
		}

		return false;		

	}

	function _setRootUrl() {

		$this->root_url =  'http://' 
				. $_SERVER['SERVER_NAME'] 
				. common::GetUrl('Admin_AntiSpamSFS_ShowLog')
				;

	}

	function _doDeleteLog() {

		return (isset($_POST['cmd']) && ($_POST['cmd'] == 'delete_log') ? true : false);

	}

	function _deleteLog() {

		$this->_session_start();
		
		foreach ($this->log as $key => $val) unset($this->log_backup[$key]);
		$this->log_backup = array_values($this->log_backup);
		gpFiles::SaveArray($this->log_file,'log',$this->log_backup);

		$count = count($this->log);

		if ($count > 1) {
			$message = gpOutput::SelectText('%d log delected!');
		} else {
			$message = gpOutput::SelectText('%d logs deleted!');
		}
		
		$message = sprintf($message, $count);
		$this->_message($message);

	}

	function _doSetLimit() {

		return (isset($_POST['cmd']) && ($_POST['cmd'] == 'set_limit') ? true : false);

	}

	function _setLimit() {

		$this->_session_start();

		$limit = common::GetCommand('limit');
		$_SESSION['AntiSpamSFS']['limit'] = $limit;
		$message = gpOutput::SelectText('Limit parameter set to %d');
		$this->_message(sprintf($message, $limit));

	}

	function _getLimit() {

		$this->_session_start();

		if (isset($_SESSION['AntiSpamSFS']['limit']) && $_SESSION['AntiSpamSFS']['limit'] && ($_SESSION['AntiSpamSFS']['limit'] == intval($_SESSION['AntiSpamSFS']['limit']))) {	
			$this->limit = $_SESSION['AntiSpamSFS']['limit'];
			return;
		} else {
			$this->limit = $this->config['log_spammers_limit'];
			return;
		}

	}

	function _setLimitRedirUrl() {

		$query = array();
		
		if (isset($_POST['search_key']) && $_POST['search_key'] && isset($_POST['search_val']) && $_POST['search_val']) {
			$query[] = 'search_key=' . $_POST['search_key'] . '&search_val=' . urlencode($_POST['search_val']);
		}

		if (isset($_POST['sort_by']) && $_POST['sort_by'] && isset($_POST['sort_dir']) && $_POST['sort_dir']) {
			$query[] = 'sort_by=' . $_POST['sort_by'] . '&sort_dir=' . $_POST['sort_dir'];
		}

		$query_string = trim(implode('&', array_filter($query)));
		 
		$this->limit_redir_url = $this->root_url . ($query_string ? '?' . $query_string: '');

	}

	function _makeSortHtml() {

		$ordering = array();
		$valid_columns = array('email', 'username', 'ip', 'date', 'formid');

		foreach ($valid_columns as $column) {

			$buffer = array();

			$buffer['url'] = $this->_makeOrderingUrl($column);
			$buffer['arrow'] = $this->_makeOrderingArrow($column);

			$ordering[$column] = $buffer;
		}

		$this->ordering = $ordering;

	}

	function _getSearchParam() {

		// Default
		$this->do_search = false;
		$this->search_query = '';

		// Key
		$search_key = common::getCommand('search_key');
		if (!$search_key) {
			return;
		}

		$valid_keys = array('email', 'username', 'ip', 'date', 'formid');
		if (!in_array($search_key, $valid_keys)) {
			$message = gpOutput::SelectText('invalid search_key value');
			message($message);
			return;
		} 

		// Val
		$search_val = common::getCommand('search_val');
		if ($search_val === false) {
			return;
		}

		$transform_search_val = $this->_transformSearchValue($search_val, $search_key);
		$valid_values = $this->_getValidValues($search_key);
		if (!in_array($transform_search_val, $valid_values, true)) {
		//if (!in_array($transform_search_val, $valid_values)) {
			$message = gpOutput::SelectText('invalid %s value');
			message(sprintf($message, $search_key));
			return;
		}

		$this->do_search = true;
		$this->search_query = 'search_key=' . $search_key . '&search_val=' . urlencode($search_val);
		$this->search_key = $search_key;
		$this->search_val = $search_val;

	}

	function _doSearch() {

		if (!$this->do_search) {
			return;
		}
		
		// Let's search the log array
		if ($this->search_key == 'date') {
			$myfunc = create_function(
					'$a', 
					'return (strftime(\'%m/%d/%y\', $a["'.$this->search_key.'"]) == strftime(\'%m/%d/%y\', "'.$this->search_val.'"));'
					);
		} else {
			$myfunc = create_function(
					'$a', 
					'return ($a["'.$this->search_key.'"] == "'.$this->search_val.'");'
					);
		}

		$this->log = array_filter($this->log, $myfunc);
		$this->log_search = $this->log;

	}

	function _getSortParam() {

		// Default
		$this->do_sort = false;
		$this->sort_query = '';

		//Sort By
		$sort_by = common::getCommand('sort_by');
		if (!$sort_by) {
			return;
		}

		//$valid_columns = array('email', 'username', 'ip', 'date');
		$valid_columns = array('email', 'username', 'ip', 'date', 'formid');
		if (!in_array($sort_by, $valid_columns)) {
			$message = gpOutput::SelectText('invalid sort_by value');
			message($message);
			return;
		} 

		// Sort Dir
		$sort_dir = common::getCommand('sort_dir');
		if (!$sort_dir) {;
			return;
		}

		$valid_dirs = array('asc', 'desc');
		if (!in_array($sort_dir, $valid_dirs)) {
			$message = gpOutput::SelectText('invalid sort_dir value');
			message($message);
			return;
		} 

		$this->do_sort = true;
		$this->sort_query = 'sort_by=' . $sort_by . '&sort_dir=' . $sort_dir;
		$this->sort_dir = $sort_dir;
		$this->sort_by = $sort_by;

	}

	function _doSort() {

		if (!$this->do_sort) {
			return;
		}
		
		// Let's order the log array
		$var = ($this->sort_dir == 'asc' ? array('a', 'b') : array('b', 'a'));
		$myfunc = create_function(
				'$a, $b', 
				'return strcasecmp($'.$var[0].'["'.$this->sort_by.'"], $'.$var[1].'["'.$this->sort_by.'"]);'
				);

		uasort($this->log, $myfunc);

	}

	function _getPageParam() {

		if (($page = common::getCommand('page')) === false) {
			$page = 1;
		}

		$page = intval($page);

		if ($page <= 0) {
			$message = gpOutput::SelectText('invalid page value');
			message($message);
			$page = 1;
		}

		$this->page = $page;

	}

	function _makePageHtml() {

		$pagination = array();

		$query = array();
		$query[] = $this->search_query;
		$query[] = $this->sort_query;
		$query_string = trim(implode('&', array_filter($query)));
		 
		$url = $this->root_url . '?' . ($query_string ? $query_string . '&': '');
			
		$pagination['first'] = ($this->page > 1 ? $url . 'page=1' : '');
		$pagination['prev'] = ($this->page > 1 ? $url . 'page=' . strval($this->page - 1) : '');
		$pagination['next'] = ($this->page < $this->total ?  $url . 'page=' . strval($this->page + 1) : '');
		$pagination['last'] = ($this->page < $this->total ? $url . 'page=' . $this->total : '');

		$this->pagination = $pagination;

	}

	function _doPage() {

		$limit = $this->limit;
		$count_log = count($this->log);
		$total = ceil($count_log / $limit);

		if ($this->page > $total) {
			$message = gpOutput::SelectText('invalid page value');
			message($message);
			$this->page = 1;
		}

		$this->page_query = ($this->page > 1 ? '&page=' . $this->page : '');
		$this->total = $total;
		$this->count_log = $count_log;

		$this->log = array_slice(	$this->log, 
						$this->limit * ($this->page - 1), 
						$this->limit,
						true // preserve_keys = true (important)
					);

	}

	function _makeSearchHtml() {

		global $config;

		$new_log = array();

		$properties = array('email', 'username', 'ip');

		foreach ($this->log as $item) {

			$new_item = array();

			// Email, Username & IP
			foreach ($properties as $prop) {

				// Empty $item[$prop] case
				// ==> No need to create a link
				if (empty($item[$prop])) {
					$new_item[$prop] = '&nbsp;';
					continue;
				}
	
				// Normal case
				$query = array();
				$query[] = 'search_key=' . $prop . '&search_val=' . urlencode($item[$prop]);
				$query[] = $this->sort_query;
				$query_string = trim(implode('&', array_filter($query)));
				$url = $this->root_url . ($query_string ? '?' . $query_string: '');

				$new_item[$prop] 	= '<a href="' . $url . '">'
							. '<span style="color: ' . $this->_setColor($item[$prop . '_status']) . ';">' 
							. $item[$prop] 
							. '</span>'
							. '</a>'
							;
	
			}
			
			// Date
			$query = array();
			$query[] = 'search_key=date&search_val=' . urlencode($item['date']);
			$query[] = $this->sort_query;
			$query_string = trim(implode('&', array_filter($query)));
			$url = $this->root_url . ($query_string ? '?' . $query_string: '');

			$new_item['date']	= '<a href="' . $url . '">'
						. strftime($config['dateformat'], $item['date'])
						. '</a>'
						;

			// Form
			$query = array();
			$query[] = 'search_key=formid&search_val=' . urlencode($item['formid']);
			$query[] = $this->sort_query;
			$query_string = trim(implode('&', array_filter($query)));
			$url = $this->root_url . ($query_string ? '?' . $query_string: '');

			$new_item['formid']	= '<a href="' . $url . '">'
						. $this->forms[$item['formid']]['name']
						. '</a>'
						;

			$new_log[] = $new_item;

		}

		$this->log = $new_log;

	}

	function _showLog() {

		global $langmessage, $addonFolderName, $addonPathCode, $addonRelativeCode;

		// We load the showLog template
		//gpPlugin::incl('Admin/ShowLog/ShowLog_Tmpl.php');
		//include($addonPathCode.'/Admin/ShowLog/ShowLog_Tmpl.php');
		$this->_incl('Admin/ShowLog/ShowLog_Tmpl.php');

	}


	/////////////////////////////////////////////////////////////////////
	// PRIVATE METHODS - OTHER
	/////////////////////////////////////////////////////////////////////

	function _setColor($status) {

		switch ($status) {
			case -1:
				return $this->config['color_not_tested'];
				break;
			case 0:
				return $this->config['color_negative'];
				break;
			case 1:
				return $this->config['color_positive'];
				break;
		}

	}


	function _makeOrderingUrl($column) {

		$dir = 'asc';
		
		if ($this->do_sort && ($column == $this->sort_by)) {
			switch ($this->sort_dir) {
				case 'asc':
					$dir = 'desc';
					break;
				case 'desc':
					$dir = '';
					break;
			}

		} 

		$query = array();
		$query[] = ($dir ? 'sort_by=' . $column . '&sort_dir=' . $dir : '');
		$query[] = $this->search_query;
		$query[] = $this->page_query;
		$query_string = trim(implode('&', array_filter($query)));
		 
		$url = $this->root_url . ($query_string ? '?' . $query_string : '');

		return $url;

	}

	function _makeOrderingArrow($column) {

		$arrow = '&uarr;';
		$visibility = 'hidden';

		if ($this->do_sort && ($column == $this->sort_by)) {			
			if ($this->sort_dir == 'desc') {
				$arrow = '&darr;';
			}
			$visibility = 'visible';		
		} 

		$arrow = '<span style="visibility: ' . $visibility . '">' . $arrow . '</span>';

		return $arrow;

	}

	function _transformSearchValue($search_val, $search_key) {

		switch ($search_key) {

			case 'date':
				return strftime('%m/%d/%y', intval($search_val));	
				break;
			case 'ip':
			case 'username':
			case 'email':
			case 'formid':
			default:
				return $search_val;
				break;
		}

	}

	function _getValidValues($search_key) {

		switch ($search_key) {

			case 'date': // We compare the date only, not the time
				$myfunc = create_function(
					'$a', 
					'return strftime(\'%m/%d/%y\', $a["'.$search_key.'"]);'
				);
				break;
			case 'formid': // form ids are integer, they should casted to string
				$myfunc = create_function(
					'$a', 
					'return strval($a["'.$search_key.'"]);'
				);
				break;
			case 'ip':
			case 'username':
			case 'email':
			default:
				$myfunc = create_function(
					'$a', 
					'return $a["'.$search_key.'"];'
				);
				break;
		}

		return array_unique(array_map($myfunc, $this->log_backup));

	}


} // End AntiSpamSFS_Admin_ShowLog class



<?php defined('is_running') or die('Not an entry point...');

gpPlugin::incl('Common/AntiSpamSFS.php');

class AntiSpamSFS_Gadget extends AntiSpamSFS {

	// Log
	var $log_search; // array

	// Constructor
	function AntiSpamSFS_Gadget() {

		global $langmessage;

		// Parent Class Constructor
		parent::AntiSpamSFS();

		// Load the addon config
		$this->_loadConfig();

		// Load the (raw) log data
		if ($this->_loadLog(false) === false) {
			echo '<h3>'.gpOutput::SelectText('Latest Blocked Spammers').'</h3>';
			echo '<p>'.gpOutput::SelectText('Log file empty or logging feature disabled.').'</p>';
			return;
		}

		// Load the forms data
		$this->_loadForms();

		// Process the raw log data accordingly
		$this->_doSelectForm();		
		$this->_doSelectCriteria();
		$this->_doSelectLimit();

		if (!count($this->log_search)) {
			echo '<h3>'.gpOutput::SelectText('Latest Blocked Spammers').'</h3>';
			echo '<p>'.gpOutput::SelectText('There is currently no spammers in the logs corresponding to the gadget settings.').'</p>';
			return;
		}

		// Make the html
		$this->_makeTitles();
		$this->_makeLinks();
		
		// Load the html template
		$this->_showGadget();

	}

	/////////////////////////////////////////////////////////////////////
	// PRIVATE METHODS - MAIN
	/////////////////////////////////////////////////////////////////////

	function _doSelectForm() {

		if (!strlen($this->config['gadget_form'])) {
			$this->log_search = $this->log;
			return;
		}
		
		// Let's search the log array
		$myfunc = create_function(
				'$a', 
				'return ($a["formid"] == "'.$this->config['gadget_form'].'");'
				);

		$this->log = array_filter($this->log, $myfunc);
		$this->log_search = $this->log;

	}

	function _doSelectCriteria() {

		if (!count($this->log_search)) {
			return;
		}

		$myfunc = create_function(
				'$a', 
				'return ($a["'.$this->config['gadget_criteria'].'_status"] == 1);'
				);

		$this->log_search = array_filter($this->log_search, $myfunc);

	}

	function _doSelectLimit() {

		if (!count($this->log_search)) {
			return;
		}

		$this->log_search = array_slice($this->log_search, 0, $this->config['gadget_limit']);

	}

	function _makeTitles() {

		global $config;

		$new_log_search = array();		

		foreach ($this->log_search as $log) {
			$date = strftime($config['dateformat'], $log['date']);
			$log['title'] = sprintf(gpOutput::SelectText('Spammer detected and blocked on the %s'), $date);
			$new_log_search[] = $log;
		}

		$this->log_search = $new_log_search;

	}


	function _makeLinks() {

		$new_log_search = array();		

		foreach ($this->log_search as $log) {
			$log['link'] = 'http://www.stopforumspam.com/search?q=' . urlencode($log[$this->config['gadget_criteria']]);
			$new_log_search[] = $log;
		}

		$this->log_search = $new_log_search;

	}
	
	function _showGadget() {

		global $page, $addonFolderName, $GP_DEFAULT;

		// We load the showLog template
		//$this->_incl('Site/AntiSpamSFS_Gadget_Tmpl.php');

		$tmpl = 'Site/AntiSpamSFS_Gadget_Tmpl.php';

		$default = (isset($GP_DEFAULT) ? $GP_DEFAULT : 'Default');
		$html_default = $page->theme_dir . '/' . $default . '/html/' . $addonFolderName . '/' . $tmpl;
		$html_current = $page->theme_dir . '/' . $page->theme_color . '/html/' . $addonFolderName . '/' . $tmpl;

		if (file_exists($html_current)) {
			include($html_current);
		} elseif (file_exists($html_default)) {
			include($html_default);
		} else {
			$this->_incl($tmpl);
		}

	}

}



<?php
defined('is_running') or die('Not an entry point...');

gpPlugin::incl('Common/AntiSpamSFS.php');

class AntiSpamSFS_Site extends AntiSpamSFS {
	
	var $check_list; // array
	var $use_it; // array
	var $user_data; // array
	var $is_bad; // array

	function Form($html){
		return $html;
	}

	// Added v1.3
	// PageRunScript Hook
	function PageRunScript($cmd) {

		// Start - Workaround - gpEasy bug
		// Solved in gpEasy 3.0.3
		// Prevents this method from being executed twice
		static $already_called = false;

		if ($already_called) {
			return $cmd;
		}

		$already_called = true;
		// End - Workaround

		// The $_POST is empty? go away
		if (!isset($_POST) || empty($_POST)) {
			return $cmd;
		}

		$this->_loadConfig();

		// Load the forms data
		$this->_loadForms();

		// No form defined yet? Go away
		if (empty($this->forms)) {
			return $cmd;
		}

		// The actual submitted form is not in the forms list? Go away
		if (!$this->_whichForm('PageRunScript')) {
			return $cmd;
		}

		// We increment the hits counter of the form
		$this->_incrementHitsCounter();

		// Get the form data from the $_POST superglobal
		if (!$this->_getFormData()) {
			return $cmd;
		}

		// Get the data from the Stop Forum Spam server		
		if (!$this->_getXMLFromSFS()) {
			return $cmd;
		}		
	
		// Check the form data against the SFS ones
		if($this->_isSpammerDetected()) {
			$this->_incrementSpammersCounter();
			$this->_logSpammer();
			$this->_notifyAdmin();
			$this->_notifySpammer();
			$this->_unsetCmd(); // This does prevent the form from being processed
		}

		return $cmd;
	}

	// AntiSpam_Check Hook
	function Check($passed) {

		// Start - Workaround - gpEasy bug
		// Solved in gpEasy 3.0.3
		// Prevents this method from being executed twice
		static $already_called = false;

		if ($already_called) {
			return;
		}

		$already_called = true;
		// End - Workaround

		// The $_POST is empty? go away
		if (!isset($_POST) || empty($_POST)) {
			return $passed;
		}

		$this->_loadConfig();

		// Load the forms data
		$this->_loadForms();

		// No form defined yet? Go away
		if (empty($this->forms)) {
			return $passed;
		}

		// The actual submitted form is not in the forms list? Go away
		if (!$this->_whichForm()) {
			return $passed;
		}

		// We increment the hits counter of the form
		$this->_incrementHitsCounter();

		// Get the form data from the $_POST superglobal
		if (!$this->_getFormData()) {
			return $passed;
		}

		// Get the data from the Stop Forum Spam server		
		if (!$this->_getXMLFromSFS()) {
			return $passed;
		}		
	
		// Check the form data against the SFS ones
		if($this->_isSpammerDetected()) {
			$this->_incrementSpammersCounter();
			$this->_logSpammer();
			$this->_notifyAdmin();
			$this->_notifySpammer();
			return false;
		}

		return $passed;
	}

	/////////////////////////////////////////////////////////////////////
	// PRIVATE METHODS
	/////////////////////////////////////////////////////////////////////

	function _whichForm($hook = 'AntiSpam_Check') {

		global $gp_index, $page;

		//$title = common::WhichPage();
		$title = $page->title;
		//$title = $this->_whichPage();

		if (!isset($gp_index[$title])) {
			return false;
		}

		$index = $gp_index[$title];

		foreach ($this->forms as $id => $form) {

			// Published?
			if (!$form['published']) {
				continue;
			}

			// Added v1.3
			// hook
			if ($form['hook'] != $hook) {
				continue;
			}
			
			// Page title
			if ($index !== $form['control_title']) {
				continue;
			}

			// Command
			$cmd = common::getCommand($form['control_cmd_name']);
			if ($cmd !== $form['control_cmd_value']) {
				continue;
			}

			// Got it!
			$this->form = $form;
			$this->formid = $id;

			return true;

		}

		return false;
	}

	function _getFormData() {

		$this->check_list = array('email', 'username', 'ip');
		
		// Which criteria should we use/check?
		$this->use_it = array(	'email' => min($this->config['use_email'], $this->form['criteria_email']), 
					'username' => min($this->config['use_username'], $this->form['criteria_username']), 
					'ip' => $this->config['use_ip']
					);
		// Criteria aliases
		$email_alias = (trim($this->form['criteria_email_alias']) ? trim($this->form['criteria_email_alias']) : 'email');
		$username_alias = (trim($this->form['criteria_username_alias']) ? trim($this->form['criteria_username_alias']) : 'email');

		// Get posted email & username 
		$this->user_data['email'] = (isset($_POST[$email_alias]) ? trim($_POST[$email_alias]) : '');
		$this->user_data['username'] = (isset($_POST[$username_alias]) ? trim($_POST[$username_alias]) : '');
	
		includeFile('tool/sessions.php');
		$this->user_data['ip'] = gpsession::clientIP(true);

		// No need to test invalid data
		// Changed in v1.2 
		$new_use_it = array();
		foreach ($this->use_it as $key => $val) {
			$new_use_it[$key] = ($val && $this->_isValid($this->user_data[$key], $key) ? $val : 0);
		}

		// Nothing to test, let's the form processing go ahead
		if (max($new_use_it) == 0) {
			return false;
		}		

		$this->use_it = $new_use_it;

		return true;	

	}

	function _getXMLFromSFS() {
	
		// Initialisations
		$this->is_bad = array();
		$return = true;
	
		foreach ($this->check_list as $check_element) {
		
			$this->is_bad[$check_element] = 'not tested';
			if (!$this->use_it[$check_element]) {
				continue;
			}

			$url = $this->config['base_url'] . "api?" . $check_element . "=" . urlencode($this->user_data[$check_element]);
			if (!($xml = $this->_openConnexionToSFS($url))) {
				$return = false;
				break;
			}

			if (!($is_bad = $this->_readXML($xml))) {
				$return = false;
				break;
			}
			// At this stage $is_bad contains either 'yes' or 'no'
			// ==> We save the value
			$this->is_bad[$check_element] = $is_bad;
		
		}
		
		return $return;
	
	}
	
	function _isSpammerDetected() {
	
		// $bad is a binary array 
		// 1 : Positive
		// 0 : Negative
		$bad = array();

		foreach ($this->check_list as $check_element) {
			if ($this->use_it[$check_element]) {
				$bad[] = ($this->is_bad[$check_element] == 'yes' ? 1 : 0);
			}
		}		

		// Filter Power application
		// Example 1: Max(1,0) ==> 1 ==> We block the form processing
		// Example 2: Min(1,0) ==> 0 ==> We let the form processing go further
		// Example 3: Min(1,1) ==> 1 ==> We block the form processing
		$is_bad_user_eval = $this->config['filter_power']. '($bad)';
		eval( "\$is_bad_user = $is_bad_user_eval;" );

		return $is_bad_user;
		
	}

	function _openConnexionToSFS($url) {

		// Start - Request SFS data using cUrl
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->config['curlopt_timeout']);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->config['curlopt_connect_timeout']);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$ret = curl_exec($ch);

		curl_close($ch);
		// End - Request SFS data using cUrl

		// Something went wrong during the request
		if (!$ret) {
			return false;
		} 

		return $ret;
			
	}

	function _readXML($xml) {
		
		if (preg_match("#<appears>(.*)</appears>#Ui", $xml, $out)) {
			return $out[1]; // yes/no
		} else {
			return false;
		}
			
	}

	function _notifySpammer() {

		// Spammer message
		$message = gpOutput::SelectText('Spammer Detected! The form processing was aborted.');
		message($message);

	}

	function _notifyAdmin() {

		// Admin notification
		if (!$this->config['notify']) {
			return;
		}

		global $gp_mailer;
		includeFile('tool/email_mailer.php');

		$email_subject = gpOutput::SelectText('AntiSpamSFS - Spammer Notification');
		$email_text = gpOutput::SelectText('A spammer has been detected and the form processing stopped.');
		$email_text .= "\n";
		$email_text .= "-----------------------------\n";
		$email_text .= 'Form: ' . $this->form['name'] . "\n";
		$email_text .= 'Form ID: ' . $this->formid . "\n";
		$email_text .= "-----------------------------\n";
		$email_text .= 'IP : ' . $this->user_data['ip'] . ' (' . $this->is_bad['ip'] . ')' . "\n";
		$email_text .= 'Email : ' . $this->user_data['email'] . ' (' . $this->is_bad['email'] . ')' . "\n";
		$email_text .= 'Username : ' . $this->user_data['username'] . ' (' . $this->is_bad['username'] . ')' . "\n";	

		$email_text = nl2br($email_text);

		$gp_mailer->SendEmail($this->config['notify_email'], $email_subject, $email_text);

	}
	
	function _logSpammer() {

		if (!$this->config['log_spammers']) {
			return;
		}

		// Load the log array
		global $addonPathData;

		$log_file = $addonPathData.'/log.php';

		if (file_exists($log_file)) {
			include($log_file);
		} else {
			$log = array();
		}

		// Add the new entry to the log array
		$spammer = array();

		// We store only valid data (to prevent html injection)
		// Changed in v1.2
		foreach ($this->check_list as $check_elt) {
			$spammer[$check_elt] = ($this->_isValid($this->user_data[$check_elt], $check_elt) ? $this->user_data[$check_elt] : '' );
		}		

		$spammer['email_status'] = $this->_getStatus($this->use_it['email'], $this->is_bad['email']);
		$spammer['username_status'] = $this->_getStatus($this->use_it['username'], $this->is_bad['username']);
		$spammer['ip_status'] = $this->_getStatus($this->use_it['ip'], $this->is_bad['ip']);

		$spammer['date'] = time();
		$spammer['formid'] = $this->formid;

		array_unshift($log, $spammer);
						
		// save the log array
		gpFiles::SaveArray($log_file,'log',$log);
			
	}

	function _incrementHitsCounter() {

		$this->form['count_hits']++; // +1
		$this->forms[$this->formid] = $this->form;
		$this->_saveForms(false);

	}

	function _incrementSpammersCounter() {

		$this->form['count_spammers']++; // +1
		$this->forms[$this->formid] = $this->form;
		$this->_saveForms(false);

	}
	
	function _getStatus($use, $is_bad) {
	
		if (!$use) return -1;
		
		if ($is_bad == 'yes') return 1;
		
		return 0;
		
	}

	// Added in v1.3
	function _unsetCmd() {

		unset($_POST["cmd"]);
		unset($_GET["cmd"]);
		unset($_REQUEST["cmd"]);

	}

	// NOT USED
	function _redirectSpammer() {

		$message = gpOutput::SelectText('Spammer Detected! The form processing was aborted.');
		$this->_message($message);
		common::Redirect(common::GetUrl($this->form['control_title']), 302);

	}
		
	// NOT USED
	function _whichPage(){
		global $config, $gp_internal_redir, $gp_menu;

		if( isset($gp_internal_redir) ){
			return $gp_internal_redir;
		}

		$path = common::CleanRequest($_SERVER['REQUEST_URI']);

		$pos = mb_strpos($path,'?');
		if( $pos !== false ){
			$path = mb_substr($path,0,$pos);
		}

		if( empty($path) ){
			return $config['homepath'];
		}

		return $path;
	}

}

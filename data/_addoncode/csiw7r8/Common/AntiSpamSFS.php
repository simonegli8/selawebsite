<?php
defined('is_running') or die('Not an entry point...');

class AntiSpamSFS {

	// Super Admin ?
	var $isSuperAdmin; // Boolean

	// Configuration
	var $config; // array

	// Log data
	var $log_file; // string
	var $log; // array
	var $log_backup; // array

	// Forms list
	var $forms; // array

	// Current form data
	var $form; // array

	// Current form id
	var $formid; // int

	// Constructor
	function AntiSpamSFS() {

		global $addonPathData, $gpAdmin;
		gpFiles::CheckDir($addonPathData);

		$this->isSuperAdmin = ($gpAdmin['granted'] == 'all' ? true : false);

	}

	// Unstack the messages stored in the session
	// Called when the GetContent_After hook is fired
	function Messages() {

		$this->_session_start();

		if (!isset($_SESSION['AntiSpamSFS']['messages'])) {
			return;
		}

		if (empty($_SESSION['AntiSpamSFS']['messages'])) {
			return;
		}

		foreach ($_SESSION['AntiSpamSFS']['messages'] as $message) {
			message($message);
		}

		$_SESSION['AntiSpamSFS']['messages'] = array();

	}

	/////////////////////////////////////////////////////////////////////
	// PRIVATE METHODS
	/////////////////////////////////////////////////////////////////////

	// Dump of the gpPlugins::incl() method
	function _incl($file){
		global $addonPathCode;
		if( gp_safe_mode ){
			return;
		}
		return include_once($addonPathCode.'/'.$file);
	}

	function _loadConfig() {

		global $addonPathData;

		$cfg_file = $addonPathData.'/config.php';

		if (file_exists($cfg_file)) {
			include($cfg_file);
		} else {
			$cfg = $this->_loadDefaults($cfg_file);
		}

		$this->config = $cfg;

	}

	function _loadDefaults($cfg_file) {

		global $addonPathData, $config;

		//use default addon configuration
		$cfg = array();

		$cfg['base_url'] = 'http://www.stopforumspam.com/';
		$cfg['filter_power'] = 'max';

		$cfg['use_email'] = 1;
		$cfg['use_username'] = 0;
		$cfg['use_ip'] = 1;

		$cfg['curlopt_timeout'] = 10;
		$cfg['curlopt_connect_timeout'] = 10;

		$cfg['notify'] = 0;
		$cfg['notify_email'] = $config['toemail'];

		$cfg['log_spammers'] = 0;
		$cfg['log_spammers_limit'] = 15;
		$cfg['color_not_tested'] = '#808080'; // grey
		$cfg['color_positive'] = '#ff0000'; // red
		$cfg['color_negative'] = '#0000ff'; // blue

		$cfg['percent_decimals'] = 2; 
		$cfg['percent_color'] = 0;
		$cfg['percent_threshold'] = 25; 

		$cfg['gadget_form'] = ''; // All forms
		$cfg['gadget_criteria'] = 'email';
		$cfg['gadget_limit'] = 3; 
 
		$cfg['p_save_config'] = 0;
		$cfg['p_save_form'] = 0;
		$cfg['p_delete_form'] = 0;
		$cfg['p_publish_form'] = 0;
		$cfg['p_delete_log'] = 0;

		if (gpFiles::SaveArray($cfg_file,'cfg',$cfg)) {
			$message = gpOutput::SelectText('Default settings saved.');
			message($message);
		}

		return $cfg;

	}

	function _loadLog($msg=true) {

		global $addonFolderName, $langmessage, $addonPathData;

		if (!$this->config['log_spammers']) {
			if ($msg) {
				$message = gpOutput::SelectText('Spammer logging is currently disabled.');
				echo $message;
			}
			return false;
		}

		$this->log_file = $addonPathData.'/log.php';

		if (!file_exists($this->log_file)) {
			if ($msg) {
				$message = gpOutput::SelectText('No log file found.');
				echo $message;
			}
			return false;
		} 

		// Load the log array
		include($this->log_file);

		if (!count($log)) {
			if ($msg) {
				$message = gpOutput::SelectText('No log stored yet.');
				echo $message;
			}	
			return false;
		}

		$this->log = $this->log_backup = $log;

		return true;

	}

	/* load all existing forms list */
	function _loadForms() {

		global $addonPathData;

		if (file_exists($addonPathData.'/forms.php')) {
			include($addonPathData.'/forms.php');
			$this->forms = $forms;
		} else {
			$this->forms = $this->_loadContactForm();
		}

	}
	
	// Default Contact Form
	function _loadContactForm() {

		global $addonPathData;

		$forms = array();
		$form = array();

		$form['name'] = 'Contact';
		$form['description'] = 'Built-in gpEasy contact form';
		$form['published'] = 1; // 0==unpublished, 1==published
		$form['hook'] = 'AntiSpam_Check'; // Added v1.3 
		$form['control_title'] = 'special_contact';
		$form['control_cmd_name'] = 'cmd';
		$form['control_cmd_value'] = 'gp_send_message';
		$form['criteria_email'] = 1; // 0==not used, 1==use it
		$form['criteria_email_alias'] = ''; 
		$form['criteria_username'] = 0; // 0==not used, 1==use it
		$form['criteria_username_alias'] = 'name'; 
		$form['count_hits'] = 0;
		$form['count_spammers'] = 0;

		$forms[] = $form;

		//gpFiles::CheckDir($addonPathData);
		if (gpFiles::SaveArray($addonPathData.'/forms.php','forms',$forms)) {
			$message = gpOutput::SelectText('Default contact form created.');
			message($message);
		}

		return $forms;

	}	

	/* save all existing forms */
	function _saveForms($msg = true){

		global $addonPathData, $langmessage;

		if (gpFiles::SaveArray($addonPathData.'/forms.php', 'forms', $this->forms ) && $msg) {
			$message = gpOutput::SelectText('Forms list saved.');
			message($message);
		}

	}

	function _session_start() {

		if (!isset($_SESSION)) {
			session_start();
		}

	}

	function _message($message) {

		$this->_session_start();

		$_SESSION['AntiSpamSFS']['messages'][] = $message;

	}

	function _cleanText($text) {
		
		return trim(htmlspecialchars(strip_tags(strval($text))));
	
	}

	/**
	 * Generic isValid method
	 *
	 * @param $val
	 * @param $type
	 *
	 * @return
	 *   TRUE if the value has the right type, and FALSE if it doesn't.
	 */
	function _isValid($val, $type) {

		$method =  '_isValid' . ucfirst(strtolower($type));

		if (method_exists($this, $method)) {
			return call_user_func(array($this, $method), $val);
		}

		return false;

	}

	function _isValidHexColor($color) 	{

		if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
			return true; 
		} 
		
		return false; 

	}

	function _isValidEmail($email) 	{

		// Special_contact compliant
		return (bool)preg_match('/^[^@]+@[^@]+\.[^@]+$/', $email);

	}

	function _isValidDate($date) 	{

		$intval_date = intval($date);

		if (!$intval_date) {
			return false;
		}

		if ($intval_date !== $date) {
			return false;
		}
		
		if ($intval_date > intval(time())) {
			return false;			
		}

		return true; 

	}

	function _isValidIp($ip) 	{

		if (ip2long($ip)) {
			return true; 
		} 
		
		return false; 

	}

	function _isValidUsername($username) {

		$username = trim($username);

		if (empty($username)) {
			return false;
		}
		
		// Special_contact compliant
		$clean_username = str_replace(array("\r","\n"),array(' '), $username);
		$clean_username = strip_tags($clean_username);
		$clean_username = htmlspecialchars($clean_username);

		return ($clean_username == $username ? true : false); 

	}

	function _isValidFormid($formid) {

		if (intval($formid) == $formid) {
			return true;
		}

		return false;

	}

	function _isValidNameAttribute($name) {

		if (preg_match('/^[a-zA-Z_:][-a-zA-Z0-9_:.]*$/i', $name)) {
			return true; 
		} 
		
		return false; 

	}
}

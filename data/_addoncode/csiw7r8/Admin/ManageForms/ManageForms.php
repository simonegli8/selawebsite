<?php
defined('is_running') or die('Not an entry point...');

gpPlugin::incl('Common/AntiSpamSFS.php');

class AntiSpamSFS_Admin_ManageForms extends AntiSpamSFS{

	// cmd
	var $cmd; // string

	// Root url
	var $root_url; // string

	function AntiSpamSFS_Admin_ManageForms() { //constructor

		global $langmessage; 

		// Parent Class Constructor
		parent::AntiSpamSFS();

		// Load the addon config
		$this->_loadConfig();
	
		// Load the forms data
		$this->_loadForms();

		// Get the form id
		// New form & forms list ==> -1
		$this->_getFormid();

		// Set the root url
		$this->_setRootUrl();
		
		// Save form?
		if ($this->_doSaveForm()) {
			if ($this->_canSaveForm()) {
				$this->_updateForm();
				if (!$this->_isValidForm()) {
					$this->_showForm();
					return;
				} else {
					$this->_saveForm();
					common::Redirect($this->root_url, 302);
				}
			} else {
				message($langmessage['not_permitted']);
			}
		}

		// Delete form?
		if ($this->_doDeleteForm()) {
			if ($this->_canDeleteForm()) {
				$this->_deleteForm();
				common::Redirect($this->root_url, 302);
			} else {
				message($langmessage['not_permitted']);
			}
		}

		// Published/Unpublished switch?
		if ($this->_doPublishedSwitch()) {
			if ($this->_canPublishForm()) {
				$this->_publishedSwitcher();
				common::Redirect($this->root_url, 302);
			} else {
				message($langmessage['not_permitted']);
			}
		}

		// Get cmd param
		$this->_getCmd();

		switch ($this->cmd) {
			// Single form edition/creation
			case 'new_form': 
			case 'edit_form': 
				$this->_loadForm();
				$this->_showForm(); 
				break;
			// All Forms listing
			default:
				$this->_showformsList(); 
				break;
		}
		
	}


	/////////////////////////////////////////////////////////////////////
	// PRIVATE METHODS - MAIN
	/////////////////////////////////////////////////////////////////////

	function _canPublishForm() {

		global $gpAdmin;

		// Super Admin?
		if ($this->isSuperAdmin) {
			return true;
		}

		// Check user permission
		if ($this->config['p_publish_form']) {
			return true;
		}

		return false;		

	}

	function _canSaveForm() {

		global $gpAdmin;

		// Super Admin?
		if ($this->isSuperAdmin) {
			return true;
		}

		// Check user permission
		if ($this->config['p_save_form']) {
			return true;
		}

		return false;		

	}

	function _canDeleteForm() {

		global $gpAdmin;

		// Super Admin?
		if ($this->isSuperAdmin) {
			return true;
		}

		// Check user permission
		if ($this->config['p_delete_form']) {
			return true;
		}

		return false;		

	}

	function _setRootUrl() {

		$this->root_url =  'http://' 
				. $_SERVER['SERVER_NAME'] 
				. common::GetUrl('Admin_AntiSpamSFS_ManageForms')
				;

	}

	function _doSaveForm() {

		return (isset($_POST['cmd']) && ($_POST['cmd'] == 'save_form') ? true : false);

	}

	function _isValidForm()	{

		$errors = array();

		// START - CHECK LIST

		// Name
		if (!$this->form['name']) {
			$msg = gpOutput::SelectText('Field %s should not be empty');
			$msg = sprintf($msg, 'Name');
			$errors[] = $msg;
		}

		// Control - Page Title
		if (!$this->form['control_title']) {
			$msg = gpOutput::SelectText('Field %s should not be empty');
			$msg = sprintf($msg, 'Page title');
			$errors[] = $msg;
		}

		// Control - cmd name
		if (!$this->form['control_cmd_name']) {
			$msg = gpOutput::SelectText('Field %s should not be empty');
			$msg = sprintf($msg, $this->form['control_cmd_name']);
			$errors[] = $msg;
		} elseif (!$this->_isValidNameAttribute($this->form['control_cmd_name'])) {
			$msg = gpOutput::SelectText('%s is not a valid name attribute');
			$msg = sprintf($msg, $this->form['control_cmd_name']);
			$errors[] = $msg;
		}

		// Control - cmd value
		if (!$this->form['control_cmd_value']) {
			$msg = gpOutput::SelectText('Field %s should not be empty');
			$msg = sprintf($msg, $this->form['control_cmd_name']);
			$errors[] = $msg;
		}

		// Email alias
		if (!empty($this->form['criteria_email_alias'])) { // Enpty is ok ==> 'email' will be used
			if (!$this->_isValidNameAttribute($this->form['criteria_email_alias'])) { // Valid?
				$msg = gpOutput::SelectText('%s is not a valid name attribute');
				$msg = sprintf($msg, $this->form['criteria_email_alias']);
				$errors[] = $msg;
			}
		}

		// Username alias
		if (!empty($this->form['criteria_username_alias'])) { // Enpty is ok ==> 'username' will be used
			if (!$this->_isValidNameAttribute($this->form['criteria_username_alias'])) { // Valid?
				$msg = gpOutput::SelectText('%s is not a valid name attribute');
				$msg = sprintf($msg, $this->form['criteria_username_alias']);
				$errors[] = $msg;
			}
		}			

		// END - CHECK LIST

		// Have we got some errors?
		if (empty($errors)) {
			return true;
		}

		// Output the error message and return false
		if (count($errors) == 1) {
			$message =  gpOutput::SelectText('One error has been found in the form:');
		} else {
			$message =  gpOutput::SelectText('Some errors have been found in the form:');
		}

		$message = '<p style="text-align: left">' . $message . '</p>';

		$message .= '<ul>';
		foreach ($errors as $error) {
			$message .= '<li>' . $error . '</li>';
		}
		$message .= '</ul>';
	
		message($message);

		return false;
	}

	function _saveForm() {

		//$formid = intval($_POST['formid']);
		$formid = $this->formid;

		if ($formid == -1) {
			$this->forms[] = $this->form;
			end($this->forms); // Added v1.2
			$formid = key($this->forms); // Added v1.2
			krsort($this->forms); // Set the latest created form first
		} else {
			$this->forms[$formid] = $this->form;
		}

		$this->_saveForms();

		$message = gpOutput::SelectText('Form %s saved!');
		$message = sprintf($message, $this->forms[$formid]['name']);
		$this->_message($message);

	}

	function _doDeleteForm() {

		return (isset($_POST['cmd']) && ($_POST['cmd'] == 'delete_form') ? true : false);

	}
	
	function _deleteForm() {

		$formid = $this->formid;
		$name = $this->forms[$formid]['name'];

		// Delete form
		if (isset($this->forms[$formid])) {
			unset($this->forms[$formid]);
			$this->_saveForms();
		}

		// Delete associated logs
		if ($this->_loadLog(true) === false) {
			return;
		}

		foreach ($this->log as $key => $val) {
			if ($val['formid'] == $formid) {
				unset($this->log[$key]);
			}
		}

		$this->log = array_values($this->log);
		gpFiles::SaveArray($this->log_file,'log',$this->log);

		$message = gpOutput::SelectText('Form %s deleted!');
		$message = sprintf($message, $name);
		$this->_message($message);

	}

	function _doPublishedSwitch() {

		return (isset($_POST['cmd']) && ($_POST['cmd'] == 'published_switch') ? true : false);

	}

	function _publishedSwitcher() {

		$formid = $this->formid;
		$this->forms[$formid]['published'] = 1 - $this->forms[$formid]['published'];
		$this->_saveForms();

		switch ($this->forms[$formid]['published']) {
			case 1:
				$status = 'published';
				break;
			case 0:
				$status = 'unpublished';
				break;
			default:
				$status = 'unknown';
				break;				
		}

		$message = gpOutput::SelectText('Form %s is now %s!');
		$message = sprintf($message, $this->forms[$formid]['name'], $status);
		$this->_message($message);

	}

	function _getCmd() {

		$this->cmd = (isset($_GET['cmd']) ? $_GET['cmd'] : false);

	}

	function _getFormid() {

		if (isset($_POST['formid'])) {
			$this->formid = intval($_POST['formid']);
			return;
		}
		
		if (isset($_GET['formid'])) {
			$this->formid = intval($_GET['formid']);
			return;
		}
		
		$this->formid = -1;

	}
	
	/* show all existing forms */
	function _showFormsList() {

		global $langmessage, $addonPathCode;

		// We load the editConfig.php template
		//gpPlugin::incl('Admin/ManageForms/ManageForms_FormsList_Tmpl.php');
		//include($addonPathCode.'/Admin/ManageForms/ManageForms_FormsList_Tmpl.php');
		$this->_incl('Admin/ManageForms/ManageForms_FormsList_Tmpl.php');

	}
	
	/* update a form by using posted data */
	function _updateForm() {

		//$this->_loadForm($formid);

		$form = array();

		$form['name'] = $this->_cleanText($_POST['name']);
		$form['description'] = $this->_cleanText($_POST['description']);
		$form['published'] = intval($_POST['published']); // 0==unpublished, 1==published

		$form['hook'] = strval($_POST['hook']); // Added v1.3

		$form['control_title'] = strval($_POST['control_title']);

		$buffer = $this->_cleanText($_POST['control_cmd_name']);
		$form['control_cmd_name'] = (empty($buffer) ? 'cmd' : $buffer);

		$form['control_cmd_value'] = $this->_cleanText($_POST['control_cmd_value']);
		$form['criteria_email'] = intval($_POST['criteria_email']); // 0==not used, 1==use it
		$form['criteria_email_alias'] = $this->_cleanText($_POST['criteria_email_alias']); 
		$form['criteria_username'] = intval($_POST['criteria_username']); // 0==not used, 1==use it
		$form['criteria_username_alias'] = $this->_cleanText($_POST['criteria_username_alias']); 
		$form['count_hits'] = intval($_POST['count_hits']);
		$form['count_spammers'] = intval($_POST['count_spammers']);

		$this->form = $form;
	
	}

	/* load form by id */
	function _loadForm(){

		$formid = $this->formid;
		$this->form = array();
		
		// New form
		if ($formid == -1) {

			$this->form['name'] = '';
			$this->form['description'] = '';
			$this->form['hook'] = 'AntiSpam_Check'; // Added v1.3
			$this->form['published'] = 0; // 0==unpublished, 1==published
			$this->form['control_title'] = '';
			$this->form['control_cmd_name'] = 'cmd';
			$this->form['control_cmd_value'] = '';
			$this->form['criteria_email'] = 0; // 0==not used, 1==use it
			$this->form['criteria_email_alias'] = ''; 
			$this->form['criteria_username'] = 0; // 0==not used, 1==use it
			$this->form['criteria_username_alias'] = ''; 
			$this->form['count_hits'] = 0;
			$this->form['count_spammers'] = 0;
			
			return true;

		}

		// Edit an existing form
		if (isset($this->forms[$formid])) {

			$this->form['name'] = $this->forms[$formid]['name'];
			$this->form['description'] = $this->forms[$formid]['description'];
			$this->form['hook'] = $this->forms[$formid]['hook']; // Added v1.3
			$this->form['published'] = $this->forms[$formid]['published']; // 0==unpublished, 1==published
			$this->form['control_title'] = $this->forms[$formid]['control_title'];
			$this->form['control_cmd_name'] = $this->forms[$formid]['control_cmd_name'];
			$this->form['control_cmd_value'] = $this->forms[$formid]['control_cmd_value'];
			$this->form['criteria_email'] = $this->forms[$formid]['criteria_email']; // 0==not used, 1==use it
			$this->form['criteria_email_alias'] = $this->forms[$formid]['criteria_email_alias']; 
			$this->form['criteria_username'] = $this->forms[$formid]['criteria_username']; // 0==not used, 1==use it
			$this->form['criteria_username_alias'] = $this->forms[$formid]['criteria_username_alias']; 
			$this->form['count_hits'] = $this->forms[$formid]['count_hits'];
			$this->form['count_spammers'] = $this->forms[$formid]['count_spammers'];

			return true;
		}

		return false;
	}

	
	function _backLink($msg) {
		global $langmessage;
		echo '<span style="float:right">'.common::Link('Admin_AntiSpamSFS_ManageForms','&#9650;','','title="'.$langmessage['back'].'"').'</span>';
		echo '<div style="font-size:large;margin:1em 0;">'.$msg.'</div>';
	}
	
	function _showForm(){

		global $addonPathData, $addonPathCode, $page, $addonRelativeCode, $langmessage;

		$form = false; // ?
		// We load the form
		//$this->_loadForm($this->formid);

		// Css
		$css	= '<style type="text/css">'
			. '.AntiSpamSFS_Form label{'
			. '	display: block;'
			. '	width: 250px;'
			. '	float: left'
			. '}'
			. '.AntiSpamSFS_Form fieldset{'
			. '	-webkit-border-radius: 8px;'
			. '	-moz-border-radius: 8px;'
			. '	border-radius: 8px;'
			. '	padding: 10px;'
			. '	border:1px solid #ccc;'
			. '	margin: 0 0 10px 0;'
			. '	position: relative'
			. '}'
			. '#control_cmd_name_editable{'
			. '	font-weight: bold;'
			. '}'
			. '</style>'
			;
		$page->head .= $css;

		// jQuery Inline Edit Plugin
		$src = $addonRelativeCode."/jQuery/InlineEdit/jquery.inlineEdit.js";
		$inlineEdit = '<script src="'.$src .'" type="text/javascript"></script>';
		$page->head .= $inlineEdit;

		// jQuery
		$jQuery	= '$(\'#control_cmd_name_editable\').inlineEdit({'
			. '	save: function( event, hash ) {'
			. '		$(\'#control_cmd_name\').val(hash.value);'
			. '	},'
			. '	buttons: \'\','
			/* . '	placeholder: \'cmd\',' */
			. '	saveOnBlur: true'
			. '});'
			;
		$page->jQueryCode .= $jQuery;

		// Backlink		
		if ($this->formid == -1) {
			$backlink = gpOutput::SelectText('Create New form');
		} else {
			$backlink = sprintf(gpOutput::SelectText('Edit Form #%d > %s'), $this->formid, $this->form['name']);
		}
		$this->_backLink($backlink);

		// Load the template
		//gpPlugin::incl('Admin/ManageForms/ManageForms_Form_Tmpl.php');
		//include($addonPathCode.'/Admin/ManageForms/ManageForms_Form_Tmpl.php');
		$this->_incl('Admin/ManageForms/ManageForms_Form_Tmpl.php');
		
	}


	/////////////////////////////////////////////////////////////////////
	// PRIVATE METHODS - OTHER
	/////////////////////////////////////////////////////////////////////

	function _calcPercent($num, $denom, $decimals = 2) {

		//$factor = pow(10, intval($decimals));

		$num = intval($num);
		$denom = intval($denom);
		$decimals = intval($decimals);

		if ($denom == 0) {
			return 'n.a.';
		}

		//return intval( 100 * $factor * $num / $denom) / $factor;
		return number_format(100 * $num / $denom, $decimals);

	}

	// http://stackoverflow.com/questions/25007/conditional-formatting-percentage-to-color-conversion
	function _percent2Color($percent, $threshold = 50){

		

		$percent = 100 * $this->_sigmoide($percent, $threshold);

		$blue = ($percent < 50 ? 255 : round(256 - ($percent - 50) * 5.12) );
		$red = ($percent > 50 ? 255 : round($percent * 5.12) );

		return "rgb(" . $red . ", 0," . $blue . ")";

	}

	function _sigmoide($x, $x0 = 0) {

		$y = 1 / (1 + exp(-(floatval($x) - floatval($x0))));

		return $y;
	}
	
}



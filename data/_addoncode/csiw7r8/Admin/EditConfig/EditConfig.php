<?php defined('is_running') or die('Not an entry point...');

gpPlugin::incl('Common/AntiSpamSFS.php');

class AntiSpamSFS_Admin_EditConfig extends AntiSpamSFS {

	// Constructor
	function AntiSpamSFS_Admin_EditConfig() {

		global $addonFolderName, $langmessage, $addonPathData;

		// Parent Class Constructor
		parent::AntiSpamSFS();

		// Load the forms data
		$this->_loadForms();

		$cfg_file = $addonPathData.'/config.php';

		if (isset($_POST['save_config'])) { //settings
			if ($this->_canSaveConfig()) {
				if ($this->_saveConfig($cfg_file)) {
					message($langmessage['SAVED']);
				} 
			} else {
				message($langmessage['not_permitted']);
			}
		}

		$this->_loadConfig();
		$this->_editConfig();

	}

	/////////////////////////////////////////////////////////////////////
	// PRIVATE METHODS
	/////////////////////////////////////////////////////////////////////

	function _canSaveConfig() {

		global $gpAdmin;

		// Super Admin?
		if ($this->isSuperAdmin) {
			return true;
		}

		// Check user permission
		if ($this->config['p_save_config']) {
			return true;
		}

		return false;		

	}

	function _editConfig() {

		global $langmessage, $addonFolderName, $addonPathCode, $addonRelativeCode, $page;

		// Css
		$css	= '<style type="text/css">'
			. '.AntiSpamSFS_EditConfig label{'
			/* . '     clear: left;' */
			. '	display: block;'
			. '	width: 250px;'
			. '	float: left'
			. '}'
			. '.AntiSpamSFS_EditConfig fieldset{'
			. '	-webkit-border-radius: 8px;'
			. '	-moz-border-radius: 8px;'
			. '	border-radius: 8px;'
			. '	padding: 10px;'
			. '	border:1px solid #ccc;'
			. '	margin: 0 0 10px 0;'
			. '	position: relative'
			. '}'
			. '</style>'
			;
		$page->head .= $css;
		
		// Color Picker
		$colorp	= '<link href="'.$addonRelativeCode.'/jQuery/ColorPicker/colorPicker.css" type="text/css" rel="stylesheet"></script>'
			. '<script src="'.$addonRelativeCode.'/jQuery/ColorPicker/jquery.colorPicker.min.js" type="text/javascript"></script>'
			. '<script type="text/javascript">'
			. ' jQuery(document).ready(function($) {'
			. '    $(\'#color_not_tested\').colorPicker();'
			. '    $(\'#color_positive\').colorPicker();'
			. '    $(\'#color_negative\').colorPicker();'
			. ' });'
			. '</script>'
			;
		$page->head .= $colorp;	

		// Slider	
		$slider	= '<link href="'.$addonRelativeCode.'/jQuery/Slider/jquery.ui.all.css" type="text/css" rel="stylesheet"></script>'
			//. '<link href="'.$addonRelativeCode.'/jQuery/Slider/custom.css" type="text/css" rel="stylesheet"></script>'
			. '<script src="'.$addonRelativeCode.'/jQuery/Slider/jquery.ui.core.js" type="text/javascript"></script>'
			. '<script src="'.$addonRelativeCode.'/jQuery/Slider/jquery.ui.widget.js" type="text/javascript"></script>'
			. '<script src="'.$addonRelativeCode.'/jQuery/Slider/jquery.ui.mouse.js" type="text/javascript"></script>'
			. '<script src="'.$addonRelativeCode.'/jQuery/Slider/jquery.ui.slider.js" type="text/javascript"></script>'
			;
		$page->head .= $slider;	

		// Slider - Css
		$css	= '<style type="text/css">'
			. '#percent_threshold_slider {'		
			. '	width: 250px;'
			. '	float: left;'
			. '	margin: 5px 15px 5px 5px'
			. '}'
			. '#percent_threshold_slider .ui-slider-range {'
			. '	background: blue;'
			. '}'
			. '</style>'
			;
		$page->head .= $css;	

		// Slider - Js
		$js	= '<script type="text/javascript">'
			. 'function refresh() {'
			. '	$("#percent_threshold_slider_value").html( $( "#percent_threshold_slider" ).slider( "value" ));'
			. '	$("#percent_threshold").val( $( "#percent_threshold_slider" ).slider( "value" ));'
			. '}'
			. '$(function() {'
			. '	$( "#percent_threshold_slider" ).slider({'
			. '		orientation: "horizontal",'
			. '		range: "min",'
			. '		max: 1,'
			. '		max: 99,'
			. '		slide: refresh,'
			. '		change: refresh'
			. '	});'
			. '	$( "#percent_threshold_slider" ).slider( "value", ' . $this->config['percent_threshold'] . ' );'
			. '});'
			. '</script>'
			;
		$page->head .= $js;	

		// We load the editConfig.php template
		//gpPlugin::incl('Admin/EditConfig/EditConfig_Tmpl.php');
		//include($addonPathCode.'/Admin/EditConfig/EditConfig_Tmpl.php');
		$this->_incl('Admin/EditConfig/EditConfig_Tmpl.php');

	}

	function _saveConfig($cfg_file) {

		global $config;

		$cfg = array();

		$cfg['base_url'] = trim(strval($_POST['base_url']));

		$cfg['filter_power'] = trim(strval($_POST['filter_power']));

		$check_sum = 0;
		$check_sum += $cfg['use_email'] = intval($_POST['use_email']);
		$check_sum += $cfg['use_username'] = intval($_POST['use_username']);
		$check_sum += $cfg['use_ip'] = intval($_POST['use_ip']);

		if (!$check_sum) {
			$message = 'Attention! At least one criteria (email, username, ip) should be selected.'; 
			$message = gpOutput::SelectText($message);
			message($message);
		}

		$buffer = intval($_POST['curlopt_timeout']);
		$buffer = ($buffer ? $buffer : 10);
		$cfg['curlopt_timeout'] = $buffer;

		$buffer = intval($_POST['curlopt_connect_timeout']);
		$buffer = ($buffer ? $buffer : 10);
		$cfg['curlopt_connect_timeout'] = $buffer;

		$cfg['notify'] = intval($_POST['notify']);
		$buffer = trim(strval($_POST['notify_email']));
		$cfg['notify_email'] = ($this->_isValidEmail($buffer) ? $buffer : $config['toemail']);

		$cfg['log_spammers'] = intval($_POST['log_spammers']);
		$buffer = intval($_POST['log_spammers_limit']);
		$cfg['log_spammers_limit'] = ($buffer ? $buffer : 5);

		$buffer = trim(strval($_POST['color_not_tested']));
		$cfg['color_not_tested'] = ($this->_isValidHexColor($buffer) ? $buffer : '#808080');
		$buffer = trim(strval($_POST['color_positive']));
		$cfg['color_positive'] = ($this->_isValidHexColor($buffer) ? $buffer : '#ff0000');
		$buffer = trim(strval($_POST['color_negative']));
		$cfg['color_negative'] = ($this->_isValidHexColor($buffer) ? $buffer : '#0000ff');

		$cfg['percent_decimals'] = intval($_POST['percent_decimals']);
		$cfg['percent_color'] = intval($_POST['percent_color']);
		$cfg['percent_threshold'] = intval($_POST['percent_threshold']);

		// Gadget
		$cfg['gadget_form'] = trim(strval($_POST['gadget_form']));
		$cfg['gadget_criteria'] = trim(strval($_POST['gadget_criteria']));
		$cfg['gadget_limit'] = intval($_POST['gadget_limit']);

		// User Permissions
		$cfg['p_save_config'] = ($this->isSuperAdmin ? intval($_POST['p_save_config']) : $this->config['p_save_config']);
		$cfg['p_save_form'] = ($this->isSuperAdmin ? intval($_POST['p_save_form']) : $this->config['p_save_form']);
		$cfg['p_delete_form'] = ($this->isSuperAdmin ? intval($_POST['p_delete_form']) : $this->config['p_delete_form']);
		$cfg['p_publish_form'] = ($this->isSuperAdmin ? intval($_POST['p_publish_form']) : $this->config['p_publish_form']);
		$cfg['p_delete_log'] = ($this->isSuperAdmin ? intval($_POST['p_delete_log']) : $this->config['p_delete_log']);
		
		return gpFiles::SaveArray($cfg_file,'cfg',$cfg);

	}


} // End AntiSpamSFS_Admin class













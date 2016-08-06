<?php
defined('is_running') or die('Not an entry point...');


/*
 * Install_Check() can be used to check the destination server for required features
 * 		This can be helpful for addons that require PEAR support or extra PHP Extensions
 * 		Install_Check() is called from step1 of the install/upgrade process
 */
function Install_Check() {

	// cUrl must be installed and enabled
	if (!extension_loaded('curl') || !function_exists('curl_exec')) {

		echo '<p style="color:red">Cannot install this addon.</p>';
		echo '<p>cUrl extension is either not installed or not enabled on your server.</p>';
		echo '<p>Check/update your server configuration and/or contact your hosting provider.</p>';

		return false;
	}

	return true;
}




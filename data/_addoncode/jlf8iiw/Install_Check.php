<?php
defined('is_running') or die('Not an entry point...');


/*
 * Install_Check() can be used to check the destination server for required features
 * 		This can be helpful for addons that require PEAR support or extra PHP Extensions
 * 		Install_Check() is called from step1 of the install/upgrade process
 */
function Install_Check() {

	global $gp_titles;
	
	// Php 5 check
	if (version_compare(PHP_VERSION, '5.0.0', '<')) {

		echo '<p style="color:red">Cannot install this addon.</p>';
		echo '<p>This plugin runs on Php5+ only.</p>';
		echo '<p>Please enable Php5 on your server or contact your hosting provider for assistance.</p>';

		return false;
	}

	// Check that SimpleBlog is installed
	if (!isset($gp_titles['special_blog'])) {

		echo '<p style="color:red">Cannot install this addon.</p>';
		echo '<p>The SimpleBlog plugin does not appear to be installed.</p>';
		echo '<p>Please install the SimpleBlog plugin first.</p>';

		return false;
	}

	return true;
}




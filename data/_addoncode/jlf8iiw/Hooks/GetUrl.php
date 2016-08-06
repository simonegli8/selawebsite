<?php

defined('is_running') or die('Not an entry point...');

gpPlugin::incl('Abstract/SBS_Abstract_1.php');

class GetUrlStatic {

	static private $_instance;

	static function GetUrl($args) {

		// Added v1.3
		// Do not create the static instance if $args is empty
		if (empty($args[0]) && empty($args[1])) {
			return false;
		}

		$obj = self::getInstance();

		return $obj->GetUrl($args);

	}

	static private function getInstance() {
		
		if (!is_object(self::$_instance)) {
			self::$_instance = new SimpleBlogSEO_GetUrl();
		} 

		return self::$_instance;
	}

}

class SimpleBlogSEO_GetUrl extends SimpleBlogSEO_Abstract_1 {

	// Constructor
	public function __construct() {
		parent::__construct();
	}

	// Called when the GetUrl hook is fired
	public function GetUrl($args) {

		global $gp_index, $page;

		// SimpleBlog not found!
		if (!$this->_blog_folder || !$this->_blog_title) {
			return false;
		}

		// Url Rewriting Off
		if (!$this->_config['url_rewriting']) {
			return false;
		}

		$href = (isset($args[0]) ? $args[0] : '');
		$query = (isset($args[1]) ? $args[1] : '');

		// Only blog post urls should be rewritten
		// Changed in v1.1 to prevent search results duplicates
		//if (($href != 'Special_Blog') || empty($query)) {
		if ((($href != 'Special_Blog') && ($href != 'Blog')) || empty($query)) {
			return false;
		}

		// Added in v1.1
		// Remove the question mark before parsing the query string
		$query = ltrim($query,'?');
	
		parse_str($query, $query_arr);

		if (!is_array($query_arr) || !isset($query_arr['id'])) {
			return false;
		}

		// Fix a problem within the admin.js file
		// 'script=b+"&cmd=inlineedit&area_id="+f;' ==> fine with non-SEF urls
		// Should be 'script=b+"?cmd=inlineedit&area_id="+f;' with SEF urls
		// NB: 'Edit (TWYSIWYG)' is language-dependant...
		$backtrace = debug_backtrace();
		if (isset($backtrace[8]['function']) && ($backtrace[8]['function'] == 'EditAreaLink')  &&
			isset($backtrace[8]['args'][1]) && ($backtrace[8]['args'][1] == 'Special_Blog')  &&
			isset($backtrace[8]['args'][4]) && ($backtrace[8]['args'][4] == 'name="inline_edit_generic" rel="text_inline_edit"')) {
			return false;
		}

		$id = $query_arr['id'];

		// Changed v1.3 - We no longer use the $_id2title property here
		if (isset($this->_id2slug[$id])) { // We already have the id<=>slug mapping
			$new_href = $href . '/' . $this->_id2slug[$id];
			unset($query_arr['id']);
			$new_query = http_build_query($query_arr);
		} else { // Return the non-SEF url (should not happen unless there is a bug in SimpleBlog)
			$new_href = $href; 
			$new_query = $query;
		}

		return array($new_href, $new_query);

	}

}


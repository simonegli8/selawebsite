<?php

defined('is_running') or die('Not an entry point...');

abstract class SimpleBlogSEO_Abstract_0 {

	// In SimpleBlog  each post_x.php file contains 20 posts max (hard-coded)
	protected $_per_file = 20; // int

	protected $_config; // array
	protected $_blog_folder; // string
	protected $_blog_title; // string
	protected $_id2title; // array
	protected $_hits; // array

	public function __construct() {
		
		$this->_loadConfig();
		$this->_setBlogFolder();
		$this->_setBlogTitle();
		$this->_loadHits();

	}

	// We use the archives.php file to initialize the $_id2title property
	protected function _setId2Title() {

		global $addonPathData, $dataDir;

		$id2title = array();

		$id2title_file = $dataDir . '/data/_addondata/' . $this->_blog_folder . '/archives.php';

		if (file_exists($id2title_file)) {
			include($id2title_file);
			foreach ($archives as $archive) $id2title += $archive;
			ksort($id2title);
		} 

		$this->_id2title = $id2title;

	}

	protected function _loadHits() {

		global $addonPathData;

		$hits_file = $addonPathData.'/hits.php';

		if (file_exists($hits_file)) {
			include($hits_file);
		} else {
			$hits = $this->_loadHitsDefaults($hits_file);
		}

		$this->_hits = $hits;

	}

	protected function _loadHitsDefaults($hits_file) {

		global $addonPathData;

		$hits = array();

		if (gpFiles::SaveArray($hits_file, 'hits', $hits) === false) {
			message($langmessage['OOPS']);
		}

		return $hits;

	}

	protected function _loadConfig() {

		global $addonPathData;

		$cfg_file = $addonPathData.'/config.php';

		if (file_exists($cfg_file)) {
			include($cfg_file);
		} else {
			$cfg = $this->_loadDefaults($cfg_file);
		}

		$this->_config = $cfg;

	}

	protected function _loadDefaults($cfg_file) {

		global $addonPathData, $config;

		//use default addon configuration
		$cfg = array();

		$cfg['url_rewriting'] = 1; 
		$cfg['url_rewriting_replace'] = ''; 
		$cfg['url_rewriting_case'] = ''; 
		$cfg['url_redirect'] = 1; 
		$cfg['not_found_redirect'] = 1; 
		$cfg['old_sefurl_redirect'] = 0; 

		$cfg['h2_rewriting'] = 0; 
		$cfg['remove_link'] = 0; 

		$cfg['meta_keywords'] = 0; 
		$cfg['remove_keywords_tags'] = 0; 
		$cfg['meta_desc'] = 0; 

		$cfg['count_hits'] = 0; 
		$cfg['show_hits'] = 0;

		$cfg['gadget_list_type'] = 'ul';  
		$cfg['gadget_list_length'] = 5; 
		$cfg['gadget_title_excerpt'] = 0; 
		$cfg['gadget_excerpt_lenght'] = 20; 

		if (gpFiles::SaveArray($cfg_file,'cfg',$cfg)) {
			$message = gpOutput::SelectText('Default settings saved.');
			message($message);
		}

		return $cfg;

	}

	protected function _setBlogFolder() {

		global $gp_titles;

		if (!isset($gp_titles['special_blog'])) {
			$this->_blog_folder = '';
			return;
		}
		
		if (!isset($gp_titles['special_blog']['addon']) || empty($gp_titles['special_blog']['addon'])) {
			$this->_blog_folder = '';
			return;
		}
		
		$this->_blog_folder = $gp_titles['special_blog']['addon'];

	}

	protected function _setBlogTitle() {

		global $gp_index;

		if (($blog_title = array_search('special_blog', $gp_index)) === false) {
			$this->_blog_title = '';
			return;
		}

		$this->_blog_title = $blog_title;

	}

	// Added v1.3
	protected function _getPostTitle($id){

		global $dataDir;

		if( !is_numeric($id) ){
			return false;
		}

		if (isset($this->_id2title[$id])) {
			return $this->_id2title[$id];
		}

		$file_index = floor($id/$this->_per_file);
		$post_file = $dataDir.'/data/_addondata/'.$this->_blog_folder.'/posts_'.$file_index.'.php';
		if( !file_exists($post_file) ){
			return false;
		}

		require($post_file);
		if( !is_array($posts) || empty($posts)){
			return false;
		}

		foreach( $posts as $postindex => $postdata ){
			$this->_id2title[$postindex] = $postdata['title'];
		}

		if (!isset($posts[$id])) {
			return false;
		}

		return $posts[$id]['title'];

	}

	// Dump of the gpPlugins::incl() method
	protected function _incl($file){
		global $addonPathCode;
		if( gp_safe_mode ){
			return;
		}
		return include_once($addonPathCode.'/'.$file);
	}

	protected function _varDump($var, $die = true) {

		echo "<pre>";
		var_dump($var);
		echo "</pre>";
		
		if ($die) die();		

	}

}

?>

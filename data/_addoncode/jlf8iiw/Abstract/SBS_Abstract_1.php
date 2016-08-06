<?php

defined('is_running') or die('Not an entry point...');

gpPlugin::incl('Abstract/SBS_Abstract_0.php');

abstract class SimpleBlogSEO_Abstract_1 extends SimpleBlogSEO_Abstract_0 {

	protected $_id2slug; // array
	protected $_post_count; // int
	protected $_subtitle_separator; // string

	public function __construct() {

		parent::__construct();

		$this->_setSimpleBlogProperties();
		$this->_loadId2Slug();

	}

	protected function _loadId2Slug() {

		global $addonPathData;

		$id2slug = array();

		$id2slug_file = $addonPathData.'/id2slug.php';

		if (file_exists($id2slug_file)) {
			include($id2slug_file);
		} 

		$this->_id2slug = $id2slug;

	}

	protected function _saveId2Slug() {

		global $addonPathData;

		$id2slug_file = $addonPathData.'/id2slug.php';	

		$id2slug = $this->_id2slug;

		return gpFiles::SaveArray($id2slug_file,'id2slug',$id2slug);

	}

	protected function _setSimpleBlogProperties() {

		global $dataDir;

		$file = $dataDir . '/data/_addondata/' . $this->_blog_folder . '/index.php';

		if (!is_file($file)) {
			// Fix v1.0.1
			// Instantiate the object properties and return
			$this->_post_count = 0;
			$this->_subtitle_separator = '';
			return;
		}

		include($file);

		if (!isset($blogData) || empty($blogData)) {
			$blogData = array();
			$blogData['post_count'] = 0;
			$blogData['subtitle_separator'] = '';
		}

		if (!isset($blogData['post_count']) || empty($blogData['post_count'])) {
			$blogData['post_count'] = 0;
		}

		if (!isset($blogData['subtitle_separator']) || empty($blogData['subtitle_separator'])) {
			$blogData['subtitle_separator'] = '';
		}

		$this->_post_count = $blogData['post_count'];
		$this->_subtitle_separator = $blogData['subtitle_separator'];

	}

	protected function _createRawSlugFromId($id) {

		if (($title = $this->_getPostTitle($id)) !== false) {
			return $this->_slugify($title);
		}

		return false;

	}

	protected function _saveNewSlug($id, $slug) {

		// Append id to slug to make sure it is unique
		$slug = $id . '_' . $slug; 

		// Save the slug in the $_id2slug class property
		$this->_id2slug[$id] = $slug;

		// Save the new slug in the id2slug.php file
		$this->_saveId2Slug();

		// Return the slug
		return $slug;

	}

	// Added v1.2 - "Blog as homepage" support
	protected function _isBlogAsHomePage() {
		
		global $config;
	
		if( isset($config['homepath']) && $this->_blog_title == $config['homepath'] ){
			return true;
		} else {
			return false;
		}

	}

	// Added v1.3
	protected function _slugify($title, $gpeasy_slug = false) {

		// gpEasy admin_tools::PostedSlug() method is no longer used in v1.3
		if ($gpeasy_slug === true) {
			includeFile('admin/admin_tools.php');
			return admin_tools::PostedSlug($title, true);
		}

		$title = html_entity_decode($title); 
		$replace = htmlspecialchars_decode($this->_config['url_rewriting_replace']);
		$case = $this->_config['url_rewriting_case'];

		// NB: We keep the underscore as separator for gpEasy compliance
		//setlocale(LC_ALL, 'en_US.UTF8');
		return $this->_toAscii($title, $replace, $case);
		
	}
 
	// Added v1.3
	// credit: http://cubiq.org/the-perfect-php-clean-url-generator
	private function _toAscii($str, $replace='', $case='uc', $sep='_') {

		$clean = (empty($replace) ? $str : preg_replace("/[".preg_quote($replace)."]+/", ' ', $str)); // Modif Fly06

		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $clean);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		//$clean = strtolower(trim($clean, '-'));
		$clean = trim($clean, " \t\n\r\0\x0B".$sep); // Modif Fly06
		$clean = preg_replace("/[\/_|+ -]+/", $sep, $clean);

		// Added Fly06
		if (in_array($case, array('lc', 'uc'))) {
			$clean = strtolower($clean);
			$clean = ($case == 'uc' ? implode($sep, array_map('ucfirst', explode($sep, $clean))) : $clean);
		}

		return $clean;
	}

}

?>

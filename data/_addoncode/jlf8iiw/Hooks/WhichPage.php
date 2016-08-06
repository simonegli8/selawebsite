<?php

defined('is_running') or die('Not an entry point...');

gpPlugin::incl('Abstract/SBS_Abstract_2.php');

class SimpleBlogSEO_WhichPage extends SimpleBlogSEO_Abstract_2 {

	// Constructor
	public function __construct() {
		parent::__construct();
	}

	public function WhichPage($path) {

		// SimpleBlog not found!
		if (!$this->_blog_folder || !$this->_blog_title) {
			return $path;
		}

		// Init property $_id2title
		$this->_setId2Title(); 

		$this->_redirectUrl($path); // Redirect 301/404
		$this->_decodeUrl($path); // slug => id

		// Do not need to go further if not logged in
		if (!common::LoggedIn()) {
			return $path;;
		}

		$this->_deleteSefUrl($path); // When an admin delete a blog post
		$this->_modifySefUrl($path); // When an admin modify a blog post

		// New in v1.3
		// When an admin creates a new a blog post
		// ==> This case is handled AFTER the blog post is actually created 
		// ==> See GetHead.php for details

		return $path;

	}

	private function _modifySefUrl(&$path) {

		global $gp_index;
	
		// Not in the blog context	
		// Added v1.2 - "Blog as homepage" support
		$isBlogAsHomePage = $this->_isBlogAsHomePage();
		if( $isBlogAsHomePage && !empty($path) ){
			return;
		} elseif (!$isBlogAsHomePage && ($path != $this->_blog_title)) {
			return;
		}

		// Not in the save_edit context
		if (($cmd = common::GetCommand()) != 'save_edit') {
			return;
		}

		// No id found
		if (!isset($_POST['id']) || empty($_POST['id'])) {
			return;
		}

		$id = $_POST['id'];	

		// No title found
		if (!isset($_POST['title']) || empty($_POST['title'])) {
			return;
		}

		// SimpleBlog compliancy
		$title = $_POST['title'];	
		$title = htmlspecialchars($title);
		$title = trim($title);

		if(empty($title)) {
			return;
		}

		$slug = $this->_slugify($title);

		// We save the slug
		$this->_saveNewSlug($id, $slug);

	}

	private function _deleteSefUrl(&$path) {

		global $gp_index, $config;

		// Not in the blog context	
		// Added v1.2 - "Blog as homepage" support
		$isBlogAsHomePage = $this->_isBlogAsHomePage();
		if( $isBlogAsHomePage && !empty($path) ){
			return;
		} elseif (!$isBlogAsHomePage && ($path != $this->_blog_title)) {
			return;
		}

		// Not in the delete context
		if (($cmd = common::GetCommand()) != 'delete') {
			return;
		}

		// No id!
		if (!isset($_POST['id']) || empty($_POST['id'])) {
			return;
		}

		$id = $_POST['id'];	

		// Delete slug
		$this->_deleteSlug($id);

		// Added v1.2 - Delete Hits
		$this->_deleteHits($id);

	}

	private function _redirectUrl(&$path) {

		// Do we have to redirect?
		if (!$this->_config['url_redirect']) {
			return;
		}

		// Do not redirect when logged in
		if (common::LoggedIn()) {
			return;
		}

		if ($this->_config['url_rewriting']) {
			$this->_redirectNonSef2Sef($path);
		} else {
			$this->_redirectSef2NonSef($path);
		}

	}

	private function _redirectNonSef2Sef(&$path) {

		global $gp_index;

		// Get the full path
		$fullpath = common::CleanRequest($_SERVER['REQUEST_URI']);

		// Added v1.3
		// "Blog as homepage" support
		$blog_title = ($this->_isBlogAsHomePage() ? '' : $this->_blog_title);

		// Regex
		$regex = '#^'.preg_quote($blog_title).'\?id=([\d]+)#is';

		// No match? go out.
		if (!preg_match($regex, $fullpath, $matches)) {
			return;
		}

		$id = $matches[1];	

		// Make sure we have a slug
		$slug = '';
		if (isset($this->_id2slug[$id])) {
			$slug = $this->_id2slug[$id];
		} elseif (($slug = $this->_createRawSlugFromId($id)) !== false) { // Not found, we have to create it
			$slug = $this->_saveNewSlug($id, $slug); // Save the new mapping id <=> slug
		} elseif ($this->_config['not_found_redirect']) {
			$path = 'Missing';
			return;	
		} else {
			return;	
		}

		// Build the SEF url
		$url =  common::GetUrl('') . $this->_blog_title . '/' . $slug;

		// Redirect to the SEF url
		common::Redirect($url);

	}

	private function _redirectSef2NonSef(&$path) {

		global $gp_index, $dirPrefix;

		// Regex
		$regex = '#^'.preg_quote($this->_blog_title).'/(.*)#is';

		// No match? go out.
		if (!preg_match($regex, $path, $matches)) {
			return;
		}		

		$slug = $matches[1];

		// Make sure we have an id
		$id = 0;
		if(($id = array_search($slug, $this->_id2slug)) !== false) {
			// OK
		} elseif ($this->_config['not_found_redirect']) {
			$path = 'Missing';
			return;	
		} else {
			return;	
		}

		// Added v1.3
		// "Blog as homepage" support
/*
		$blog_title = ($this->_isBlogAsHomePage() ? '' : $this->_blog_title);
		$base_url = ($this->_isBlogAsHomePage() ? $dirPrefix.'/' : common::getUrl());
*/
		// Build the non SEF url
		//$url = $base_url . $blog_title . '?id=' . $id;
		$url = $this->_nonsef_mid_url . '?id=' . $id;

		// Redirect to the non SEF url
		common::Redirect($url);

	}

	private function _decodeUrl(&$path) {

		if ($this->_config['url_rewriting']) {
			$this->_decodeSef2NonSefUrl($path);
		} else {
			$this->_decodeNonSef2NonSefUrl($path);
		}

	}

	private function _decodeNonSef2NonSefUrl(&$path) {

		global $gp_index;

		// No need to go further if count_hits is off
		if (!$this->_config['count_hits']) {
			return;
		}

		// Logged in user? go away.
		if (common::LoggedIn()) {
			return;
		}

		// Get the full path
		$fullpath = common::CleanRequest($_SERVER['REQUEST_URI']);

		// Regex
		$regex = '#^'.preg_quote($this->_blog_title).'\?id=([\d]+)#is';

		// No match? go out.
		if (!preg_match($regex, $fullpath, $matches)) {
			return;
		}

		$id = $matches[1];	

		// Hits counter increment
		$this->_incrementHits($id);		
	
	}

	private function _decodeSef2NonSefUrl(&$path) {

		global $gp_index, $config;

		$regex = '#^'.preg_quote($this->_blog_title).'/(.*)#is';

		// No match? go out.
		if (!preg_match($regex, $path, $matches)) {
			return;
		}

		// Added v1.2 - "Blog as homepage" support
		// Prevents the url from being redirected in the common::WhichPage() method
		$blog_title = $this->_blog_title;
		if( $this->_isBlogAsHomePage() ){
			$blog_title = '';
		} 

		// Get the id corresponding to the slug
		if(($id = array_search($matches[1], $this->_id2slug)) !== false) {
			// OK
		} elseif ($this->_config['old_sefurl_redirect'] && (($id = $this->_isOldSefUrl($matches[1])) !== false)) {		
			$url =  common::GetUrl('') . $this->_blog_title . '/' . $this->_id2slug[$id];
			common::Redirect($url);
			return;
		} elseif ($this->_config['not_found_redirect']) {
			$path = 'Missing';
			return;	
		} else {
			return; 
		}

		// Hits counter increment
		if ($this->_config['count_hits'] && !common::LoggedIn()) {
			$this->_incrementHits($id);
		}

		// We are done.
		$path = $blog_title;
		$id = strval($id); // IMPORTANT! 
		$_GET['id'] = $_REQUEST['id'] = $id;

	}

	///////////////////////////////////////////////////////////////////////////
	// TOOLS
	///////////////////////////////////////////////////////////////////////////

	function _incrementHits($id) {

		global $addonPathData;

		// Start - Workaround
		// Prevents this method from being executed twice
		static $flag = false;
		if ($flag) return;
		$flag = true;
		// End - Workaround

		if (!isset($this->_hits[$id])) {
			$this->_hits[$id] = 1;
		} else {
			$this->_hits[$id]++;
		}

		$hits_file = $addonPathData.'/hits.php';

		// We save the $_hits array
		gpFiles::SaveArray($hits_file, 'hits', $this->_hits);

	}

	function _deleteSlug($id) {

		// No need to remove if it does not exist!
		if (!isset($this->_id2slug[$id])) {
			return;
		}
		
		// Remove the $_id2slug[$id] entry
		unset($this->_id2slug[$id]);

		// Save the changes in the file
		$this->_saveId2Slug();

	}


	function _deleteHits($id) {

		global $addonPathData;

		// No need to remove if it does not exist!
		if (!isset($this->_hits[$id])) {
			return;
		}
		
		// Remove the $_hits[$id] entry
		unset($this->_hits[$id]);

		// hits file
		$hits_file = $addonPathData.'/hits.php';

		// We save the $_hits array
		gpFiles::SaveArray($hits_file, 'hits', $this->_hits);

	}

	function _isOldSefUrl($slug) {

		// No match? go out.
		$regex = '#^([\d]+)_(.*)#is';
		if (!preg_match($regex, $slug, $matches)) {
			return false;
		}

		$id = $matches[1];
		if (($title = $this->_getPostTitle($id)) === false) {
			return false;
		}

		$old_slug = $id . '_' . $this->_slugify($title, true);
		if ($slug != $old_slug) {
			return false;
		}

		return $id;

	}

}

?>

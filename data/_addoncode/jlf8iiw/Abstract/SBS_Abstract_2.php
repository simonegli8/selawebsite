<?php

defined('is_running') or die('Not an entry point...');

gpPlugin::incl('Abstract/SBS_Abstract_1.php');

abstract class SimpleBlogSEO_Abstract_2 extends SimpleBlogSEO_Abstract_1 {

	protected $_sef_mid_url; // string
	protected $_nonsef_mid_url; // string

	public function __construct() {

		parent::__construct();

		$this->_setMidUrl();
		$this->_reLoadId2Slug();

	}

	// Changed in v1.3
	// Recreate the full id=>slug mapping from raw post data when empty
	// NB: The archive file can be cleared in the Simple Blog admin
	protected function _reLoadId2Slug() {

		global $addonPathData;

/*
		if (file_exists($id2slug_file)) {
			include($id2slug_file);
		} 
*/
		// If not set (just installed) or empty (SEF urls cleared):
		// ==> Recreate SEF urls
		// ==> Rewrite static content
		//if (!isset($id2slug) || empty($id2slug)) {
		if (empty($this->_id2slug)) {
			$id2slug_file = $addonPathData.'/id2slug.php';
			$this->_id2slug = $this->_loadId2SlugDefaults($id2slug_file);
			$this->_rewriteStaticContent();
			return;
		}

		//$this->_id2slug = $id2slug;

	}

	// Changed in v1.3
	// Recreate the full id=>slug mapping from raw post data when empty
	// NB: The archive file can be cleared in the Simple Blog admin
	protected function _loadId2SlugDefaults($id2slug_file) {

		global $dataDir;

		$id2slug = array();
		$file_index = 0;

		while( file_exists($post_file = $dataDir . '/data/_addondata/' . $this->_blog_folder.'/posts_'.$file_index.'.php') ){

			include($post_file);

			foreach( $posts as $postindex => $postdata ){
				$id2slug[$postindex] = $postindex . '_' . $this->_slugify($postdata['title']);
			}

			$file_index++;

		}

		if (gpFiles::SaveArray($id2slug_file,'id2slug',$id2slug) === false) {
			message($langmessage['OOPS']);
		}

		return $id2slug;

	}

	// Added v1.3
	protected function _setMidUrl() {

		global $dirPrefix;

		// Sef Mid Url
		$base_url = common::GetUrl();
		$blog_title = $this->_blog_title;
		
		$this->_sef_mid_url = $base_url . $blog_title;

		// Non Sef Mid Url
		$base_url = ($this->_isBlogAsHomePage() ? $dirPrefix.'/' : common::getUrl());
		$blog_title = ($this->_isBlogAsHomePage() ? '' : $this->_blog_title);

		$this->_nonsef_mid_url = $base_url . $blog_title;

	}

	// Changed v1.3 
	// We no longer rely on Simple Blog GenStaticContent() method
	// We rewrite urls in the static content directly
	protected function _rewriteStaticContent() {

		$this->_rewriteSC('gadget');
		$this->_rewriteSC('feed');

	}

	// Added v1.3 
	private function _rewriteSC($sc = 'gadget') {

		global $gp_index, $dataDir;

		$sc2file = array('feed' => 'feed.atom', 'gadget' => 'gadget.php');

		if (!in_array($sc, array_keys($sc2file))) {
			return;
		}

		$file = $dataDir . '/data/_addondata/' . $this->_blog_folder . '/' . $sc2file[$sc];
		$content = @file_get_contents($file);

		if (empty($content)) {
			return;
		}

		if ($this->_config['url_rewriting']) {
			$save_1 = $this->_rewrite_sef2sef($content, $sc); 
			$save_2 = $this->_rewrite_nonsef2sef($content, $sc);
			$save = $save_1 || $save_2;
		} else {
			$save = $this->_rewrite_sef2nonsef($content, $sc);
		}

		if ($save) {
			gpFiles::Save($file, $content);
		}

	}

	// Added v1.3 
	private function _rewrite_nonsef2sef(&$html, $sc = 'gadget') {

		global $gp_index, $dirPrefix;

		$tag = ($sc == 'feed' ? 'link' : 'a');
		$server = ($sc == 'feed' ? $this->_serverName() : '');

		$regex = '#<'.$tag.'([^<>]+)href=[\'"]'.preg_quote($server.$this->_nonsef_mid_url).'\?id=([\d]+)[\'"]?([^<>]*)>#is';
		
		// Nothing to rewrite? go out.
		if (!preg_match($regex, $html)) {
			return false;
		}

		$html = preg_replace_callback($regex, array($this, '_callback_nonsef2sef_' . $sc), $html);		

		return true;

	}

	// Added v1.3 
	private function _rewrite_sef2nonsef(&$html, $sc = "gadget") {
		return $this->_rewrite_sef($html, $sc, 'nonsef'); 
	}

	// Added v1.3 
	private function _rewrite_sef2sef(&$html, $sc = "gadget") {
		return $this->_rewrite_sef($html, $sc, 'sef'); 
	}

	// Added v1.3 
	private function _rewrite_sef(&$html, $sc = "gadget", $to='nonsef') {

		global $gp_index;

		$tag = ($sc == 'feed' ? 'link' : 'a');
		$server = ($sc == 'feed' ? $this->_serverName() : '');

		$regex = '#<'.$tag.'([^<>]+)href=[\'"]('.preg_quote($server.$this->_sef_mid_url).')/([\d]+)_([^<>]+)[\'"]([^<>]*)>#Uis';

		// Nothing to rewrite? go out.
		if (!preg_match($regex, $html)) {
			return false;
		}

		$html = preg_replace_callback($regex, array($this, '_callback_sef2' . $to . '_' . $sc), $html);		

		return true;

	}

	// Added v1.3 
	protected function _callback_nonsef2sef_gadget($matches) {
		return $this->_callback_nonsef2sef($matches, 'gadget');
	}

	// Added v1.3 
	protected function _callback_nonsef2sef_feed($matches) {
		return $this->_callback_nonsef2sef($matches, 'feed');
	}

	// Added v1.3 
	protected function _callback_nonsef2sef($matches, $sc='gadget') {

		$id = $matches[2];

		$tag = ($sc == 'feed' ? 'link' : 'a');
		$server = ($sc == 'feed' ? $this->_serverName() : '');

		if (isset($this->_id2slug[$id])) { // We already have the id<=>slug mapping
			return '<'.$tag.$matches[1].'href="'.$server.$this->_sef_mid_url.'/'.$this->_id2slug[$id].'"'.$matches[3].'>';
		} elseif (($slug = $this->_createRawSlugFromId($id)) !== false) { // Not found, we have to create it
			$slug = $this->_saveNewSlug($id, $slug);
			return '<'.$tag.$matches[1].'href="'.$server.$this->_sef_mid_url.'/'.$slug.'"'.$matches[3].'>';
		} else { // Return the non-SEF url (i.e. wrong post id)
			return $matches[0];
		}

	}

	// Added v1.3 
	protected function _callback_sef2nonsef_gadget($matches) {
		return $this->_callback_sef2nonsef($matches, 'gadget');
	}

	// Added v1.3 
	protected function _callback_sef2nonsef_feed($matches) {
		return $this->_callback_sef2nonsef($matches, 'feed');
	}

	// Added v1.3 
	protected function _callback_sef2nonsef($matches, $sc='gadget') {

		$tag = ($sc == 'feed' ? 'link' : 'a');
		$server = ($sc == 'feed' ? $this->_serverName() : '');

		return '<'.$tag.$matches[1].'href="'.$server.$this->_nonsef_mid_url.'?id='.$matches[3].'"'.$matches[5] . '>';

	}

	// Added v1.3 
	protected function _callback_sef2sef_gadget($matches) {
		return $this->_callback_sef2sef($matches, 'gadget');
	}

	// Added v1.3 
	protected function _callback_sef2sef_feed($matches) {
		return $this->_callback_sef2sef($matches, 'feed');
	}

	// Added v1.3 
	protected function _callback_sef2sef($matches, $sc='gadget') {

		$tag = ($sc == 'feed' ? 'link' : 'a');

		$id = $matches[3];

		if (($slug = $this->_createRawSlugFromId($id)) !== false) { // We re-create the slug
			$slug = $this->_saveNewSlug($id, $slug);
			return '<'.$tag.$matches[1].'href="'.$matches[2].'/'.$slug.'"'.$matches[5].'>';
		} else { // Return the non-SEF url (i.e. wrong post id)
			return $this->_callback_sef2nonsef($matches, $sc);
		}

	}

	// Added v1.3 
	private function _serverName() {

		if( isset($_SERVER['HTTP_HOST']) ) {
			$server = 'http://'.$_SERVER['HTTP_HOST'];
		} else {
			$server = 'http://'.$_SERVER['SERVER_NAME'];
		}

		return $server;

	}

}

?>

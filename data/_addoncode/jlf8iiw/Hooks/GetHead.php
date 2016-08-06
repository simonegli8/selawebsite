<?php

defined('is_running') or die('Not an entry point...');

gpPlugin::incl('Abstract/SBS_Abstract_2.php');

class SimpleBlogSEO_GetHead extends SimpleBlogSEO_Abstract_2 {

	// Constructor
	public function __construct() {

		parent::__construct();

		// SimpleBlog not installed!
		if (!$this->_blog_folder || !$this->_blog_title) {
			return;
		}

		// Init property $_id2title
		$this->_setId2Title();
		
		// New in v1.3
		$this->_createSefUrl(); // When an admin create a new a blog post

		$this->_urlRewriting(); // $page->contentBuffer
		$this->_rewriteBlogHits(); // $page->contentBuffer
		$this->_titleRewriting(); // $page->contentBuffer
		$this->_rewritePostHits(); // $page->contentBuffer
		$this->_rewriteMetaKeywords(); // $page->meta_keywords
		$this->_rewriteMetaDesc(); // $page->meta_description

	}
	// New in v1.3
	// When an admin creates a new blog post, performs url rewriting:
	// - Right after the new blog post is created 
	// - Just before the page is sent back to the client
	// No need to reload the page to have the new post's url rewritten
	private function _createSefUrl() {

		global $gp_index, $page, $dirPrefix;

		// Do not need to go further if not logged in
		if (!common::LoggedIn()) {
			return;
		}

		// Not in the blog context	
		if($page->title != $this->_blog_title) {
			return;
		}

		// Not in the save_new context
		if (($cmd = common::GetCommand()) != 'save_new') {
			return;
		}

		// Rewrite static content (gadget + feed)
		$this->_rewriteStaticContent();

		// Regex
		$regex = '#<a([^<>]+)href=[\'"]'.preg_quote($this->_nonsef_mid_url).'\?id=([\d]+)[\'"]?([^<>]+)>#is';

		// Nothing to rewrite? go out.
		if (!preg_match($regex, $page->contentBuffer)) {
			return;
		}

		$page->contentBuffer = preg_replace_callback($regex, array($this, '_callback_1'), $page->contentBuffer);

	}

	private function _urlRewriting() {

		global $gp_index, $page, $dirPrefix;

		// Url Rewriting Off
		if (!$this->_config['url_rewriting']) {
			return;
		}

		if ($page->title == $this->_blog_title) {
			return;
		}

		// Regex
		$regex = '#<a([^<>]+)href=[\'"]('.preg_quote($this->_nonsef_mid_url).')\?id=([\d]+)[\'"]?([^<>]+)>#is';

		// Nothing to rewrite? go out.
		if (!preg_match($regex, $page->contentBuffer)) {
			return;
		}

		$page->contentBuffer = preg_replace_callback($regex, array($this, '_callback_1'), $page->contentBuffer);

	}

	protected function _callback_1($matches) {
		return $this->_callback_nonsef2sef($matches, 'gadget');
	}

	private function _titleRewriting() {

		global $gp_index, $page;

		// Title Rewriting Off? Go away.
		if (!$this->_config['h2_rewriting'] && !$this->_config['remove_link']) {
			return;
		}

		// Not in the appropriate context? Go away.
		if (($page->title != $this->_blog_title) || !isset($_GET['id'])) {
			return;
		}

		// Regex
		$regex = '#<h2([^>]*)id=("|\')blog_post_([\d]+)("|\')><a([^>]*)>(.*)</a><\/h2>#Uis';	

		// Nothing to rewrite? go out.
		if (!preg_match($regex, $page->contentBuffer)) {
			return;
		}	

		// Replacement pattern
 		$tag = ($this->_config['h2_rewriting'] ? 'h1' : 'h2');
		if ($this->_config['remove_link']) {
			$replacement = '<'.$tag.'$1id="blog_post_$3">$6</'.$tag.'>';
		} else {
			$replacement = '<'.$tag.'$1id="blog_post_$3"><a$5>$6</a></'.$tag.'>';
		}

		$page->contentBuffer = preg_replace($regex, $replacement, $page->contentBuffer, 1);

	}

	private function _rewriteBlogHits() {

		global $page, $gp_index;

		if (!$this->_config['count_hits'] || !$this->_config['show_hits']) {
			return;
		}

		// Not in the appropriate context? Go away.
		if (($page->title != $this->_blog_title)) {
			return;
		}

		// Not in the appropriate context? Go away.
		if (isset($_GET['id'])) {
			return;
		}

		// Regex
		$regex = '#<h2 id="blog_post_([\d]+)">(.*)</h2>([^<>]*)<div class="simple_blog_info">(.*)<\/div>#Uis';

		// Get the content of the SimpleBlog Info div block
		if (!preg_match($regex, $page->contentBuffer, $matches)) {
			return;
		}

		$page->contentBuffer = preg_replace_callback($regex, array($this, '_callback_2'), $page->contentBuffer);

		// Remove keywords span tags
		$regex = '#<span([^>]*)class=("|\')keywords("|\')>(.*)<\/span>#Uis';
		$page->contentBuffer = preg_replace($regex, '$4', $page->contentBuffer);

	}

	private function _callback_2($matches) {

		$id = $matches[1];

		// Modif v1.3 - Show hits only if > 0
		if (isset($this->_hits[$id]) && $this->_hits[$id]) {
			$replacement	= '<h2 id="blog_post_' . $id . '">' . $matches[2] . '</h2>'
							. $matches[3]
							. '<div class="simple_blog_info">'
							. $matches[4]
							. $this->_subtitle_separator 

							// Changes v1.1
							. '<span class="simple_blog_hits">' 
							. sprintf(gpOutput::SelectText('Hits: %s'), $this->_hits[$id])

							//. '<span class="simple_blog_hits">Hits: ' 
							//. (isset($this->_hits[$id]) ? $this->_hits[$id] : 0)

							. '</span>'
							. '</div>'
							;
		} else {
			$replacement	= $matches[0];
		}

		return $replacement;

	}

	private function _rewritePostHits() {

		global $page, $gp_index;

		if (!$this->_config['count_hits'] || !$this->_config['show_hits']) {
			return;
		}

		// Not in the appropriate context? Go away.
		if (($page->title != $this->_blog_title) || !isset($_GET['id'])) {
			return;
		}

		$id = $_GET['id'];	

		// Regex
		$regex = '#<div class="simple_blog_info">(.*)<\/div>#Uis';

		// Get the content of the SimpleBlog Info div block
		if (!preg_match($regex, $page->contentBuffer)) {
			return;
		}

		// Modif v1.3 - Show hits only if > 0
		if (isset($this->_hits[$id]) && $this->_hits[$id]) {
			$replacement	= '<div class="simple_blog_info">'
							. '$1' 
							. $this->_subtitle_separator 

							// Changes v1.1
							. '<span class="simple_blog_hits">' 
							. sprintf(gpOutput::SelectText('Hits: %s'), (isset($this->_hits[$id]) ? $this->_hits[$id] : 0))

							//. '<span class="simple_blog_hits">Hits: ' 
							//. (isset($this->_hits[$id]) ? $this->_hits[$id] : 0)

							. '</span>'
							. '</div>'
							;
		} else {
			$replacement	= '$0';
		}

		$page->contentBuffer = preg_replace($regex, $replacement, $page->contentBuffer, 1);

	}

	private function _rewriteMetaKeywords() {

		global $page, $gp_index;

		if (!$this->_config['meta_keywords']) {
			return;
		}

		// Not in the appropriate context? Go away.
		if (($page->title != $this->_blog_title) || !isset($_GET['id'])) {
			return;
		}

		// Regex
		$regex = '#<span([^>]*)class=("|\')keywords("|\')>(.*)<\/span>#Uis';

		// Get keywords from content	
		preg_match_all($regex, $page->contentBuffer, $matches);

		// No keywords defined in the post text
		// ... bla bla bla <span class="keywords">I am a keyword</span> bla bla bla ...
		if (empty($matches)) {
			return;
		}

		// Remove keywords span tags
		if ($this->_config['remove_keywords_tags']) {
			$page->contentBuffer = preg_replace($regex, '$4', $page->contentBuffer);
		}

		// Keywords Cleaning
		$keywords = $matches[4];
		array_map("strip_tags", $keywords);
		array_map(array($this, "_nbsp2Space"), $keywords);
		//array_map("htmlentities", $keywords);
		array_map("trim", $keywords);

		// Add keywords in the meta_keywords array
		$page->meta_keywords = array_merge($page->meta_keywords, $keywords);

	}

	private function _rewriteMetaDesc() {

		global $page, $gp_index;

		if (!$this->_config['meta_desc']) {
			return;
		}

		// Not in the appropriate context? Go away.
		if (($page->title != $this->_blog_title) || !isset($_GET['id'])) {
			return;
		}

		// Regex
		$regex = '#<p([^>]*)>(.*)<\/p>#Uis';

		// Get the content text of the first paragraph
		preg_match($regex, $page->contentBuffer, $matches);

		// No paragraph! Strange post...
		if (empty($matches)) {
			return;
		}

		// Description cleaning
		$desc = $matches[2];
		$desc = strip_tags($desc);
		$desc = $this->_nbsp2Space($desc);
		//$desc = htmlentities($desc);
		$desc = trim($desc);

		// Save description
		switch ($this->_config['meta_desc']) {
			case 1: // Full first paragraph
				$page->meta_description = $desc;	
				break;
			case 2: // First sentence only
				$desc = explode('.', $desc);
				$page->meta_description = $desc[0];
				break;				
		}

	}

	private function _nbsp2Space($string) {
		return str_replace('&nbsp;', ' ', $string);
	}

}


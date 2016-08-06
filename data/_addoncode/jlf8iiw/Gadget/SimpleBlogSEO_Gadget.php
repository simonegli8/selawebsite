<?php defined('is_running') or die('Not an entry point...');

gpPlugin::incl('Abstract/SBS_Abstract_0.php');

class SimpleBlogSEO_Gadget extends SimpleBlogSEO_Abstract_0 {

	// Constructor
	function __construct() {

		global $langmessage;

		// Parent Class Constructor
		parent::__construct();

		// SimpleBlog not found!
		if (!$this->_blog_folder || !$this->_blog_title) {
			echo '<h3>'.gpOutput::SelectText('Most Read Blog Posts').'</h3>';
			echo '<p>'.gpOutput::SelectText('SimpleBlog not installed!').'</p>';
			return;
		}

		// Init property $_id2title
		$this->_setId2Title();

		// Load the (raw) log data
		if (empty($this->_hits)) {
			echo '<h3>'.gpOutput::SelectText('Most Read Blog Posts').'</h3>';
			echo '<p>'.gpOutput::SelectText('No data found or stored yet!').'</p>';
			return;
		}

		// We sort the hits array
		asort($this->_hits, SORT_NUMERIC);

		// We reverse the previous sorting
		$this->_hits = array_reverse($this->_hits, true);

		// Keep the X first values only
		$this->_hits = array_slice($this->_hits, 0, $this->_config['gadget_list_length'], true);

		// Load the html template
		$this->_showGadget();		

	}

	/////////////////////////////////////////////////////////////////////
	// PRIVATE/PROTECTED METHODS 
	/////////////////////////////////////////////////////////////////////

	private function _showGadget() {

		// We load the gadget template
		$this->_incl('Gadget/SimpleBlogSEO_Gadget_Tmpl.php');

	}

	protected function _createTitle($id, $excerpt = true) {

		if (($title = $this->_getPostTitle($id)) !== false) {
			return ($excerpt ? $this->_makeExcerpt($title) : $title);
		} else {
			return 'Error: Unknown Title';
		}

	}

	private function _makeExcerpt($text) {

		if (!$this->_config['gadget_title_excerpt']) {
			return $text;
		}

		if (strlen($text) <= $this->_config['gadget_excerpt_lenght']) {
			return $text;
		}
		
		// Make the raw excerpt
		$text = substr($text, 0, $this->_config['gadget_excerpt_lenght']);

		// Clean the end
		$flag = true;
		while ($flag) {
			$new_text = rtrim($text);
			$new_text = rtrim($new_text, "_-,.;:!?");
			if (!strcmp($text, $new_text)) $flag = false;
			$text = $new_text;
		}

		// Add the ellipsis
		$text .= '...';

		return $text;

	}

}



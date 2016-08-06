<?php defined('is_running') or die('Not an entry point...');

gpPlugin::incl('Abstract/SBS_Abstract_1.php');

class SimpleBlogSEO_EditConfig extends SimpleBlogSEO_Abstract_1 {

		private $_regen; // bool
		private $_sef_cover; // string
		//private $_is_dev_install; // bool

	// Constructor
	public function __construct() {

		global $langmessage;

		// Parent Class Constructor
		parent::__construct();

		// SimpleBlog not found!
		if (!$this->_blog_folder || !$this->_blog_title) {
			echo '<p style="color:red">We have got a problem!</p>';
			echo '<p>The SimpleBlog plugin does not appear to be installed.</p>';
			echo '<p>Please install the SimpleBlog plugin or uninstall the SimpleBlogSEO plugin.</p>';
			return;
		}

		//$this->_setIsDevInstall();

		if ($this->_doSaveConfig()) { 
			$this->_setRegen();
			$this->_updateConfig();
			if (!$this->_isValidConfig()) {
				$this->_editConfig();
				return;
			} else {
				if ($this->_saveConfig()) {
					if ($this->_doRegenSBStaticContent()) {
						$this->_clearSefUrls(false);
					}
					message($langmessage['SAVED']);
				} else {
					message($langmessage['OOPS']);
				}
			}
		}

		if ($this->_doResetHits()) {
			if (!$this->_resetHits()) {
				message($langmessage['OOPS']);
			}
			// Update the $_hits property
			$this->_loadHits(); 
		}

		if ($this->_doClearSefUrls()) {
			if (!$this->_clearSefUrls()) {
				message($langmessage['OOPS']);
			}
			// Update the $_is2slug property
			$this->_loadId2Slug(); 
		}

		$this->_calcSefCoverage();
		$this->_editConfig();

	}

	/////////////////////////////////////////////////////////////////////
	// PRIVATE METHODS
	/////////////////////////////////////////////////////////////////////

	private function _calcSefCoverage() {
		// Bug - No post ==> division by zero
		//$this->_sef_cover = number_format(100 * (count($this->_id2slug) / $this->_post_count), 0) . '%';

		// Fixed in v1.0.1
		if($this->_post_count) {
			$this->_sef_cover = number_format(100 * (count($this->_id2slug) / $this->_post_count), 0) . '%';
		} else {
			$this->_sef_cover = 'n.a.';
		}
	}

	// Changed v1.3
	private function _setRegen() {

		$url_rewriting = intval($_POST['url_rewriting']);
		$url_rewriting_replace = trim(htmlspecialchars($_POST['url_rewriting_replace']));
		$url_rewriting_case = trim(strval($_POST['url_rewriting_case']));

		if (	($this->_config['url_rewriting'] != $url_rewriting)
			||	($this->_config['url_rewriting_replace'] != $url_rewriting_replace)
			||	($this->_config['url_rewriting_case'] != $url_rewriting_case)	) {
			$this->_regen = true;
			return; // Fix v1.3
		}

		$this->_regen = false;

	}

	private function _doRegenSBStaticContent() {

		return $this->_regen;

	}

	private function _doResetHits() {

		return isset($_POST['reset_hits']);

	}

	private function _doClearSefUrls() {

		return isset($_POST['clear_sefurls']);

	}

	private function _resetHits() {

		global $addonPathData;

		$hits_file = $addonPathData.'/hits.php';

		$hits = array();

		if (gpFiles::SaveArray($hits_file,'hits',$hits)) {
			$message = gpOutput::SelectText('Hits counter reset done.');
			message($message);
			return true;
		}

		return false;

	}

	private function _clearSefUrls($msg=true) {

		global $addonPathData;

		$id2slug_file = $addonPathData.'/id2slug.php';

		$id2slug = array();

		if (gpFiles::SaveArray($id2slug_file,'id2slug',$id2slug) && $msg) {
			$message = gpOutput::SelectText('All SEF urls have been destroyed.');
			message($message);
			return true;
		}

		return false;

	}

	private function _isValidConfig() {

		return true;

	}

	private function _updateConfig() {

		global $dataDir;

		$cfg = array();

		$cfg['url_rewriting'] = intval($_POST['url_rewriting']);

		$url_rewriting_replace = preg_replace("/[\s]+/", '', $_POST['url_rewriting_replace']);
		$cfg['url_rewriting_replace'] = htmlspecialchars($url_rewriting_replace);

		$cfg['url_rewriting_case'] = trim(strval($_POST['url_rewriting_case']));
		$cfg['url_redirect'] = intval($_POST['url_redirect']);
		$cfg['not_found_redirect'] = intval($_POST['not_found_redirect']);
		$cfg['old_sefurl_redirect'] = intval($_POST['old_sefurl_redirect']);

		$cfg['h2_rewriting'] = intval($_POST['h2_rewriting']);
		$cfg['remove_link'] = intval($_POST['remove_link']);

		$cfg['meta_keywords'] = intval($_POST['meta_keywords']);
		$cfg['remove_keywords_tags'] = intval($_POST['remove_keywords_tags']);
		$cfg['meta_desc'] = intval($_POST['meta_desc']);

		$cfg['count_hits'] = intval($_POST['count_hits']);
		$cfg['show_hits'] = intval($_POST['show_hits']);

		$cfg['gadget_list_type'] = trim(strval($_POST['gadget_list_type']));
		$cfg['gadget_list_length'] = intval($_POST['gadget_list_length']);
		$cfg['gadget_title_excerpt'] = intval($_POST['gadget_title_excerpt']);
		$cfg['gadget_excerpt_lenght'] = intval($_POST['gadget_excerpt_lenght']);

		$this->_config = $cfg;
	
	}

	private function _doSaveConfig() {

		return isset($_POST['save_config']);

	}

	private function _editConfig() {

		global $addonPathCode, $page;

		// Css
		$css	= '<style type="text/css">'
			. '.SimpleBlogSEO_EditConfig h2{'
			. '	margin-bottom: 20px'
			. '}'
			. '.SimpleBlogSEO_EditConfig form{'
			. '	clear: both'
			. '}'
			. '.SimpleBlogSEO_EditConfig label{'
			. '	display: block;'
			. '	width: 250px;'
			. '	float: left'
			. '}'
			. '.SimpleBlogSEO_EditConfig fieldset{'
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

		// We load the editConfig.php template
		include($addonPathCode.'/Config/EditConfig_Tmpl.php');

	}

	private function _saveConfig() {

		global $config, $addonPathData;

		$cfg_file = $addonPathData.'/config.php';	

		return gpFiles::SaveArray($cfg_file, 'cfg', $this->_config);

	}

}


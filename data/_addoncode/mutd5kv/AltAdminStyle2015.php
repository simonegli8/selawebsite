<?php /* UTF-8! ÄÖÜäöüß
######################################################################
PHP script for gpEasy FlatAdmin 2015 
Author: J. Krausz
Date: 2015-03-18
Version 1.0
######################################################################
*/
defined('is_running') or die('Not an entry point...');


function AAS_AdminGetHead() {
  global $page, $addonRelativeCode;
  $page->css_admin[] =  $addonRelativeCode . '/AltAdminStyle2015.css';
  $page->head_js[] =    $addonRelativeCode . '/AltAdminStyle2015.js';
}

function AAS_CKEditor_minimalist_Skin($options) { 
  global $addonRelativeCode, $addonFolderName;
  $JSrelativeDir = strpos($addonRelativeCode, "_addoncode") ? '../../../../data/_addoncode/' . $addonFolderName : '../../../../addons/' . $addonFolderName ;
  $options['skin'] =  $JSrelativeDir . "/CKEditor_skins/minimalist";
  return $options;
}

function AAS_InlineEdit_Scripts($scripts) {
  global $addonRelativeCode;
  $scripts[] = $addonRelativeCode.'/inlineEdit.js';
  return $scripts;
}

?>
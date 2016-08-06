<?php
defined('is_running') or die('Not an entry point...');

function Install_Check() {
  global $langmessage;
  message($langmessage['REFRESH']);
  return true;
}

?>
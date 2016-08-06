<?php
defined('is_running') or die('Not an entry point...');
$fileVersion = '5.0.3';
$fileModTime = '1470482834';
$file_stats = array (
  'created' => 1470482834,
  'gpversion' => '5.0.3',
  'modified' => 1470482834,
  'username' => 'sela',
);

$forms = array (
  0 => 
  array (
    'name' => 'Contact',
    'description' => 'Built-in gpEasy contact form',
    'published' => 1,
    'hook' => 'AntiSpam_Check',
    'control_title' => 'special_contact',
    'control_cmd_name' => 'cmd',
    'control_cmd_value' => 'gp_send_message',
    'criteria_email' => 1,
    'criteria_email_alias' => '',
    'criteria_username' => 0,
    'criteria_username_alias' => 'name',
    'count_hits' => 0,
    'count_spammers' => 0,
  ),
);


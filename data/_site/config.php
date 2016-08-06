<?php
defined('is_running') or die('Not an entry point...');
$fileVersion = '5.0.3';
$fileModTime = '1470489835';
$file_stats = array (
  'created' => 1470480313,
  'gpversion' => '5.0.3',
  'modified' => 1470489835,
  'username' => 'sela',
);

$config = array (
  'language' => 'de',
  'toemail' => 'kontakt@gemeindesela.ch',
  'gpLayout' => 'jwvjc4d',
  'title' => 'Gemeinde Sela',
  'keywords' => 'Typesetter , Easy CMS, Content Management, PHP, Free CMS, Website builder, Open Source',
  'desc' => 'A new Typesetter installation. You can change your site\'s description in the configuration.',
  'timeoffset' => '0',
  'langeditor' => 'de',
  'dateformat' => '%m/%d/%y - %I:%M %p',
  'gpversion' => '5.0.3',
  'passhash' => 'sha512',
  'gpuniq' => 'XZsYYLC5dNimQNerHGJO',
  'combinecss' => true,
  'combinejs' => true,
  'etag_headers' => true,
  'addons' => 
  array (
    'xzboqmh' => 
    array (
      'code_folder_part' => '/data/_addoncode/xzboqmh',
      'data_folder' => 'xzboqmh',
      'name' => 'Simple Blog',
      'version' => '3.0.4',
      'id' => '17',
      'remote_install' => true,
      'editable_text' => 'Text.php',
      'About' => 'Designed to enable a simple reverse-chronological commentary on your site. Includes RSS feed, editable labels, categories gadget and archive gadget.',
    ),
    'csiw7r8' => 
    array (
      'code_folder_part' => '/data/_addoncode/csiw7r8',
      'data_folder' => 'csiw7r8',
      'name' => 'AntiSpamSFS',
      'version' => '1.3',
      'id' => '169',
      'remote_install' => true,
      'editable_text' => 'Text.php',
      'About' => 'Protect your gpEasy forms against spammers black-listed on the stopforumspam.com site',
    ),
    'y10hg35' => 
    array (
      'code_folder_part' => '/data/_addoncode/y10hg35',
      'data_folder' => 'y10hg35',
      'name' => 'Google Maps Marker',
      'version' => '1.0.6',
      'id' => '303',
      'remote_install' => true,
      'About' => 'Marker for Google Maps',
    ),
    'xwq2st3' => 
    array (
      'code_folder_part' => '/data/_addoncode/xwq2st3',
      'data_folder' => 'xwq2st3',
      'name' => 'Search Engine Poker',
      'version' => '1.1',
      'id' => '134',
      'remote_install' => true,
      'About' => 'Pokes Search Engines from admin area if your site or sitemap has been updated.',
    ),
    'jlf8iiw' => 
    array (
      'code_folder_part' => '/data/_addoncode/jlf8iiw',
      'data_folder' => 'jlf8iiw',
      'name' => 'SimpleBlogSEO',
      'version' => '1.3.1',
      'id' => '188',
      'remote_install' => true,
      'editable_text' => 'Text.php',
      'About' => 'Add Clean Urls and other SEO related features to Simple Blog',
    ),
    '18cuscl' => 
    array (
      'code_folder_part' => '/data/_addoncode/18cuscl',
      'data_folder' => '18cuscl',
      'name' => 'GoogleAnalytics',
      'version' => '1.0',
      'id' => '295',
      'remote_install' => true,
      'About' => 'Support for Google Analytics for your site.',
    ),
    'mutd5kv' => 
    array (
      'code_folder_part' => '/data/_addoncode/mutd5kv',
      'data_folder' => 'mutd5kv',
      'name' => 'FlatAdmin 2015',
      'version' => '1.2',
      'id' => '279',
      'remote_install' => true,
      'About' => 'A flat and reduced style alternative for gpEasy’s admin interface. Includes a minimalistic and monochromatic CKEditor skin. An artistic similarity to WordPress cannot be completely denied ;o)',
    ),
    'Sermons' => 
    array (
      'code_folder_part' => '/addons/Sermons',
      'data_folder' => 'Sermons',
      'name' => 'Sermons',
      'version' => '1.24',
      'id' => '269',
      'About' => 'A plugin to stream mp3 sermons. The sermons can be uploaded via ftp or the file browser and get instantly published.',
      'html_head' => '<link rel="stylesheet" href="/sela/addons/Sermons/style.css" type="text/css" />',
    ),
  ),
  'file_count' => 10,
  'maximgarea' => '',
  'maxthumbsize' => '100',
  'check_uploads' => false,
  'colorbox_style' => 'example1',
  'customlang' => 
  array (
  ),
  'showgplink' => true,
  'showsitemap' => true,
  'showlogin' => true,
  'auto_redir' => '90',
  'history_limit' => '30',
  'resize_images' => true,
  'themes' => 
  array (
  ),
  'gadgets' => 
  array (
    'Contact' => 
    array (
      'class' => '\\gp\\special\\ContactGadget',
    ),
    'Search' => 
    array (
      'method' => 
      array (
        0 => '\\gp\\special\\Search',
        1 => 'gadget',
      ),
    ),
    'Simple_Blog' => 
    array (
      'addon' => 'xzboqmh',
      'data' => '/data/_addondata/xzboqmh/gadget.php',
    ),
    'Simple_Blog_Categories' => 
    array (
      'addon' => 'xzboqmh',
      'class' => 'SimpleBlogCategories',
      'script' => '/data/_addoncode/xzboqmh/CategoriesGadget.php',
    ),
    'Simple_Blog_Archives' => 
    array (
      'addon' => 'xzboqmh',
      'class' => 'SimpleBlogArchives',
      'script' => '/data/_addoncode/xzboqmh/ArchivesGadget.php',
    ),
    'AntiSpamSFS_Gadget' => 
    array (
      'addon' => 'csiw7r8',
      'class' => 'AntiSpamSFS_Gadget',
      'script' => '/data/_addoncode/csiw7r8/Site/AntiSpamSFS_Gadget.php',
    ),
    'SimpleBlogSEO_Gadget' => 
    array (
      'addon' => 'jlf8iiw',
      'class' => 'SimpleBlogSEO_Gadget',
      'script' => '/data/_addoncode/jlf8iiw/Gadget/SimpleBlogSEO_Gadget.php',
    ),
    'Sermon_Player' => 
    array (
      'addon' => 'Sermons',
      'class' => 'Gadget_Player',
      'script' => '/addons/Sermons/Gadget_Player.php',
    ),
    'Sermon_PlayerRecent' => 
    array (
      'addon' => 'Sermons',
      'class' => 'Gadget_PlayerRecent',
      'script' => '/addons/Sermons/Gadget_PlayerRecent.php',
    ),
  ),
  'hooks' => 
  array (
    'RenameFileDone' => 
    array (
      'xzboqmh' => 
      array (
        'addon' => 'xzboqmh',
        'class' => 'SimpleBlogCommon',
        'script' => '/data/_addoncode/xzboqmh/SimpleBlogCommon.php',
      ),
    ),
    'Search' => 
    array (
      'xzboqmh' => 
      array (
        'addon' => 'xzboqmh',
        'class' => 'BlogSearch',
        'script' => '/data/_addoncode/xzboqmh/Search.php',
      ),
    ),
    'GetHead' => 
    array (
      'xzboqmh' => 
      array (
        'addon' => 'xzboqmh',
        'script' => '/data/_addoncode/xzboqmh/HookHead.php',
      ),
      'jlf8iiw' => 
      array (
        'addon' => 'jlf8iiw',
        'class' => 'SimpleBlogSEO_GetHead',
        'script' => '/data/_addoncode/jlf8iiw/Hooks/GetHead.php',
      ),
      '18cuscl' => 
      array (
        'addon' => '18cuscl',
        'script' => '/data/_addoncode/18cuscl/Get_Head_GoogleAnalytics.php',
        'method' => 'Get_Head',
      ),
      'mutd5kv' => 
      array (
        'addon' => 'mutd5kv',
        'script' => '/data/_addoncode/mutd5kv/AltAdminStyle2015.php',
        'method' => 'AAS_AdminGetHead',
      ),
    ),
    'PageRunScript' => 
    array (
      'csiw7r8' => 
      array (
        'addon' => 'csiw7r8',
        'class' => 'AntiSpamSFS_Site',
        'script' => '/data/_addoncode/csiw7r8/Site/AntiSpamSFS.php',
        'method' => 'PageRunScript',
      ),
    ),
    'AntiSpam_Form' => 
    array (
      'csiw7r8' => 
      array (
        'addon' => 'csiw7r8',
        'class' => 'AntiSpamSFS_Site',
        'script' => '/data/_addoncode/csiw7r8/Site/AntiSpamSFS.php',
        'method' => 'Form',
      ),
    ),
    'AntiSpam_Check' => 
    array (
      'csiw7r8' => 
      array (
        'addon' => 'csiw7r8',
        'class' => 'AntiSpamSFS_Site',
        'script' => '/data/_addoncode/csiw7r8/Site/AntiSpamSFS.php',
        'method' => 'Check',
      ),
    ),
    'GetContent_After' => 
    array (
      'csiw7r8' => 
      array (
        'addon' => 'csiw7r8',
        'class' => 'AntiSpamSFS',
        'script' => '/data/_addoncode/csiw7r8/Common/AntiSpamSFS.php',
        'method' => 'Messages',
      ),
    ),
    'WhichPage' => 
    array (
      'jlf8iiw' => 
      array (
        'addon' => 'jlf8iiw',
        'class' => 'SimpleBlogSEO_whichPage',
        'script' => '/data/_addoncode/jlf8iiw/Hooks/WhichPage.php',
        'method' => 'whichPage',
      ),
    ),
    'GetUrl' => 
    array (
      'jlf8iiw' => 
      array (
        'addon' => 'jlf8iiw',
        'script' => '/data/_addoncode/jlf8iiw/Hooks/GetUrl.php',
        'method' => 
        array (
          0 => 'GetUrlStatic',
          1 => 'GetUrl',
        ),
      ),
    ),
    'CKEditorConfig' => 
    array (
      'mutd5kv' => 
      array (
        'addon' => 'mutd5kv',
        'script' => '/data/_addoncode/mutd5kv/AltAdminStyle2015.php',
        'method' => 'AAS_CKEditor_minimalist_Skin',
      ),
    ),
    'InlineEdit_Scripts' => 
    array (
      'mutd5kv' => 
      array (
        'addon' => 'mutd5kv',
        'script' => '/data/_addoncode/mutd5kv/AltAdminStyle2015.php',
        'method' => 'AAS_InlineEdit_Scripts',
      ),
    ),
  ),
  'space_char' => '-',
  'cdn' => '',
  'homepath_key' => 'a',
  'homepath' => 'Home',
  'admin_links' => 
  array (
    'Admin_Blog' => 
    array (
      'label' => 'Admin Blog',
      'addon' => 'xzboqmh',
      'class' => 'AdminSimpleBlogPosts',
      'script' => '/data/_addoncode/xzboqmh/Admin/Posts.php',
    ),
    'Admin_BlogConfig' => 
    array (
      'label' => 'Admin Blog Configuration',
      'addon' => 'xzboqmh',
      'class' => 'AdminSimpleBlogConfig',
      'script' => '/data/_addoncode/xzboqmh/Admin/Configuration.php',
    ),
    'Admin_BlogCategories' => 
    array (
      'label' => 'Admin Blog Categories',
      'addon' => 'xzboqmh',
      'class' => 'AdminSimpleBlogCategories',
      'script' => '/data/_addoncode/xzboqmh/Admin/Categories.php',
    ),
    'Admin_BlogComments' => 
    array (
      'label' => 'Admin Blog Comments',
      'addon' => 'xzboqmh',
      'class' => 'SimpleBlogComments',
      'script' => '/data/_addoncode/xzboqmh/Admin/Comments.php',
    ),
    'Admin_AntiSpamSFS_EditConfig' => 
    array (
      'label' => 'Edit Config',
      'addon' => 'csiw7r8',
      'class' => 'AntiSpamSFS_Admin_EditConfig',
      'script' => '/data/_addoncode/csiw7r8/Admin/EditConfig/EditConfig.php',
    ),
    'Admin_AntiSpamSFS_ManageForms' => 
    array (
      'label' => 'Manage Forms',
      'addon' => 'csiw7r8',
      'class' => 'AntiSpamSFS_Admin_ManageForms',
      'script' => '/data/_addoncode/csiw7r8/Admin/ManageForms/ManageForms.php',
    ),
    'Admin_AntiSpamSFS_ShowLog' => 
    array (
      'label' => 'Show Log',
      'addon' => 'csiw7r8',
      'class' => 'AntiSpamSFS_Admin_ShowLog',
      'script' => '/data/_addoncode/csiw7r8/Admin/ShowLog/ShowLog.php',
    ),
    'Admin_GoogleMapsMarker' => 
    array (
      'label' => 'Admin GoogleMapsMarker',
      'addon' => 'y10hg35',
      'class' => 'Admin',
      'script' => '/data/_addoncode/y10hg35/Admin_GoogleMapsMarker.php',
    ),
    'Admin_SearchEnginePoker' => 
    array (
      'label' => 'Poke Search Engines',
      'addon' => 'xwq2st3',
      'class' => 'Admin_SE_Poker',
      'script' => '/data/_addoncode/xwq2st3/Admin_SE_Poker.php',
    ),
    'Admin_SimpleBlogSEO_EditConfig' => 
    array (
      'label' => 'Edit Config',
      'addon' => 'jlf8iiw',
      'class' => 'SimpleBlogSEO_EditConfig',
      'script' => '/data/_addoncode/jlf8iiw/Config/EditConfig.php',
    ),
    'Admin_GoogleAnalytics' => 
    array (
      'label' => 'Configure',
      'addon' => '18cuscl',
      'class' => 'Admin_GoogleAnalytics',
      'script' => '/data/_addoncode/18cuscl/Admin_GoogleAnalytics.php',
    ),
    'Admin_Sermons' => 
    array (
      'label' => 'Admin Sermons',
      'addon' => 'Sermons',
      'class' => 'Admin_Sermons',
      'script' => '/addons/Sermons/Admin_Sermons.php',
    ),
    'Admin_Edit_Sermons' => 
    array (
      'label' => 'Edit Sermons',
      'addon' => 'Sermons',
      'class' => 'Edit_Sermons',
      'script' => '/addons/Sermons/Edit_Sermons.php',
    ),
  ),
  'HTML_Tidy' => '',
  'Report_Errors' => false,
  'toname' => '',
  'from_address' => 'AutomatedSender@localhost',
  'from_name' => 'Automated Sender',
  'from_use_user' => true,
  'require_email' => 'email',
  'mail_method' => 'smtp',
  'sendmail_path' => '',
  'smtp_hosts' => 'mail.gemeindesela.ch:25',
  'smtp_user' => 'kontakt@gemeindesela.ch',
  'smtp_pass' => '0192iw0192IW',
  'recaptcha_public' => '6Lf9WPgSAAAAALIcZ0nLaMWXZ_2v3EhPpQzbgHCI',
  'recaptcha_private' => '6Lf9WPgSAAAAANJM0esl3B7FjIjJrGCiZ4uD8xZS',
  'recaptcha_language' => 'de',
  'menus' => 
  array (
    'm1' => 'Öffentliche Seiten',
    'm2' => 'Intranet',
  ),
);

$meta_data = array (
);
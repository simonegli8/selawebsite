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

$pages = array (
  'gp_menu' => 
  array (
    'a' => 
    array (
      'level' => 0,
    ),
    'c' => 
    array (
      'level' => 0,
    ),
    'g' => 
    array (
      'level' => 0,
    ),
    'special_contact' => 
    array (
      'level' => 0,
    ),
    'special_blog' => 
    array (
      'level' => 0,
    ),
    'l' => 
    array (
      'level' => 0,
    ),
    'special_blog_feed' => 
    array (
      'level' => 0,
    ),
  ),
  'gp_index' => 
  array (
    'Home' => 'a',
    'Contact' => 'special_contact',
    'Site_Map' => 'special_site_map',
    'Galleries' => 'special_galleries',
    'Missing' => 'special_missing',
    'Search' => 'special_gpsearch',
    'Blog' => 'special_blog',
    'Blog_Categories' => 'special_blog_categories',
    'Blog_Feed' => 'special_blog_feed',
    'GoogleMapsMarker' => 'special_googlemapsmarker',
    'Predigten' => 'c',
    'Rundbriefe' => 'g',
    'Zeugnisse' => 'h',
    'Prophetien' => 'i',
    'Intern' => 'j',
    'Photos' => 'k',
    'Podcast' => 'l',
    'Cloud' => 'o',
    'Verwaltung' => 'p',
    'Sermons_Podcast' => 'special_sermons_podcast',
  ),
  'gp_titles' => 
  array (
    'a' => 
    array (
      'label' => 'Home',
      'type' => 'text',
      'gpLayout' => 'jwvjc4d',
    ),
    'special_contact' => 
    array (
      'lang_index' => 'contact',
      'type' => 'special',
    ),
    'special_site_map' => 
    array (
      'lang_index' => 'site_map',
      'type' => 'special',
    ),
    'special_galleries' => 
    array (
      'lang_index' => 'galleries',
      'type' => 'special',
    ),
    'special_missing' => 
    array (
      'label' => 'Missing',
      'type' => 'special',
    ),
    'special_gpsearch' => 
    array (
      'label' => 'Search',
      'type' => 'special',
    ),
    'special_blog' => 
    array (
      'label' => 'Blog',
      'type' => 'special',
      'addon' => 'xzboqmh',
      'class' => 'SimpleBlog',
      'script' => '/data/_addoncode/xzboqmh/SimpleBlog.php',
      'gpLayout' => '1832',
    ),
    'special_blog_categories' => 
    array (
      'label' => 'Blog Categories',
      'type' => 'special',
      'addon' => 'xzboqmh',
      'class' => 'BlogCategories',
      'script' => '/data/_addoncode/xzboqmh/SimpleBlogCategories.php',
    ),
    'special_blog_feed' => 
    array (
      'label' => 'Blog Feed',
      'type' => 'special',
      'addon' => 'xzboqmh',
      'script' => '/data/_addoncode/xzboqmh/SimpleBlogFeed.php',
    ),
    'special_googlemapsmarker' => 
    array (
      'label' => 'GoogleMapsMarker',
      'type' => 'special',
      'addon' => 'y10hg35',
      'class' => 'GoogleMapsMarker',
      'script' => '/data/_addoncode/y10hg35/GoogleMapsMarker.php',
    ),
    'c' => 
    array (
      'label' => 'Predigten',
      'type' => 'include,text,wrapper_section',
    ),
    'g' => 
    array (
      'label' => 'Rundbriefe',
      'type' => 'text',
    ),
    'h' => 
    array (
      'label' => 'Zeugnisse',
      'type' => 'include,text',
      'gpLayout' => '7495',
    ),
    'i' => 
    array (
      'label' => 'Prophetien',
      'type' => 'include,text',
      'gpLayout' => '7495',
    ),
    'j' => 
    array (
      'label' => 'Intern',
      'type' => 'text',
      'gpLayout' => '7495',
    ),
    'k' => 
    array (
      'label' => 'Photos',
      'type' => 'text',
    ),
    'l' => 
    array (
      'label' => 'Podcast',
      'type' => 'text',
    ),
    'o' => 
    array (
      'label' => 'Cloud',
      'type' => 'text',
      'gpLayout' => '7495',
    ),
    'p' => 
    array (
      'label' => 'Verwaltung',
      'type' => 'text',
      'gpLayout' => '7495',
    ),
    'special_sermons_podcast' => 
    array (
      'label' => 'Sermons Podcast',
      'type' => 'special',
      'addon' => 'Sermons',
      'script' => '/addons/Sermons/SermonsPodcastRSS.php',
    ),
  ),
  'gpLayouts' => 
  array (
    'default' => 
    array (
      'theme' => 'Bootswatch_Scss/Flatly',
      'label' => 'Bootswatch_Scss/Flatly',
      'color' => '#93c47d',
    ),
    'jwvjc4d' => 
    array (
      'theme' => 'Three_point_5/Shore',
      'color' => '#76a5af',
      'label' => 'Three Point 5/Shore',
      'addon_id' => '228',
      'version' => '1.1',
      'name' => 'Three Point 5',
      'handlers' => 
      array (
        'TWVudTo_0' => 
        array (
          0 => 'Menu:m1',
        ),
        'VG9wVHdvTWVudTo_0' => 
        array (
          0 => 'TopTwoMenu:m1',
        ),
        'GetAllGadgets' => 
        array (
          0 => 'Extra:Intranet_Link',
          1 => 'Sermon_Player',
          2 => 'Sermon_PlayerRecent',
        ),
        'RXh0cmE6TG9yZW0_0' => 
        array (
          0 => 'Extra:Lorem',
        ),
      ),
      'hander_v' => '2',
      'css' => true,
    ),
    7495 => 
    array (
      'theme' => 'Three_point_5/Shore',
      'color' => '#ffe599',
      'label' => 'Three Point 5/Intranet',
      'addon_id' => '228',
      'version' => '1.1',
      'name' => 'Three Point 5',
      'handlers' => 
      array (
        'TWVudTo_0' => 
        array (
          0 => 'Menu:m2',
        ),
        'VG9wVHdvTWVudTo_0' => 
        array (
          0 => 'TopTwoMenu:m1',
        ),
        'GetAllGadgets' => 
        array (
          0 => 'Extra:Intranet_Link',
          1 => 'Sermon_Player',
          2 => 'Sermon_PlayerRecent',
        ),
        'RXh0cmE6TG9yZW0_0' => 
        array (
          0 => 'Extra:Lorem',
        ),
      ),
      'hander_v' => '2',
      'css' => true,
    ),
    1832 => 
    array (
      'theme' => 'Three_point_5/Shore',
      'color' => '#6fa8dc',
      'label' => 'Three Point 5/Blog',
      'addon_id' => '228',
      'version' => '1.1',
      'name' => 'Three Point 5',
      'handlers' => 
      array (
        'TWVudTo_0' => 
        array (
          0 => 'Menu:m1',
        ),
        'VG9wVHdvTWVudTo_0' => 
        array (
          0 => 'TopTwoMenu:m1',
          1 => 'Extra:Intranet_Link',
        ),
        'GetAllGadgets' => 
        array (
          0 => 'Simple_Blog_Categories',
          1 => 'Simple_Blog_Archives',
          2 => 'Sermon_Player',
          3 => 'Sermon_PlayerRecent',
        ),
        'RXh0cmE6TG9yZW0_0' => 
        array (
          0 => 'Extra:Lorem',
        ),
      ),
      'hander_v' => '2',
      'css' => true,
    ),
  ),
);

$meta_data = array (
);
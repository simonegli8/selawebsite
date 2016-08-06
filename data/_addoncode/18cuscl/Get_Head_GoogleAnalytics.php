<?php
function Get_Head()
{
	global                   $page;
	global                   $addonPathData;

	$configFile              = $addonPathData.'/config.php';
	if( ! file_exists( $configFile ) )
	{
    return;
	}

	include_once $configFile;

	if( ! isset( $config ) )
  {
    return;
	}

	$key                   = $config['key'];
  $displayFeatures       = $config['displayFeatures'];

	$page->head           .= "\n";
	$page->head           .= "\n<script>";
	$page->head           .= "\n  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){";
	$page->head           .= "\n  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),";
	$page->head           .= "\n  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)";
	$page->head           .= "\n  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');";
	$page->head           .= "\n";
	$page->head           .= "\n  ga('create', '$key', 'auto');";

  if( $displayFeatures )
  {
		$page->head         .= "\n  ga('require', 'displayfeatures');";
  }

	$page->head           .= "\n  ga('send', 'pageview');";
	$page->head           .= "\n";
	$page->head           .= "\n</script>";
	$page->head           .= "\n";
}

// vim: set noai ts=2 sw=2: 
?>

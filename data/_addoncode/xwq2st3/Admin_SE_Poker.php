<?php
/*
  Programmer : Johannes Pretorius
  Company : Dominion IT
  URL : http://www.dominion-it.co.za
  Purpose : To update search engines with the websites sitemaps
  Version : 1.1
*/
defined('is_running') or die('Not an entry point...');


class Admin_SE_Poker{
	function Admin_SE_Poker(){
	   $sitemap_url =  'http://'.$_SERVER['SERVER_NAME'].common::GetUrl('Special_Site_Map','xml');
       $sitemapFILE = 'sitemap.xml'; 
       $sitedata = file_get_contents($sitemap_url);
	   file_put_contents($sitemapFILE,$sitedata);
	   $sitemap_url =  'http://'.$_SERVER['SERVER_NAME'].'/sitemap.xml';
	   $pokeresults = $this->pokeSitemaps($sitemap_url);
		echo '<h1>Search Engine Poke results</h1>';
		echo "<p>This only submits the sitemaps to the respective search engines. No AddURL's are done</p>";
        echo '<h2>Success ('.count($pokeresults['success']).')</h2>';		
		echo '<p>';
		foreach ($pokeresults['success'] as $pokeresult) {
		  echo $pokeresult.'<br/>';
		}  
		echo '</p>';
        echo '<h2>Fail ('.count($pokeresults['fail']).')</h2>';				
		echo '<p>';
		foreach ($pokeresults['fail'] as $pokeresult) {
		  echo $pokeresult.'<br/>';
		}  
		echo '</p>';
		
	}
	
   function pokeSitemaps($xmlurl) {
	   $bing 	 = 'www.bing.com';
	   $ask 	 = 'submissions.ask.com';
	   $google = 'www.google.com';
	   //$yahoo  = 'search.yahooapis.com'; //PART of BING now
	   $icerocket = 'www.icerocket.com';
       
   	   $status = $this->submitSitemapToSE($bing,'/webmaster/ping.aspx?sitemap='.urlencode( $xmlurl ));
	   //error_log('['.$bing .'] : '.$status);
       if( $status !== 200){
			$result['fail'][] = $bing; 
		} else {
		  $result['success'][] = $bing; 
		}

   	  /* $status = $this->submitSitemapToSE($yahoo,'/SiteExplorerService/V1/ping?sitemap='.urlencode( $xmlurl ));
	   //error_log('['.$yahoo .'] : '.$status);
	   
       if( $status !== 200){
			$result['fail'][] = $yahoo; 
		} else {
		  $result['success'][] = $yahoo; 
		}
        */
   	   $status = $this->submitSitemapToSE($ask,'/ping?sitemap='.urlencode( $xmlurl ));
	   //error_log('['.$ask .'] : '.$status);
       if( $status !== 200){
			$result['fail'][] = $ask; 
		} else {
		  $result['success'][] = $ask; 
		}

   	   $status = $this->submitSitemapToSE($google,'/webmasters/tools/ping?sitemap='.urlencode( $xmlurl ));
	   //error_log('['.$google .'] : '.$status);
//	   error_log($google.'/webmasters/tools/ping?sitemap='.urlencode( $xmlurl ));
       if( $status !== 200){
			$result['fail'][] = $google; 
		} else {
		  $result['success'][] = $google; 
		}
		
	 	$status = $this->submitSitemapToSE($icerocket,'/c?p=ping&url='.urlencode( $xmlurl ));
		//error_log('['.$icerocket .'] : '.$status);
		
       if( $status !== 200){
			$result['fail'][] = $icerocket; 
		} else {
		  $result['success'][] = $icerocket; 
		}

	   return  $result;
   }

   function submitSitemapToSE($searchEngine,$sitemapURL){
        $ans = false;
		if( $fp=@fsockopen($searchEngine, 80) ) {
		  $req =  'GET '.$sitemapURL . " HTTP/1.1\r\n" .
				  "Host: $searchEngine\r\n" .
				  "User-Agent: Mozilla/5.0 (compatible; " .
				  PHP_OS . ") PHP/" . PHP_VERSION . "\r\n" .
				  "Connection: Close\r\n\r\n";
		  fwrite( $fp, $req );
		  while( !feof($fp) ) {
			 if( @preg_match('~^HTTP/\d\.\d (\d+)~i', fgets($fp, 128), $m) ) {
				$ans = intval( $m[1] );
				break;
			 }
		  }
		  fclose( $fp );
	   }
	   return $ans;
   }   
}
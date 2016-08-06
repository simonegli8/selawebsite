<?php
/*
plugin for Google Maps Marker
Author: a2exfr
http://my-sitelab.com/
Version 1.0.6 */


defined('is_running') or die('Not an entry point...');


class Admin{

	function __construct(){
	
		global $page, $addonRelativeCode;
		
		$this->loadConfig();
					
		$page->head .= '<script type="text/javascript" src="http://maps.google.com/maps/api/js?key='.$this->apikey.'"></script>';
				
		$page->head .= '<script type="text/javascript" src="'.$addonRelativeCode .'/maps_admin.js"></script>' ;
		
		$page->head_js[] =   $addonRelativeCode . '/admin.js';
		$page->head .= "\n" . '<script type="text/javascript">gpFinder_url = "' . common::GetUrl('Admin_Browser') . '";</script>' . "\n";
		
		$page->css_admin[] = $addonRelativeCode.'/maps_admin.css';
		
		

		
		
		
	echo '<h2>Google Maps marker admin</h2>';
	
	
	
	 echo '  <div id="mapCanvas"></div>';
  
	
	
	
	$cmd = common::GetCommand();

    switch($cmd){
      case 'saveConfig':
        $this->saveConfig();
		break;
    }
		$this->loadConfig();
		$this->showForm();
		

	
	
	}

	 function showForm()
  {
   global $langmessage,$addonRelativeCode, $config;


  	echo '<div id="infoPanel">';
	echo '<form action="'.common::GetUrl('Admin_GoogleMapsMarker').'" method="post">';
	
		
	 echo '<div id="map_data">'; 
	 if( $this->markers and $this->markers<>"" ){ 
	  foreach ($this->markers as $key=>$value){
		  echo '<input type="hidden" id="'.$key.'" name="coords" value="'.$value['info'].'"/>';
		  
	  }
	 
	 }
	echo '</div>';
	
	//	echo '<p>Laitude<br>';
		echo '<input type="hidden" name="lat" id="lat" value="'.$this->Lat.'" class="gpinput" style="width:200px" />';
	//	echo '</p>';
	//	echo '<p>Longitude<br>';
		echo '<input type="hidden" name="long" id="long" value="'.$this->Long.'" class="gpinput" style="width:200px" />';
	//	echo '</p>';
	
	//echo '<p>Zoom<br>';
	echo '<input type="hidden" name="zoom" id="zoom" value="'.$this->Zoom.'" class="gpinput" style="width:200px" />';
	//echo '</p>';
	
	echo '<p>Google maps <b>API key</b> <a href="#read_me">[read how to get one...]</a><br/>';
	echo '<input   name="apikey" value="'.$this->apikey .'" class="gpinput" style="width:200px" />';
	echo '</p>';
	
	echo '<p>Bounce marker?  ';
	if( $this->Bouncemarker  ){
	echo '<input type="checkbox" name="Bouncemarker" value="Bouncemarker" id="Bouncemarker" checked="checked" />';
		}else{
	echo '<input type="checkbox" name="Bouncemarker" value="Bouncemarker" id="Bouncemarker"/>';
		}
	echo '<br/>';
	echo '</p>';
	
	echo '<p>Show fullscreen button?  ';
	if( $this->fullscreen  ){
	echo '<input type="checkbox" name="fullscreen" value="fullscreen" id="fullscreen" checked="checked" />';
		}else{
	echo '<input type="checkbox" name="fullscreen" value="fullscreen" id="fullscreen"/>';
		}
	echo '<br/>';
	echo '</p>';
	
	
		echo '<p>Custom icon for marker (.jpg, .png or .gif image)<br/>';
		echo '<input class="gpinput" id="CustomIcon" style="width:200px"';
        echo 'type="text" name="CustomIcon" value="';
        echo  urldecode($this->CustomIcon);
        echo '" placeholder="Custom icon for marker" />';
        echo ' <button class="Gmap_browse_files gpsubmit">' . $langmessage['uploaded_files'] . '</button>';
		echo '</p>';
	
	
	echo '<p>Width of map(px) ';
    echo '<input  type="number" step="1" name="sizeW" value="'.$this->sizeW .'" class="gpinput" style="width:200px" />';
    echo '</p>';
	echo '<p>Height of map(px) ';
    echo '<input  type="number" step="1" name="sizeH" value="'.$this->sizeH .'" class="gpinput" style="width:200px" />';
    echo '</p>';
	
	echo '<p>Use responsive style?  ';
	if( $this->relative  ){
	echo '<input type="checkbox" name="relative" value="relative" id="relative" checked="checked" />';
		}else{
	echo '<input type="checkbox" name="relative" value="relative" id="relative"/>';
		}
	echo '<br/>'; 
	
	echo '<p>Max-height of map(px) for responsive style<br/>';
    echo '<input  type="number" step="1" name="mheight" value="'.$this->mheight .'" class="gpinput" style="width:200px" />';
    echo '</p>';
	
	echo '<p>Disable drag map for screen size smaller than (px)<br/>';
    echo '<input  type="number" step="1" name="dragheight" value="'.$this->dragheight .'" class="gpinput" style="width:200px" />';
    echo '</p>';
	
	
	echo '<p>Google Map Style: <a target="_blank" href="https://www.google.com/search?q=google+maps+styles&ie=utf-8&oe=utf-8&gws_rd=cr&ei=_iw3VqLULaSAzAO_27eQBw#q=free+google+maps+styles">(find styles)</a></br> ';
  echo '<textarea rows="5" cols="45" name="GMStyle" id="GMStyle" placeholder="[your style]">'.$this->GMStyle.'</textarea>';
	echo '</p>';
	
	
	echo '<input type="hidden" name="cmd" value="saveConfig" />';
    echo '<input type="submit" value="'.$langmessage['save_changes'].'" class="gpsubmit GMsave"/>';
    echo '</p>';
    echo '</form>';
	echo '</div>';
  
  
  
  
	foreach ($config['addons'] as $addon_key => $addon_info) {
	  if ($addon_info['name'] == 'Google Maps Marker') {
		$addon_vers = $addon_info['version'];
			  
	  }
	}
  
  
  echo '<div style="float:left;margin-top:2em; border:1px solid #ccc; background:#fafafa; border-radius:3px; padding:12px;">';
 echo ' <p id="read_me"><b>Important: </b> As per Google recent announcement, usage of the Google Maps APIs now requires a key.<br/>
	Maps API applications use the Key for browser apps.<br/>
	To use plugin you need to get google api key(if you already do not have one), and insert it in Google maps API key field.<br/>	

	Go to  <a class=" gpbutton" href="https://console.developers.google.com/" target="_blank">Get API key </a> <br/>
	
   1. Under Google Map\'s Apis choose Google Maps JavaScript API <br/>
   2. Enable the Api. <br/>
   3. Go to credentials section.Choose create Credentials. <br/>
   4. Choose API Key from the popup,and then choose browser key from the proceeding popup. <br/>
   5. Insert in "Google maps API key" field  your own api key obtained. <br/>


		
		
		</p>
		<p>  
		<b>Usage:</b>  Left click on map to set marker. Right click on marker to remove it. Markers is dragable.<br/>
		Left click on marker to edit info window. Info window acsepts html tags.</p>'; 
echo '<p><b>Note:</b> When using several markers, the zoom is automatically set. When using only one marker, you can save the chosen zoom.<br/></p>';
		echo '<br>';
		echo '<h4>Google Maps Marker</h4>';
		echo '<h5>version '.$addon_vers .'</h5>';
		echo '<ul>';
		echo '<li><a href="http://ts-addons.my-sitelab.com/Marker_Google_Maps" target="_blank">Plugin page </a>(Demo,documentation)</li>'; 
		echo '<li><a href="http://www.typesettercms.com/Forum?show=f1303" target="_blank">Support Forum </a>(Qwestions, bugs, issues, suggestions for improvements are welcome.)</li>'; 
		echo '<li><a href="http://www.gpeasy.com/User/2617/Plugins" target="_blank">Another my plugins</a></li>'; 
		echo '</ul>';
		echo '<p><i>plugin for Typesetter CMS</i></p>';
		echo '<p><i>Made by Sitelab</i></p>';
		echo '<p><a href="http://my-sitelab.com/" target="_blank"><img alt="Sitelab" src="'.$addonRelativeCode.'/img/st_logo.jpg'.'"  /></a> </p>';
		
		echo '</div>';		
  echo '</div>';
  
  }


	function saveConfig()
  {
    global                   $addonPathData;
    global                   $langmessage;
	
	$saveMsg="";

    $configFile            		= $addonPathData.'/config.php';
    $config                		= array();
    
	$config['lat']  		= $_POST['lat'];
    $config['long'] 		= $_POST['long'];
	$config['zoom'] 		= $_POST['zoom'];
	$config['apikey'] 		= $_POST['apikey'];
		
	if (isset($_POST['Bouncemarker'])){
	$config['Bouncemarker'] = $_POST['Bouncemarker'];
	} else {
		$config['Bouncemarker'] = '';
	}
	
	if (isset($_POST['fullscreen'])){
	$config['fullscreen'] = $_POST['fullscreen'];
	} else {
		$config['fullscreen'] = '';
	}
	
	$config['sizeW'] 		= $_POST['sizeW'];
	$config['sizeH'] 		= $_POST['sizeH'];
	
	
	if (isset($_POST['relative'])){
	$config['relative'] = $_POST['relative'];
	} else {
		$config['relative'] = '';
	}
	
	$config['mheight'] 		= $_POST['mheight'];
	$config['dragheight'] 	= $_POST['dragheight'];
	$config['GMStyle'] 		= $_POST['GMStyle'];
	
	$config['markers'] 		= $_POST['markers'];
	
		
	
	if (!isset($_POST['CustomIcon']) || $_POST['CustomIcon'] == "") {
      
	  $this->CustomIcon= "";
	  $config['CustomIcon'] = "";
    } elseif (preg_match('/^.*\.(jp?g|bmp|png|gif)$/i', $_POST['CustomIcon']))  {
      $config['CustomIcon'] = trim(urlencode($_POST['CustomIcon']));
	  $this->CustomIcon = trim(urlencode($_POST['CustomIcon']));
	  
    } else { $saveMsg = "Warning: Please specify a image source for marker(.jpg, .png or .gif image)!<br/>";
      $this->CustomIcon = "";
	  $config['CustomIcon'] = "";
	  }
	
	 
	
	
	$this->apikey		= $config['apikey'];
	$this->Lat			= $config['lat'];
    $this->Long			= $config['long'];
	$this->Zoom			= $config['zoom'];
	$this->Bouncemarker	= $config['Bouncemarker']; 
	$this->fullscreen	= $config['fullscreen']; 
	$this->sizeW 		= $config['sizeW'];
	$this->sizeH 		= $config['sizeH'];
	$this->relative 	= $config['relative'];
	$this->mheight 		= $config['mheight'];
	$this->dragheight 	= $config['dragheight'];
	$this->GMStyle 		= $config['GMStyle'];
	
	
	
    if( !gpFiles::SaveArray($configFile,'config',$config) )
    {
      message($langmessage['OOPS'].$saveMsg);
      return false;
    }

    message($langmessage['SAVED'].$saveMsg);
    return true;
  }



  function loadConfig()
  {
    global                   $addonPathData;

    $configFile            = $addonPathData.'/config.php';
     if (file_exists($configFile)) {
            include $configFile;
        }

    if (isset($config)) {
	  $this->apikey  		= $config['apikey'];
      $this->Lat  			= $config['lat'];
      $this->Long 			= $config['long'];
	  $this->Zoom 			= $config['zoom'];
	  $this->Bouncemarker 	= $config['Bouncemarker'];
	  $this->fullscreen 	= $config['fullscreen'];
	  $this->sizeW 			= $config['sizeW'];
	  $this->sizeH 			= $config['sizeH'];
	  $this->relative 		= $config['relative'];
	  $this->mheight 		= $config['mheight'];
	  $this->dragheight 	= $config['dragheight'];
	  $this->CustomIcon 	= $config['CustomIcon'];
	  $this->GMStyle 		= $config['GMStyle'];
	  $this->markers 		= $config['markers'];
	  
	 
    } else {
	  $this->apikey			= 'YOUR_API_KEY';
	  $this->Lat  			= '';
      $this->Long 			= '';
	  $this->Zoom 			= '';
	  $this->Bouncemarker 	= '';
	  $this->fullscreen 	= '';
	  $this->sizeW 			= '';
	  $this->sizeH 			= '';
	  $this->relative 		= '';
	  $this->mheight 		= '';
	  $this->dragheight		= '';
	  $this->CustomIcon 	= '';
	  $this->GMStyle 		= '';
	  $this->markers 		= '';
		
		
		
		
	}
  }  
	
	
	
	
	}



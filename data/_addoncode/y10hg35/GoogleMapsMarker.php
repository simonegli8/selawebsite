<?php
/*
plugin for Google Maps Marker
Author: a2exfr
http://my-sitelab.com/
Version 1.0.6 */


defined('is_running') or die('Not an entry point...');


class GoogleMapsMarker {

	function __construct(){
		
		global $page, $addonRelativeCode,$addonPathData;
				
		$configFile       = $addonPathData.'/config.php';
		
		if( ! file_exists( $configFile ) )	{
		//	$this->getDefaultConfig();
				}
		
		 if (file_exists($configFile)) {
            include $configFile;
        
		
		
		$this->apikey		= $config['apikey'];
		$this->Zoom			= $config['zoom'];
		$this->Bouncemarker	= $config['Bouncemarker']; 
		$this->fullscreen	= $config['fullscreen']; 
		$this->sizeW 		= $config['sizeW'];
		$this->sizeH 		= $config['sizeH'];
		$this->relative 	= $config['relative'];
		$this->mheight 		= $config['mheight'];
		$this->dragheight 	= $config['dragheight'];
		$this->CustomIcon 	= $config['CustomIcon'];
		$this->GMStyle 		= $config['GMStyle'];
		$this->markers 		= $config['markers'];
		
		} else {
			  $this->apikey			= '';
			  $this->Lat  			= '';
			  $this->Long 			= '';
			  $this->Zoom 			= '';
			  $this->infowindow		= '';
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
	
		
	$page->head .= '<script type="text/javascript" src="http://maps.google.com/maps/api/js?key='.$this->apikey.'"></script>';
		
	$page->head .= '<script type="text/javascript" src="'.$addonRelativeCode .'/maps_page.js"></script>' ;
	
	$page->css_user[] = $addonRelativeCode.'/maps_page.css';
		

	
		if($this->relative ){
			$this->sizeW='';
			if(!$this->mheight){ $page->css_user[] = $addonRelativeCode.'/map_relative.css'; }
			else{$page->css_user[] = $addonRelativeCode.'/map_relative.css';
			$page->head .= '<style>#mapCanvas {max-height:'.$this->mheight.'px!important;}</style>';
				}
				
			};
		if (!$this->sizeW ){$this->sizeW=500;$this->sizeH=400;}
		if (!$this->sizeH ){$this->sizeW=500;$this->sizeH=400;}
		
		
		
		
		echo '<div style="display:none;">';
		echo '<div id="map_data">'; 
		 if( $this->markers and $this->markers<>"" ){ 
		  foreach ($this->markers as $key=>$value){
			  echo '<input type="hidden" id="'.$key.'" name="coords" value="'.$value['info'].'"/>';
			  
		  }
		 
		 }
		echo '</div>';
		
		echo '<input type="hidden" name="zoom" id="zoom" value="'.$this->Zoom.'" class="gpinput" style="width:200px" />';
		echo '<input type="hidden" name="dragheight" id="dragheight" value="'.$this->dragheight.'" class="gpinput" style="width:200px" />';
		
		if( $this->Bouncemarker  ){
		echo '<input type="checkbox" name="Bouncemarker" value="Bouncemarker" id="Bouncemarker" checked="checked" />';
		}else{
		echo '<input type="checkbox" name="Bouncemarker" value="Bouncemarker" id="Bouncemarker"/>';
		}
		echo '<input type="hidden" name="CustomIcon" id="CustomIcon" value="'.urldecode($this->CustomIcon).'" class="gpinput" style="width:200px" />';
				
		echo '<textarea rows="5" cols="45" name="GMStyle" id="GMStyle">'.$this->GMStyle.'</textarea>';
	
		echo '</div>';
		
		
		
	
	if ($this->fullscreen)	{
			if($this->relative ){$style='style="width:100%;"'; } else { $style='style=" width: '.$this->sizeW.'px;"'; }
			echo ' <div id="map-container" '.$style.'>';
			   echo ' <div class="btn-full-screen">';
				   echo ' <a id="btn-enter-full-screen"><img src="' . $addonRelativeCode . '/fullscreen_enter.png"/></a>';
					echo '<a id="btn-exit-full-screen"><img src="' . $addonRelativeCode . '/fullscreen_exit.png "/></a>';
			  echo '  </div>';
				echo '<div id="mapCanvas" style=" width: '.$this->sizeW.'px; height: '.$this->sizeH.'px;"></div>';
		   echo ' </div>';
	
	
	} else {
		
		echo '<div id="mapCanvas" style=" width: '.$this->sizeW.'px; height: '.$this->sizeH.'px;"></div>';
		
	}
	

	
		
	}

	
	
	
	
}



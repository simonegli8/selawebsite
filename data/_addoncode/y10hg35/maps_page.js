/*
plugin for Google Maps Marker
Author: a2exfr
http://my-sitelab.com/
Version 1.0.5 */
function initialize() {
	
	var z = Number(document.getElementById("zoom").value);

	var styl = document.getElementById("GMStyle").value;
	if(styl != '') {var styl =  JSON.parse(styl);} 

	var ico = document.getElementById('CustomIcon').value;
	var dragh = document.getElementById("dragheight").value;

	var isDraggable = $(document).width() > dragh ? true : false;
	
	var wrapper         = $("#map_data");
	
	markers = {};
	
  var map = new google.maps.Map(document.getElementById('mapCanvas'), {
    center: new google.maps.LatLng(0, 0),
    mapTypeId: google.maps.MapTypeId.ROADMAP,
	draggable: isDraggable,
	scrollwheel: false,
	styles: styl
  });
  
  
   
 if (document.getElementById('Bouncemarker').checked) {
    var ani = google.maps.Animation.BOUNCE;
  } else {
    var ani = null;
  }

var getLatLng = function(lat, lng) {
    return new google.maps.LatLng(lat, lng);
}; 
var getMarkerUniqueId= function(lat, lng) {
    return lat + '_' + lng;
}
  
 var setMarkers = function() {
	var inp = wrapper.find("input[name='coords']" );
	
	inp.each(function(){
		 var coords = $(this).attr('id');
		 var info =$(this).val();
		 var res = coords.split("_"); 
		 setMarker(res[0], res[1], info);
	})	

	
}


var setMarker = function(lat, lng, info) {
	var markerId = getMarkerUniqueId(lat, lng); 
   	var marker = new google.maps.Marker({
        position: getLatLng(lat, lng),
        map: map,
		animation:ani,
		icon:ico,
		html : info
    });
	setInfoWindow(marker);
	markers[markerId] = marker; 
  //  bindMarkerEvents(marker); 
}


var setInfoWindow = function(marker) {
	if (marker.html !== ""  && marker.html !== "You can edit the text of this infowindow.<br> Html also possible") {	
		var infoWnd = new google.maps.InfoWindow({
		  content : marker.html
		});
		
		google.maps.event.addListener(marker, "click", function() {
		  infoWnd.open(marker.getMap(), marker);
		});
	}
}  


function AutoCenter() {

	var bounds = new google.maps.LatLngBounds();

		var count=0;
		$.each(markers, function (index, marker) {
			bounds.extend(marker.position);
			latLng1 = marker.getPosition(); 
			count++;
		});
	
	if(count > 1) {
			map.fitBounds(bounds);
	} else {
			if (typeof latLng1 !== "undefined"){
			map.setCenter(latLng1);
			}
			map.setZoom(z);
	}
	
} 
  
setMarkers();
AutoCenter();

 
//enable scroll on  
 google.maps.event.addListener(map, 'click', function(event){
          this.setOptions({scrollwheel:true});
  });
 
 
  //fullscreen buttons
  
	var googleMapWidth = $("#map-container").css('width');
	var googleMapHeight = $("#map-container").css('height');
	var googleMapWidthR = $("#mapCanvas").css('width');
	var googleMapHeightR = $("#mapCanvas").css('height');

$('#btn-enter-full-screen').click(function() {

    $("#map-container").css({
        position: 'fixed',
        top: 0,
        left: 0,
        width: '100%',
        height: '100%',
        backgroundColor: 'white',
		"z-index": '99999'
    });

    $("#mapCanvas").css({
        height: '100%',
		width: '100%',
		
    });

    google.maps.event.trigger(map, 'resize');
	AutoCenter();

    // Gui
    $('#btn-enter-full-screen').toggle();
    $('#btn-exit-full-screen').toggle();

    return false;
});

$('#btn-exit-full-screen').click(function() {

    $("#map-container").css({
        position: 'relative',
        top: 0,
        width: googleMapWidth,
        height: googleMapHeight,
        backgroundColor: 'transparent'
    });
	
	$("#mapCanvas").css({
		width: googleMapWidthR,
        height: googleMapHeightR,
		
    });
	
    google.maps.event.trigger(map, 'resize');
  	AutoCenter();
	
    // Gui
    $('#btn-enter-full-screen').toggle();
    $('#btn-exit-full-screen').toggle();
    return false;
});
  
  
  
 

}

google.maps.event.addDomListener(window, 'load', initialize);


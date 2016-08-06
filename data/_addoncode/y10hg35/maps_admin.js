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
	var wrapper         = $("#map_data");

  var map = new google.maps.Map(document.getElementById('mapCanvas'), {
    center: new google.maps.LatLng(0, 0),
    mapTypeId: google.maps.MapTypeId.ROADMAP,
	styles: styl
  });
  
 markers = {};

 
if (document.getElementById('Bouncemarker').checked) {
   var ani = google.maps.Animation.BOUNCE;
 } else {
   var ani = null;
 } 
 
document.getElementById('Bouncemarker').onclick = function() {
 if ( this.checked ) {
	var ani = google.maps.Animation.BOUNCE;
	$.each(markers, function (index, marker) {
		marker.setAnimation(google.maps.Animation.BOUNCE);
		});
	} else {
	 var ani = null;
	 $.each(markers, function (index, marker) {
		marker.setAnimation(null);
		});
	} 
};

var getMarkerUniqueId= function(lat, lng) {
    return lat + '_' + lng;
}

var getLatLng = function(lat, lng) {
    return new google.maps.LatLng(lat, lng);
};


var addMarker = google.maps.event.addListener(map, 'click', function(e) {
    var lat = e.latLng.lat(); 
    var lng = e.latLng.lng();
    var markerId = getMarkerUniqueId(lat, lng); 
    var marker = new google.maps.Marker({
        position: getLatLng(lat, lng),
        map: map,
		animation:ani,
		icon:ico,
		draggable: true,
        id: 'marker_' + markerId,
		html : "You can edit the text of this infowindow.<br> Html also possible"
    });
	
	setEditableInfo(marker);
		
    markers[markerId] = marker; // cache marker in markers object
	bindMarkerEvents(marker); // bind rightclick event to marker
});

var bindMarkerEvents = function(marker) {
    google.maps.event.addListener(marker, "rightclick", function (point) {
        var markerId = getMarkerUniqueId(point.latLng.lat(), point.latLng.lng()); // get marker id by using clicked point's coordinate
        var marker = markers[markerId]; // find marker
		removeMarker(marker, markerId); // remove it
	   });

//	google.maps.event.addListener(marker, 'click', function() {
//			console.log(marker.id);
//	});
	   
	   
	   };  
  
var removeMarker = function(marker, markerId) {
    marker.setMap(null); // set markers setMap to null to remove it from map
    delete markers[markerId]; // delete marker instance from markers object
}; 


var setMarker = function(lat, lng, info) {
	var markerId = getMarkerUniqueId(lat, lng); 
   	var marker = new google.maps.Marker({
        position: getLatLng(lat, lng),
        map: map,
		animation:ani,
		icon:ico,
		draggable: true,
        id: 'marker_' + markerId,
		html : info
    });
	setEditableInfo(marker);
	markers[markerId] = marker; 
    bindMarkerEvents(marker); 
}


var setEditableInfo	= function(marker) { 
	
	marker.set("editing", false);
    
    var htmlBox = document.createElement("div");
    htmlBox.innerHTML = marker.html || "";
    htmlBox.style.width = "300px";
    htmlBox.style.height = "100px";
    
    var textBox = document.createElement("textarea");
    textBox.innerText = marker.html || "";
    textBox.style.width = "300px";
    textBox.style.height = "100px";
    textBox.style.display = "none";
    
    var container = document.createElement("div");
    container.style.position = "relative";
    container.appendChild(htmlBox);
    container.appendChild(textBox);
    
    var editBtn = document.createElement("button");
    editBtn.innerText = "Edit";
    container.appendChild(editBtn);
   
    var infoWnd = new google.maps.InfoWindow({
      content : container
    });
     
    google.maps.event.addListener(marker, "click", function() {
      infoWnd.open(marker.getMap(), marker);
    });
    
    google.maps.event.addDomListener(editBtn, "click", function() {
      marker.set("editing", !marker.editing);
    });
    
    google.maps.event.addListener(marker, "editing_changed", function(){
      textBox.style.display = this.editing ? "block" : "none";
      htmlBox.style.display = this.editing ? "none" : "block";
    });
    
    google.maps.event.addDomListener(textBox, "change", function(){
      htmlBox.innerHTML = textBox.value;
      marker.set("html", textBox.value);
    });

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

setMarkers();


/* if many markers auto center and zoom, if one  marker set desiraed zoom*/
function AutoCenter() {
//  Create a new viewpoint bound
var bounds = new google.maps.LatLngBounds();

var count=0;
	$.each(markers, function (index, marker) {
		bounds.extend(marker.position);
		latLng1 = marker.getPosition(); 
		count++;
	});
//  Fit these bounds to the map or-- center and set zoom in case of one marker
	if(count > 1) {
		map.fitBounds(bounds);
	} else {
		if (typeof latLng1 !== "undefined"){
		map.setCenter(latLng1);
		}
		map.setZoom(z);
	}
	
}

AutoCenter();

function updateZoom() {
 document.getElementById('zoom').value = map.getZoom();
}

//zoom change listener
google.maps.event.addListener(map, 'zoom_changed', function() {
    updateZoom();
});


}


google.maps.event.addDomListener(window, 'load', initialize);

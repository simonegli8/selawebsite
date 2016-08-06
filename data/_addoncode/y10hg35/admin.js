
$(document).ready(function() {


  $("button.Gmap_browse_files").on("click", function(e) {

      
    e.preventDefault();
   
	var currentInput = $('#CustomIcon');

      // create a faux CKEDITOR object to handle gpFinder file select callback ;-)   .... -thanks J.
      window.CKEDITOR = {
        tools : { 
          callFunction : function(funcNum,fileUrl) { 
            
            if (fileUrl != "") {
              currentInput.val(fileUrl);
            }
            return true;
          }
        }
      };

      // open new gpFinder popup window
      var new_gpFinder = window.open(gpFinder_url, 'gpFinder', 'menubar=no,width=960,height=512');
      if (window.focus) {
        new_gpFinder.focus();
      }

    });


 $(".GMsave").on("click", function(e) {
	e.preventDefault(); 
	
	var wrapper         = $("#map_data");
		 
	$.each( markers, function( key, value ) {
		var lat = value.getPosition().lat(); 
		var lng = value.getPosition().lng();
		var coord = lat + '_' + lng;
		wrapper.append( '<input type="hidden" id="'+coord+'" name=markers['+coord+'][coords] / value='+coord+'>');
		wrapper.append( '<input type="hidden" id="'+coord+'" name=markers['+coord+'][info] / value="'+value.html+'">');
		// console.log( key + ": " + value.html );
		// console.log( lat + '_' + lng );
		
		
	});
	
	 var form = $(this).parents('form:first');
	 form.submit();
	 
 });



}); // domready end


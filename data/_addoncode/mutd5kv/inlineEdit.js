/* 
######################################################################
JS/jQuery script for gpEasy FlatAdmin 2015 inline editing
Author: J. Krausz
Date: 2015-03-18
Version 1.0
######################################################################
*/

if (typeof(CKEDITOR) != "undefined") {
  CKEDITOR.on('instanceReady', function(e) {
    setTimeout( function() {
      $("#ckeditor_area:not(.docked):has(.cke_toolgroup)").width( $("#ckeditor_area .cke_toolgroup").width() + 10 );
    }, 300);
  });
}
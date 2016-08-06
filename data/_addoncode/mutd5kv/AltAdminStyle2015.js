/* UTF-8! ÄÖÜäöüß
######################################################################
JS/jQuery script for gpEasy FlatAdmin 2015 
Author: J. Krausz
Date: 2015-03-18
Version 1.0
######################################################################
*/

$(document).ready( function() {

  $("#simplepanel ul.submenu>li").each( function() { 
    var href = $(this).find("a").attr("href");
    var url =  window.location.pathname + window.location.search;
    // $(this).attr("data-href",href).attr("data-url",url);
    if (href == url) {
      $(this).addClass("simplepanel_selected_li");
      $(this).closest(".panelgroup").children("a.toplink").addClass("simplepanel_selected_panelgroup");
    }
  });

});
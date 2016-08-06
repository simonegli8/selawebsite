$(function() {
  finder_opts.getFileCallback && !0 === finder_opts.getFileCallback && (finder_opts.getFileCallback = function(a) {
    "object" == typeof a && (a = a.url);
    if ("function" == typeof window.top.opener.gp_editor.FinderSelect) {
      window.top.opener.gp_editor.FinderSelect(a);
    } else {
      var b = window.top.location.search.match(/(?:[?&]|&amp;)CKEditorFuncNum=([^&]+)/i);
      window.top.opener.CKEDITOR.tools.callFunction(b && 1 < b.length ? b[1] : "", a);
    }
    window.top.close();
    window.top.opener.focus();
  });
  var c = $("#finder").finder(finder_opts), d = $(window);
  d.resize(function() {
    var a = c.offset().top, b = d.height();
    parseInt(b - a) != c.height() && c.height(b - a).resize();
  }).resize();
});

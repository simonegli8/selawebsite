$(function() {
  if (document.getElementById("gp_layout_iframe")) {
    var d = $("#gp_iframe_wrap");
    window.setInterval(function() {
      var a = document.getElementById("gp_layout_iframe"), b = a.contentWindow.document.body;
      b && (a = a.contentWindow.document.documentElement, height = Math.max(b.scrollHeight, b.offsetHeight), d.height(height), height = Math.max(b.scrollHeight, b.offsetHeight, a.clientHeight, a.scrollHeight, a.offsetHeight, $gp.$win.height()), d.height(height));
    }, 300);
  }
  var e = Math.min(gpui.thw, $gp.$win.width() - 50);
  $("#gp_iframe_wrap").css("margin-right", e);
  $("#theme_editor").css("width", e);
  $("#theme_editor").resizable({handles:"w", minWidth:172, resize:function(a, b) {
    $("#gp_iframe_wrap").css("margin-right", b.size.width + 1);
    gpui.thw = b.size.width;
    $gp.SaveGPUI();
  }});
  $gp.links.SetPreviewTheme = function() {
    var a = this.href + "&cmd=newlayout";
    $(".add_layout").attr("href", a);
  };
  var c = $("#available_wrap");
  c.length && $gp.$win.resize(function() {
    var a = c.offset().top, b = $gp.$win.height();
    c.css("max-height", b - a);
    console.log(a, b, b - a);
  }).resize();
  (function() {
    var a = $("#gp_layout_css");
    if (a.length) {
      var b = {mode:"text/x-less", lineWrapping:!1};
      "scss" == a.data("mode") && (b.mode = "text/x-scss");
      var c = CodeMirror.fromTextArea(a.get(0), b);
      $(window).resize(function() {
        var b = a.parent();
        c.setSize(225, 100);
        c.setSize(225, b.height() - 5);
      }).resize();
      var d = a.val();
      $gp.inputs.preview_css = function(a) {
        $gp.loading();
      };
      $gp.inputs.reset_css = function(b) {
        a.removeClass("edited");
        d = a.val();
        $gp.loading();
      };
      window.setInterval(function() {
        a.val() != d && a.addClass("edited");
      }, 1E3);
    }
  })();
});

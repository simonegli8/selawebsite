var gplinks = {}, gpinputs = {}, gpresponse = {}, $gp = {inputs:{}, response:{}, error:"There was an error processing the last request. Please reload this page to continue.", jGoTo:function(a, b) {
  $gp.loading();
  a = $gp.jPrep(a);
  $.getJSON(a, function(a, d, e) {
    $gp.Response.call(b, a, d, e);
  });
}, cGoTo:function(a, b) {
  var c = $(a), d = a.search;
  (c = c.data("nonce")) && (d += "&verified=" + encodeURIComponent(c));
  $gp.Cookie("cookie_cmd", encodeURIComponent(d), 1);
  b ? $gp.Reload() : window.location = strip_from(strip_from(a.href, "#"), "?");
}, post:function(a, b) {
  $gp.loading();
  var c = $(a).closest("form"), d = c.serialize() + "&verified=" + encodeURIComponent(post_nonce);
  if ("INPUT" === a.nodeName || "BUTTON" === a.nodeName) {
    d += "&" + encodeURIComponent(a.name) + "=" + encodeURIComponent(a.value);
  }
  b && (d += "&" + b);
  $.post($gp.jPrep(c.attr("action")), d, function(c, d, b) {
    $gp.Response.call(a, c, d, b);
  }, "json");
  return !1;
}, post_link:function(a) {
  $gp.loading();
  var b = $(a), b = strip_to(a.search, "?") + "&gpreq=json&jsoncallback=?&verified=" + encodeURIComponent(b.data("nonce"));
  $.post(strip_from(a.href, "?"), b, function(c, d, b) {
    $gp.Response.call(a, c, d, b);
  }, "json");
}, postC:function(a, b, c, d, e) {
  c = c || $gp.Response;
  d = d || "json";
  "object" === typeof b && (b = $.param(b));
  b += "&verified=" + encodeURIComponent(post_nonce);
  "json" === d && (b += "&gpreq=json&jsoncallback=?");
  $.post(strip_from(a, "?"), b, function(a, d, b) {
    c.call(e, a, d, b);
  }, d);
}, cboxSettings:function(a) {
  a = a || {};
  "object" != typeof colorbox_lang && (colorbox_lang = {});
  return $.extend(colorbox_lang, {opacity:.75, maxWidth:"90%", maxHeight:"90%"}, a);
}, Cookie:function(a, b, c) {
  var d = "";
  c && (d = new Date, d.setTime(d.getTime() + 864E5 * c), d = "; expires=" + d.toGMTString());
  document.cookie = a + "=" + b + d + "; path=/";
}, jPrep:function(a, b) {
  b = "undefined" === typeof b ? "gpreq=json&jsoncallback=?" : b;
  a = strip_from(a, "#");
  -1 === a.indexOf("?") ? a += "?" : a.indexOf("?") !== a.length - 1 && (a += "&");
  return a + b;
}, Response:function(a, b, c) {
  function d(a, c, d) {
    "window" == a && (a = window);
    a = $(a);
    if ("function" == typeof a[c]) {
      a[c](d);
    }
  }
  $(".messages").detach();
  try {
    "undefined" == typeof gp_editing && $gp.CloseAdminBox();
  } catch (g) {
  }
  try {
    $.fn.colorbox.close();
  } catch (g) {
  }
  var e = this;
  $.each(a, function(a, f) {
    if ("function" === typeof $gp.response[f.DO]) {
      $gp.response[f.DO].call(e, f, b, c);
    } else {
      if ("function" === typeof gpresponse[f.DO]) {
        console.log("gpresponse is deprecated as of 3.6"), gpresponse[f.DO].call(e, f, b, c);
      } else {
        switch(f.DO) {
          case "replace":
            d(f.SELECTOR, "replaceWith", f.CONTENT);
            break;
          case "inner":
            d(f.SELECTOR, "html", f.CONTENT);
            break;
          case "admin_box_data":
            $gp.AdminBoxC(f.CONTENT);
            break;
          case "messages":
            $(f.CONTENT).appendTo("body").show().css({top:0});
            break;
          case "reload":
            $gp.Reload();
            break;
          default:
            d(f.SELECTOR, f.DO, f.CONTENT);
        }
      }
    }
  });
  $gp.loaded();
}, loading:function() {
  var a = $("#loading1");
  0 == a.length && (a = $('<div id="loading1"><i class="fa fa-spinner fa-pulse fa-3x"></i></div>').appendTo("body"));
  a.css("zIndex", 99E3).fadeIn();
}, loaded:function() {
  $("#loading1").clearQueue().fadeOut();
}, CopyVals:function(a, b) {
  var c = $(a).find("form").get(0);
  c && $(b).find("input").each(function(a, b) {
    c[b.name] && (c[b.name].value = b.value);
  });
}, Reload:function() {
  typeof req_type && "post" == req_type ? window.location.href = strip_from(window.location.href, "#") : window.location.reload(!0);
}, links:{gallery:function(a, b) {
  a.preventDefault();
  b = "" === b ? this : "a[rel=" + b + "],a." + b;
  $.colorbox.remove();
  $(b).colorbox($gp.cboxSettings({resize:!0, rel:b}));
  $(this).trigger("click.cbox");
}}};
$gp.Cookie("cookie_cmd", "", -1);
$(function() {
  function a(a) {
    return btoa(encodeURIComponent(a).replace(/%([0-9A-F]{2})/g, function(a, c) {
      return String.fromCharCode("0x" + c);
    }));
  }
  var b = $(document);
  $("body").addClass("STCLASS");
  b.ajaxError(function(c, b, e, g) {
    $gp.loaded();
    if ("abort" != b.statusText && "function" !== typeof e.error && "" != g) {
      c = {thrownError:g};
      for (var f = "name message fileName lineNumber columnNumber stack".split(" "), h = 0;h < f.length;h++) {
        g.hasOwnProperty(f[h]) && (c[f[h]] = g[f[h]]);
      }
      g.hasOwnProperty("lineNumber") && (g = g.lineNumber, f = b.responseText.split("\n"), c["Line-" + (g - 1)] = f[g - 2], c["Line-" + g] = f[g - 1], c["Line-" + (g + 1)] = f[g]);
      c.responseStatus = b.status;
      c.statusText = b.statusText;
      c.url = e.url;
      c.type = e.type;
      c.browser = navigator.userAgent;
      c.responseText = b.responseText;
      e.data && (c.ajaxdata = e.data.substr(0, 100));
      window.console && console.log && console.log(c);
      "undefined" !== typeof debugjs && "send" === debugjs && (e.data && (c.data = e.data), c.cmd = "javascript_error", $.ajax({type:"POST", url:"http://www.gpeasy.com/Resources", data:c, success:function() {
      }, error:function() {
      }}));
      "undefined" !== typeof $gp.AdminBoxC && "undefined" != typeof JSON ? (delete c.responseText, b = JSON.stringify(c), b = a(b), b = b.replace(/\=/g, ""), b = b.replace(/\+/g, "-").replace(/\//g, "_"), $gp.AdminBoxC('<div class="inline_box"><h3>Error</h3><p>' + $gp.error + '</p><a href="' + ("http://www.typesettercms.com/index.php/Debug?data=" + b) + '" target="_blank">More Info<?a></div>')) : alert($gp.error);
    }
  });
  b.on("click", "input,button", function(a) {
    var b = $(this);
    $(this.form).filter("[method=post]").filter(":not(:has(input[type=hidden][name=verified]))").append('<input type="hidden" name="verified" value="' + post_nonce + '" />');
    if (!b.hasClass("gpvalidate") || "function" != typeof this.form.checkValidity || this.form.checkValidity()) {
      if (b.hasClass("gpconfirm") && !confirm(this.title)) {
        a.preventDefault();
      } else {
        var e = b.data("cmd");
        e || (e = strip_from(b.attr("class"), " "));
        if ("function" === typeof $gp.inputs[e]) {
          return $gp.inputs[e].call(this, a);
        }
        if ("function" === typeof gpinputs[e]) {
          return console.log("gpinputs is deprecated as of 3.6"), gpinputs[e].call(this, a, a);
        }
        switch(e) {
          case "gppost":
          ;
          case "gpajax":
            return a.preventDefault(), $gp.post(this);
        }
        return !0;
      }
    }
  });
  b.delegate(".expand_child", {mouseenter:function() {
    var a = $(this).addClass("expand");
    a.hasClass("simple_top") && a.addClass("simple_top_hover");
  }, mouseleave:function() {
    $(this).removeClass("expand simple_top_hover");
  }});
  b.on("click", "a", function(a) {
    var b = $(this), e = b.data("cmd"), g = b.data("arg");
    e || (e = b.attr("name"), g = b.attr("rel"));
    if (b.hasClass("gpconfirm") && !confirm(this.title)) {
      a.preventDefault();
    } else {
      if ("function" === typeof $gp.links[e]) {
        return $gp.links[e].call(this, a, g);
      }
      if ("function" === typeof gplinks[e]) {
        return console.log("gplinks is deprecated as of 3.6"), gplinks[e].call(this, g, a);
      }
      switch(e) {
        case "toggle_show":
          $(g).toggle();
          break;
        case "inline_box":
          $gp.CopyVals(g, this);
          $(this).colorbox($gp.cboxSettings({inline:!0, href:g, open:!0}));
          break;
        case "postlink":
          $gp.post_link(this);
          break;
        case "gpajax":
          $gp.jGoTo(this.href, this);
          break;
        case "creq":
          $gp.cGoTo(this, !0);
          break;
        case "cnreq":
          $gp.cGoTo(this, !1);
          break;
        case "close_message":
          b.closest("div").slideUp();
          break;
        default:
          return !0;
      }
      a.preventDefault();
      return !1;
    }
  });
});
function strip_to(a, b) {
  if (!a) {
    return a;
  }
  var c = a.indexOf(b);
  return -1 < c ? a.substr(c + 1) : a;
}
function strip_from(a, b) {
  if (!a) {
    return a;
  }
  var c = a.indexOf(b);
  -1 < c && (a = a.substr(0, c));
  return a;
}
function jPrep(a, b) {
  return $gp.jPrep(a, b);
}
function ajaxResponse(a, b, c) {
  return $gp.Response(a, b, c);
}
function loading() {
  $gp.loading();
}
function loaded() {
  $gp.loaded();
}
;
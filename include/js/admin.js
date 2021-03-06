var gp_editor = !1;
$gp.curr_edit_id = null;
$gp["interface"] = [];
$gp.cached = {};
$gp.defaults = {};
$gp.editors = [];
$gp.Coords = function(a) {
  a.hasClass("inner_size") && (a = a.children(":first"));
  var b = a.offset();
  b.w = a.outerWidth();
  b.h = a.outerHeight();
  return b;
};
$gp.div = function(a, b) {
  var c = $("#" + a);
  0 === c.length && (c = $('<div id="' + a + '" class="' + (b || "") + '"></div>').appendTo("#gp_admin_html"));
  return c;
};
$gp.links.inline_edit_generic = function(a, b) {
  a.preventDefault();
  var c = $gp.AreaId($(this));
  $gp.LoadEditor(this.href, c, b);
  c = $(a.target);
  c.closest(".panel_tabs").length && "undefined" != typeof gp_editing && ("extra" == c.data("mode") ? gp_editing.is_extra_mode = !0 : gp_editing.is_extra_mode = !1, gp_editing.ShowEditor());
};
$gp._loadingEditor = !1;
$gp.LoadEditor = function(a, b, c) {
  b = b || 0;
  b !== $gp.curr_edit_id && ($gp._loadingEditor ? console.log("editor still loading") : ($gp._loadingEditor = !0, "undefined" == typeof gp_editing && ($gp.defaults.links = $gp.Properties($gp.links), $gp.defaults.inputs = $gp.Properties($gp.inputs), $gp.defaults.response = $gp.Properties($gp.response)), $gp.CacheInterface(function() {
    if ("undefined" !== typeof gp_editing) {
      if (gp_editing.RestoreCached(b)) {
        $gp._loadingEditor = !1;
        return;
      }
    } else {
      $gp.LoadStyle("/include/css/inline_edit.css"), $gp.LoadStyle("/include/css/manage_sections.css");
    }
    $gp.curr_edit_id = b;
    var d = $gp.CurrentDiv();
    $gp.DefinedObjects();
    $gp.loading();
    if ("function" === typeof gplinks[c]) {
      gplinks[c].call(this, c, evt), $gp._loadingEditor = !1;
    } else {
      var e = strip_from(a, "#"), e = e + ("&gpreq=json&defined_objects=" + $gp.DefinedObjects());
      "manage_sections" != c && (e += "&cmd=inlineedit&area_id=" + b + "&section=" + d.data("gp-section"));
      $.getScript(e, function(a) {
        "false" === a ? (alert($gp.error), $gp.loaded()) : "function" == typeof gp_editor.wake && gp_editor.wake();
        $gp._loadingEditor = !1;
      });
    }
  })));
};
$gp.Properties = function(a) {
  var b = [], c;
  for (c in a) {
    a.hasOwnProperty(c) && b.push(c);
  }
  return b;
};
$gp.CurrentDiv = function() {
  return $("#ExtraEditArea" + $gp.curr_edit_id);
};
$gp.CacheInterface = function(a) {
  $gp.CurrentDiv().removeClass("gp_edit_current");
  "undefined" == typeof gp_editing ? a.call() : gp_editing.SaveChanges(function() {
    "function" == typeof gp_editor.sleep && gp_editor.sleep();
    $gp["interface"][$gp.curr_edit_id] = $("#ck_area_wrap").children().detach();
    $gp.editors[$gp.curr_edit_id] = gp_editor;
    $gp.cached[$gp.curr_edit_id] = {};
    $gp.CacheObjects("links");
    $gp.CacheObjects("inputs");
    $gp.CacheObjects("response");
    $(".cktabs .ckeditor_control.selected").removeClass("selected");
    a.call();
  });
};
$gp.CacheObjects = function(a, b) {
  var c = $gp[a];
  $gp.cached[$gp.curr_edit_id][a] = {};
  for (var d in c) {
    c.hasOwnProperty(d) && (-1 < $gp.defaults[a].indexOf(d) || ($gp.cached[$gp.curr_edit_id][a][d] = c[d]));
  }
};
$gp.RestoreObjects = function(a, b) {
  var c = $gp.cached[b][a], d;
  for (d in c) {
    c.hasOwnProperty(d) && ($gp[a][d] = c[d]);
  }
};
$gp.defined_objects = [];
$gp.DefinedObjects = function() {
  if ("undefined" == typeof gp_editing) {
    for (var a in window) {
      "object" == typeof window[a] && $gp.defined_objects.push(a);
    }
  }
  var b = [];
  for (a in window) {
    "object" == typeof window[a] && -1 == $gp.defined_objects.indexOf(a) && b.push(a);
  }
  return b.join(",");
};
$gp.links.remote = function(a) {
  a.preventDefault();
  a = $gp.jPrep(this.href, "gpreq=body");
  if (gpRem) {
    var b = window.location.href.split("/"), b = b[0] + "//" + b[2] + gpBase;
    0 < window.location.href.indexOf("index.php") && (b += "/index.php");
    a += "&inUrl=" + encodeURIComponent(b) + "&gpRem=" + encodeURIComponent(gpRem);
  }
  b = $gp.$win.height() - 130;
  $gp.AdminBoxC('<iframe src="' + a + '" style="height:' + b + 'px;" frameborder="0" />', {context:"iframe", width:780});
};
$gp.LoadStyle = function(a, b) {
  var c = req_time || (new Date).getTime();
  a = b ? a : gpBase + a;
  a = a + "?t=" + c;
  $('link[href="' + a + '"]').length || $('<link rel="stylesheet" type="text/css" />').appendTo("head").attr({href:a});
};
$gp.AdminBoxC = function(a, b) {
  $gp.CloseAdminBox();
  if ("" === a) {
    return !1;
  }
  "string" == typeof b ? b = {context:b} : "undefined" == typeof b && (b = {});
  b = $.extend({context:"", width:640}, b);
  var c = b.width, d = Math.round(($gp.$win.width() - c - 40) / 2), e = Math.max($gp.$doc.height(), $("body").outerHeight(!0));
  $gp.div("gp_admin_box1").css({zIndex:11E3, "min-height":e}).stop(!0, !0).fadeTo(0, 0).fadeTo(200, .2);
  $gp.div("gp_admin_box").css({zIndex:"11001", left:d, top:$gp.$win.scrollTop()}).stop(!0, !0).fadeIn(400).html('<a class="gp_admin_box_close" data-cmd="admin_box_close"></a><div id="gp_admin_boxc" class="' + (b.context || "") + '" style="width:' + c + 'px"></div>').find("#gp_admin_boxc").html(a).find("input:visible:first").focus();
  $(".messages").detach();
  $gp.$doc.on("keyup.abox", function(a) {
    27 == a.keyCode && $gp.CloseAdminBox();
  });
  return !0;
};
$gp.CloseAdminBox = function(a) {
  a && a.preventDefault();
  $gp.$doc.off("keyup.abox");
  $("#gp_admin_box1").fadeOut();
  $("#gp_admin_box").fadeOut(300, function() {
    $("#gp_admin_boxc").hasClass("inline") ? $("#gp_admin_boxc").children().appendTo("#gp_hidden") : $("#gp_admin_boxc").children().remove();
  });
  "undefined" !== typeof $.fn.colorbox && $.fn.colorbox.close();
};
$gp.links.admin_box_close = gpinputs.admin_box_close = $gp.CloseAdminBox;
$gp.SaveGPUI = function() {
  if (isadmin) {
    var a = "do=savegpui";
    $.each(gpui, function(b, c) {
      a += "&gpui_" + b + "=" + c;
    });
    $gp.postC(window.location.href, a);
  }
};
$gp.links.dd_menu = function(a) {
  a.preventDefault();
  a.stopPropagation();
  $(".messages").detach();
  var b = this, c = $(this).parent().find(".dd_list");
  c.show();
  if (c.find(".selected").length) {
    a = c.find("ul:first");
    var d = c.find(".selected").prev().prev().prev().position();
    d && a.scrollTop(d.top + a.scrollTop());
  }
  $("body").on("click.gp_select", function(a) {
    c.hide();
    c.off(".gp_select");
    $("body").off(".gp_select");
    $(a.target).closest(b).length && a.stopPropagation();
  });
};
$gp.links.tabs = function(a) {
  a.preventDefault();
  a = $(this);
  a.siblings("a").removeClass("selected").each(function(a, c) {
    c.hash && $(c.hash).hide().find("input[type=submit],button[type=submit]").prop("disabled", !0);
  });
  this.hash && (a.addClass("selected"), $(this.hash).show().find("input[type=submit],button[type=submit]").prop("disabled", !1));
};
$gp.Loaded = {};
$gp.LoadScripts = function(a, b, c) {
  function d() {
    e--;
    0 === e && "function" === typeof b && b.call(this);
  }
  var e = a.length, f = (new Date).getTime(), k = "";
  c && (k = gpBase);
  $.each(a, function(a, b) {
    b = k + b;
    $gp.Loaded[b] ? d() : ($gp.Loaded[b] = !0, $.getScript($gp.jPrep(b, "t=" + f), function() {
      d();
    }));
  });
};
$gp.links.gp_refresh = function(a) {
  a.preventDefault();
  $gp.Reload();
};
$gp.links.toggle_panel = function(a) {
  var b = $("#simplepanel");
  a.preventDefault();
  var c = "";
  b.hasClass("minb") ? (c = "", a = 0) : b.hasClass("compact") ? (c = "minb toggledmin", a = 3) : (c = "compact", a = 1);
  b.hasClass("toggledmin") || b.unbind("mouseenter touchstart").bind("mouseenter touchstart", function(a) {
    b.unbind(a).removeClass("toggledmin");
  });
  b.attr("class", "keep_viewable " + c);
  gpui.cmpct = a;
  $gp.SaveGPUI();
};
$gp.links.toplink = function() {
  var a = $(this), b = $("#simplepanel");
  if (!b.hasClass("compact")) {
    var c = a.next();
    gpui.vis = !(c.is(":visible") && 0 < c.height());
    b.find(".panelgroup2:visible").slideUp(300);
    gpui.vis && (gpui.vis = a.data("arg"), c.slideDown(300));
    $gp.SaveGPUI();
  }
};
$gp.links.collapsible = function() {
  var a = $(this).parent();
  a.hasClass("one") && a.hasClass("gp_collapsed") ? (a.parent().find(".head").addClass("gp_collapsed"), a.parent().find(".collapsearea").slideUp(300), a.removeClass("gp_collapsed").next().slideDown(300)) : a.toggleClass("gp_collapsed").next().slideToggle(300);
};
$gp.links.ajax_box = $gp.links.admin_box = function(a) {
  alert(' "ajax_box" and "admin_box" are deprecated link arguments. Use gpabox instead.');
  a.preventDefault();
  $gp.loading();
  a = $gp.jPrep(this.href, "gpreq=flush");
  $.get(a, "", function(a) {
    $gp.AdminBoxC(a);
    $gp.loaded();
  }, "html");
};
$gp.links.gpabox = function(a) {
  a.preventDefault();
  $gp.loading();
  a = $gp.jPrep(this.href) + "&gpx_content=gpabox";
  $.getJSON(a, $gp.Response);
};
$gp.links.add_table_row = function(a) {
  a = $(this).closest("tr");
  var b = a.closest("tbody").find("tr:first").clone();
  b.find(".class_only").remove();
  b.find("input").val("").attr("value", "");
  a.before(b);
};
$gp.links.rm_table_row = function(a) {
  a = $(this);
  2 > a.closest("tbody").find(".rm_table_row").length || a.closest("tr").remove();
};
$gp.inputs.gpabox = function() {
  return $gp.post(this, "gpx_content=gpabox");
};
$gp.inputs.gpcheck = function() {
  this.checked ? $(this).parent().addClass("checked") : $(this).parent().removeClass("checked");
};
$gp.inputs.check_all = function() {
  $(this).closest("form").find("input[type=checkbox]").prop("checked", this.checked);
};
$gp.inputs.cnreq = function(a) {
  a.preventDefault();
  a = $(this.form).serialize();
  $gp.Cookie("cookie_cmd", encodeURIComponent(a), 1);
  window.location = strip_from(strip_from(this.form.action, "#"), "?");
};
$gp.htmlchars = function(a) {
  a = a || "";
  return $("<a>").text(a).html();
};
$gp.response.location = function(a) {
  window.setTimeout(function() {
    window.location = a.SELECTOR;
  }, a.CONTENT);
};
$gp.AreaId = function(a) {
  var b = a.data("gp-area-id");
  if ("undefined" != typeof b) {
    return parseInt(b);
  }
  b = a.attr("id");
  if ("undefined" != typeof b) {
    return parseInt(a.attr("id").substr(13));
  }
};
$gp.links.expand = function() {
  var a = $(this).siblings("ul");
  "block" != a.css("display") && (a.css("display", "block"), $(document).one("click", function(b) {
    a.css("display", "");
  }));
};
$gp.IndicateDraft = function() {
  $(".gp_extra_edit").removeClass("msg_publish_draft");
  $(".editable_area").each(function() {
    if (1 == $(this).data("draft")) {
      return $(".gp_extra_edit").addClass("msg_publish_draft"), !1;
    }
  });
};
$(function() {
  function a() {
    function a() {
      if (g && !n) {
        var b = g.offset(), c = g.position(), l = $gp.$win.scrollTop(), b = Math.max(0, l - (b.top - c.top));
        g.stop(!0, !0).animate({top:b});
      }
    }
    function b() {
      k && window.clearTimeout(k);
      k = window.setTimeout(function() {
        e();
      }, 200);
    }
    function e() {
      g && g.stop(!0, !0).hide(500, function() {
        f();
      });
      n = !1;
      l.find("div").stop(!0, !0).fadeOut();
    }
    function f() {
      n = !1;
      g.css({left:"auto", top:0, right:0, position:"absolute"}).removeClass("gp_hover").unbind("mouseenter touchstart").one("mouseenter touchstart", function() {
        h.hasClass("gp_no_overlay") || (g.addClass("gp_hover").stop(!0, !0).show(), m.stop(!0, !0).fadeIn());
      });
    }
    var k = !1, l, g = !1, h, m, n = !1;
    l = $gp.div("gp_edit_overlay");
    l.click(function(a) {
      var b = $(a.target);
      0 < b.filter("a").length && (b.hasClass("gp_overlay_close") && a.preventDefault(), h && h.addClass("gp_no_overlay"));
      e();
    }).on("mouseleave touchend", function() {
      b();
    }).on("mouseenter touchstart", function() {
      k && window.clearTimeout(k);
    });
    $(".editable_area").on("mousemove.gp mouseenter.gp touchstart.gp", function(b) {
      k && window.clearTimeout(k);
      var d = $(this);
      if (!d.hasClass("gp_no_overlay") && !d.hasClass("gp_editing")) {
        if (0 < d.parent().closest(".editable_area").length && (b.stopPropagation(), d.trigger("admin:" + b.type)), g && h && d.attr("id") === h.attr("id")) {
          g.show();
        } else {
          h && h.removeClass("gp_no_overlay");
          h = d;
          a: {
            var d = h, e;
            if (e = d.attr("id")) {
              e = e.substr(13);
              b = $("#ExtraEditLnks" + e).children();
              if (0 === b.length && (b = $("#ExtraEditLink" + e), 0 === b.length)) {
                break a;
              }
              d = $gp.Coords(d);
              l.show().css({top:d.top - 3, left:d.left - 2, width:d.w + 6});
              g ? g.stop(!0, !0).show().removeClass("gp_hover") : (g = $("<span>"), m = $("<div>"), l.html(m).append(g));
              m.stop(!0, !0).hide().css({height:d.h + 5, width:d.w + 4});
              a();
              b = b.clone(!0).removeClass("ExtraEditLink");
              g.html('<a class="gp_overlay_expand fa fa-pencil"></a>').append(b);
              f();
            }
          }
        }
      }
    }).on("mouseleave touchend", function() {
      b();
      h && h.removeClass("gp_no_overlay");
    });
    $gp.$win.scroll(function() {
      a();
    });
    $gp.$doc.on("click.gp", ".editable_area, #gp_edit_overlay", function(a) {
      var b;
      b = a.ctrlKey || a.altKey || a.shiftKey ? void 0 : !h || h.hasClass("gp_editing") || h.hasClass("gp_no_overlay") || !g ? void 0 : !0;
      if (b) {
        n = !0;
        b = a.pageX - $gp.$win.scrollLeft();
        var c = b + g.width() - $gp.$win.width();
        a = a.pageY - $gp.$win.scrollTop();
        0 < c && (b -= c);
        g.show().stop(!0, !0).css({top:a, left:b, right:"auto", position:"fixed"});
      }
    });
  }
  function b() {
    SimpleDrag("#simplepanel .toolbar, #simplepanel .toolbar a", "#simplepanel", "fixed", function(a) {
      gpui.tx = a.left;
      gpui.ty = a.top;
      $gp.SaveGPUI();
    }, !0);
    $(".in_window").parent().bind("mouseenter touchstart", function() {
      var a = $(this).children(".in_window").css({right:"auto", left:"100%", top:0});
      window.setTimeout(function() {
        var b = a.offset(), e = b.left + a.width(), b = b.top + a.height();
        e > $gp.$win.width() + $gp.$win.scrollLeft() && a.css({right:"100%", left:"auto"});
        e = $gp.$win.height() + $gp.$win.scrollTop();
        b > e && a.css({top:e + -b - 10});
      }, 1);
    });
  }
  $gp.$win = $(window);
  $gp.$doc = $(document);
  $gp.$doc.on("mousedown", "form", function() {
    var a = $(this);
    "checked" !== a.data("gpForms") && ("undefined" !== typeof this["return"] && (console.log("return"), this["return"].value = window.location), a.data("gpForms", "checked"));
  });
  isadmin && "undefined" === typeof gp_bodyashtml && ($("body").addClass("gpAdmin"), $gp.IndicateDraft(), window.setTimeout(function() {
    a();
    b();
  }, 1), $gp.$doc.on("keyup keypress paste change", ".show_character_count textarea", function() {
    $(this).parent().find(".character_count span").html(this.value.length);
  }), $(document).on("keyup", "input.gpsearch", function() {
    var a = this.value.toLowerCase();
    $(this.form).find(".gp_scrolllist > div > *").each(function() {
      var b = $(this);
      -1 == b.text().toLowerCase().indexOf(a) ? b.addClass("filtered") : b.removeClass("filtered");
    });
  }));
});
function SimpleDrag(a, b, c, d) {
  function e(a, b) {
    if (a.hasClass("keep_viewable")) {
      var c, d = {}, e = a.position();
      if (b) {
        a.data({gp_left:e.left, gp_top:e.top});
      } else {
        if (c = a.data("gp_left")) {
          e.left = d.left = c, e.top = d.top = a.data("gp_top");
        }
      }
      c = a.width();
      var f = $gp.$win.height();
      -10 > e.top ? d.top = -10 : e.top > f && (d.top = f + -20);
      c = $gp.$win.width() - c - -10;
      e.left > c && (d.left = c);
      (d.left || d.top) && a.css(d);
    }
  }
  var f = $(b);
  $gp.$doc.off("mousedown.sdrag", a).on("mousedown.sdrag", a, function(a) {
    function b() {
      var c = f.offset();
      h = a.clientX - c.left + $gp.$win.scrollLeft();
      m = a.clientY - c.top + $gp.$win.scrollTop();
    }
    if (1 == a.which) {
      var e, h, m;
      a.preventDefault();
      if (!(1 > f.length)) {
        return b(), $gp.$doc.bind("mousemove.sdrag", function(a) {
          if (!e) {
            var b = f.offset(), c = f.width(), d = f.height();
            e = $gp.div("admin_drag_box").css({top:b.top, left:b.left, width:c, height:d});
          }
          e.css({left:Math.max(-10, a.clientX - h), top:Math.max(-10, a.clientY - m)});
          a.preventDefault();
          return !1;
        }), $gp.$doc.unbind("mouseup.sdrag").bind("mouseup.sdrag", function(a) {
          var b, l, k;
          $gp.$doc.unbind("mousemove.sdrag mouseup.sdrag");
          if (!e) {
            return !1;
          }
          a.preventDefault();
          e.remove();
          e = !1;
          b = a.clientX - h;
          l = a.clientY - m;
          "absolute" === c && (b += $gp.$win.scrollLeft(), l += $gp.$win.scrollTop());
          b = Math.max(-10, b);
          l = Math.max(-10, l);
          k = {left:b, top:l};
          f.css(k).data({gp_left:b, gp_top:l});
          "function" === typeof d && d.call(f, k, a);
          f.trigger("dragstop");
          return !1;
        }), !1;
      }
    }
  });
  "fixed" !== f.css("position") && "fixed" !== f.parent().css("position") || e(f.addClass("keep_viewable"), !0);
  $gp.$win.resize(function() {
    $(".keep_viewable").each(function() {
      e($(this), !1);
    });
  });
}
$gp.response.renameprep = function() {
  function a() {
    f.val().replace(/ /g, k).toLowerCase() !== e ? $("#gp_rename_redirect").show(500) : $("#gp_rename_redirect").hide(300);
  }
  function b() {
    var b = $("input.title_label").val();
    f.filter(".sync_label").val(c(b));
    $("input.browser_title.sync_label").val(b);
    a();
    return !0;
  }
  function c(a) {
    a = a.replace(/[\x00-\x1F\x7F]/g, "");
    a = a.replace(/(\?|\*|:|#)/g, "");
    a = a.replace(/(<(\/?[a-zA-Z0-9][^<>]*)>)/ig, "");
    a = a.replace(/[\\]/g, "/");
    a = a.replace(/^\.+[\/\/]/, "/");
    a = a.replace(/[\/\/]\.+$/, "/");
    a = a.replace(/[\/\/]\.+[\/\/]/g, "/");
    a = a.replace(/[\/\/]+/g, "/");
    a = "." === a ? "" : a.replace(/^\/\/*/g, "");
    return a.replace(/ /g, k);
  }
  var d = $("#gp_rename_form"), e = $("#old_title").val().toLowerCase(), f = d.find("input.new_title").bind("keyup change", a), k = $("#gp_space_char").val();
  $("input:disabled").each(function(a, b) {
    $(b).fadeTo(400, .6);
  });
  $("input.title_label").bind("keyup change", b).change();
  $gp.links.showmore = function() {
    $("#gp_rename_table tr").show(500);
    $(this).parent().remove();
  };
  $gp.links.ToggleSync = function(a) {
    a = $(this).closest("td");
    var c = a.find("a:visible");
    a.find("a").show();
    c.hide();
    c = a.find("a:visible");
    c.length && (c.hasClass("slug_edit") ? (a.find("input").addClass("sync_label").prop("disabled", !0).fadeTo(400, .6), b()) : a.find("input").removeClass("sync_label").prop("disabled", !1).fadeTo(400, 1));
  };
};

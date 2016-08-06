(function() {
  gp_editing = {is_extra_mode:!1, is_dirty:!1, get_path:function(b) {
    var a = $("a#ExtraEditLink" + b);
    return 0 == a.length ? (console.log("get_path() link not found", b, a.length), !1) : a.attr("href");
  }, get_edit_area:function(b) {
    var a = $("#ExtraEditArea" + b);
    if (0 == a.length) {
      return console.log("no content found for get_edit_area()", b), !1;
    }
    $("#edit_area_overlay_top").hide();
    b = a.find(".twysiwygr:first");
    b.length && (a = b);
    a.addClass("gp_editing gp_edit_current");
    return a;
  }, close_editor:function(b) {
    b.preventDefault();
    $gp.Reload();
  }, SaveChanges:function(b) {
    if (gp_editor) {
      if (gp_editing.IsDirty()) {
        var a = $("#ckeditor_wrap");
        if (!a.hasClass("ck_saving")) {
          a.addClass("ck_saving");
          var c = $gp.CurrentDiv(), d = strip_from(gp_editor.save_path, "#"), e = "", f = gp_editing.GetSaveData();
          0 < d.indexOf("?") && (e = strip_to(d, "?") + "&", d = strip_from(d, "?"));
          e += "cmd=save_inline&section=" + c.data("gp-section") + "&req_time=" + req_time + "&";
          e = e + f + ("&verified=" + encodeURIComponent(post_nonce));
          e += "&gpreq=json&jsoncallback=?";
          gp_editing.SamePath(d) && (e += "&gpreq_toolbar=1");
          $gp.response.ck_saved = function() {
            gp_editing.DraftStatus(c, 1);
            gp_editing.PublishButton(c);
            gp_editor && (gp_editing.GetSaveData() == f && (gp_editor.resetDirty(), gp_editing.is_dirty = !1, gp_editing.DisplayDirty()), "function" == typeof b && b.call());
          };
          $.ajax({type:"POST", url:d, data:e, success:$gp.Response, dataType:"json", complete:function(b, c) {
            a.removeClass("ck_saving");
          }});
        }
      } else {
        "function" == typeof b && b.call();
      }
    }
  }, GetSaveData:function() {
    return "function" == typeof gp_editor.SaveData ? gp_editor.SaveData() : gp_editor.gp_saveData();
  }, PublishButton:function(b) {
    $(".ck_publish").hide();
    b && void 0 != b.data("draft") && (1 == b.data("draft") && $(".ck_publish").show(), $gp.IndicateDraft());
  }, DraftStatus:function(b, a) {
    b && void 0 != b.data("draft") && (b.data("draft", a).attr("data-draft", a), $gp.IndicateDraft());
  }, SamePath:function(b) {
    return $("<a>").attr("href", b).get(0).pathname.replace(/^\/index.php/, "") == window.location.pathname.replace(/^\/index.php/, "") ? !0 : !1;
  }, editor_tools:function() {
    var b = $("#ck_area_wrap");
    if (!b.length) {
      var a;
      a = '<div id="ckeditor_wrap" class="nodisplay"><a id="cktoggle" data-cmd="ToggleEditor"><i class="fa fa-angle-double-left"></i><i class="fa fa-angle-double-right"></i></a><div id="ckeditor_tabs">';
      a += "</div>";
      a += '<div id="ck_area_wrap">';
      a += "</div>";
      a += '<div id="ckeditor_save">';
      a += '<a data-cmd="ck_save" class="ckeditor_control ck_save">' + gplang.Save + "</a>";
      a += '<span class="ck_saved">' + gplang.Saved + "</span>";
      a += '<a data-cmd="Publish" class="ckeditor_control ck_publish">' + gplang.Publish + "</>";
      a += '<span class="ck_saving">' + gplang.Saving + "</span>";
      a += '<a data-cmd="ck_close" class="ckeditor_control">' + gplang.Close + "</a>";
      a += "</div>";
      a += "</div>";
      $("#gp_admin_html").append(a);
      b = $("#ck_area_wrap");
    }
    a = '<div id="ckeditor_area"><div class="toolbar"></div>';
    a += '<div class="tools">';
    a += '<div id="ckeditor_top"></div>';
    a += '<div id="ckeditor_controls"></div>';
    a += '<div id="ckeditor_bottom"></div>';
    a += "</div>";
    a += "</div>";
    b.html(a);
    gp_editing.ShowEditor();
  }, IsExtraMode:function() {
    var b = $gp.CurrentDiv();
    return b.length ? "undefined" == typeof b.data("gp-section") ? gp_editing.is_extra_mode = !0 : gp_editing.is_extra_mode = !1 : gp_editing.is_extra_mode;
  }, ShowEditor:function() {
    var b = $gp.CurrentDiv(), a = $("#ckeditor_wrap").addClass("show_editor");
    $gp.$win.resize();
    var c = $("#ckeditor_tabs").html(""), d = gp_editing.IsExtraMode();
    d ? (a.addClass("edit_mode_extra"), c.append('<a href="?cmd=ManageSections" data-cmd="inline_edit_generic" data-arg="manage_sections">' + gplang.Extra + "</a>")) : (a.removeClass("edit_mode_extra"), c.append('<a href="?cmd=ManageSections" data-cmd="inline_edit_generic" data-arg="manage_sections">' + gplang.Page + "</a>"));
    0 != b.length && (a = gp_editing.SectionLabel(b), $("<a>").text(a).appendTo(c));
    0 == b.length && d ? $("#ckeditor_save").hide() : $("#ckeditor_save").show();
    gp_editing.PublishButton(b);
  }, SectionLabel:function(b) {
    var a = b.data("gp_label");
    a || (b = gp_editing.TypeFromClass(b), a = gp_editing.ucfirst(b));
    return a;
  }, TypeFromClass:function(b) {
    b = $(b);
    var a = b.data("gp_type");
    if (a) {
      return a;
    }
    a = b.prop("class").substring(16);
    return a.substring(0, a.indexOf(" "));
  }, ucfirst:function(b) {
    return b.charAt(0).toUpperCase() + b.slice(1);
  }, CreateTabs:function() {
    var b = $(".inline_edit_area");
    if (b.length) {
      var a = "selected", c = '<div id="cktabs" class="cktabs">';
      b.each(function() {
        c += '<a class="ckeditor_control ' + a + '" data-cmd="SwitchEditArea" data-arg="#' + this.id + '">' + this.title + "</a>";
        a = "";
      });
      c += "</div>";
      $("#ckeditor_area .toolbar").append(c).find("a").mousedown(function(a) {
        a.stopPropagation();
      });
    }
  }, AddTab:function(b, a) {
    var c = $("#" + a);
    c.length ? (c.replaceWith(b), $('#cktabs .ckeditor_control[data-arg="#' + a + '"]').click()) : (c = $(b).appendTo("#ckeditor_top"), $('<a class="ckeditor_control" data-cmd="SwitchEditArea" data-arg="#' + a + '">' + c.attr("title") + "</a>").appendTo("#cktabs").click());
  }, RestoreCached:function(b) {
    if ("object" != typeof $gp["interface"][b]) {
      return !1;
    }
    if ($gp.curr_edit_id === b) {
      return !0;
    }
    $("#ck_area_wrap").html("").append($gp["interface"][b]);
    gp_editor = $gp.editors[b];
    $gp.curr_edit_id = b;
    $gp.RestoreObjects("links", b);
    $gp.RestoreObjects("inputs", b);
    $gp.RestoreObjects("response", b);
    gp_editing.ShowEditor();
    "function" == typeof gp_editor.wake && gp_editor.wake();
    $gp.CurrentDiv().addClass("gp_edit_current");
    return !0;
  }, IsDirty:function() {
    gp_editing.is_dirty = !0;
    return "undefined" == typeof gp_editor.checkDirty || gp_editor.checkDirty() ? !0 : gp_editing.is_dirty = !1;
  }, DisplayDirty:function() {
    gp_editing.is_dirty || gp_editing.IsDirty() ? $("#ckeditor_wrap").addClass("not_saved") : $("#ckeditor_wrap").removeClass("not_saved");
  }, save_changes:function(b) {
    console.log("Please use gp_editing.SaveChanges() instead of gp_editing.save_changes()");
    gp_editing.SaveChanges(b);
  }};
  $gp.links.ck_close = gp_editing.close_editor;
  $gp.links.ck_save = function(b, a) {
    b.preventDefault();
    gp_editing.SaveChanges(function() {
      a && "ck_close" == a && gp_editing.close_editor(b);
    });
  };
  $gp.links.SwitchEditArea = function(b, a) {
    this.href && $gp.links.inline_edit_generic.call(this, b, "manage_sections");
    var c = $(this);
    $(".inline_edit_area").hide();
    $(c.data("arg")).show();
    c.siblings().removeClass("selected");
    c.addClass("selected");
  };
  $(window).on("beforeunload", function() {
    if ("undefined" !== typeof gp_editor.checkDirty && gp_editor.checkDirty()) {
      return "Unsaved changes will be lost.";
    }
  });
  $gp.$doc.on("click", ".editable_area:not(.filetype-wrapper_section)", function(b) {
    var a = $gp.AreaId($(this));
    if (a != $gp.curr_edit_id) {
      b.stopImmediatePropagation();
      b = $("#ExtraEditLink" + a);
      var c = b.data("arg");
      $gp.LoadEditor(b.get(0).href, a, c);
    }
  });
  window.setInterval(function() {
    ("function" != typeof gp_editor.CanAutoSave || gp_editor.CanAutoSave()) && gp_editing.SaveChanges();
  }, 5E3);
  $gp.$doc.on("keyup mouseup", function() {
    window.setTimeout(gp_editing.DisplayDirty, 100);
  });
  $gp.links.ToggleEditor = function() {
    $("#ckeditor_wrap").hasClass("show_editor") ? ($("html").css({"margin-left":0}), $("#ckeditor_wrap").removeClass("show_editor"), $gp.$win.resize()) : gp_editing.ShowEditor();
  };
  $gp.$win.resize(function() {
    var b = $("#ckeditor_area");
    if (b.length) {
      var a = $gp.$win.height(), a = a - b.position().top, a = a - $("#ckeditor_save").outerHeight();
      $("#ckeditor_area").css({"max-height":a});
      $("html").css({"margin-left":0, width:"auto"});
      var b = $gp.$win.width(), c = $gp.CurrentDiv();
      if (c.length) {
        var d = c.offset().left, a = d - 10;
        if (!(0 > a)) {
          var e = b - $("#ckeditor_wrap").outerWidth(!0), c = d + c.outerWidth() - e, c = c + 10;
          0 > c || (a = Math.min(c, a), $("html").css({"margin-left":-a, width:b}));
        }
      }
    }
  }).resize();
  $gp.links.Publish = function() {
    var b = $gp.CurrentDiv(), b = $gp.AreaId(b), a = gp_editing.get_path(b), a = $gp.jPrep(a, "cmd=PublishDraft");
    $(this).data("gp-area-id", b);
    $gp.jGoTo(a, this);
  };
  $gp.response.DraftPublished = function() {
    var b = $(this).hide(), b = $gp.AreaId(b), b = $("#ExtraEditArea" + b);
    gp_editing.DraftStatus(b, 0);
  };
  $(".editable_area").off(".gp");
  $gp.$doc.off("click.gp");
})();

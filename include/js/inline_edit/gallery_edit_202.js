gp_editor = {sortable_area_sel:".gp_gallery", img_name:"gallery", img_rel:"gallery_gallery", edit_links_target:!1, auto_start:!1, make_sortable:!0, edit_div:null, updateCaption:function(e, c) {
}, removeImage:function(e) {
}, removedImage:function(e) {
}, addedImage:function(e) {
}, sortStop:function() {
}, editorLoaded:function() {
}, widthChanged:!1, heightChanged:!1, intervalSpeed:!1, checkDirty:function() {
  return !1;
}, getData:function(e) {
  var c = {images:[], captions:[]};
  gp_editor.edit_div.find(gp_editor.sortable_area_sel).find("li > a").each(function() {
    c.images.push($(this).attr("href"));
  });
  gp_editor.edit_div.find(gp_editor.edit_links_target).find(".caption").each(function() {
    c.captions.push($(this).html());
  });
  e = $("#gp_gallery_options").find("input,select").serialize();
  var g = gp_editor.edit_div.clone();
  g.find("li.holder").remove();
  g.find("ul").enableSelection().removeClass("ui-sortable").removeAttr("unselectable");
  g.find(".gp_nosave").remove();
  g = g.html();
  return $.param(c) + "&" + e + "&gpcontent=" + encodeURIComponent(g);
}};
function gp_init_inline_edit(e, c) {
  function g() {
    function b(a, b, f) {
      a.append('<a data-cmd="' + b + '" class="' + f + '"></a>');
    }
    function f(a) {
      a = $(a).closest(".expand_child").index();
      return gp_editor.edit_div.find(gp_editor.edit_links_target).eq(a);
    }
    h = gp_editor.edit_div.find(gp_editor.sortable_area_sel);
    if (0 == h.length) {
      console.log("sortable area not found", gp_editor.sortable_area_sel);
    } else {
      gp_editor.resetDirty();
      strip_from(m, "?");
      gp_editing.editor_tools();
      var a = '<div id="gp_current_images"></div><a class="ckeditor_control full_width ShowImageSelect" data-cmd="ShowImageSelect"> ' + gplang.SelectImage + '</a><div id="gp_select_wrap"><div id="gp_image_area"></div><div id="gp_upload_queue"></div><div id="gp_folder_options"></div></div>';
      $("#ckeditor_top").html(a);
      $("#ckeditor_wrap").addClass("multiple_images");
      l = $("#gp_current_images");
      r();
      LoadImages(!1, gp_editor);
      a = $('<div id="gp_gallery_options">').appendTo("#ckeditor_area");
      if (gp_editor.heightChanged) {
        $('<div class="half_width">' + gplang.Height + ': <input class="ck_input" type="text" name="height" /></div>').appendTo(a).find("input").val(c.height).on("keyup paste change", gp_editor.heightChanged);
      }
      if (gp_editor.widthChanged) {
        $('<div class="half_width">' + gplang.Width + ': <input class="ck_input" type="text" name="width" /></div>').appendTo(a).find("input").val(c.width).on("keyup paste change", gp_editor.widthChanged);
      }
      gp_editor.auto_start && (gplang.Auto_Start = "Auto Start", $('<div class="half_width">' + gplang.Auto_Start + ': <input class="ck_input" type="checkbox" name="auto_start" value="true" /></div>').appendTo(a).find("input").prop("checked", c.auto_start));
      gp_editor.intervalSpeed && (gplang.Speed = "Speed", $('<div class="half_width">' + gplang.Speed + ': <input class="ck_input" type="text" name="interval_speed" /></div>').appendTo(a).find("input").val(c.interval_speed).on("keyup paste change", gp_editor.intervalSpeed));
      k = $('<span class="gp_gallery_edit gp_floating_area"></span>').appendTo("body").hide();
      b(k, "gp_gallery_caption", "fa fa-pencil");
      b(k, "gp_gallery_rm", "fa fa-remove");
      $(document).delegate("#gp_current_images span", {"mousemove.gp_edit":function() {
        var a = $(this).offset();
        k.show().css({left:a.left, top:a.top});
        d = this;
      }, "mouseleave.gp_edit":function() {
        k.hide();
      }, "mousedown.gp_edit":function() {
        k.hide();
      }});
      $gp.links.gp_gallery_caption = function() {
        d = f(this);
        var a = $(d), a = a.find(".caption").html() || a.find("a:first").attr("title"), a = '<div class="inline_box" id="gp_gallery_caption"><form><h3>' + gplang.cp + '</h3><textarea name="caption" cols="50" rows="3">' + $gp.htmlchars(a) + '</textarea><p><button class="gpsubmit" data-cmd="gp_gallery_update">' + gplang.up + '</button><button class="gpcancel" data-cmd="admin_box_close">' + gplang.ca + "</button></p></form></div>";
        $gp.AdminBoxC(a);
      };
      $gp.links.gp_gallery_rm = function() {
        d = f(this);
        gp_editor.removeImage(d);
        $(d).remove();
        gp_editor.removedImage(gp_editor.edit_div);
        $(this).closest(".expand_child").remove();
      };
      $gp.inputs.gp_gallery_update = function(a) {
        a.preventDefault();
        a = $(this.form).find("textarea").val();
        var b = $(d).find(".caption");
        console.log(a);
        console.log(d);
        console.log(b);
        b.html(a);
        a = b.html();
        $gp.CloseAdminBox();
        gp_editor.updateCaption(d, a);
      };
      $gp.links.ShowImageSelect = function() {
        $(this).toggleClass("gp_display");
        $("#gp_select_wrap").toggleClass("gp_display");
      };
    }
  }
  function r() {
    h.children().each(function() {
      p(this);
    });
    l.sortable({tolerance:"pointer", cursorAt:{left:25, top:25}, stop:function() {
      l.children().each(function() {
        h.append($(this).data("original"));
      });
      gp_editor.sortStop();
    }}).disableSelection();
  }
  function p(b) {
    var f = $(b), a = f.find("img").attr("src");
    a && (a = $("<img>").attr("src", a), a = $("<a>").append(a), b = $('<div class="expand_child"><span><a data-cmd="gp_gallery_caption" class="fa fa-pencil"></a><a data-cmd="gp_gallery_rm" class="fa fa-remove"></a></span></div>').data("original", b).append(a).appendTo(l), f.hasClass("gp_to_remove") && b.addClass("gp_to_remove"));
  }
  function n(b, f) {
    gp_editor.edit_div.find(".gp_to_remove").remove();
    l.find(".gp_to_remove").remove();
    b.attr({"data-cmd":gp_editor.img_name, "data-arg":gp_editor.img_rel, title:"", "class":gp_editor.img_rel});
    var a = $("<li>").append(b).append('<div class="caption"></div>');
    f ? f.replaceWith(a) : h.append(a);
    a.trigger("gp_gallery_add");
    gp_editor.addedImage(a);
    p(a);
  }
  function t(b) {
    b.attr("action");
    b.find(".file").auto_upload({start:function(b, a) {
      a.bar = $('<a data-cmd="gp_file_uploading">' + b + "</a>").appendTo("#gp_upload_queue");
      a.holder = $('<li class="holder" style="display:none"></li>').appendTo(h);
      return !0;
    }, progress:function(b, a, e) {
      b = Math.round(100 * b);
      b = Math.min(98, b - 1);
      e.bar.text(b + "% " + a);
    }, finish:function(b, a, e) {
      var c = e.bar;
      c.text("100% " + a);
      var d = $(b);
      b = d.find(".status").val();
      d = d.find(".message").val();
      "success" == b ? (c.addClass("success"), c.slideUp(1200), a = $("#gp_gallery_avail_imgs"), a = $(d).appendTo(a).find("a[name=gp_gallery_add],a[data-cmd=gp_gallery_add]"), n(a.clone(), e.holder)) : "notimage" == b ? c.addClass("success") : (c.addClass("failed"), c.text(a + ": " + d));
    }, error:function(b, a, c) {
      alert("error: " + c);
    }});
  }
  $gp.LoadStyle("/include/css/inline_image.css");
  "undefined" !== typeof gp_gallery_options && $.extend(gp_editor, gp_gallery_options);
  gp_editor.edit_links_target || (gp_editor.edit_links_target = gp_editor.sortable_area_sel + " > li");
  var h, l, k = !1, d = !1, m = gp_editing.get_path(e);
  gp_editor.edit_div = gp_editing.get_edit_area(e);
  if (0 != gp_editor.edit_div && 0 != m) {
    gp_editor.save_path = m;
    gp_editor.checkDirty = function() {
      var b = gp_editor.getData(gp_editor.edit_div);
      return q !== b ? !0 : !1;
    };
    gp_editor.SaveData = function() {
      return gp_editor.getData(gp_editor.edit_div, gp_editor);
    };
    gp_editor.resetDirty = function() {
      q = gp_editor.getData(gp_editor.edit_div);
    };
    g();
    var q = gp_editor.getData(gp_editor.edit_div);
    gp_editor.editorLoaded();
    $gp.links.gp_gallery_add = function(b) {
      b.preventDefault();
      b = $(this).stop(!0, !0);
      n(b.clone());
      b.parent().fadeTo(100, .2).fadeTo(2E3, 1);
    };
    $gp.links.gp_gallery_add_all = function(b) {
      b.preventDefault();
      $("#gp_gallery_avail_imgs").find("a[name=gp_gallery_add],a[data-cmd=gp_gallery_add]").each(function(b, a) {
        n($(this).clone());
      });
    };
    $gp.response.gp_gallery_images = function(b) {
      t($("#gp_upload_form"));
    };
    $gp.links.gp_file_uploading = function() {
      var b = $(this), c = !1;
      b.hasClass("failed") ? c = !0 : b.hasClass("success") && (c = !0);
      c && b.slideUp(700);
    };
  }
}
;
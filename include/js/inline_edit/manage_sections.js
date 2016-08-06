(function() {
  function n(b, a) {
    b = b.split(" ");
    var c = "", d = "";
    if (1 < b.length) {
      for (var c = c + "<select>", e = 0;e < b.length;e++) {
        var f = "";
        0 <= a.indexOf(b[e]) && (d = "checked", f = "selected");
        c += '<option value="' + b[e] + '" ' + f + ">" + b[e] + "</option>";
      }
      c += '</select><span class="gpcaret"></span>';
    } else {
      0 <= a.indexOf(b[0]) && (d = "checked"), c += "<span>" + b[0] + "</span>";
    }
    c = '<label class="gpcheckbox"><input class="gpcheck" type="checkbox" data-cmd="ClassChecked" ' + d + "/>" + c;
    return c += "</label>";
  }
  function k(b, a) {
    var c = $('#section_attributes_form td input.attr_name[value="class"]').closest("tr").find("input.attr_value"), d = c.val(), d = $("<div/>").addClass(d);
    "add" == a ? d.addClass(b) : d.removeClass(b);
    c.val(d.attr("class"));
    d.remove();
  }
  function l(b) {
    m(b);
    b.find(".editable_area").each(function() {
      m($(this));
    });
  }
  function m(b) {
    var a = 1, c;
    do {
      a++, c = "ExtraEditArea" + a;
    } while (document.getElementById(c) || document.getElementById("ExtraEditLink" + a));
    b.attr("id", c).data("gp-area-id", a);
    $('<a href="?" class="nodisplay" data-cmd="inline_edit_generic" data-gp-area-id="' + a + '" id="ExtraEditLink' + a + '">').appendTo("#gp_admin_html");
  }
  function p() {
    var b = $("#ck_editable_areas ul").html(""), a = $gp.div("gp_edit_box");
    $("a.ExtraEditLink").clone(!1).attr("class", "").show().each(function() {
      var c = $(this), d = $gp.AreaId(c), e = $("#ExtraEditArea" + d);
      if (e.hasClass("gp_no_overlay") || 0 === e.length || "undefined" != typeof e.data("gp-section")) {
        return !0;
      }
      var f = $gp.Coords(e), g = this.title.replace(/_/g, " "), g = decodeURIComponent(g);
      15 < g.length && (g = g.substr(0, 14));
      c.attr("id", "editable_mark" + d).html('<i class="fa fa-pencil"></i> ' + g).on("mouseenter touchstart", function() {
        var b = $gp.Coords(e);
        a.stop(!0, !0).css({top:b.top - 3, left:b.left - 2, width:b.w + 4, height:b.h + 5}).fadeIn();
        ($gp.$win.scrollTop() > b.top || $gp.$win.scrollTop() + $gp.$win.height() < b.top) && $("html,body").stop(!0, !0).animate({scrollTop:Math.max(0, b.top - 100)}, "slow");
      }).on("mouseleave touchend click", function() {
        a.stop(!0, !0).fadeOut();
      });
      c = $("<li>").append(c).data("top", f.top).appendTo(b);
      e.data("draft") && (f = $gp.jPrep(this.href, "cmd=PublishDraft"), $('<a class="draft" data-cmd="gpajax" data-gp-area-id="' + d + '">' + gplang.Draft + "</a>").attr("href", f).appendTo(c));
    });
    b.find("li").sort(function(a, b) {
      var e = $(a).data("top"), f = $(b).data("top");
      return e < f ? -1 : e > f ? 1 : 0;
    }).appendTo(b);
  }
  var h = null;
  gp_editor = {save_path:"?", saved_data:"", checkDirty:function() {
    var b = this.SaveData();
    return this.saved_data != b ? !0 : !1;
  }, SaveData:function() {
    var b = {section_order:[], attributes:[], contains_sections:[], gp_label:[], gp_color:[], gp_collapse:[], cmd:"SaveSections"};
    $("#gpx_content.gp_page_display").find(".editable_area").each(function(a) {
      var c = $(this), d = gp_editing.TypeFromClass(this), e = c.data("gp-section");
      d && ("undefined" == typeof e && (e = d), b.section_order.push(e), b.attributes[a] = c.data("gp-attrs"), "wrapper_section" == d && (b.contains_sections[a] = c.children(".editable_area").length), b.gp_label[a] = c.data("gp_label"), b.gp_color[a] = c.data("gp_color"), b.gp_collapse[a] = c.data("gp_collapse"));
    });
    return $.param(b);
  }, resetDirty:function() {
    gp_editor.SectionNumbers();
    this.saved_data = this.SaveData();
  }, SectionNumbers:function() {
    $("#gpx_content.gp_page_display").find(".editable_area").each(function(b) {
      var a = $(this);
      a.data("gp-section", b).attr("data-gp-section", b);
      var a = $gp.AreaId(a), c = $("#ExtraEditLink" + a).attr("href") || "", c = c.replace(/section\=[0-9]+/, ""), c = $gp.jPrep(c, "section=" + b);
      $("#ExtraEditLink" + a).attr("href", c);
    });
  }, InitEditor:function() {
    $("#ckeditor_top").append(section_types);
    this.InitSorting();
    this.resetDirty();
    $gp.$win.on("resize", this.MaxHeight).resize();
    $("#ckeditor_area").on("dragstop", this.MaxHeight);
    $(document).trigger("section_sorting:loaded");
  }, wake:function() {
    p();
  }, InitSorting:function() {
    var b = this, a = $("#section_sorting").html(""), c = this.BuildSortHtml($("#gpx_content.gp_page_display"));
    a.html(c);
    $(".section_drag_area").sortable({tolerance:"pointer", stop:function(a, c) {
      b.DragStop(a, c);
    }, connectWith:".section_drag_area", cursorAt:{left:7, top:7}}).disableSelection();
    this.HoverListener(a);
  }, BuildSortHtml:function(b) {
    var a = "", c = this;
    b.children(".editable_area").each(function() {
      var b = $(this);
      this.id || (this.id = c.GenerateId());
      var e = gp_editing.TypeFromClass(this), f = gp_editing.SectionLabel(b), g = b.data("gp_color") || "#aabbcc", q = ' class="' + (b.data("gp_collapse") || "") + '"', r = b.data("gp-attrs")["class"] || f;
      a += '<li data-gp-area-id="' + $gp.AreaId(b) + '" ' + q + ' title="' + r + '">';
      a += '<div><a class="color_handle" data-cmd="SectionColor" style="background-color:' + g + '"></a>';
      a += '<span class="options">';
      b.hasClass("filetype-wrapper_section") || (a += '<a class="fa fa-pencil" data-cmd="SectionEdit" title="Edit"></a>');
      a += '<a class="fa fa-sliders" data-cmd="SectionOptions" title="Options"></a>';
      a += '<a class="fa fa-files-o" data-cmd="CopySection" title="Copy"></a>';
      a += '<a class="fa fa-trash RemoveSection" data-cmd="RemoveSection" title="Remove"></a>';
      a += "</span>";
      a += '<i class="section_label_wrap">';
      "wrapper_section" == e && (a += '<a data-cmd="WrapperToggle" class="secsort_wrapper_toggle"/>');
      a += '<span class="section_label">' + f + "</span>";
      a += "</i>";
      a += "</div>";
      b.hasClass("filetype-wrapper_section") && (a += '<ul class="section_drag_area">', a += c.BuildSortHtml(b), a += "</ul>");
      a += "</li>";
    });
    return a;
  }, GenerateId:function() {
    var b;
    do {
      b = String.fromCharCode(65 + Math.floor(26 * Math.random())) + Date.now();
    } while (document.getElementById(b));
    return b;
  }, DragStop:function(b, a) {
    var c = this.GetArea(a.item), d = this.GetArea(a.item.prev());
    d.length ? c.insertAfter(d).trigger("SectionSorted") : (d = a.item.parent().closest("ul"), "section_sorting" == d.attr("id") ? c.prependTo("#gpx_content").trigger("SectionSorted") : (this.GetArea(d.parent()).prepend(c), c.trigger("SectionSorted")));
  }, HoverListener:function(b) {
    var a = this;
    b.find("div").hover(function() {
      var b = $(this).parent();
      $(".section-item-hover").removeClass("section-item-hover");
      b.addClass("section-item-hover");
      $(".section-highlight").removeClass("section-highlight");
      a.GetArea(b).addClass("section-highlight");
    }, function() {
      var b = $(this).parent();
      a.GetArea(b).removeClass("section-highlight");
      b.removeClass("section-item-hover");
    });
  }, GetArea:function(b) {
    b = $gp.AreaId(b);
    return $("#ExtraEditArea" + b);
  }};
  $(document).on("mousemove", ".preview_section", function() {
    var b = $(this);
    h && clearTimeout(h);
    b.hasClass("previewing") || ($(".previewing").removeClass("previewing"), $(".temporary-section").stop().slideUp(function() {
      $(this).remove();
    }), h = setTimeout(function() {
      var a = $("#gpx_content .editable_area:last"), a = a.offset().top + a.height() - 200;
      $("html,body").stop().animate({scrollTop:a});
      b.addClass("previewing");
      a = $(b.data("response"));
      a.find(".editable_area").addClass("temporary-section").removeClass("editable_area");
      a.addClass("temporary-section").removeClass("editable_area").appendTo("#gpx_content").hide().delay(300).slideDown().trigger("PreviewAdded");
      a = a.get(0);
      b.data("preview-section", a);
    }, 200));
  }).on("mouseleave", ".preview_section", function() {
    h && clearTimeout(h);
    $(this).removeClass("previewing");
    $(".temporary-section").stop().slideUp(function() {
      $(this).parent().trigger("PreviewRemoved");
      $(this).remove();
    });
  });
  $gp.links.AddSection = function(b) {
    var a = $(this);
    b.preventDefault();
    a.removeClass("previewing");
    $(".temporary-section").remove();
    b = $(a.data("response")).appendTo("#gpx_content");
    l(b);
    b.trigger("SectionAdded");
    gp_editor.InitSorting();
    a.removeClass("previewing").trigger("mousemove");
  };
  $gp.links.RemoveSection = function(b) {
    if (1 < $("#gpx_content").find(".editable_area").length) {
      b = $(this).closest("li");
      var a = gp_editor.GetArea(b);
      a.parent().trigger("SectionRemoved");
      a.remove();
      b.remove();
    }
  };
  $gp.links.CopySection = function(b) {
    b = gp_editor.GetArea($(this).closest("li"));
    var a = b.clone();
    l(a);
    b.after(a);
    a.trigger("SectionAdded");
    gp_editor.InitSorting();
  };
  $gp.links.SectionColor = function(b) {
    var a = $(this).closest("li");
    b = "#1192D6 #3E5DE8 #8D3EE8 #C41FDD #ED2F94 #ED4B1E #FF8C19 #FFD419 #C5E817 #5AC92A #0DA570 #017C7C #DDDDDD #888888 #555555 #000000".split(" ");
    for (var c = '<span class="secsort_color_swatches">', d = 0;d < b.length;d++) {
      c += '<a style="background:' + b[d] + ';" data-color="' + b[d] + '"\tdata-cmd="SelectColor"/>';
    }
    a.children("div").hide();
    var e = $(c + "</span>").prependTo(a);
    $(document).one("click", function() {
      e.remove();
      a.children().show();
    });
  };
  $gp.links.SelectColor = function(b) {
    var a = $(this);
    b = a.closest("li");
    var c = gp_editor.GetArea(b), a = a.attr("data-color");
    b.find(".color_handle:first").css("background-color", a);
    c.attr("data-gp_color", a).data("gp_color", a);
    b.find(".secsort_color_swatches").remove();
    b.children().show();
  };
  $gp.links.WrapperToggle = function(b) {
    b = $(this).closest("li");
    var a = "wrapper_collapsed", c = gp_editor.GetArea(b);
    b.hasClass(a) ? (b.removeClass(a), a = "") : b.addClass(a);
    c.attr("data-gp_collapse", a).data("gp_collapse", a);
  };
  $gp.links.SectionEdit = function(b) {
    var a = $(this).closest("li");
    b = gp_editor.GetArea(a);
    var a = $gp.AreaId(a), c = $("#ExtraEditLink" + a), d = c.data("arg");
    $gp.LoadEditor(c.get(0).href, a, d);
    a = b.offset().top;
    b = a + b.height();
    c = $gp.$win.scrollTop();
    d = c + $gp.$win.height();
    b > c && a < d || $("html,body").stop().animate({scrollTop:a - 200});
  };
  $gp.links.SectionOptions = function(b) {
    var a = $(this).closest("li");
    b = a.data("gp-area-id");
    var a = gp_editor.GetArea(a).data("gp-attrs"), c = "";
    html = '<div class="inline_box"><form id="section_attributes_form" data-gp-area-id="' + b + '">';
    html += "<h2>Section Attributes</h2>";
    html += '<table class="bordered full_width">';
    html += "<thead><tr><th>Attribute</th><th>Value</th></tr></thead><tbody>";
    $.each(a, function(a) {
      a = a.toLowerCase();
      if ("id" != a && "data-gp" != a.substr(0, 7)) {
        var b = $.trim(this);
        if ("" != b || "class" == a) {
          "class" == a && (c = b.split(" ")), html += "<tr><td>", html += '<input class="gpinput attr_name" value="' + $gp.htmlchars(a) + '" size="8" />', html += '</td><td style="white-space:nowrap">', html += '<input class="gpinput attr_value" value="' + $gp.htmlchars(b) + '" size="40" />', "class" == a && (html += '<div class="class_only admin_note">Default: GPAREA filetype-*</div>'), html += "</td></tr>";
        }
      }
    });
    html += '<tr><td colspan="3">';
    html += '<a data-cmd="add_table_row">Add Attribute</a>';
    html += "</td></tr>";
    html += "</tbody></table>";
    html += "<br/>";
    html += '<div id="gp_avail_classes">';
    html += '<table class="bordered full_width">';
    html += '<thead><tr><th colspan="2">Available Classes</th></tr></thead>';
    html += "<tbody>";
    for (b = 0;b < gp_avail_classes.length;b++) {
      html += "<tr><td>", html += n(gp_avail_classes[b].names, c), html += '</td><td class="sm text-muted">', html += gp_avail_classes[b].desc, html += "</td></tr>";
    }
    html += "</table>";
    html += "</tbody>";
    html += "</div>";
    html += "<p>";
    html += '<input type="button" name="" value="' + gplang.up + '" class="gpsubmit" data-cmd="UpdateAttrs" /> ';
    html += '<input type="button" name="" value="' + gplang.ca + '" class="gpcancel" data-cmd="admin_box_close" />';
    html += "</p>";
    html += "</form></div>";
    b = $(html);
    b.find("select").on("change input", function() {
      var a = $(this).closest("label").find(".gpcheck");
      a.prop("checked", !0);
      $gp.inputs.ClassChecked.apply(a);
    });
    $gp.AdminBoxC(b);
    $(document).trigger("section_options:loaded");
  };
  $gp.inputs.ClassChecked = function() {
    var b = $(this), a = b.prop("checked") ? "add" : "remove", c = b.siblings("select"), d = "";
    0 == c.length ? (d = b.siblings("span").text(), k(d, a)) : (d = [], c.find("option").each(function() {
      d.push(this.value);
    }), d = d.join(" "), k(d, "remove"), "add" == a && (d = c.val(), k(d, "add")));
  };
  $gp.inputs.UpdateAttrs = function() {
    var b = $("#section_attributes_form"), a = gp_editor.GetArea(b), c = a.data("gp-attrs"), d = {}, e = "", f = $("<div>"), g = "";
    $.each(c, function(b) {
      "class" != b && (d[b] = "", a.attr(b, ""));
    });
    b.find("tbody tr").each(function() {
      var b = $(this), c = b.find(".attr_name").val();
      (c = $.trim(c).toLowerCase()) && "id" != c && "data-gp" != c.substr(0, 7) && (b = b.find(".attr_value").val(), "class" == c ? e = b : (d[c] = b, a.attr(c, b)));
    });
    b = a.attr("class") || "";
    f.attr("class", b);
    f.removeClass(c["class"]);
    f.addClass(e);
    a.attr("class", f.attr("class"));
    d["class"] = e;
    c = $gp.AreaId(a);
    c = $("#section_sorting li[data-gp-area-id=" + c + "]");
    "" == g && (g = c.find("> div .section_label").text());
    c.attr("title", g);
    a.data("gp-attrs", d);
    $gp.CloseAdminBox();
  };
  $(document).on("dblclick", ".section_label", function() {
    var b = $(this), a = b.closest("div");
    a.hide();
    var c = $('<input type="text" value="' + b.text() + '"/>').insertAfter(a).focus().select().on("keydown blur", function(d) {
      if ("blur" == d.type || 13 === d.which || 27 === d.which) {
        a.show();
        var e = c.val();
        c.remove();
        27 !== d.which && b.text() !== e && (b.text(e), d = a.closest("li"), gp_editor.GetArea(d).attr("data-gp_label", e).data("gp_label", e));
      }
    });
  });
  gp_editing.editor_tools();
  gp_editor.InitEditor();
  loaded();
})();

$(document).ready
( function()
  { init_reference_select();
    init_tinymce();
    init_derivations();
    init_reference();
  }
);

var last_document_id = 1;

function select_groupe(id_groupe)
{ var content =
    "<label for=\"album\">album</label>"
  + "<p>"
  + "<select name=\"album\" id=\"album\">"
  + "<option value=\"0\" selected=\"selected\">hors album</option>";
  for(var id_album in albums["" + id_groupe])
  { content +=
      "<option value=\"" + id_album + "\">" + albums["" + id_groupe]["" + id_album] + "</option>";
  }
  content +=
    "</select>"
  + "</p>";
  $("#album_select").html(content);
}

function select_morceaux_groupe(id_groupe)
{ var content =
    "<label for=\"morceau\">morceau</label>"
  + "<p>"
  + "<select name=\"morceau\" id=\"morceau\">"
  + "<option value=\"0\" selected=\"selected\">hors morceau</option>";
  for(var id_album in morceaux["" + id_groupe])
  { for(var id_morceau in morceaux["" + id_groupe]["" + id_album])
    { content +=
        "<option value=\"" + id_morceau + "\">" + morceaux["" + id_groupe]["" + id_album]["" + id_morceau] + "</option>";
    }
  }
  content +=
    "</select>"
  + "</p>";
  $("#morceau_select").html(content);
}

function init_reference_select()
{ $("#is_derivation").click
  ( function()
    { if($(this).get(0).checked) $("#derivation_input").slideDown(200);
      else $("#derivation_input").slideUp(200);
    }
  );
  $("#is_reference").click
  ( function()
    { if($(this).get(0).checked)
      { $("#original_form").slideUp(200);
        $("#reference_form").slideDown(200);
      }
      else 
      { $("#reference_form").slideUp(200);
        $("#original_form").slideDown(200);
      }
    }
  );
}

function add_document()
{ last_document_id++;
  var id_document = last_document_id;
  var content = "<div class=\"document\" id=\"document_" + id_document + "\">\n"
              + "  <div class=\"delete\"><a href=\"#\" onclick=\"del_document('" + id_document + "'); return false;\">Enlever ce fichier</a></div>\n"
              + "  <label for=\"document_nom_" + id_document + "\">nom</label>\n"
              + "  <input type=\"text\" class=\"long_text\" name=\"document_nom_" + id_document + "\" id=\"document_nom_" + id_document + "\" value=\"\" />\n"
              + "  <div class=\"clear\"><!-- --></div>\n"
              + "  <label for=\"document_url_" + id_document + "\">url</label>\n"
              + "  <input type=\"text\" size=\"48\" name=\"document_url_" + id_document + "\" id=\"document_url_" + id_document + "\" value=\"\" />\n"
              + "</div>\n";
  $("#documents").append(content);
}

function del_document(id_document)
{ $("#document_" + id_document).remove();
}

function init_tinymce()
{ $(".tinymce").each
  ( function()
    { tinyMCE.execCommand("mceAddControl", true, $(this).attr("id"));
    }
  );
}

function init_toggle_edit_derivation_content(id_derivation)
{ $("#derivation_edit_" + id_derivation + " .use_edit_derivation_content").click
  ( function()
    { var id_derivation_content = $(this).attr("id");
      if(id_derivation_content.length > 28)
      { id_derivation_content = id_derivation_content.substr(28);
        if($(this).get(0).checked)
        { $("#derivation_edit_" + id_derivation_content + " span").slideDown(200);
        }
        else
        { $("#derivation_edit_" + id_derivation_content + " span").slideUp(200);
        }
      }
    }
  );
}

function init_derivations()
{ if(typeof(derivations) != "undefined")
  { for(var id_derivation in derivations)
    { init_toggle_edit_derivation_content(id_derivation);
      $("#derivation_infos_" + id_derivation).slideDown(200);
    }
  }
}

function init_toggle_edit_reference_content()
{ $("#reference_edit .use_edit_reference_content").click
  ( function()
    { if($(this).get(0).checked)
      { $("#reference_edit span").slideDown(200);
      }
      else
      { $("#reference_edit span").slideUp(200);
      }
    }
  );
}

function init_reference()
{ init_toggle_edit_reference_content();
  if(typeof(is_reference) != "undefined")
  { if(is_reference) $("#reference_form").slideDown(200);
  }
}

function add_derivation(id_source, id_source_derivation)
{ if(!id_source)
  { index_derivation++;
    id_source_derivation = index_derivation;
  }
  $.ajax
  ( { url: site_url + "index.php?e=content/sources/xml_form&id=" + id_source + "&derivation=" + id_source_derivation + "&form=" + (id_source ? "edit" : "add"),
      dataType: "html",
      success: function(content)
      { $("#derivations_items").append(content);
        init_toggle_edit_derivation_content(id_source_derivation);
        $("#derivation_infos_" + id_source_derivation).slideDown(200);
      }
    }
  );
}

function del_sml_xf(id_xf)
{ $("#" + id_xf).slideUp(200, function () { $(this).remove(); });
}

$(document).ready
( function()
  { init_contact_form_ckeckbox();
  }
);

function select_groupe(id_groupe)
{ var content =
    "<label for=\"album\">album</label>"
  + "<select name=\"album\" id=\"album\">"
  + "<option value=\"0\" selected=\"selected\">hors album</option>";
  for(var id_album in albums["" + id_groupe])
  { content +=
      "<option value=\"" + id_album + "\">" + albums["" + id_groupe]["" + id_album] + "</option>";
  }
  content +=
    "</select>";
  $("#album_select").html(content);
}

function init_contact_form_ckeckbox()
{ $("#contact_form").click
  ( function()
    { if($(this).get(0).checked) $("#email_li").slideDown(200);
      else $("#email_li").slideUp(200);
    }
  );
}
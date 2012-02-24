$(document).ready
( function()
  { init_contact_form_ckeckbox();
  }
);

function select_start_action(start_action)
{ if(start_action == "sources/groupe")
  { $("#groupe_param").css("display", "none");
    $("#album_param").css("display", "none");
    return;
  }
  if(start_action == "sources/groupe/view")
  { $("#groupe_param").css("display", "inline");
    $("#album_param").css("display", "none");
    return;
  }
  if(start_action == "sources/album/view")
  { $("#groupe_param").css("display", "none");
    $("#album_param").css("display", "inline");
    return;
  }
}

function init_contact_form_ckeckbox()
{ $("#contact_form").click
  ( function()
    { if($(this).get(0).checked) $("#email_li").slideDown(200);
      else $("#email_li").slideUp(200);
    }
  );
}

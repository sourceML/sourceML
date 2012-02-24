$(document).ready
( function()
  { init_cache_radios();
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

function init_cache_radios()
{ $("#cache_actif_oui").click
  ( function()
    { $("#li_cache_maj_auto").slideDown(200);
      if($("#cache_maj_auto_oui").get(0).checked) $("#li_cache_time").slideDown(200);
    }
  );
  $("#cache_actif_non").click
  ( function()
    { $("#li_cache_maj_auto").slideUp(200);
      $("#li_cache_time").slideUp(200);
    }
  );
  $("#cache_maj_auto_oui").click
  ( function()
    { $("#li_cache_time").slideDown(200);
    }
  );
  $("#cache_maj_auto_non").click
  ( function()
    { $("#li_cache_time").slideUp(200);
    }
  );
}

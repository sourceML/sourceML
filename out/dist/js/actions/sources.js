  var loaded = false;

  $(document).ready
  ( function()
    { loaded = true;
      init_show_xml_links();
    }
  );

  var show_xml_links = {};

  var tracks = {};
  var current_track = false;
  var has_started = false;
  var autoplay_next = false;

  var player_listener = new Object();

  player_listener.onInit = function() { this.position = 0; };

  player_listener.onUpdate = function()
  { if(current_track != false)
    { var loaded = Math.round((100 * this.bytesLoaded) / this.bytesTotal);
      var position = Math.round((100 * this.position) / this.duration);
      $("#track_" + current_track).find(".loaded").get(0).style.width = loaded + "%";
      $("#track_" + current_track).find(".position").get(0).style.width = position + "%";
      if(this.position > 0) has_started = true;
      if(this.position == 0 && has_started)
      { _stop();
        var FOUND = false;
        if(autoplay_next)
        { for(var track in tracks)
          { if(FOUND)
            { play(track);
              break;
            }
            else
            { if(track == current_track) FOUND = true;
            }
          }
        }
        if(!FOUND) current_track = false;
      }
    }
  };

  function get_player() { return $("#player").get(0); }

  function play_all()
  { autoplay_next = true;
    for(var track in tracks)
    { play(track);
      break;
    }
  }

  function play(track)
  { if(current_track != track)
    { var save_autoplay_next = autoplay_next;
      stop();
      autoplay_next = save_autoplay_next;
      current_track = track;
      get_player().SetVariable("method:setUrl", tracks[track]);
    }
    has_started = false;
    get_player().SetVariable("method:play", "");
    get_player().SetVariable("enabled", "true");
    $("#track_" + track).find(".play").get(0).style.display = "none";
    $("#track_" + track).find(".play").get(0).blur();
    $("#track_" + track).find(".pause").get(0).style.display = "inline";
    $("#track_" + track).find(".stop").get(0).style.display = "inline";
    $("#track_" + track).get(0).className = "playing_track";
  }

  function pause()
  { if(current_track != false)
    { get_player().SetVariable("method:pause", "");
      $("#track_" + current_track).find(".pause").get(0).style.display = "none";
      $("#track_" + current_track).find(".pause").get(0).blur();
      $("#track_" + current_track).find(".play").get(0).style.display = "inline";
    }
  }

  function stop()
  { _stop();
    current_track = false;
    autoplay_next = false;
  }

  function _stop()
  { if(current_track != false)
    { get_player().SetVariable("method:stop", "");
      $("#track_" + current_track).find(".pause").get(0).style.display = "none";
      $("#track_" + current_track).find(".stop").get(0).style.display = "none";
      $("#track_" + current_track).find(".stop").get(0).blur();
      $("#track_" + current_track).find(".play").get(0).style.display = "inline";
      $("#track_" + current_track).find(".loaded").get(0).style.width = "0%";
      $("#track_" + current_track).find(".position").get(0).style.width = "0%";
      $("#track_" + current_track).get(0).className = "track";
    }
  }

  function show_xml(id)
  { alert(xml_contents[id]);
  }

  function toggle_source_list(id_block)
  { var content = $("#source_list_" + id_block + " .pistes").html();
    if(content.length > 0)
    { $("#source_list_" + id_block).slideUp(200);
      $("#source_list_" + id_block + " .pistes").empty();
      $("#toggle_sources_list_" + id_block + ".block_list_toggle").html("[+]");
    }
    else
    { $("#source_list_" + id_block).slideDown(200);
      $("#toggle_sources_list_" + id_block + ".block_list_toggle").html("[-]");
      $("#source_list_" + id_block + " .pistes").html("<div class=\"loading\"><span>en chargement...</span></div>");
      $.ajax
      ( { url: site_url + "index.php?e=content/sources/sources&id=" + id_block,
          dataType: "html",
          success: function(content)
          { $("#source_list_" + id_block + " .pistes").html(content);
          }
        }
      );
    }
  }

  function toggle_derivation_list(id_block)
  { var content = $("#derivation_list_" + id_block + " .derivation").html();
    if(content.length > 0)
    { $("#derivation_list_" + id_block).slideUp(200);
      $("#derivation_list_" + id_block + " .derivation").empty();
      $("#toggle_derivation_list_" + id_block + ".block_list_toggle").html("[+]");
    }
    else
    { $("#derivation_list_" + id_block).slideDown(200);
      $("#toggle_derivation_list_" + id_block + ".block_list_toggle").html("[-]");
      $("#derivation_list_" + id_block + " .derivation").html("<div class=\"loading\"><span>en chargement...</span></div>");
      $.ajax
      ( { url: site_url + "index.php?e=content/sources/derivations&id=" + id_block,
          dataType: "html",
          success: function(content)
          { $("#derivation_list_" + id_block + " .derivation").html(content);
          }
        }
      );
    }
  }

  function init_show_xml_links()
  { for(var i in show_xml_links)
    { $("#show_xml_" + i).colorbox();
    }
  }

  function set_show_xml_links(i)
  { if(loaded) $("#show_xml_" + i).colorbox();
    else show_xml_links[i] = true;
  }

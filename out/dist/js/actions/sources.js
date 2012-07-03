$(document).ready(
  function(){
    init_show_xml_links();
    init_players("*");
    init_player_listener();
  }
);

// -----------------------------------------------------------------
//                                                      player audio
//

var current_document = false;
var autoplay_next = false;

function init_players(target){
  $(target == "*" ? "audio" : target + " audio").each
  ( function()
    { var audio_elt = $(this).get(0);
      var CAN_PLAY = false;
      var id_document = $(this).attr("id").substring(6);
      var id_source = null;
      if((k = id_document.indexOf("_")) != -1) id_source = id_document.substring(0, k);
      if(id_source){
        $(this).find("source").each(
          function(){
            if(audio_elt.canPlayType($(this).attr("type"))) CAN_PLAY = true;
         }
        );
        if(CAN_PLAY){
          audio_elt.addEventListener("ended", track_ended, false);
          $("#player_" + id_document + " .play").click(function() { play(id_document); return false; });
          $("#player_" + id_document + " .play").css("display", "inline");
          $("#player_" + id_document + " .pause").click(function() { pause(); return false; });
          $("#player_" + id_document + " .pause").css("display", "none");
          $("#player_" + id_document + " .stop").click(function() { stop(); return false; });
          $("#player_" + id_document + " .stop").css("display", "none");
          $("#document_" + id_document + " .no_player").remove();
        }
      }
      if(!CAN_PLAY) $("#player_" + id_document).remove();
    }
  );
  $(target == "*" ? ".track" : target + " .track").each(
    function(){
      if($(this).find(".player").size()){
        var id_source = $(this).attr("id").substring(6);
        $(this).find(".player_progress").first().click(
          function(e){
            if((progress_width = $(this).width()) != 0){
              play_source_from(id_source, (100 * (e.pageX - this.offsetLeft)) / progress_width);
            }
          }
        );
      }
      else $(this).find(".player_progress").first().css("cursor", "default");
    }
  );
}

function init_player_listener(){
  setInterval("player_listener_update()", 300);
}

function player_listener_update(){
  if(
       (current_document != false)
    && (source_id = get_current_source_id())
    && (current_audio = $("#audio_" + current_document).get(0))
    && ($("#track_" + source_id + " .player_progress").size())
  ){
    $("#track_" + source_id + " .player_progress .position:not(#track_" + source_id + " .track .player_progress .position)").css(
      "width",
      Math.round((100 * current_audio.currentTime) / current_audio.duration) + "%"
    );
  }
}

function gui_state(state){
  gui_blur();
  if(current_document != false){
    var source_id = get_current_source_id();
    if(state == "playing"){
      $("#player_" + current_document).find(".play").css("display", "none");
      $("#player_" + current_document).find(".pause").css("display", "inline");
      $("#player_" + current_document).find(".stop").css("display", "inline");
      $("#track_" + source_id).get(0).className = "playing_track";
    }
    else if(state == "paused"){
      $("#player_" + current_document).find(".play").get(0).style.display = "inline";
      $("#player_" + current_document).find(".pause").get(0).style.display = "none";
      $("#player_" + current_document).find(".stop").get(0).style.display = "inline";
      $("#track_" + source_id).get(0).className = "playing_track";
    }
    else if(state == "stoped"){
      $("#player_" + current_document).find(".play").get(0).style.display = "inline";
      $("#player_" + current_document).find(".pause").get(0).style.display = "none";
      $("#player_" + current_document).find(".stop").get(0).style.display = "none";
      $("#track_" + source_id).get(0).className = "track";
      $("#track_" + source_id + " .player_progress .position:not(#track_" + source_id + " .track .player_progress .position)").css("width", "0%");
    }
  }
}

function gui_blur(){
  if((current_document != false) && $("#player_" + current_document).size()){
    $("#player_" + current_document).find(".play").get(0).blur();
    $("#player_" + current_document).find(".pause").get(0).blur();
    $("#player_" + current_document).find(".stop").get(0).blur();
  }
}

function play(id_document){
  if(current_document == id_document){
    if($("#audio_" + current_document).get(0).paused){
      $("#audio_" + current_document).get(0).play();
    }
  }
  else{
    if(current_document != false){
      var audio_elt = $("#audio_" + current_document).get(0);
      audio_elt.pause();
      audio_elt.currentTime = 0;
      gui_state("stoped");
    }
    current_document = id_document;
    setTimeout("_play(0)", 1000);
  }
  gui_state("playing");
}

function play_source_from(id_source, position){
  if(
       (current_document != false)
    && (current_source_id = get_current_source_id())
    && (id_source == current_source_id)
  ){
    _play(0 + position);
  }
  else{
    stop();
    var FOUND = false;
    $("#track_" + id_source + " .documents li").each(
      function(){
        if(!FOUND){
          var source_document_id = $(this).attr("id").substring(9);
          if(source_document_id.length > 0){
            if($("#player_" + source_document_id).size()){
              FOUND = true;
              current_document = source_document_id;
              setTimeout("_play(" + position + ")", 1000);
            }
          }
        }
      }
    );
  }
  gui_state("playing");
}

function _play(position){
  if(current_document != false){
    var audio_elt = $("#audio_" + current_document).get(0);
    audio_elt.pause();
    audio_elt.currentTime = position ? (position * audio_elt.duration) / 100 : 0;
    audio_elt.play();
  }
}

function pause(){
  if(current_document != false){
    var audio_elt = $("#audio_" + current_document).get(0);
    audio_elt.pause();
    gui_state("paused");
  }
}

function stop(){
  if(current_document != false){
    var audio_elt = $("#audio_" + current_document).get(0);
    audio_elt.pause();
    audio_elt.currentTime = 0;
    gui_state("stoped");
    current_document = false;
  }
  autoplay_next = false;
}

function play_all(){
  autoplay_next = play_first_source();
}

function track_ended(){
  gui_state("stoped");
  var current_audio = $("#audio_" + current_document).get(0);
  current_audio.pause();
  if(current_audio.currentTime) current_audio.currentTime = 0;
  if(autoplay_next) autoplay_next = play_next_source();
}

function get_current_source_id(){
  if(current_document != false){
    if($("#document_" + current_document).size()){
      var source_document_id = $("#document_" + current_document).attr("id").substring(9);
      if((k = source_document_id.indexOf("_")) != -1){
        return source_document_id.substring(0, k);
      }
    }
  }
  return false;
}

function play_first_source(){
  var FOUND = false;
  $(".track").not(".pistes .track").not(".derivation .track").each(
    function(){
      if(!FOUND){
        $(this).find(".documents li").not(".pistes .documents li").not(".derivation .documents li").each(
          function(){
            if(!FOUND){
              var source_document_id = $(this).attr("id").substring(9);
              if(source_document_id.length > 0){
                if($("#player_" + source_document_id).size()){
                  FOUND = true;
                  play(source_document_id);
                }
              }
            }
          }
        );
      }
    }
  );
  return FOUND;
}

function play_next_source(){
  var FOUND = false;
  if(current_document != false){
    if($("#document_" + current_document).size()){
      var current_source_document_id = $("#document_" + current_document).attr("id");
      var current_source_id = get_current_source_id();
      if(current_source_id != false){
        var CURRENT_FOUND = false;
        $(".track").not(".pistes .track").not(".derivation .track").each(
          function(){
            if(!FOUND){
              if(CURRENT_FOUND){
                $(this).find(".documents li").not(".pistes .documents li").not(".derivation .documents li").each(
                  function(){
                    if(!FOUND){
                      var source_document_id = $(this).attr("id").substring(9);
                      if(source_document_id.length > 0){
                        if($("#player_" + source_document_id).size()){
                          FOUND = true;
                          play(source_document_id);
                        }
                      }
                    }
                  }
                );
              }
              else{
                if(current_source_id == $(this).attr("id").substring("6")){
                  CURRENT_FOUND = true;
                }
              }
            }
          }
        );
      }
    }
  }
  return FOUND;
}

// -----------------------------------------------------------------
//                                                        source xml
//

var loaded = false;
var show_xml_links = {};

function show_xml(id){
  alert(xml_contents[id]);
}

function init_show_xml_links(){
  for(var i in show_xml_links){
    $("#show_xml_" + i).colorbox();
  }
  loaded = true;
}

function set_show_xml_links(i){
  if(loaded) $("#show_xml_" + i).colorbox();
  else show_xml_links[i] = true;
}

// -----------------------------------------------------------------
//                                             sources / derivations
//

function toggle_source_list(id_block){
  var content = $("#source_list_" + id_block + " .pistes").html();
  if(content.length > 0){
    $("#source_list_" + id_block).slideUp(200);
    $("#source_list_" + id_block + " .pistes").empty();
    $("#toggle_sources_list_" + id_block + ".block_list_toggle").html("[+]");
  }
  else{
    $("#source_list_" + id_block).slideDown(200);
    $("#toggle_sources_list_" + id_block + ".block_list_toggle").html("[-]");
    $("#source_list_" + id_block + " .pistes").html("<div class=\"loading\"><span>en chargement...</span></div>");
    $.ajax({
      url: site_url + "index.php?e=content/sources/sources&id=" + id_block,
      dataType: "html",
      success: function(content){
        $("#source_list_" + id_block + " .pistes").html(content);
        init_players("#source_list_" + id_block + " .pistes");
      }
    });
  }
}

function toggle_derivation_list(id_block){
  var content = $("#derivation_list_" + id_block + " .derivation").html();
  if(content.length > 0){
    $("#derivation_list_" + id_block).slideUp(200);
    $("#derivation_list_" + id_block + " .derivation").empty();
    $("#toggle_derivation_list_" + id_block + ".block_list_toggle").html("[+]");
  }
  else{
    $("#derivation_list_" + id_block).slideDown(200);
    $("#toggle_derivation_list_" + id_block + ".block_list_toggle").html("[-]");
    $("#derivation_list_" + id_block + " .derivation").html("<div class=\"loading\"><span>en chargement...</span></div>");
    $.ajax({
      url: site_url + "index.php?e=content/sources/derivations&id=" + id_block,
      dataType: "html",
      success: function(content){
        $("#derivation_list_" + id_block + " .derivation").html(content);
        init_players("#derivation_list_" + id_block + " .derivation");
      }
    });
  }
}

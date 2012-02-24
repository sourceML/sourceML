<?php

  class sml_data_images extends sml_data
  {

    function img_size($file, $max_width, $max_height)
    { $img_infos = getimagesize($file);
      $img_size = array();
      if($img_infos)
      { if($img_infos[0] > $max_width || $img_infos[1] > $max_height)
        { $r = $max_width / $img_infos[0];
          if($r * $img_infos[1] > $max_height) $r = $max_height / $img_infos[1];
          return array
          ( "width" => floor($r * $img_infos[0]),
            "height" => floor($r * $img_infos[1]) 
          );
        }
        return array
        ( "width" => $img_infos[0],
          "height" => $img_infos[1] 
        );
      }
      return false;
    }

  }

?>
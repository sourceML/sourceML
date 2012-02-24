<?php

  class sml_data_sources_cache_db extends sml_data
  {

    function source_cache_db()
    { $sgbd = $this->sgbd();
      $source_cache = array();
      if($rst = $sgbd->open_data("cache/sources"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(isset($v_rst["id"])) $source_cache[$v_rst["id"]] = $v_rst;
          }
          else
          { $source_cache = false;
            $break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $source_cache = false;
      return $source_cache;
    }

    function source_cache_infos_db($url)
    { $sgbd = $this->sgbd();
      $cache_infos = array();
      if($rst = $sgbd->open_data("cache/sources"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(isset($v_rst["id"]) && isset($v_rst["url"]))
            { if($v_rst["url"] == $url)
              { $cache_infos = $v_rst;
                $break;
              }
            }
          }
          else
          { $cache_infos = false;
            $break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $cache_infos = false;
      return $cache_infos;
    }

    function add_source_cache_db($url, $cache_index)
    { $sgbd = $this->sgbd();
      return $sgbd->add_data
      ( "cache/sources",
        array
        ( "url" => $url,
          "id_source" => $cache_index,
          "last_update" => date("Y-m-d H:i:s")
        )
      ) ? true : false;
    }

    function del_source_cache_db($id_cache_data)
    { $sgbd = $this->sgbd();
      return $sgbd->del_data("cache/sources", $id_cache_data) ? true : false;
    }

  }

?>
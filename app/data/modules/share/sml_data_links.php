<?php

  class sml_data_links extends sml_data
  {
    var $links;

    function init_links()
    { $this->links = array();
      return true;
    }

    function load_link(&$links, $v_path, $url, $intitule = "", $position = 0)
    { if($path_item = array_shift($v_path))
      { if(!isset($links[$path_item])) $links[$path_item] = array
        ( "nom" => $path_item,
          "url" => $v_path ? null : $url,
          "intitule" => $v_path ? null : $intitule,
          "subs" => array(),
          "position" => 0
        );
        if($v_path) $this->load_link($links[$path_item]["subs"], $v_path, $url, $intitule, $position);
        else
        { $links[$path_item]["nom"] = $path_item;
          $links[$path_item]["url"] = $url;
          $links[$path_item]["intitule"] = $intitule;
          $links[$path_item]["position"] = $position;
        }
      }
    }

    function valid_link_path($path)
    { $v_path = explode("/", $path);
      $SYNTAX_OK = true;
      foreach($v_path as $i => $path_item)
      { if($path_item)
        { if(!preg_match("/^[a-zA-Z]+[a-zA-Z0-9\-_\.]*$/", $path_item))
          { $SYNTAX_OK = false;
            break;
          }
        }
        else unset($v_path[$i]);
      }
      return $v_path && $SYNTAX_OK ? $v_path : false;
    }

    function get_link($path = null)
    { if(!isset($this->links)) $this->init_links();
      if($this->links !== false)
      { if(!isset($path)) return $this->links;
        if($v_path = $this->valid_link_path($path))
        { return $this->_get_link($this->links, $v_path);
        }
      }
      return false;
    }

    function _get_link($links, $v_path)
    { if($path_item = array_shift($v_path))
      { if(isset($links[$path_item]))
        { if($v_path) return $this->_get_link($links[$path_item]["subs"], $v_path);
          else return $links[$path_item];
        }
        else return false;
      }
    }

    function set_link($path, $url, $intitule = "", $position = 0)
    { if(!isset($this->links)) $this->init_links();
      if($v_path = $this->valid_link_path($path))
      { $this->load_link($this->links, $v_path, $url, $intitule, $position);
        $this->links = $this->ordonne_links($this->links);
      }
    }

    function ordonne_links($links)
    { if(!is_array($links)) return false;
      $values = array_values($links);
      $maximum = count($values);
      while($maximum > 0)
      { $maximumTemporaire = 0;
        for($i = 0; $i < $maximum - 1; $i++)
        { if($values[$i]["position"] > $values[$i + 1]["position"])
          { $tmp = $values[$i];
            $values[$i] = $values[$i + 1];
            $values[$i + 1] = $tmp;
            $maximumTemporaire = $i + 1;
          }
        }
        $maximum = $maximumTemporaire;
      }
      $res = array();
      foreach($values as $value) { if($value["nom"]) $res[$value["nom"]] = $value; }
      foreach($res as $nom => $sub) { if($sub["subs"]) $res[$nom]["subs"] = $this->ordonne_links($res[$nom]["subs"]); }
      return $res;
    }

  }

?>
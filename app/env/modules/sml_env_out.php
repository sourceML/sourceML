<?php

  class sml_env_out extends sml_env
  {
    var $out;
    var $out_config;
    var $layout;

    function set_out($key, $value) { $this->out[$key] = $value; return $value; }
    function get_out() { return $this->out; }
    function out($key) { return $this->out[$key]; }

    function out_pathes()
    { $pathes = array();
      if($dh = opendir($this->path("out")))
      { while(($file = readdir($dh)) !== false)
        { if(is_dir($this->path("out").$file) && substr($file, 0 ,1) != ".") $pathes[] = $file;
        }
        closedir($dh);
      }
      else $pathes = false;
      return $pathes;
    }

    function out_file_exists($file, $PRIORITE = "DESC")
    { $out_file = $this->_out_file($file, $PRIORITE);
      return $out_file ? true : false;
    }

    function out_file($file, $PRIORITE = "DESC")
    { $out_file = $this->_out_file($file, $PRIORITE);
      return $out_file ? $out_file : $file;
    }

    function _out_file($file, $PRIORITE = "DESC")
    { $out_file = false;
      if($PRIORITE == "ASC")
      { $tmp_out_file = $this->path("out").$this->config("out").$file;
        if($file && file_exists($tmp_out_file))
        { $out_file = $tmp_out_file;
        }
        if(!$out_file)
        { $tmp_out_file = $this->path("out").$this->path("dist_out").$file;
          if($file && file_exists($tmp_out_file))
          { $out_file = $tmp_out_file;
          }
        }
      }
      if($out_file) return $out_file;
      if(($plugins = $this->plugins($PRIORITE)) !== false)
      { foreach($plugins as $plugin_name => $plugin)
        { $tmp_out_file = $this->path("plugins").$plugin_name."/out/".$this->config("out").$file;
          if($file && $plugin["installed"] && $plugin["enabled"] && file_exists($tmp_out_file))
          { $out_file = $tmp_out_file;
            break;
          }
          if(!$out_file)
          { $tmp_out_file = $this->path("plugins").$plugin_name."/out/".$this->path("dist_out").$file;
            if($file && $plugin["installed"] && $plugin["enabled"] && file_exists($tmp_out_file))
            { $out_file = $tmp_out_file;
              break;
            }
          }
        }
        if($PRIORITE == "DESC" && !$out_file)
        { $tmp_out_file = $this->path("out").$this->config("out").$file;
          if($file && file_exists($tmp_out_file))
          { $out_file = $tmp_out_file;
          }
          if(!$out_file)
          { $tmp_out_file = $this->path("out").$this->path("dist_out").$file;
            if($file && file_exists($tmp_out_file))
            { $out_file = $tmp_out_file;
            }
          }
        }
      }
      return $out_file;
    }

    # ---------------------------------------------------------------------------------
    #                                                                        out config
    #

    function set_out_config($out_config)
    { $this->out_config = $out_config;
      return $this->out_config;
    }

    function get_out_config() { return isset($this->out_config) ? $this->out_config : array(); }

    function out_config($name)
    { if(isset($this->out_config))
      { $CONFIG = $this->get_CONFIG();
        return isset($CONFIG["out_".$name]) ? $CONFIG["out_".$name] : $this->out_config[$name]["default"];
      }
      return null;
    }

    # ---------------------------------------------------------------------------------
    #                                                                           layouts
    #

    function layout() { return $this->layout; }

    function render_layout($layout = null)
    { if(!isset($layout)) $layout = $this->init_layout();
      if(($plugins = $this->plugins("ASC")) !== false)
      { foreach($plugins as $plugin_name => $plugin)
        { if($plugin["installed"] && $plugin["enabled"])
          { $FOUND = false;
            $functions_file = $this->path("plugins").$plugin_name."/out/".$this->config("out")."functions.php";
            if(file_exists($functions_file))
            { $FOUND = true;
              require $functions_file;
            }
            if(!$FOUND)
            { $functions_file = $this->path("plugins").$plugin_name."/out/".$this->path("dist_out")."functions.php";
              if($plugin["installed"] && $plugin["enabled"] && file_exists($functions_file))
              { require $functions_file;
              }
            }
          }
        }
        $FOUND = false;
        $functions_file = $this->path("out").$this->config("out")."functions.php";
        if(file_exists($functions_file))
        { $FOUND = true;
          require $functions_file;
        }
        if(!$FOUND)
        { $functions_file = $this->path("out").$this->path("dist_out")."functions.php";
          if(file_exists($functions_file))
          { require $functions_file;
          }
        }
        if($layout["page"])
        { if($this->out_file_exists($layout["page"])) require $this->out_file($layout["page"]);
        }
        elseif($layout["content"])
        { if($this->out_file_exists($layout["content"])) require $this->out_file($layout["content"]);
        }
      }
    }

    function init_layout()
    { $this->layout = array();
      $this->_init_layout("index");
      if(($mod = $this->etat("mod")) != "index") $this->_init_layout($mod);
      return $this->get_layout();
    }

    function _init_layout($mod)
    { if(($plugins = $this->plugins("ASC")) !== false)
      { $layout_file = false;
        $tmp_layout_file = $this->path("out").$this->config("out")."layouts/".$mod.".xml";
        if(file_exists($tmp_layout_file)) $layout_file = $tmp_layout_file;
        if(!$layout_file)
        { $tmp_layout_file = $this->path("out").$this->path("dist_out")."layouts/".$mod.".xml";
          if(file_exists($tmp_layout_file)) $layout_file = $tmp_layout_file;
        }
        if($layout_file) $this->load_layout($layout_file);
        foreach($plugins as $plugin_name => $plugin)
        { if($plugin["installed"] && $plugin["enabled"])
          { $layout_file = false;
            $tmp_layout_file = $this->path("plugins").$plugin_name."/out/".$this->config("out")."layouts/".$mod.".xml";
            if(file_exists($tmp_layout_file)) $layout_file = $tmp_layout_file;
            if(!$layout_file)
            { $tmp_layout_file = $this->path("plugins").$plugin_name."/out/".$this->path("dist_out")."layouts/".$mod.".xml";
              if(file_exists($tmp_layout_file)) $layout_file = $tmp_layout_file;
            }
            if($layout_file) $this->load_layout($layout_file);
          }
        }
      }
    }

    function load_layout($layout_file)
    { if(file_exists($layout_file))
      { $xml_parser = new sxml();
        $xml_parser->parse(file_get_contents($layout_file));
        $layout = $xml_parser->data;
        if(isset($layout["layout"][0]["subs"]))
        { foreach($layout["layout"][0]["subs"] as $mod => $mod_node)
          { if(!isset($this->layout[$mod]))
            { $this->layout[$mod] = array
              ( "page" => null,
                "content" => null,
                "controllers" => array()
              );
            }
            if(isset($mod_node[0]["attrs"]["page"])) $this->layout[$mod]["page"] = $mod_node[0]["attrs"]["page"];
            if(isset($mod_node[0]["attrs"]["content"])) $this->layout[$mod]["content"] = $mod_node[0]["attrs"]["content"];
            if(isset($mod_node[0]["subs"]))
            { foreach($mod_node[0]["subs"] as $controller => $controller_node)
              { if(!isset($this->layout[$mod]["controllers"][$controller]))
                { $this->layout[$mod]["controllers"][$controller] = array
                  ( "page" => null,
                    "content" => null,
                    "actions" => array()
                  );
                }
                if(isset($controller_node[0]["attrs"]["page"])) $this->layout[$mod]["controllers"][$controller]["page"] = $controller_node[0]["attrs"]["page"];
                if(isset($controller_node[0]["attrs"]["content"])) $this->layout[$mod]["controllers"][$controller]["content"] = $controller_node[0]["attrs"]["content"];
                if(isset($controller_node[0]["subs"]))
                { foreach($controller_node[0]["subs"] as $action => $action_node)
                  { if(!isset($this->layout[$mod]["controllers"][$controller]["actions"][$action]))
                    { $this->layout[$mod]["controllers"][$controller]["actions"][$action] = array
                      ( "page" => null,
                        "content" => null
                      );
                    }
                    if(isset($action_node[0]["attrs"]["page"])) $this->layout[$mod]["controllers"][$controller]["actions"][$action]["page"] = $action_node[0]["attrs"]["page"];
                    if(isset($action_node[0]["attrs"]["content"])) $this->layout[$mod]["controllers"][$controller]["actions"][$action]["content"] = $action_node[0]["attrs"]["content"];
                  }
                }
              }
            }
          }
        }
      }
      return false;
    }

    function get_layout()
    { $mod = $this->etat("mod");
      $controller = $this->etat("controller");
      $action = $this->etat("action");
      $content = "";
      if(isset($this->layout[$mod]["controllers"][$controller]["actions"][$action]["content"]))
      { $content = $this->layout[$mod]["controllers"][$controller]["actions"][$action]["content"];
      }
      else
      { if(isset($this->layout[$mod]["controllers"][$controller]["content"]))
        { $content = $this->layout[$mod]["controllers"][$controller]["content"];
        }
        else
        { if(isset($this->layout[$mod]["content"]))
          { $content = $this->layout[$mod]["content"];
          }
          else
          { if(isset($this->layout["index"]["content"]))
            { $content = $this->layout["index"]["content"];
            }
          }
        }
      }
      $page = "";
      if(isset($this->layout[$mod]["controllers"][$controller]["actions"][$action]["page"]))
      { $page = $this->layout[$mod]["controllers"][$controller]["actions"][$action]["page"];
      }
      else
      { if(isset($this->layout[$mod]["controllers"][$controller]["page"]))
        { $page = $this->layout[$mod]["controllers"][$controller]["page"];
        }
        else
        { if(isset($this->layout[$mod]["page"]))
          { $page = $this->layout[$mod]["page"];
          }
          else
          { if(isset($this->layout["index"]["page"]))
            { $content = $this->layout["index"]["page"];
            }
          }
        }
      }
      return array
      ( "page" => $page,
        "content" => $content
      );
    }

  }

?>
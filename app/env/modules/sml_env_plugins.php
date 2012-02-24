<?php

  class sml_env_plugins extends sml_env
  {

    var $plugins_asc;
    var $plugins_desc;

    function plugins($PRIORITE = "ASC")
    { $this->init_plugins($PRIORITE);
      if($PRIORITE == "ASC") return $this->plugins_asc;
      if($PRIORITE == "DESC") return $this->plugins_desc;
      return false;
    }

    # ---------------------------------------------------------------------------------
    #                                                                              init
    #

    function init_plugins($PRIORITE = "ASC")
    { if(isset($this->plugins_asc) || isset($this->plugins_desc))
      { if($PRIORITE == "ASC")
        { if(!isset($this->plugins_asc)) $this->plugins_asc = $this->ordonne_plugins($this->plugins_desc, $PRIORITE);
        }
        elseif($PRIORITE == "DESC")
        { if(!isset($this->plugins_desc)) $this->plugins_desc = $this->ordonne_plugins($this->plugins_asc, $PRIORITE);
        }
        return;
      }
      $plugins = array();
      if(!class_exists("sml_plugin"))
      { require $this->path("app")."sml_plugin.php";
        if(!class_exists("sml_plugin"))
        { $plugins = false;
          return;
        }
      }
      if(file_exists($this->path("plugins")))
      { if($dh = opendir($this->path("plugins")))
        { $OK = true;
          while($OK && ($plugin_name = readdir($dh)) !== false)
          { if(substr($plugin_name, 0 ,1) !== "." && is_dir($this->path("plugins").$plugin_name))
            { if(!isset($plugins[$plugin_name]))
              { if(($plugin = $this->plugin_data($plugin_name)) !== false)
                { $MAJ = false;
                  if(!isset($plugin["installed"]) || !isset($plugin["enabled"]))
                  { $plugin["installed"] = false;
                    $plugin["enabled"] = false;
                    $plugin["priorite"] = 0;
                    $MAJ = true;
                  }
                  if(!$plugin["installed"] && $plugin["enabled"]) { $plugin["enabled"] = false; $MAJ = true; }
                  if($MAJ) $OK = $this->set_plugin_data($plugin_name, $plugin);
                  if($OK)
                  { if(($plugin["impl"] = $this->plugin_impl($plugin_name)) !== false)
                    { $plugin["title"] =  ($plugin_title = $this->plugin_call($plugin["impl"], "title")) ? $plugin_title : "";
                      $plugin["description"] = ($plugin_description = $this->plugin_call($plugin["impl"], "description")) ? $plugin_description : "";
                      $plugin["name"] = $plugin_name;
                      $plugins[$plugin_name] = $plugin;
                    }
                    else $OK = false;
                  }
                }
                else $OK = false;
              }
            }
            if(!$OK) $plugins = false;
          }
          closedir($dh);
          if($plugins !== false)
          { if(file_exists($this->plugins_data_dir()) && is_dir($this->plugins_data_dir()))
            { if($dh = opendir($this->plugins_data_dir()))
              { $plugins_data_files = array();
                $OK = true;
                while($OK && ($plugin_name = readdir($dh)) !== false)
                { if(substr($plugin_name, 0 ,1) != "." && !is_dir($this->plugin_data_file($plugin_name)))
                  { if(!$plugins[$plugin_name]) $this->del_plugin_data($plugin_name);
                  }
                  if(!$OK) $plugins = false;
                }
                closedir($dh);
              }
            }
          }
        }
        else $plugins = false;
      }
      if($plugins !== false)
      { if($PRIORITE == "ASC") $this->plugins_asc = $this->ordonne_plugins($plugins, $PRIORITE);
        elseif($PRIORITE == "DESC") $this->plugins_desc = $this->ordonne_plugins($plugins, $PRIORITE);
      }
      else
      { $this->plugins_asc = false;
        $this->plugins_desc = false;
      }
    }

    function ordonne_plugins($plugins, $PRIORITE = "ASC")
    { $values = array_values($plugins);
      $maximum = count($values);
      while($maximum > 0)
      { $maximumTemporaire = 0;
        for($i = 0; $i < $maximum - 1; $i++)
        { if
          (    ($PRIORITE == "ASC" && $values[$i]["priorite"] > $values[$i + 1]["priorite"])
            || ($PRIORITE == "DESC" && $values[$i]["priorite"] < $values[$i + 1]["priorite"])
          )
          { $tmp = $values[$i];
            $values[$i] = $values[$i + 1];
            $values[$i + 1] = $tmp;
            $maximumTemporaire = $i + 1;
          }
        }
        $maximum = $maximumTemporaire;
      }
      $res = array();
      foreach($values as $value) if($value["name"]) $res[$value["name"]] = $value;
      return $res;
    }

    function plugin_call($impl, $method) { if(method_exists($impl, $method)) return $impl->$method($this); }

    # ---------------------------------------------------------------------------------
    #                                                                              impl
    #

    function plugin_impl($plugin_name)
    { $plugin = false;
      if(file_exists($this->path("plugins")))
      { if(substr($plugin_name, 0 ,1) !== "." && is_dir($this->path("plugins").$plugin_name))
        { if(file_exists($this->path("plugins").$plugin_name."/".$plugin_name.".php"))
          { require $this->path("plugins").$plugin_name."/".$plugin_name.".php";
            if(class_exists($plugin_name))
            { $plugin = new $plugin_name();
            }
          }
        }
      }
      return $plugin;
    }

    # ---------------------------------------------------------------------------------
    #                                                                              data
    #

    function plugins_data_dir()
    { return $this->path("content")."data/plugins/";
    }

    function plugin_data_file($plugin_name)
    { return $this->plugins_data_dir().$plugin_name;
    }

    function plugin_data($plugin_name)
    { $data_file = $this->plugin_data_file($plugin_name);
      $data = array();
      if(file_exists($data_file))
      { if($content = file_get_contents($data_file))
        { $data = unserialize($content);
        }
      }
      return $data;
    }

    function set_plugin_data($plugin_name, $data)
    { $data_dir = $this->plugins_data_dir();
      if(!is_dir($data_dir)) @mkdir($data_dir);
      if(!is_dir($data_dir)) return false;
      $data_file = $this->plugin_data_file($plugin_name);
      $content = serialize($data);
      $OK = false;
      if($fh = fopen($data_file, "w"))
      { if(fwrite($fh, $content) !== false)
        { $OK = true;
        }
        fclose($fh);
      }
      return $OK;
    }

    function del_plugin_data($plugin_name)
    { $data_file = $this->plugin_data_file($plugin_name);
      if(file_exists($data_file)) return @unlink($data_file);
      return true;
    }

  }

?>
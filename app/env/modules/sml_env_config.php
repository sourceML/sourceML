<?php

  class sml_env_config extends sml_env
  {

    var $config_file;
    var $PATHES;
    var $PARAMS;
    var $CONFIG;
    var $bdd;

    function load_config($bdd, $CONFIG)
    { if(true)
      { $this->bdd = $bdd;
        $this->bdd["table_prefix"] = array();
        $this->CONFIG = isset($CONFIG) ? $CONFIG : array();
        $this->PARAMS = array();
        $xml_parser = new sxml();
        $app_config_file = $this->path("app")."config.xml";
        if(file_exists($app_config_file))
        { $xml_parser->parse(file_get_contents($app_config_file));
          $app_config = $xml_parser->data["config"][0];
          if(isset($app_config["subs"]["params"]))
          { foreach($app_config["subs"]["params"][0]["subs"] as $param_key => $param_elt)
            { $this->PARAMS[$param_key] = $param_elt[0]["data"];
            }
          }
          if(isset($app_config["subs"]["config"]))
          { foreach($app_config["subs"]["config"][0]["subs"] as $config_key => $config_elt)
            { $this->CONFIG[$config_key] = $config_elt[0]["data"];
            }
          }
          if(isset($app_config["subs"]["bdd"][0]["subs"]["table_prefix_code"]))
          { $this->add_table_prefix
            ( array
              ( $app_config["subs"]["bdd"][0]["subs"]["table_prefix_code"][0]["data"] => $bdd["table_prefix"]
              )
            );
          }
        }
        if(($plugins = $this->plugins("ASC")) !== false)
        { foreach($plugins as $plugin_name => $plugin)
          { $app_config_file = $this->path("plugins").$plugin_name."/app/config.xml";
            if(file_exists($app_config_file) && $plugin["installed"] && $plugin["enabled"])
            { $xml_parser->parse(file_get_contents($app_config_file));
              $app_config = $xml_parser->data["config"][0];
              if(isset($app_config["subs"]["params"]))
              { foreach($app_config["subs"]["params"][0]["subs"] as $param_key => $param_elt)
                { $this->PARAMS[$param_key] = $param_elt[0]["data"];
                }
              }
              if(isset($app_config["subs"]["config"]))
              { foreach($app_config["subs"]["config"][0]["subs"] as $config_key => $config_elt)
                { $this->CONFIG[$config_key] = $config_elt[0]["data"];
                }
              }
              if(isset($app_config["subs"]["bdd"][0]["subs"]["table_prefix_code"]))
              { $this->add_table_prefix
                ( array
                  ( $app_config["subs"]["bdd"][0]["subs"]["table_prefix_code"][0]["data"] => $bdd["table_prefix"]
                  )
                );
              }
            }
          }
          $this->init_additional_get_params();
        }
        else $this->erreur("impossible de lire les fichiers de configuration pour les plugins", true);
      }
      else $this->erreur("impossible de trouver le fichier de configuration pour l'installation", true);
    }

    function get_config_file() { return $this->config_file; }
    function set_config_file($config_file) { $this->config_file = $config_file; }

    function get_PATHES() { return $this->PATHES; }
    function path($name)
    { if(isset($this->PATHES[$name])) return $this->PATHES[$name];
      return "";
    }
    function set_PATHES($PATHES)
    { foreach($PATHES as $path_name => $path_value)
      { if($path_value && substr($path_value, -1) != "/") $PATHES[$path_name] .= "/";
      }
      $this->PATHES = $PATHES;
    }

    function get_PARAMS() { return $this->PARAMS; }
    function param($name) { return $this->PARAMS[$name]; }

    function get_CONFIG() { return $this->CONFIG; }
    function config($name) { return $this->CONFIG[$name]; }
    function set_config($config)
    { if(is_array($config))
      { foreach($config as $key => $value) $this->CONFIG[$key] = $value;
        return true;
      }
      return false;
    }

    function get_bdd() { return $this->bdd; }
    function bdd($name) { return $this->bdd[$name]; }
    function set_bdd($key, $value) { $this->bdd[$key] = $value; }
    function add_table_prefix($table_prefix)
    { if(is_array($table_prefix))
      { foreach($table_prefix as $prefix_code => $prefix) $this->bdd["table_prefix"][$prefix_code] = $prefix;
        return true;
      }
      return false;
    }

  }

?>
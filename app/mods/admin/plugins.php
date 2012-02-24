<?php

  class sml_admin_plugins extends sml_mod
  {
    var $plugins;

    function validate(&$env)
    { if(($this->plugins = $env->plugins("DESC")) === false) return "impossible de lire la liste des plugins";
      return true;
    }

    function index(&$env)
    { if($this->plugins !== false)
      { if($_POST)
        { $OK = true;
          foreach($this->plugins as $plugin_name => $plugin)
          { if(isset($_POST["priorite_".$plugin_name]))
            { $this->plugins[$plugin_name]["priorite"] = $_POST["priorite_".$plugin_name];
              if(!preg_match("/^[0-9]+(\.[0-9]+)?$/", $_POST["priorite_".$plugin_name]))
              { $env->message("les priorit&eacute;s des plugins doivent &ecirc;tre des nombres");
                $OK = false;
                break;
              }
            }
            else $this->plugins[$plugin_name]["priorite"] = 0;
          }
          if($OK)
          { foreach($this->plugins as $plugin_name => $plugin)
            { $plugin_data = array
              ( "installed" => $this->plugins[$plugin_name]["installed"],
                "enabled" => $this->plugins[$plugin_name]["enabled"],
                "priorite" => $this->plugins[$plugin_name]["priorite"]
              );
              if(!$env->set_plugin_data($plugin_name, $plugin_data))
              { $env->erreur("impossible de mettre &agrave; jour la priorit&eacute; du plugin ".$plugin_name);
                $OK = false;
                break;
              }
            }
            if($OK)
            { $env->redirect
              ( $env->url("admin/plugins/index"),
                "les priorit&eacute;s des plugins ont &eacute;t&eacute; enregistr&eacute;es"
              );
            }
          }
        }
        $env->set_out("plugins", $this->plugins);
      }
      else $env->erreur("impossible de lire la liste des plugins");
    }

    function install(&$env)
    { $plugin_name = $_GET[$env->param("id")];
      if(isset($this->plugins[$plugin_name]))
      { $impl = $this->plugins[$plugin_name]["impl"];
        $res = $impl->install($env);
        if($res === true)
        { $plugin_data = array
          ( "installed" => true,
            "enabled" => false,
            "priorite" => isset($this->plugins[$plugin_name]["priorite"]) ? $this->plugins[$plugin_name]["priorite"] : 0
          );
          if($env->set_plugin_data($plugin_name, $plugin_data))
          { $env->redirect
            ( $env->url("admin/plugins/index"),
              "le plugin a &eacute;t&eacute; install&eacute;"
            );
          }
          else $env->erreur("impossible de mettre &agrave; jour le statut du plugin ".$plugin_name);
        }
        else $env->erreur("erreur lors de l'installation du plugin ".$plugin_name."<br>".$res);
      }
      else $env->erreur("impossible de trouver le plugin ".$plugin_name);
    }

    function uninstall(&$env)
    { $plugin_name = $_GET[$env->param("id")];
      if(isset($this->plugins[$plugin_name]))
      { $impl = $this->plugins[$plugin_name]["impl"];
        $res= $impl->uninstall($env);
        if($res === true)
        { $plugin_data = array
          ( "installed" => false,
            "enabled" => false,
            "priorite" => isset($this->plugins[$plugin_name]["priorite"]) ? $this->plugins[$plugin_name]["priorite"] : 0
          );
          if($env->set_plugin_data($plugin_name, $plugin_data))
          { $env->redirect
            ( $env->url("admin/plugins/index"),
              "le plugin a &eacute;t&eacute; d&eacute;sinstall&eacute;"
            );
          }
          else $env->erreur("impossible de mettre &agrave; jour le statut du plugin ".$plugin_name);
        }
        else $env->erreur("erreur lors de la d&eacute;sinstallation du plugin ".$plugin_name."<br>".$res);
      }
      else $env->erreur("impossible de trouver le plugin ".$plugin_name);
    }

    function enable(&$env)
    { $plugin_name = $_GET[$env->param("id")];
      if(isset($this->plugins[$plugin_name]))
      { if($this->plugins[$plugin_name]["installed"])
        { if(!$this->plugins[$plugin_name]["enabled"])
          { $impl = $this->plugins[$plugin_name]["impl"];
            $res = $impl->enable($env);
            if($res === true)
            { $plugin_data = array
              ( "installed" => true,
                "enabled" => true,
                "priorite" => isset($this->plugins[$plugin_name]["priorite"]) ? $this->plugins[$plugin_name]["priorite"] : 0
              );
              if($env->set_plugin_data($plugin_name, $plugin_data))
              { $env->redirect
                ( $env->url("admin/plugins/index"),
                  "le plugin a &eacute;t&eacute; activ&eacute;"
                );
              }
              else $env->erreur("impossible de mettre &agrave; jour le statut du plugin ".$plugin_name);
            }
            else $env->erreur("erreur lors de l'activation du plugin ".$plugin_name."<br>".$res);
          }
          else $env->erreur("le plugin ".$plugin_name." est d&eacute;j&agrave; actif");
        }
        else $env->erreur("le plugin ".$plugin_name." n'est pas install&eacute;");
      }
      else $env->erreur("impossible de trouver le plugin ".$plugin_name);
    }

    function disable(&$env)
    { $plugin_name = $_GET[$env->param("id")];
      if(isset($this->plugins[$plugin_name]))
      { if($this->plugins[$plugin_name]["installed"])
        { if($this->plugins[$plugin_name]["enabled"])
          { $impl = $this->plugins[$plugin_name]["impl"];
            $res = $impl->disable($env);
            if($res === true)
            { $plugin_data = array
              ( "installed" => true,
                "enabled" => false,
                "priorite" => isset($this->plugins[$plugin_name]["priorite"]) ? $this->plugins[$plugin_name]["priorite"] : 0
              );
              if($env->set_plugin_data($plugin_name, $plugin_data))
              { $env->redirect
                ( $env->url("admin/plugins/index"),
                  "le plugin a &eacute;t&eacute; d&eacute;sactiv&eacute;"
                );
              }
              else $env->erreur("impossible de mettre &agrave; jour le statut du plugin ".$plugin_name);
            }
            else $env->erreur("erreur lors de la d&eacute;sactivation du plugin ".$plugin_name."<br>".$res);
          }
          else $env->erreur("le plugin ".$plugin_name." est d&eacute;j&agrave; inactif");
        }
        else $env->erreur("le plugin ".$plugin_name." n'est pas install&eacute;");
      }
      else $env->erreur("impossible de trouver le plugin ".$plugin_name);
    }

  }

?>
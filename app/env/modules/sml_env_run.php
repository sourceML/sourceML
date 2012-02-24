<?php

  class sml_env_run extends sml_env
  {
    var $etat;

    function user()
    { $data = $this->data();
      return $data->get_session_user();
    }

    function set_etat($etat, $valid_status = true)
    { if(($this->etat = $this->valid_etat($etat)) !== false)
      { if(!$valid_status || $this->status_ok($this->etat, false))
        { return $this->etat;
        }
        else $this->erreur("Vous n'avez pas le statut requis pour effectuer cette action");
      }
      else $this->erreur("etat invalide");
      return false;
    }

    function valid_etat($etat)
    { $_etat = array();
      $_etat["mod"] = "";
      $_etat["controller"] = "";
      $_etat["action"] = "";
      if(is_array($etat))
      { $_etat["mod"] = isset($etat["mod"]) ? $etat["mod"] : "";
        $_etat["controller"] = isset($etat["controller"]) ? $etat["controller"] : "";
        $_etat["action"] = isset($etat["action"]) ? $etat["action"] : "";
      }
      else
      { $etat = explode("/", $etat);
        foreach($etat as $etat_item)
        { if($etat_item)
          { if(!$_etat["mod"]) $_etat["mod"] = $etat_item;
            else
            { if(!$_etat["controller"]) $_etat["controller"] = $etat_item;
              else
              { if(!$_etat["action"]) $_etat["action"] = $etat_item;
                break;
              }
            }
          }
        }
      }
      if(!$_etat["mod"])
      { $_etat["mod"] = "index";
        $_etat["controller"] = "index";
        $_etat["action"] = "index";
      }
      else
      { if(!$_etat["controller"])
        { $_etat["controller"] = "index";
          $_etat["action"] = "index";
        }
        else
        { if(!$_etat["action"]) $_etat["action"] = "index";
        }
      }
      if
      (    is_array($_etat)
        && count($_etat) == 3
        && isset($_etat["mod"]) && preg_match("/^[a-zA-Z0-9_]+$/", $_etat["mod"])
        && isset($_etat["controller"]) && preg_match("/^[a-zA-Z0-9_]+$/", $_etat["controller"])
        && isset($_etat["action"]) && preg_match("/^[a-zA-Z0-9_]+$/", $_etat["action"])
      ) return $_etat;
      return false;
    }

    function etat_is_valid()
    { return $this->valid_etat($this->etat);
    }

    function status_ok($etat, $CHECK_FORMAT = true)
    { $OK = $this->config("default_allow");
      $data = $this->data();
      if($CHECK_FORMAT) $etat = $this->valid_etat($etat);
      if($etat !== false)
      { if(($user_status = $data->get_user_status()) !== false)
        { if
          ( ( $action_status = $data->get_action_status
              ( $etat["mod"],
                $etat["controller"],
                $etat["action"]
              )
            ) !== false
          )
          { $action = $etat["mod"]."/".$etat["controller"]."/".$etat["action"];
            if(isset($action_status[$action]))
            { $OK = $action_status[$action][0] || (isset($action_status[$action][$user_status]) && $action_status[$action][$user_status]);
            }
            else
            { $action = $etat["mod"]."/".$etat["controller"];
              if(isset($action_status[$action]))
              { $OK = $action_status[$action][0] || (isset($action_status[$action][$user_status]) && $action_status[$action][$user_status]);
              }
              else
              { $action = $etat["mod"];
                if(isset($action_status[$action]))
                { $OK = $action_status[$action][0] || (isset($action_status[$action][$user_status]) && $action_status[$action][$user_status]);
                }
              }
            }
          }
          else $this->erreur("Impossible de lire les status des actions en base");
        }
        else $this->erreur("Impossible de lire le statut de l'utilisateur courant");
      }
      else $this->erreur("etat invalide");
      return $OK;
    }

    function run($etat, $valid_status = true, $params = array(), $method = "GET")
    { if($this->set_etat($etat, $valid_status))
      { $controller_file = "mods/".$this->etat("mod")."/".$this->etat("controller").".php";
        if($this->app_file_exists($controller_file = "mods/".$this->etat("mod")."/".$this->etat("controller").".php", "DESC"))
        { if(!class_exists("sml_mod")) require $this->app_file("mods/sml_mod.php");
          if(!class_exists($controller_class = "sml_".$this->etat("mod")."_".$this->etat("controller")))
          { require $this->app_file($controller_file, "DESC");
          }
          if(class_exists($controller_class))
          { $controller = new $controller_class();
            $action_method = $this->etat("action");
            if(method_exists($controller, $action_method))
            { foreach($params as $key => $value)
              { switch(strtolower($method))
                { case "get": $_GET[$this->param($key)] = $value; break;
                  case "post": $_POST[$key] = $value; break;
                  default: break;
                }
              }
              if(($controller_validate = $controller->validate($this)) === true)
              { if(($controller_prepare_inputs = $controller->prepare_inputs($this)) === true)
                { $controller->$action_method($this);
                }
                else $this->erreur($controller_prepare_inputs);
              }
              else $this->erreur($controller_validate);
            }
            else $this->erreur("Impossible de trouver l'action ".$this->etat("action"));
          }
          else $this->erreur("Impossible d'instancier le controleur ".$this->etat("controller"));
        }
        else $this->erreur("Impossible de trouver le controleur ".$this->etat("controller")." pour le module ".$this->etat("mod"));
      }
      else $this->erreur("Impossible d'effectuer cette action");
    }

    function etat($name) { return $this->etat[$name]; }

    function check_stop()
    { return $this->etat("mod") == "reponses";
    }

    function get_mod($mod_name)
    { if($etat = $this->valid_etat($mod_name))
      { if($this->app_file_exists($controller_file = "mods/".$etat["mod"]."/".$etat["controller"].".php"))
        { if(!class_exists("sml_mod")) require $this->app_file("mods/sml_mod.php");
          if(!class_exists($controller_class = "sml_".$etat["mod"]."_".$etat["controller"]))
          { require $this->app_file($controller_file);
          }
          if(class_exists($controller_class))
          { return new $controller_class();
          }
        }
      }
      return false;
    }

  }

?>
<?php

  class sml_admin_config extends sml_mod
  {

    var $groupes;
    var $albums;
    var $status;
    var $album_status_id;

    function validate(&$env)
    { $data = $env->data();
      if(($this->status = $data->source_status()) === false)
      { return "Impossible de lire la liste des status de source";
      }
      foreach($this->status as $id_source_status => $source_status)
      { if($source_status["nom"] == "album") $this->album_status_id = $id_source_status;
        if(isset($this->album_status_id) && isset($this->morceau_status_id)) break;
      }
      if(!isset($this->album_status_id)) return "Type de source inconnu: album";
      if(($this->groupes = $data->groupes()) === false)
      { return "Impossible de lire la liste des groupes";
      }
      $select = array("status" => $this->album_status_id);
      if(($this->albums = $data->sources($select)) === false) return "impossible de lire la liste des albums";
      return true;
    }

    function index(&$env)
    { $data = $env->data();
      if($groupes = $this->groupes)
      { $env->set_out("groupes", $this->groupes);
        if($albums = $this->albums)
        { $env->set_out("albums", $this->albums);
          if(($CONFIG = $env->get_CONFIG()) !== false)
          { if(!$CONFIG["out"]) $CONFIG["out"] = "dist";
            $env->set_out("config", $CONFIG);
            if(($out_config = $env->get_out_config()) !== false)
            { $env->set_out("out_config", $out_config);
              if($env->set_out("out_pathes", $env->out_pathes()) !== false)
              { if($_POST)
                { $env->set_out("config", $_POST);
                  if(preg_match("/^[0-9]+$/", $_POST["max_list"]))
                  { if(!$_POST["contact_form"] || trim($_POST["email"]))
                    { $CONTINUE = true;
                      if($CONTINUE && $data->set_config("site_name", $_POST["site_name"]));
                      else $CONTINUE = false;
                      if($CONTINUE && $data->set_config("description", $_POST["description"]));
                      else $CONTINUE = false;
                      if($CONTINUE && $data->set_config("max_list", $_POST["max_list"]));
                      else $CONTINUE = false;
                      if($CONTINUE && $data->set_config("contact_form", $_POST["contact_form"] ? "1" : "0"));
                      else $CONTINUE = false;
                      if($CONTINUE && $data->set_config("email", $_POST["email"]));
                      else $CONTINUE = false;
                      if($CONTINUE && $data->set_config("captcha", $_POST["captcha"] ? "1" : "0"));
                      else $CONTINUE = false;
                      if($CONTINUE)
                      { if($_POST["start_action"] == "sources/groupe")
                        { if($data->set_config("start_action", "sources/groupe"));
                          else $CONTINUE = false;
                        }
                        elseif($_POST["start_action"] == "sources/groupe/view")
                        { $params = array("id" => $_POST["groupe_param"]);
                          $CONTINUE =
                             $data->set_config("start_action", "sources/groupe/view")
                          && $data->set_config("start_action_params", @serialize($params));
                        }
                        elseif($_POST["start_action"] == "sources/album/view")
                        { $params = array("album" => $_POST["album_param"]);
                          $CONTINUE =
                             $data->set_config("start_action", "sources/album/view")
                          && $data->set_config("start_action_params", @serialize($params));
                        }
                      }
                      else $CONTINUE = false;
                      if($CONTINUE && $data->set_config("out", $_POST["out"]));
                      else $CONTINUE = false;
                      if($CONTINUE)
                      { foreach($out_config as $key => $values)
                        { if($data->set_config("out_".$key, isset($_POST["out_".$key]) ? $_POST["out_".$key] : "") === false)
                          { $CONTINUE = false;
                            break;
                          }
                        }
                      }
                      if($CONTINUE) $env->redirect
                      ( $env->url("admin/config"),
                        "la configuration a &eacute;t&eacute; enregistr&eacute;e"
                      );
                      else $env->erreur("Impossible d'enregistrer la configuration");
                    }
                    else $env->message("merci de pr&eacute;ciser un email pour le formulaire de contact");
                  }
                  else $env->message("la taille maximum des listes doit &ecirc;tre un nombre");
                }
              }
              else $env->erreur("Impossible de lire la liste des templates");
            }
            else $env->erreur("Impossible de lire l configuration du templates");
          }
          else $env->erreur("Impossible de lire la configuration");
        }
        else $env->erreur("Impossible de lire la liste des albums");
      }
      else $env->erreur("Impossible de lire la liste des groupes");
    }

  }

?>
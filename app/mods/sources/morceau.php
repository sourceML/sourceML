<?php

  class sml_sources_morceau extends sml_mod
  {
    var $content_sources_mod;

    function validate(&$env)
    { if($this->content_sources_mod = $env->get_mod("content/sources"))
      { $this->content_sources_mod->validation_result = true;
        $this->content_sources_mod->validate_status($env);
        $this->content_sources_mod->validate_licences($env);
        return $this->content_sources_mod->validation_result;
      }
      return "impossible de valider le module";
    }

    function index(&$env)
    { $data = $env->data();
      if($this->content_sources_mod->licences !== false)
      { $env->set_out("licences", $this->content_sources_mod->licences);
        if(($groupes = $data->groupes()) !== false)
        { $env->set_out("groupes", $groupes);
          $groupe = null;
          $album = null;
          if(isset($_GET[$env->param("groupe")]) && $_GET[$env->param("groupe")])
          { if(isset($groupes["list"][$_GET[$env->param("groupe")]]))
            { $groupe = $groupes["list"][$_GET[$env->param("groupe")]];
              $env->set_out("groupe", $groupe);
            }
            else $groupe = false;
          }
          if($groupe !== false)
          { if(isset($_GET[$env->param("album")]) && $_GET[$env->param("album")])
            { if(($album = $data->source($_GET[$env->param("album")], true)) !== false)
              { $env->set_out("album", $album);
                if(!isset($groupe))
                { $groupe = $data->get_admin_groupe($album["groupes"]);
                  if(!$groupe) $groupe = false;
                }
              }
            }
          }
          if($groupe !== false)
          { $env->set_out("groupe", $groupe);
            if($album !== false)
            { $select = array("status" => $this->content_sources_mod->album_status_id);
              if(isset($groupe)) $select["id_groupe"] = $groupe["id"];
              if(($albums = $data->sources($select)) !== false)
              { $env->set_out("albums", $albums);
                $select = array();
                $select["status"] = $this->content_sources_mod->morceau_status_id;
                $select["order_by"] = "ordre";
                if(isset($groupe)) $select["id_groupe"] = $groupe["id"];
                if(isset($_GET[$env->param("album")])) $select["id_composition"] = $_GET[$env->param("album")];
                $select["start"] = isset($_GET[$env->param("start")]) && $_GET[$env->param("start")] ? $_GET[$env->param("start")] : 0;
                if(($morceaux = $data->sources($select, true)) !== false) $env->set_out("morceaux", $morceaux);
                else $env->erreur("Impossible de lire la liste des morceaux");
              }
              else $env->erreur("impossible de lire la liste des albums");
            }
            else $env->erreur("Impossible de lire les informations de l'album");
          }
          else $env->erreur("Impossible de lire les informations du groupe");
        }
        else $env->erreur("Impossible de lire la liste des groupes");
      }
      else $env->erreur("Impossible de lire la liste des licences");
    }

    function view(&$env)
    { $data = $env->data();
      if($this->content_sources_mod->licences !== false)
      { $env->set_out("licences", $this->content_sources_mod->licences);
        if(isset($_GET[$env->param("morceau")]) && $_GET[$env->param("morceau")])
        { if($morceau = $data->source($_GET[$env->param("morceau")], true))
          { $env->set_out("morceau", $morceau);
            if($groupe = $data->get_admin_groupe($morceau["groupes"]))
            { $env->set_out("groupe", $groupe);
              $select = array("status" => $this->content_sources_mod->album_status_id);
              $select["id_groupe"] = $groupe["id"];
              if(($albums = $data->sources($select)) !== false)
              { $env->set_out("albums", $albums);
                if(($ariane = $data->source_ariane($morceau["id"])) !== false)
                { $ariane = array_reverse($ariane);
                  $album = null;
                  foreach($ariane as $id_ariane => $source_ariane)
                  { if(!isset($album)) $album = $source_ariane;
                  }
                  if($album) $env->set_out("album", $album);
                }
                else $env->erreur("Impossible de lire le fil d'ariane");
              }
              else $env->erreur("impossible de lire la liste des albums");
            }
            else $env->erreur("Impossible de lire les informations du groupe");
          }
          else $env->erreur("Impossible de lire les informations du morceau");
        }
        else $env->erreur("identifiant de morceau manquant");
      }
      else $env->erreur("Impossible de lire la liste des licences");
    }

  }

?>
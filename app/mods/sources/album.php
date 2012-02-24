<?php

  class sml_sources_album extends sml_mod
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
      if(isset($this->content_sources_mod->licences) && $this->content_sources_mod->licences !== false)
      { $env->set_out("licences", $this->content_sources_mod->licences);
        if(($groupes = $data->groupes()) !== false)
        { $env->set_out("groupes", $groupes);
          $select = array();
          $select["status"] = $this->content_sources_mod->album_status_id;
          $select["order_by"] = "ordre";
          if(isset($_GET[$env->param("groupe")]) && $_GET[$env->param("groupe")]) $select["id_groupe"] = $_GET[$env->param("groupe")];
          $select["start"] = isset($_GET[$env->param("start")]) && $_GET[$env->param("start")] ? $_GET[$env->param("start")] : 0;
          if(($albums = $data->sources($select)) !== false)
          { $env->set_out("albums", $albums);
            if(isset($_GET[$env->param("groupe")]) && $_GET[$env->param("groupe")])
            { if(($groupe = $data->groupe($_GET[$env->param("groupe")])) !== false)
              { $env->set_out("groupe", $groupe);
              }
              else $env->erreur("Impossible de lire les informations du groupe");
            }
          }
          else $env->erreur("Impossible de lire la liste des albums");
        }
        else $env->erreur("Impossible de lire la liste des groupes");
      }
    }


    function view(&$env)
    { $data = $env->data();
      if(isset($this->content_sources_mod->licences) && $this->content_sources_mod->licences !== false)
      { $env->set_out("licences", $this->content_sources_mod->licences);
        if(isset($_GET[$env->param("album")]) && $_GET[$env->param("album")])
        { if($album = $data->source($_GET[$env->param("album")], true))
          { $env->set_out("album", $album);
            if($groupe = $data->get_admin_groupe($album["groupes"]))
            { $env->set_out("groupe", $groupe);
              $select = array("status" => $this->content_sources_mod->album_status_id);
              $select["id_groupe"] = $groupe["id"];
              if(($albums = $data->sources($select)) !== false)
              { $env->set_out("albums", $albums);
                $select = array("status" => $this->content_sources_mod->morceau_status_id);
                $select["id_composition"] = $album["id"];
                $select["start"] = isset($_GET[$env->param("start")]) && $_GET[$env->param("start")] ? $_GET[$env->param("start")] : 0;
                if(($morceaux = $data->sources($select)) !== false)
                { $env->set_out("morceaux", $morceaux);
                }
                else $env->erreur("impossible de lire la liste des morceaux");
              }
              else $env->erreur("impossible de lire la liste des albums");
            }
            else $env->erreur("Impossible de lire les informations du groupe");
          }
          else $env->erreur("Impossible de lire les informations de l'album");
        }
        else $env->erreur("parametre d'album manquant");
      }
    }

  }

?>
<?php

  class sml_sources_groupe extends sml_mod
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
      if
      ( ( $groupes = $data->groupes
          ( null,
            isset($_GET[$env->param("start")]) ? $_GET[$env->param("start")] : 0
          )
        ) !== false
      ) $env->set_out("groupes", $groupes);
      else $env->erreur("Impossible de lire la liste des groupes");
    }

    function view(&$env)
    { $data = $env->data();
      if(isset($this->content_sources_mod->licences) && $this->content_sources_mod->licences !== false)
      { $env->set_out("licences", $this->content_sources_mod->licences);
        if(isset($_GET[$env->param("id")]) && $_GET[$env->param("id")])
        { if(($groupe = $data->groupe($_GET[$env->param("id")])) !== false)
          { $env->set_out("groupe", $groupe);
            $select = array("status" => $this->content_sources_mod->album_status_id);
            $select["id_groupe"] = $groupe["id"];
            if(($albums = $data->sources($select)) !== false)
            { $env->set_out("albums", $albums);
            }
            else $env->erreur("impossible de lire la liste des albums");
          }
          else $env->erreur("Impossible de lire les informations du groupe");
        }
        else $env->erreur("parametre de groupe manquant");
      }
      else $env->erreur("Impossible de lire la liste des licences");
    }

  }

?>
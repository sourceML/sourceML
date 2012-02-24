<?php

  class sml_content_sources extends sml_mod
  {
    var $status;
    var $album_status_id;
    var $morceau_status_id;
    var $piste_status_id;
    var $url_params;
    var $source_param;
    var $licences;

    var $groupe;
    var $source;
    var $source_controller;

    var $validation_result;

    function validate(&$env)
    { $this->validation_result = true;
      $this->validate_status($env);
      $this->validate_licences($env);
      return true;
    }

    function validate_status(&$env)
    { if($this->validation_result === true)
      { $data = $env->data();
        if(($this->status = $data->source_status()) !== false)
        { foreach($this->status as $id_source_status => $source_status)
          { if($source_status["nom"] == "album") $this->album_status_id = $id_source_status;
            if($source_status["nom"] == "morceau") $this->morceau_status_id = $id_source_status;
            if($source_status["nom"] == "piste") $this->piste_status_id = $id_source_status;
            if(isset($this->album_status_id) && isset($this->morceau_status_id) && isset($this->piste_status_id)) break;
          }
          if(!isset($this->album_status_id)) $this->validation_result = "Type de source inconnu: album";
          elseif(!isset($this->morceau_status_id)) $this->validation_result = "Type de source inconnu: morceau";
          elseif(!isset($this->piste_status_id)) $this->validation_result = "Type de source inconnu: piste";
        }
        else $this->validation_result = "Impossible de lire la liste des status de source";
      }
    }

    function validate_licences(&$env)
    { if($this->validation_result === true)
      { $data = $env->data();
        if(($this->licences = $data->licences()) !== false)
        { $env->set_out("licences", $this->licences);
        }
        else $this->validation_result = "Impossible de lire la liste des licences";
      }
    }

    function validate_source(&$env)
    { if($this->validation_result === true)
      { $data = $env->data();
        if(isset($_GET[$env->param("id")]) && $_GET[$env->param("id")])
        { if($this->source = $data->source($_GET[$env->param("id")]))
          {
          }
          else $this->validation_result = "Impossible de lire les informations de la source";
        }
        else $this->validation_result = "parametre source manquant";
      }
    }

    function validate_groupes(&$env)
    { if($this->validation_result === true)
      { $data = $env->data();
        if(($groupes = $data->groupes()) !== false)
        { $env->set_out("groupes", $groupes);
          if(isset($_GET[$env->param("groupe")]) && $_GET[$env->param("groupe")])
          { if(($this->groupe = $data->groupe($_GET[$env->param("groupe")])) !== false)
            { $env->set_out("groupe", $groupe);
            }
            else $this->validation_result = "Impossible de lire les informations du groupe";
          }
        }
        else $this->validation_result = "Impossible de lire la liste des groupes";
      }
    }

    function sources(&$env)
    { $this->validate_source($env);
      if($this->validation_result === true)
      { $data = $env->data();
        $sources = array("list" => array(), "total" => 0);
        if($this->source)
        { if(($source_ariane = $data->source_ariane($this->source["id"])) !== false)
          { $this->url_params = array();
            foreach($source_ariane as $id_composition => $composition)
            { if($composition["status"] == $this->morceau_status_id) $this->url_params["morceau"] = $id_composition;
              elseif($composition["status"] == $this->album_status_id) $this->url_params["album"] = $id_composition;
            }
            if($this->source["status"] == $this->morceau_status_id)
            { $this->source_param = $env->set_out("source_param", "piste");
              $this->source_controller = $env->set_out("source_controller", "piste");
              $this->url_params["morceau"] = $this->source["id"];
            }
            elseif($this->source["status"] == $this->album_status_id)
            { $this->source_param = $env->set_out("source_param", "morceau");
              $this->source_controller = $env->set_out("source_controller", "morceau");
              $this->url_params["album"] = $this->source["id"];
            }
            $env->set_out("url_params", $this->url_params);
            if(($_sources = $data->source_compositions(array("id_composition" => $this->source["id"]))) !== false)
            { if(isset($_sources[$this->source["id"]]) && $_sources[$this->source["id"]])
              { foreach($_sources[$this->source["id"]] as $id_source)
                { if(($sources["list"][$id_source] = $data->source($id_source, true)) !== false)
                  { $sources["total"]++;
                  }
                  else
                  { $this->erreur("Impossible de lire les informations de l'une des sources");
                    break;
                  }
                }
              }
              $sources["list"] = $data->ordonne($sources["list"], "ordre");
              $env->set_out("sources", $sources);
            }
            else $this->erreur("Impossible de lire la liste des sources");
          }
          else $this->erreur("Impossible de lire le fil d'ariane");
        }
        else $this->erreur("Impossible de lire les informations de la source");
      }
      else $this->erreur($this->validation_result);
    }

    function derivations(&$env)
    { $this->validate_source($env);
      if($this->validation_result === true)
      { $data = $env->data();
        $derivations = array("list" => array(), "total" => 0);
        if($this->source)
        { if(($source_ariane = $data->source_ariane($this->source["id"])) !== false)
          { $this->url_params = array();
            foreach($source_ariane as $id_composition => $composition)
            { if($composition["status"] == $this->morceau_status_id) $this->url_params["morceau"] = $id_composition;
              elseif($composition["status"] == $this->album_status_id) $this->url_params["album"] = $id_composition;
            }
            if($this->source["status"] == $this->piste_status_id)
            { $this->source_param = $env->set_out("source_param", "piste");
              $this->source_controller = $env->set_out("source_controller", "piste");
              $this->url_params["piste"] = $this->source["id"];
            }
            elseif($this->source["status"] == $this->morceau_status_id)
            { $this->source_param = $env->set_out("source_param", "morceau");
              $this->source_controller = $env->set_out("source_controller", "morceau");
              $this->url_params["morceau"] = $this->source["id"];
            }
            $env->set_out("url_params", $this->url_params);
            if(($_derivations = $data->source_derivations(array("derivation" => $this->source["id"]))) !== false)
            { foreach($_derivations as $id_derivation => $derivation)
              { if(($derivations["list"][$id_derivation] = $data->source($id_derivation, true)) !== false)
                { $derivations["total"]++;
                }
                else
                { $this->erreur("Impossible de lire les informations de l'une des derivations");
                  break;
                }
              }
              $derivations["list"] = $data->ordonne($derivations["list"], "ordre");
              $env->set_out("sources", $derivations);
            }
            else $this->erreur("Impossible de lire la liste des derivations");
          }
          else $this->validation_result = "Impossible de lire le fil d'ariane";
        }
        else $this->erreur("Impossible de lire les informations de la source");
      }
      else $this->erreur($this->validation_result);
    }

    function xml_form(&$env)
    { if(isset($_GET[$env->param("form")]))
      { $data = $env->data();
        $form_params = array();
        if(isset($_GET[$env->param("derivation")]))
        { if($_GET[$env->param("form")] == "edit")
          { if(($source_derivation = $data->source_derivation($_GET[$env->param("derivation")])) !== false)
            { $source = array
              ( "id" => $source_derivation["id"],
                "xml" => array
                ( "url" => $source_derivation["derivation"],
                  "content" => "",
                  "use_edit_content" => false
                )
              );
              $derivation_edit_file = $data->derivation_edit_xml_path($source_derivation["id_source"], $source_derivation["id"]);
              if(file_exists($derivation_edit_file))
              { if(($derivation_edit_content = $data->get_edit_derivation_content($source_derivation["id_source"], $source_derivation["id"])) !== false)
                { if(($source = $data->source_xml_read($source_derivation["derivation"], $derivation_edit_content)) !==false)
                  { $source["xml"] = array
                    ( "url" => $source_derivation["derivation"],
                      "content" => $derivation_edit_content,
                      "use_edit_content" => true
                    );
                  }
                  else $this->erreur("Impossible de lire le XML de la source");
                }
                else $this->erreur("Impossible de lire le XML de la source");
              }
              else
              { if(($source = $data->source_xml_read($source_derivation["derivation"])) === false)
                { $source = $data->empty_source();
                }
              }
              $source["id_source"] = $source_derivation["id_source"];
              $source["id"] = $_GET[$env->param("derivation")];
              $form_params["maj_url"] = $env->url("users/morceaux/maj_xml", array("id" => $_GET[$env->param("derivation")], "xml" => "derivation"));
            }
          }
          elseif($_GET[$env->param("form")] == "add")
          { $source = array
            ( "id" => $_GET[$env->param("derivation")],
              "xml" => array()
            );
          }
          $form_params["name"] = "derivation";
          $form_params["label"] = "d&eacute;rive de &raquo; ";
          $form_params["can_delete"] = true;
          $form_params["id"] = $_GET[$env->param("derivation")];
        }
        elseif(isset($_GET[$env->param("reference")]))
        { 
        }
        $env->set_out("form_params", $form_params);
        $env->set_out("xml_form_source", $source);
      }
      else $this->erreur("Parametres invalides pour le formulaire");
    }

    function xml(&$env)
    { $this->validate_source($env);
      if($this->validation_result === true)
      { $data = $env->data();
        $xml = "";
        if($this->source)
        { if($this->source = $data->load_source($this->source))
          { $xml =
             "<pre>"
            .htmlentities
            ( $this->source["reference"] ? $this->source["reference"]["xml"]["content"] : $this->source["xml"]["content"],
              ENT_COMPAT,
              "UTF-8"
            )
            ."</pre>";
          }
          else $this->erreur("Impossible de lire les informations XML de la source");
        }
        else $this->erreur("Impossible de lire les informations de la source");
      }
      else $this->erreur($this->validation_result);
      echo $xml;
      exit;
    }

    function erreur($content)
    { echo $content;
      exit;
    }

  }

?>
<?php

  class sml_users_morceaux extends sml_mod
  {
    var $groupes;
    var $albums;
    var $morceau;
    var $user;

    var $status;
    var $album_status_id;
    var $morceau_status_id;

    function validate(&$env)
    { $data = $env->data();
      if(($this->status = $data->source_status()) !== false)
      { foreach($this->status as $id_source_status => $source_status)
        { if($source_status["nom"] == "album") $this->album_status_id = $id_source_status;
          if($source_status["nom"] == "morceau") $this->morceau_status_id = $id_source_status;
          if(isset($this->album_status_id) && isset($this->morceau_status_id)) break;
        }
        if(isset($this->album_status_id) && isset($this->morceau_status_id))
        { if($this->user = $env->user())
          { if(($this->groupes = $data->groupes($this->user["id"])) !== false)
            { $env->set_out("groupes", $this->groupes);
              $this->albums = array();
              if($this->groupes["total"] > 0)
              { $select = array();
                $select["status"] = $this->album_status_id;
                $select["id_user"] = $this->user["id"];
                foreach($this->groupes["list"] as $id_groupe => $groupe)
                { $select["id_groupe"] = $id_groupe;
                  if(($albums = $data->sources($select)) !== false)
                  { $this->albums[$id_groupe] = $albums["list"];
                  }
                  else $this->albums = false;
                }
              }
              if($this->albums !== false)
              { $env->set_out("albums", $this->albums);
                if($env->etat("action") == "edit" || $env->etat("action") == "del" || $env->etat("action") == "maj_xml")
                { if($this->morceau = $data->source($_GET[$env->param("id")], true))
                  { 
                  }
                  else return "Impossible de lire les informations du morceau";
                }
                if($env->etat("action") == "add" || $env->etat("action") == "edit")
                { if(($this->licences = $data->licences()) !== false)
                  { $env->set_out("licences", $this->licences);
                  }
                  else return "Impossible de lire la liste des licences";
                }
              }
              else return "Impossible de lire la liste des albums";
            }
            else return "Impossible de lire la liste des groupes";
          }
          else return "Vous devez &ecirc;tre identifier pour acc&eacute;der &agrave; cette page";
        }
        else
        { if(!isset($this->album_status_id)) return "Type de source inconnu: album";
          return "Type de source inconnu: morceau";
        }
      }
      else return "Impossible de lire la liste des status de source";
      return true;
    }

    function index(&$env)
    { $data = $env->data();
      $select = array();
      $select["status"] = $this->morceau_status_id;
      $select["id_user"] = $this->user["id"];
      $select["order_by"] = "ordre";
      $select["start"] = isset($_GET[$env->param("start")]) && $_GET[$env->param("start")] ? $_GET[$env->param("start")] : 0;
      if(isset($_GET[$env->param("groupe")]) && $_GET[$env->param("groupe")]) $select["id_groupe"] = $_GET[$env->param("groupe")];
      if(isset($_GET[$env->param("album")])) $select["id_composition"] = $_GET[$env->param("album")];
      if(($morceaux = $data->sources($select)) !== false)
      { foreach($morceaux["list"] as $id_morceau => $morceau)
        { $morceaux["list"][$id_morceau]["permissions"] = $data->source_permissions($morceau, $this->user["id"]);
        }
        if($_POST)
        { $OK = true;
          foreach($morceaux["list"] as $id_morceau => $morceau)
          { if(isset($_POST["ordre_".$id_morceau]))
            { if($data->set_source_info($morceau["id"], "ordre", $_POST["ordre_".$id_morceau]) === false)
              { $OK = false;
                break;
              }
            }
          }
          if($OK)
          { $get_params = array();
            if(isset($_GET[$env->param("groupe")]) && $_GET[$env->param("groupe")]) $get_params["groupe"] = $_GET[$env->param("groupe")];
            if(isset($_GET[$env->param("album")]) && $_GET[$env->param("album")]) $get_params["album"] = $_GET[$env->param("album")];
            $env->redirect
            ( $env->url("users/morceaux", $get_params),
              "l'ordre des morceaux a &eacute;t&eacute; enregistr&eacute;"
            );
          }
          else $env->erreur("Impossible d'enregistrer l'ordre des morceaux");
        }
        $env->set_out("morceaux", $morceaux);
      }
      else $env->erreur("Impossible de lire la liste des morceaux");
    }

    function add(&$env)
    { $data = $env->data();
      if($data->check_user_uploads_dir())
      { $web_path = $env->path("web");
        $_SESSION["upload_dir"] = $web_path.($web_path ? "" : "/").$env->path("content")."uploads/".$this->user["id"];
        $this->morceau = $data->empty_source();
        $users_sources_mod = $env->get_mod("users/sources");
        $source_infos = array
        ( "date_inscription" => date("Y")."-".date("m")."-".date("d"),
          "ordre" => 0
        );
        if($_POST)
        { if(($groupe = $data->groupe($_POST["id_groupe"])) !== false)
          { $groupe["id_groupe_status"] = $data->id_groupe_status_admin();
            $this->morceau["groupes"] = array($groupe["id"] => $groupe);
            if($_POST["album"])
            { if($album = $data->source($_POST["album"], true))
              { $album["permissions"] = $data->source_permissions($album, $this->user["id"]);
                if(!$album["permissions"]["contributeur"])
                { $env->erreur("vous n'avez pas la permission d'ajouter un morceau dans cet album");
                  return;
                }
              }
              else
              { $env->erreur("impossible de lire les informations de l'album");
                return;
              }
              $this->morceau["album"] = $_POST["album"];
            }
            if($_POST["is_derivation"])
            { foreach($_POST as $key => $value)
              { if(substr($key, 0, 14) == "derivation_id_")
                { $id_source_derivation = substr($key, 14);
                  $xml_url = trim($_POST["derivation_".$id_source_derivation]);
                  $this->morceau["derivations"][$id_source_derivation] = $users_sources_mod->get_source_from_xml
                  ( $env,
                    $xml_url,
                    ( $_POST["use_edit_derivation_content_".$id_source_derivation] ?
                        $_POST["edit_derivation_content_".$id_source_derivation]
                      : ($xml_url ? $data->get_source_xml_from_url($xml_url) : "")
                    ),
                    $_POST["use_edit_derivation_content_".$id_source_derivation] ? true : false
                  );
                }
              }
            }
            if($_POST["is_reference"])
            { $xml_url = trim($_POST["reference"]);
              $this->morceau["reference"] = $users_sources_mod->get_source_from_xml
              ( $env,
                $xml_url,
                ( $_POST["use_edit_reference_content"] ?
                    $_POST["edit_reference_content"]
                  : ($xml_url ? $data->get_source_xml_from_url($xml_url) : "")
                ),
                $_POST["use_edit_reference_content"] ? true : false
              );
            }
            else
            { if($this->morceau["titre"] = trim($_POST["titre"]))
              { $this->morceau["licence"] = array
                ( "id" => $_POST["licence"]
                );
                $source_infos["date_creation"] = $_POST["annee_date_creation"]."-".$_POST["mois_date_creation"]."-".$_POST["jour_date_creation"];
                $source_infos["description"] = $_POST["description"];
                foreach($_POST as $post_key => $post_value)
                { if(substr($post_key, 0, 13) == "document_nom_")
                  { if($id_document = substr($post_key, 13))
                    { $this->morceau["documents"][$id_document] = array
                      ( "nom" => $_POST["document_nom_".$id_document],
                        "url" => $_POST["document_url_".$id_document]
                      );
                    }
                  }
                }
              }
              else $env->message("merci de pr&eacute;ciser un titre pour le morceau");
            }
            if(!$env->out("messages") && !$env->out("erreur"))
            { if
              ( ( $id_source = $data->add_source
                  ( $this->morceau["groupes"],
                    $this->morceau["titre"],
                    $this->morceau_status_id,
                    $this->morceau["licence"]["id"],
                    $this->morceau["documents"],
                    $this->morceau["reference"],
                    $this->morceau["derivations"],
                    $source_infos
                  )
                ) !== false
              )
              { if($_POST["album"])
                { if($data->set_source_composition($id_source, $_POST["album"]))
                  { $env->redirect
                    ( $env->url("users/morceaux"),
                      "le morceau a &eacute;t&eacute; ajout&eacute;"
                    );
                  }
                  else $env->erreur("Le morceau a &eacute;t&eacute; ajout&eacute; mais impossible de l'associer &agrave; cet album");
                }
                else $env->redirect
                ( $env->url("users/morceaux"),
                  "le morceau a &eacute;t&eacute; ajout&eacute;"
                );
              }
              else $env->erreur("Impossible d'ajouter le morceau");
            }
          }
          else $env->erreur("Impossible de lire les informations du groupe");
        }
        $env->set_out("morceau", $this->morceau);
      }
      else $env->erreur("Impossible de creer le repertoire utilisateur");
    }

    function edit(&$env)
    { $data = $env->data();
      $this->morceau["premissions"] = $data->source_permissions($this->morceau, $this->user["id"]);
      if(!$this->morceau["premissions"]["editeur"])
      { $env->erreur("vous n'avez la permission d'editer ce morceau");
        return;
      }
      if($data->check_user_uploads_dir())
      { $web_path = $env->path("web");
        $_SESSION["upload_dir"] = $web_path.($web_path ? "" : "/").$env->path("content")."uploads/".$this->user["id"];
        if(($compositions = $data->source_compositions(array("id_source" => $this->morceau["id"]))) !== false)
        { if($compositions) foreach($compositions[$this->morceau["id"]] as $id_album) { $this->morceau["album"] = $id_album; break; }
          $env->set_out("groupe", $data->get_admin_groupe($this->morceau["groupes"]));
          if($_POST)
          { $source_infos = array
            ( "date_inscription" => $_POST["date_inscription"],
              "ordre" => isset($this->morceau["ordre"]) ? $this->morceau["ordre"] : 0
            );
            $this->morceau = $data->empty_source(array("id" => $this->morceau["id"]));
            $users_sources_mod = $env->get_mod("users/sources");
            if(!($groupe = $data->groupe($_POST["id_groupe"])))
            { $env->erreur("Impossible de lire les informations du groupe");
              return;
            }
            $env->set_out("groupe", $groupe);
            $groupe["id_groupe_status"] = $data->id_groupe_status_admin();
            $this->morceau["groupes"] = array($groupe["id"] => $groupe);
            $this->morceau["derivations"] = array();
            if($_POST["album"])
            { if($album = $data->source($_POST["album"], true))
              { $album["permissions"] = $data->source_permissions($album, $this->user["id"]);
                if(!$album["permissions"]["contributeur"])
                { $env->erreur("vous n'avez pas la permission d'ajouter un morceau dans cet album");
                  return;
                }
              }
              else
              { $env->erreur("Impossible de lire les informations de l'album");
                return;
              }
              $this->morceau["album"] = $_POST["album"];
            }
            if($_POST["is_derivation"])
            { foreach($_POST as $key => $value)
              { if(substr($key, 0, 14) == "derivation_id_")
                { $id_source_derivation = substr($key, 14);
                  $xml_url = trim($_POST["derivation_".$id_source_derivation]);
                  $this->morceau["derivations"][$id_source_derivation] = $users_sources_mod->get_source_from_xml
                  ( $env,
                    $xml_url,
                    ( $_POST["use_edit_derivation_content_".$id_source_derivation] ?
                        $_POST["edit_derivation_content_".$id_source_derivation]
                      : ($xml_url ? $data->get_source_xml_from_url($xml_url) : "")
                    ),
                    $_POST["use_edit_derivation_content_".$id_source_derivation] ? true : false
                  );
                }
              }
            }
            if($_POST["is_reference"])
            { $xml_url = trim($_POST["reference"]);
              $this->morceau["reference"] = $users_sources_mod->get_source_from_xml
              ( $env,
                $xml_url,
                ( $_POST["use_edit_reference_content"] ?
                    $_POST["edit_reference_content"]
                  : ($xml_url ? $data->get_source_xml_from_url($xml_url) : "")
                ),
                $_POST["use_edit_reference_content"] ? true : false
              );
            }
            else
            { if($this->morceau["titre"] = trim($_POST["titre"]))
              { $this->morceau["licence"] = array
                ( "id" => $_POST["licence"]
                );
                $source_infos["date_creation"] = $_POST["annee_date_creation"]."-".$_POST["mois_date_creation"]."-".$_POST["jour_date_creation"];
                $source_infos["description"] = $_POST["description"];
                foreach($_POST as $post_key => $post_value)
                { if(substr($post_key, 0, 13) == "document_nom_")
                  { if($id_document = substr($post_key, 13))
                    { $this->morceau["documents"][$id_document] = array
                      ( "nom" => $_POST["document_nom_".$id_document],
                        "url" => $_POST["document_url_".$id_document]
                      );
                    }
                  }
                }
              }
              else $env->message("merci de pr&eacute;ciser un titre pour le morceau");
            }
            if(!$env->out("messages") && !$env->out("erreur"))
            { if
              ( ( $data->set_source
                  ( $this->morceau["id"],
                    $this->morceau["groupes"],
                    $this->morceau["titre"],
                    $this->morceau_status_id,
                    $this->morceau["licence"]["id"],
                    $this->morceau["documents"],
                    $this->morceau["reference"],
                    $this->morceau["derivations"],
                    $source_infos
                  )
                ) !== false
              )
              { if($data->del_source_compositions(array("id_source" => $this->morceau["id"])))
                { if($_POST["album"])
                  { if($data->set_source_composition($this->morceau["id"], $_POST["album"]))
                    { $env->redirect
                      ( $env->url("users/morceaux/edit", array("id" => $this->morceau["id"])),
                        "le morceau a &eacute;t&eacute; modifi&eacute;"
                      );
                    }
                    else $env->erreur("Le morceau a &eacute;t&eacute; modifi&eacute; mais impossible de l'associer &agrave; cet album");
                  }
                  else $env->redirect
                  ( $env->url("users/morceaux/edit", array("id" => $this->morceau["id"])),
                    "le morceau a &eacute;t&eacute; modifi&eacute;"
                  );
                }
                else $env->erreur("Le morceau a &eacute;t&eacute; modifi&eacute; mais impossible de l'associer &agrave; cet album");
              }
              else $env->erreur("Impossible de modifier le morceau");
            }
          }
        }
        else $env->erreur("Impossible de lire la liste des compositions");
        $env->set_out("morceau", $this->morceau);
      }
      else $env->erreur("Impossible de creer le repertoire utilisateur");
    }

    function maj_xml(&$env)
    { $data = $env->data();
      $this->morceau["premissions"] = $data->source_permissions($this->morceau, $this->user["id"]);
      if(!$this->morceau["premissions"]["editeur"])
      { $env->erreur("vous n'avez la permission d'editer ce morceau");
        return;
      }
      if
      (    isset($_GET[$env->param("id")])
        && isset($_GET[$env->param("xml")])
        && ($_GET[$env->param("xml")] == "derviation" ? isset($_GET[$env->param("derivation")]) : true)
      )
      { $xml_url = "";
        if($_GET[$env->param("xml")] == "derivation")
        { if(isset($this->morceau["derivations"][$_GET[$env->param("derivation")]]["xml"]["url"]))
          $xml_url = $this->morceau["derivations"][$_GET[$env->param("derivation")]]["xml"]["url"];
        }
        elseif($_GET[$env->param("xml")] == "reference")
        { if(isset($this->morceau["reference"]["xml"]["url"]))
          $xml_url = $this->morceau["reference"]["xml"]["url"];
        }
        $erreur = "";
        if($xml_url)
        { if(($res = $data->maj_source_cache($xml_url)) !== false)
          { if($res === true)
            { if($this->morceau = $data->source($this->morceau["id"]))
              { $env->redirect
                ( $env->url("users/morceaux/edit", array("id" => $this->morceau["id"])),
                  "Les informations du fichier XML ont &eacute;t&eacute; mises &agrave; jour",
                  2
                );
                return;
              }
              else $erreur =
               "le contenu du fichier XML a &eacute;t&eacute; mis &agrave; jour"
              .", mais impossible de lire les informations du morceau";
            }
            else
            { switch($res)
              { case -1: $env->message("Impossible de lire le contenu du fichier XML"); break;
                case -2: $env->message("Le fichier XML indiqu&eacute; ne contient pas un fichier de source valide"); break;
                default: $erreur = "Erreur inconnue (?)"; break;
              }
            }
          }
          else $erreur = "Impossible de mettre &agrave; jour le contenu du fichier XML dans le cache";
        }
        else $env->message("Pas d'URL pour ce fichier XML. Impossible de recharger les informations");
        if($erreur) $env->erreur($erreur);
        else $env->run("users/morceaux/edit", array("id" => $this->morceau["id"]));
      }
      else $env->erreur("parametre de fichier xml manquant");
    }

    function del(&$env)
    { $data = $env->data();
      $this->morceau["premissions"] = $data->source_permissions($this->morceau, $this->user["id"]);
      if($this->morceau["premissions"]["admin"])
      { if($data->del_source($this->morceau["id"]))
        { $env->redirect
          ( $env->url("users/morceaux"),
            "le morceau a &eacute;t&eacute; supprim&eacute;"
          );
        }
        else $env->erreur("Impossible de supprimer le morceau");
      }
      else $env->erreur("vous n'avez la permission d'effacer ce morceau");
    }

  }

?>
<?php

  class sml_users_pistes extends sml_mod
  {
    var $groupes;
    var $albums;
    var $morceaux;
    var $piste;
    var $user;

    var $status;
    var $album_status_id;
    var $morceau_status_id;
    var $piste_status_id;

    function validate(&$env)
    { $data = $env->data();
      if(($this->status = $data->source_status()) !== false)
      { foreach($this->status as $id_source_status => $source_status)
        { if($source_status["nom"] == "album") $this->album_status_id = $id_source_status;
          if($source_status["nom"] == "morceau") $this->morceau_status_id = $id_source_status;
          if($source_status["nom"] == "piste") $this->piste_status_id = $id_source_status;
          if(isset($this->album_status_id) && isset($this->morceau_status_id) && isset($this->piste_status_id)) break;
        }
        if(isset($this->album_status_id) && isset($this->morceau_status_id) && isset($this->piste_status_id))
        { if($this->user = $env->user())
          { if(($this->groupes = $data->groupes($this->user["id"])) !== false)
            { $env->set_out("groupes", $this->groupes);
              $this->albums = array();
              $this->morceaux = array();
              if($this->groupes["total"] > 0)
              { foreach($this->groupes["list"] as $id_groupe => $groupe)
                { $this->albums[$id_groupe] = array();
                  $select = array
                  ( "status" => $this->album_status_id,
                    "id_user" => $this->user["id"],
                    "id_groupe" => $id_groupe
                  );
                  if(($albums = $data->sources($select)) !== false)
                  { $this->albums[$id_groupe] = $albums["list"];
                  }
                  else
                  { $this->albums = false;
                    break;
                  }

                  $this->morceaux[$id_groupe] = array();
                  $select = array
                  ( "status" => $this->morceau_status_id,
                    "id_user" => $this->user["id"],
                    "id_groupe" => $id_groupe
                  );
                  if(($morceaux = $data->sources($select)) !== false)
                  { foreach($morceaux["list"] as $id_morceau => $morceau)
                    { if(($compositions = $data->source_compositions(array("id_source" => $id_morceau))) !== false)
                      { if($compositions) foreach($compositions[$id_morceau] as $_id_album) { $morceaux["list"][$id_morceau]["album"] = $_id_album; break; }
                        if(!isset($morceaux["list"][$id_morceau]["album"])) $morceaux["list"][$id_morceau]["album"] = 0;
                      }
                      else
                      { $this->morceaux = false;
                        break;
                      }
                    }
                    if($this->morceaux !== false)
                    { foreach($morceaux["list"] as $id_morceau => $morceau)
                      { if(!isset($this->morceaux[$id_groupe][$morceau["album"]])) $this->morceaux[$id_groupe][$morceau["album"]] = array();
                        $this->morceaux[$id_groupe][$morceau["album"]][$id_morceau] = $morceau;
                      }
                    }
                  }
                  else
                  { $this->morceaux = false;
                    break;
                  }
                  if($this->morceaux === false) break;
                }
              }
              if($this->albums !== false && $this->morceaux !== false)
              { $env->set_out("albums", $this->albums);
                $env->set_out("morceaux", $this->morceaux);
                if($env->etat("action") == "edit" || $env->etat("action") == "del" || $env->etat("action") == "maj_xml")
                { if(($this->piste = $data->source($_GET[$env->param("id")], true)) !== false && $this->piste)
                  {
                  }
                  else return "Impossible de lire les informations de la source";
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
      $select["status"] = $this->piste_status_id;
      $select["id_user"] = $this->user["id"];
      $select["order_by"] = "ordre";
      if(isset($_GET[$env->param("groupe")]) && $_GET[$env->param("groupe")]) $select["id_groupe"] = $_GET[$env->param("groupe")];
      if(isset($_GET[$env->param("morceau")]) && $_GET[$env->param("morceau")]) $select["id_composition"] = $_GET[$env->param("morceau")];
      $select["start"] = isset($_GET[$env->param("start")]) && $_GET[$env->param("start")] ? $_GET[$env->param("start")] : 0;
      if(($pistes = $data->sources($select)) !== false)
      { foreach($pistes["list"] as $id_piste => $piste)
        { $pistes["list"][$id_piste]["permissions"] = $data->source_permissions($piste, $this->user["id"]);
        }
        if($_POST)
        { $OK = true;
          foreach($pistes["list"] as $id_piste => $piste)
          { if(isset($_POST["ordre_".$id_piste]))
            { if($data->set_source_info($piste["id"], "ordre", $_POST["ordre_".$id_piste]) === false)
              { $OK = false;
                break;
              }
            }
          }
          if($OK)
          { $get_params = array();
            if(isset($_GET[$env->param("groupe")]) && $_GET[$env->param("groupe")]) $get_params["groupe"] = $_GET[$env->param("groupe")];
            if(isset($_GET[$env->param("morceau")]) && $_GET[$env->param("morceau")]) $get_params["morceau"] = $_GET[$env->param("morceau")];
            $env->redirect
            ( $env->url("users/pistes", $get_params),
              "l'ordre des sources a &eacute;t&eacute; enregistr&eacute;"
            );
          }
          else $env->erreur("Impossible d'enregistrer l'ordre des sources");
        }
        $env->set_out("pistes", $pistes);
      }
      else $env->erreur("Impossible de lire la liste des sources");
    }

    function add(&$env)
    { $data = $env->data();
      if($data->check_user_uploads_dir())
      { $web_path = $env->path("web");
        $_SESSION["upload_dir"] = $web_path.($web_path ? "" : "/").$env->path("content")."uploads/".$this->user["id"];
        $this->piste = $data->empty_source();
        $users_sources_mod = $env->get_mod("users/sources");
        $source_infos = array
        ( "date_inscription" => date("Y")."-".date("m")."-".date("d"),
          "ordre" => 0
        );
        if($_POST)
        { if(($groupe = $data->groupe($_POST["id_groupe"])) !== false)
          { $groupe["id_groupe_status"] = $data->id_groupe_status_admin();
            $this->piste["groupes"] = array($groupe["id"] => $groupe);
            $env->set_out("groupe", $groupe);
          }
          else
          { $env->erreur("Impossible de lire les informations du groupe");
            return;
          }
          if($_POST["morceau"])
          { if($morceau = $data->source($_POST["morceau"], true))
            { $morceau["permissions"] = $data->source_permissions($morceau, $this->user["id"]);
              if(!$morceau["permissions"]["contributeur"])
              { $env->erreur("vous n'avez pas la permission d'ajouter une source dans ce morceau");
                return;
              }
            }
            else
            { $env->erreur("impossible de lire les informations du morceau");
              return;
            }
            $this->piste["morceau"] = $_POST["morceau"];
          }
          if($_POST["is_derivation"])
          { foreach($_POST as $key => $value)
            { if(substr($key, 0, 14) == "derivation_id_")
              { $id_source_derivation = substr($key, 14);
                $xml_url = trim($_POST["derivation_".$id_source_derivation]);
                $this->piste["derivations"][$id_source_derivation] = $users_sources_mod->get_source_from_xml
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
            $this->piste["reference"] = $users_sources_mod->get_source_from_xml
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
          { if($this->piste["titre"] = trim($_POST["titre"]))
            { $this->piste["licence"] = array
              ( "id" => $_POST["licence"]
              );
              $source_infos["date_creation"] = $_POST["annee_date_creation"]."-".$_POST["mois_date_creation"]."-".$_POST["jour_date_creation"];
              $source_infos["description"] = $_POST["description"];
              foreach($_POST as $post_key => $post_value)
              { if(substr($post_key, 0, 13) == "document_nom_")
                { if($id_document = substr($post_key, 13))
                  { $this->piste["documents"][$id_document] = array
                    ( "nom" => $_POST["document_nom_".$id_document],
                      "url" => $_POST["document_url_".$id_document]
                    );
                  }
                }
              }
            }
            else $env->message("merci de pr&eacute;ciser un titre pour la source");
          }
          if(!$env->out("messages") && !$env->out("erreur"))
          { if
            ( ( $id_source = $data->add_source
                ( $this->piste["groupes"],
                  $this->piste["titre"],
                  $this->piste_status_id,
                  $this->piste["licence"]["id"],
                  $this->piste["documents"],
                  $this->piste["reference"],
                  $this->piste["derivations"],
                  $source_infos
                )
              ) !== false
            )
            { if($_POST["morceau"])
              { if($data->set_source_composition($id_source, $_POST["morceau"]))
                { $env->redirect
                  ( $env->url("users/pistes"),
                    "la source a &eacute;t&eacute; ajout&eacute;e"
                  );
                }
                else $env->erreur("La source a &eacute;t&eacute; ajout&eacute;e mais impossible de l'associer &agrave; ce morceau");
              }
              else $env->redirect
              ( $env->url("users/pistes"),
                "la source a &eacute;t&eacute; ajout&eacute;e"
              );
            }
            else $env->erreur("Impossible d'ajouter la source");
          }
        }
        $env->set_out("piste", $this->piste);
      }
      else $env->erreur("Impossible de creer le repertoire utilisateur");
    }

    function edit(&$env)
    { $data = $env->data();
      $this->piste["premissions"] = $data->source_permissions($this->piste, $this->user["id"]);
      if(!$this->piste["premissions"]["editeur"])
      { $env->erreur("vous n'avez la permission d'editer cette source");
        return;
      }
      if($data->check_user_uploads_dir())
      { $web_path = $env->path("web");
        $_SESSION["upload_dir"] = $web_path.($web_path ? "" : "/").$env->path("content")."uploads/".$this->user["id"];
        if(($compositions = $data->source_compositions(array("id_source" => $this->piste["id"]))) !== false)
        { if($compositions) foreach($compositions[$this->piste["id"]] as $id_morceau) { $this->piste["morceau"] = $id_morceau; break; }
          $env->set_out("groupe", $data->get_admin_groupe($this->piste["groupes"]));
          if($_POST)
          { $source_infos = array
            ( "date_inscription" => $_POST["date_inscription"],
              "ordre" => isset($this->piste["ordre"]) ? $this->piste["ordre"] : 0
            );
            $this->piste = $data->empty_source(array("id" => $this->piste["id"]));
            $users_sources_mod = $env->get_mod("users/sources");
            if(!($groupe = $data->groupe($_POST["id_groupe"])))
            { $env->erreur("Impossible de lire les informations du groupe");
              return;
            }
            $env->set_out("groupe", $groupe);
            $groupe["id_groupe_status"] = $data->id_groupe_status_admin();
            $this->piste["groupes"] = array($groupe["id"] => $groupe);
            $this->piste["derivations"] = array();
            if($_POST["morceau"])
            { if($morceau = $data->source($_POST["morceau"], true))
              { $morceau["permissions"] = $data->source_permissions($morceau, $this->user["id"]);
                if(!$morceau["permissions"]["contributeur"])
                { $env->erreur("vous n'avez pas la permission d'ajouter une source dans ce morceau");
                  return;
                }
              }
              else
              { $env->erreur("impossible de lire les informations du morceau");
                return;
              }
              $this->piste["morceau"] = $_POST["morceau"];
            }
            if($_POST["is_derivation"])
            { foreach($_POST as $key => $value)
              { if(substr($key, 0, 14) == "derivation_id_")
                { $id_source_derivation = substr($key, 14);
                  $xml_url = trim($_POST["derivation_".$id_source_derivation]);
                  $this->piste["derivations"][$id_source_derivation] = $users_sources_mod->get_source_from_xml
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
              $this->piste["reference"] = $users_sources_mod->get_source_from_xml
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
            { if($this->piste["titre"] = trim($_POST["titre"]))
              { $this->piste["licence"] = array
                ( "id" => $_POST["licence"]
                );
                $source_infos["date_creation"] = $_POST["annee_date_creation"]."-".$_POST["mois_date_creation"]."-".$_POST["jour_date_creation"];
                $source_infos["description"] = $_POST["description"];
                foreach($_POST as $post_key => $post_value)
                { if(substr($post_key, 0, 13) == "document_nom_")
                  { if($id_document = substr($post_key, 13))
                    { $this->piste["documents"][$id_document] = array
                      ( "nom" => $_POST["document_nom_".$id_document],
                        "url" => $_POST["document_url_".$id_document]
                      );
                    }
                  }
                }
              }
              else $env->message("merci de pr&eacute;ciser un titre pour la source");
            }
            if(!$env->out("messages") && !$env->out("erreur"))
            { if
              ( ( $data->set_source
                  ( $this->piste["id"],
                    $this->piste["groupes"],
                    $this->piste["titre"],
                    $this->piste_status_id,
                    $this->piste["licence"]["id"],
                    $this->piste["documents"],
                    $this->piste["reference"],
                    $this->piste["derivations"],
                    $source_infos
                  )
                ) !== false
              )
              { if($data->del_source_compositions(array("id_source" => $this->piste["id"])))
                { if($_POST["morceau"])
                  { if($data->set_source_composition($this->piste["id"], $_POST["morceau"]))
                    { $env->redirect
                      ( $env->url("users/pistes/edit", array("id" => $this->piste["id"])),
                        "la source a &eacute;t&eacute; modifi&eacute;e"
                      );
                    }
                    else $env->erreur("La source a &eacute;t&eacute; modifi&eacute;e mais impossible de l'associer &agrave; ce morceau");
                  }
                  else $env->redirect
                  ( $env->url("users/pistes/edit", array("id" => $this->piste["id"])),
                    "la source a &eacute;t&eacute; modifi&eacute;e"
                  );
                }
                else $env->erreur("La source a &eacute;t&eacute; modifi&eacute;e mais impossible de l'associer &agrave; ce morceau");
              }
              else $env->erreur("Impossible de modifier la source");
            }
          }
        }
        else $env->erreur("Impossible de lire la liste des compositions");
        $env->set_out("piste", $this->piste);
      }
      else $env->erreur("Impossible de creer le repertoire utilisateur");
    }

    function maj_xml(&$env)
    { $data = $env->data();
      $this->piste["premissions"] = $data->source_permissions($this->piste, $this->user["id"]);
      if(!$this->piste["premissions"]["editeur"])
      { $env->erreur("vous n'avez la permission d'editer cette source");
        return;
      }
      if
      (    isset($_GET[$env->param("id")])
        && isset($_GET[$env->param("xml")])
        && ($_GET[$env->param("xml")] == "derviation" ? isset($_GET[$env->param("derivation")]) : true)
      )
      { $xml_url = "";
        if($_GET[$env->param("xml")] == "derivation")
        { if(isset($this->piste["derivations"][$_GET[$env->param("derivation")]]["xml"]["url"]))
          $xml_url = $this->piste["derivations"][$_GET[$env->param("derivation")]]["xml"]["url"];
        }
        elseif($_GET[$env->param("xml")] == "reference")
        { if(isset($this->piste["reference"]["xml"]["url"]))
          $xml_url = $this->piste["reference"]["xml"]["url"];
        }
        $erreur = "";
        if($xml_url)
        { if(($res = $data->maj_source_cache($xml_url)) !== false)
          { if($res === true)
            { if($this->piste = $data->source($this->piste["id"]))
              { $env->redirect
                ( $env->url("users/pistes/edit", array("id" => $this->piste["id"])),
                  "Les informations du fichier XML ont &eacute;t&eacute; mises &agrave; jour",
                  2
                );
                return;
              }
              else $erreur =
               "le contenu du fichier XML a &eacute;t&eacute; mis &agrave; jour"
              .", mais impossible de lire les informations de la source";
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
        else $env->run("users/pistes/edit", array("id" => $this->piste["id"]));
      }
      else $env->erreur("parametre de fichier xml manquant");
    }

    function del(&$env)
    { $data = $env->data();
      $this->piste["premissions"] = $data->source_permissions($this->piste, $this->user["id"]);
      if($this->piste["premissions"]["admin"])
      { if($data->del_source($this->piste["id"]))
        { $env->redirect
          ( $env->url("users/pistes"),
            "la source a &eacute;t&eacute; supprim&eacute;e"
          );
        }
        else $env->erreur("Impossible de supprimer la source");
      }
      else $env->erreur("vous n'avez la permission d'effacer cette source");
    }

  }

?>
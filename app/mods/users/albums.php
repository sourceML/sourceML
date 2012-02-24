<?php

  class sml_users_albums extends sml_mod
  {
    var $groupes;
    var $album;
    var $user;
    var $status;
    var $album_status_id;

    function validate(&$env)
    { $data = $env->data();
      if(($this->status = $data->source_status()) !== false)
      { foreach($this->status as $id_source_status => $source_status)
        { if($source_status["nom"] == "album")
          { $this->album_status_id = $id_source_status;
            break;
          }
        }
        if(isset($this->album_status_id))
        { if($this->user = $env->user())
          { if(($this->groupes = $data->groupes($this->user["id"])) !== false)
            { $env->set_out("groupes", $this->groupes);
              if($env->etat("action") == "edit" || $env->etat("action") == "del")
              { if(($this->album = $data->source($_GET[$env->param("id")], true)) !== false && $this->album)
                {
                }
                else return "Impossible de lire les informations de l'album";
              }
              if($env->etat("action") == "add" || $env->etat("action") == "edit")
              { if(($this->licences = $data->licences()) !== false)
                { $env->set_out("licences", $this->licences);
                }
                else return "Impossible de lire la liste des licences";
              }
            }
            else return "Impossible de lire la liste des groupes";
          }
          else return "Vous devez &ecirc;tre identifier pour acc&eacute;der &agrave; cette page";
        }
        else return "Type de source inconnu: album";
      }
      else return "Impossible de lire la liste des status de source";
      return true;
    }

    function index(&$env)
    { $data = $env->data();
      $select = array();
      $select["status"] = $this->album_status_id;
      $select["id_user"] = $this->user["id"];
      $select["order_by"] = "ordre";
      $select["start"] = isset($_GET[$env->param("start")]) && $_GET[$env->param("start")] ? $_GET[$env->param("start")] : 0;
      if(isset($_GET[$env->param("groupe")]) && $_GET[$env->param("groupe")]) $select["id_groupe"] = $_GET[$env->param("groupe")];
      if(($albums = $data->sources($select)) !== false)
      { foreach($albums["list"] as $id_album => $album)
        { $albums["list"][$id_album]["permissions"] = $data->source_permissions($albums["list"][$id_album], $this->user["id"]);
        }
        if($_POST)
        { $OK = true;
          foreach($albums["list"] as $id_album => $album)
          { if(isset($_POST["ordre_".$id_album]))
            { if($data->set_source_info($id_album, "ordre", $_POST["ordre_".$id_album]) === false)
              { $OK = false;
                break;
              }
            }
          }
          if($OK)
          { $get_params = array();
            if(isset($_GET[$env->param("groupe")]) && $_GET[$env->param("groupe")]) $get_params["groupe"] = $_GET[$env->param("groupe")];
            $env->redirect
            ( $env->url("users/albums", $get_params),
              "l'ordre des albums a &eacute;t&eacute; enregistr&eacute;"
            );
          }
          else $env->erreur("Impossible d'enregistrer l'ordre des albums");
        }
        $env->set_out("albums", $albums);
      }
      else $env->erreur("Impossible de lire la liste des albums");
    }

    function add(&$env)
    { $data = $env->data();
      if($data->check_user_uploads_dir())
      { $web_path = $env->path("web");
        $upload_dir = $env->path("content")."uploads/".$this->user["id"];
        $_SESSION["upload_dir"] = $web_path.($web_path ? "" : "/").$upload_dir;
        $this->album = $data->empty_source();
        $source_infos = array
        ( "date_inscription" => date("Y")."-".date("m")."-".date("d"),
          "ordre" => 0
        );
        if($_POST)
        { if(($groupe = $data->groupe($_POST["id_groupe"])) !== false)
          { $groupe["id_groupe_status"] = $data->id_groupe_status_admin();
            $env->set_out("groupe", $groupe);
            $this->album["groupes"] = array($groupe["id"] => $groupe);
            $this->album["titre"] = trim($_POST["titre"]);
            $this->album["licence"] = array
            ( "id" => $_POST["licence"]
            );
            $source_infos["date_creation"] = $_POST["annee_date_creation"]."-".$_POST["mois_date_creation"]."-".$_POST["jour_date_creation"];
            $source_infos["description"] = $_POST["description"];
            foreach($_POST as $post_key => $post_value)
            { if(substr($post_key, 0, 13) == "document_nom_")
              { if($id_document = substr($post_key, 13))
                { $this->album["documents"][$id_document] = array
                  ( "nom" => $_POST["document_nom_".$id_document],
                    "url" => $_POST["document_url_".$id_document]
                  );
                }
              }
            }
            if($this->album["titre"])
            { if(($image = $data->upload("image", $upload_dir)) !== false)
              { if($image) $source_infos["image"] = $this->user["id"]."/".$image;
                if
                ( $data->add_source
                  ( $this->album["groupes"],
                    $this->album["titre"],
                    $this->album_status_id,
                    $this->album["licence"]["id"],
                    $this->album["documents"],
                    $this->album["reference"],
                    $this->album["derivations"],
                    $source_infos
                  )
                ) $env->redirect
                ( $env->url("users/albums"),
                  "l'album a &eacute;t&eacute; ajout&eacute;"
                );
                else $env->erreur("Impossible d'ajouter l'album");
              }
              else $env->erreur("Impossible d'uploader l'image");
            }
            else $env->message("merci de pr&eacute;ciser un titre pour l'album");
          }
          else $env->erreur("Impossible de lire les informations du groupe");
        }
        foreach($source_infos as $info_key => $info_value) $this->album[$info_key] = $info_value;
        $env->set_out("album", $this->album);
      }
      else $env->erreur("Impossible de creer le repertoire utilisateur");
    }

    function edit(&$env)
    { $data = $env->data();
      $this->album["premissions"] = $data->source_permissions($this->album, $this->user["id"]);
      if($this->album["premissions"]["editeur"])
      { if($data->check_user_uploads_dir())
        { $web_path = $env->path("web");
          $upload_dir = $env->path("content")."uploads/".$this->user["id"];
          $_SESSION["upload_dir"] = $web_path.($web_path ? "" : "/").$upload_dir;
          $source_infos = array
          ( "date_inscription" => $this->album["date_inscription"],
            "ordre" => $this->album["ordre"],
            "date_creation" => $this->album["date_creation"],
            "description" => $this->album["description"],
            "image" => $this->album["image"]
          );
          $env->set_out("groupe", $data->get_admin_groupe($this->album["groupes"]));
          if($_POST)
          { if(($groupe = $data->groupe($_POST["id_groupe"])) !== false)
            { $env->set_out("groupe", $groupe);
              $groupe["id_groupe_status"] = $data->id_groupe_status_admin();
              $this->album["groupes"] = array($groupe["id"] => $groupe);
              $this->album["titre"] = trim($_POST["titre"]);
              $this->album["licence"] = array
              ( "id" => $_POST["licence"]
              );
              $source_infos["date_creation"] = $_POST["annee_date_creation"]."-".$_POST["mois_date_creation"]."-".$_POST["jour_date_creation"];
              $source_infos["description"] = $_POST["description"];
              foreach($_POST as $post_key => $post_value)
              { if(substr($post_key, 0, 13) == "document_nom_")
                { if($id_document = substr($post_key, 13))
                  { $this->album["documents"][$id_document] = array
                    ( "nom" => $_POST["document_nom_".$id_document],
                      "url" => $_POST["document_url_".$id_document]
                    );
                  }
                }
              }
              if($this->album["titre"])
              { if($_POST["del_image"])
                { if($this->album["image"])
                  { if(@unlink($env->path("content")."uploads/".$this->album["image"])) $this->album["image"] = "";
                    else
                    { $this->album["image"] = false;
                      $env->erreur("Impossible d'effacer l'image");
                    }
                  }
                  else $this->album["image"] = "";
                }
                else
                { if(($up_image = $data->upload("image", $upload_dir)) !== false)
                  { if($up_image) $this->album["image"] = $this->user["id"]."/".$up_image;
                  }
                  else
                  { $env->erreur("Impossible d'uploader l'image");
                    return;
                  }
                }
                if($this->album["image"] !== false)
                { if($this->album["image"]) $source_infos["image"] = $this->album["image"];
                  if
                  ( $data->set_source
                    ( $this->album["id"],
                      $this->album["groupes"],
                      $this->album["titre"],
                      $this->album_status_id,
                      $this->album["licence"]["id"],
                      $this->album["documents"],
                      $this->album["reference"],
                      $this->album["derivations"],
                      $source_infos
                    )
                  ) $env->redirect
                  ( $env->url("users/albums/edit", array("id" => $this->album["id"])),
                    "l'album a &eacute;t&eacute; modifi&eacute;"
                  );
                  else $env->erreur("Impossible de modifier l'album");
                }
                else $env->erreur("Impossible d'uploader l'image");
              }
              else $env->message("merci de pr&eacute;ciser un titre pour l'album");
            }
            else $env->erreur("Impossible de lire les informations du groupe");
          }
          $env->set_out("album", $this->album);
        }
        else $env->erreur("Impossible de creer le repertoire utilisateur");
      }
      else $env->erreur("Vous n'avez pas la permission de modifier cet album");
    }

    function del(&$env)
    { $data = $env->data();
      $this->album["premissions"] = $data->source_permissions($this->album, $this->user["id"]);
      if($this->album["premissions"]["admin"])
      { if($data->del_source($this->album["id"]))
        { $env->redirect
          ( $env->url("users/albums"),
            "l'album a &eacute;t&eacute; supprim&eacute;"
          );
        }
        else $env->erreur("Impossible de supprimer l'album");
      }
      else $env->erreur("Vous n'avez pas la permission de supprimer cet album");
    }

  }

?>
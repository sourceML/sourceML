<?php

  class sml_users_groupes extends sml_mod
  {

    var $groupe;
    var $user;

    function validate(&$env)
    { $data = $env->data();
      if($this->user = $env->user())
      { if($env->etat("action") == "edit" || $env->etat("action") == "del")
        { if
          ( ( $this->groupe = $data->groupe
              ( $_GET[$env->param("id")]
              )
            ) !== false
            && $this->groupe
          )
          { if($this->groupe["id_user"] == $this->user["id"])
            { return true;
            }
            else return "Vous n'est pas autoris&eacute; &agrave; modifier ce groupe";
          }
          else return "Impossible de lire les informations du groupe";
        }
        else return true;
      }
      return "Vous devez &ecirc;tre identifier pour acc&eacute;der &agrave; cette page";
    }

    function index(&$env)
    { $data = $env->data();
      if
      ( $env->set_out
        ( "groupes",
          $data->groupes
          ( $this->user["id"],
            isset($_GET[$env->param("start")]) ? $_GET[$env->param("start")] : 0
          )
        ) !== false
      )
      {
      }
      else $env->erreur("Impossible de lire la liste des groupes");
    }

    function add(&$env)
    { $data = $env->data();
      if($data->check_user_uploads_dir())
      { $web_path = $env->path("web");
        $upload_dir = $env->path("content")."uploads/".$this->user["id"];
        $_SESSION["upload_dir"] = $web_path.($web_path ? "" : "/").$upload_dir;
        if($_POST)
        { $env->set_out("groupe", $_POST);
          if(($image = $data->upload("image", $upload_dir)) !== false)
          { if(trim($_POST["nom"]))
            { if(($exists = $data->groupe_exists($_POST["nom"])) !== false)
              { if(!$_POST["contact_form"] || trim($_POST["email"]))
                { if($exists == 0)
                  { if
                    ( $data->add_groupe
                      ( $this->user["id"],
                        $_POST["nom"],
                        $this->user["id"]."/".$image,
                        $_POST["description"],
                        $_POST["email"],
                        $_POST["contact_form"] ? 1 : 0,
                        $_POST["captcha"] ? 1 : 0
                      )
                    ) $env->redirect
                    ( $env->url("users/groupes"),
                      "le groupe a &eacute;t&eacute; ajout&eacute;"
                    );
                    else $env->erreur("Impossible d'ajouter le groupe");
                  }
                  else $env->message("Un groupe avec ce nom existe d&eacute;j&agrave;");
                }
                else $env->message("merci de pr&eacute;ciser un email pour le formulaire de contact");
              }
              else $env->erreur("Impossible de savoir si le groupe existe d&eacute;j&agrave;");
            }
            else $env->message("merci de pr&eacute;ciser un nom pour le groupe");
          }
          else $env->erreur("Impossible d'uploader l'image");
        }
      }
      else $env->erreur("Impossible de creer le repertoire utilisateur");
    }

    function edit(&$env)
    { $data = $env->data();
      if($data->check_user_uploads_dir())
      { $web_path = $env->path("web");
        $upload_dir = $env->path("content")."uploads/".$this->user["id"];
        $_SESSION["upload_dir"] = $web_path.($web_path ? "" : "/").$upload_dir;
        if($env->set_out("groupe", $this->groupe))
        { $groupe = $env->out("groupe");
          if($_POST)
          { $id = $groupe["id"];
            $nom = $groupe["nom"];
            $image = $groupe["image"];
            $_POST["image"] = $image ? $_SESSION["upload_dir"]."/".$image : "";
            $env->set_out("groupe", $_POST);
            if($_POST["del_image"])
            { if($image)
              { if(@unlink($env->path("content")."uploads/".$image)) $image = "";
                else
                { $image = false;
                  $env->erreur("Impossible d'effacer l'image");
                }
              }
              else $image = "";
            }
            else
            { if(($new_image = $data->upload("image", $upload_dir)) !== false)
              { if($new_image) $image = $this->user["id"]."/".$new_image;
              }
              else $env->erreur("Impossible d'uploader l'image");
            }
            if($image !== false)
            { if(trim($_POST["nom"]))
              { if(($exists = $data->groupe_exists($_POST["nom"], $this->groupe["id"])) !== false)
                { if(!$exists)
                  { if(!$_POST["contact_form"] || trim($_POST["email"]))
                    { if
                      ( $data->set_groupe
                        ( $id,
                          $_POST["nom"],
                          $image,
                          $_POST["description"],
                          $_POST["email"],
                          $_POST["contact_form"] ? 1 : 0,
                          $_POST["captcha"] ? 1 : 0
                        )
                      ) $env->redirect
                      ( $env->url("users/groupes/edit", array("id" => $_GET[$env->param("id")])),
                        "le groupe a &eacute;t&eacute; modifi&eacute;"
                      );
                      else $env->erreur("Impossible de modifier le groupe");
                    }
                    else $env->message("merci de pr&eacute;ciser un email pour le formulaire de contact");
                  }
                  else $env->message("Un groupe avec ce nom existe d&eacute;j&agrave;");
                }
                else $env->erreur("Impossible de savoir si le groupe existe d&eacute;j&agrave;");
              }
              else $env->message("merci de pr&eacute;ciser un nom pour le groupe");
            }
          }
          else
          { $env->set_out("groupe", $groupe);
          }
        }
        else $env->erreur("Impossible de lire les informations du groupe");
      }
      else $env->erreur("Impossible de creer le repertoire utilisateur");
    }

    function del(&$env)
    { $data = $env->data();
      if(($res = $data->del_groupe($this->groupe["id"])) !== false)
      { if($res === 1) $env->redirect
        ( $env->url("users/groupes"),
          "Ce groupe a des sources sur ce site, il n'a pas &eacute;t&eacute; supprim&eacute;.",
          5
        );
        else $env->redirect
        ( $env->url("users/groupes"),
          "le groupe a &eacute;t&eacute; supprim&eacute;"
        );
      }
      else $env->erreur("Impossible de supprimer le groupe");
    }

  }

?>
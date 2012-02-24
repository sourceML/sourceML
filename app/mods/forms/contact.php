<?php

  if(file_exists($this->path("libs")."ptitcaptcha.php")) require $this->path("libs")."ptitcaptcha.php";

  class sml_forms_contact extends sml_mod
  {

    function validate(&$env)
    { if($pages_view_mod = $env->get_mod("pages/view"))
      { return $pages_view_mod->validate(&$env);
      }
      return true;
    }

    function index(&$env)
    { if($env->config("contact_form") && $env->config("email"))
      { if($_POST)
        { if
          ( $this->send
            ( $env,
              $_POST["email"],
              "[".$env->config("site_name")."] nouveau message",
              $_POST["message"],
              $env->config("email"),
              $env->config("captcha")
            )
          )
          { $env->redirect
            ( $env->url("index"),
              "Le message a &eacute;t&eacute; envoy&eacute;",
              2
            );
          }
        }
      }
      else $env->run("index");
    }

    function groupe(&$env)
    { $data = $env->data();
      if(($status = $data->source_status()) !== false)
      { foreach($status as $id_source_status => $source_status)
        { if($source_status["nom"] == "album") $album_status_id = $id_source_status;
          if(isset($album_status_id)) break;
        }
        if(isset($album_status_id))
        { if($groupe = $data->groupe($_GET[$env->param("id")]))
          { if($groupe["contact_form"] && $groupe["email"])
            { $env->set_out("groupe", $groupe);
              $select = array("status" => $album_status_id);
              $select["id_groupe"] = $groupe["id"];
              if(($albums = $data->sources($select)) !== false)
              { $env->set_out("albums", $albums);
                if($_POST)
                { if
                  ( $this->send
                    ( $env,
                      $_POST["email"],
                      "[".$env->config("site_name")." - ".$groupe["nom"]."] nouveau message",
                      $_POST["message"],
                      $groupe["email"],
                      $groupe["captcha"]
                    )
                  )
                  { $env->redirect
                    ( $env->url("sources/groupe/view", array("id" => $_GET[$env->param("id")])),
                      "Le message a &eacute;t&eacute; envoy&eacute;"
                    );
                  }
                }
              }
              else $env->erreur("impossible de lire la liste des albums");
            }
            else
            { $env->run("sources/groupe/view", true, array("id" => $groupe["id"]));
              return;
            }
          }
          else $env->erreur("Impossible de lire les informations du groupe");
        }
        else $env->erreur("Type de source inconnu: album");
      }
      else $env->erreur("Impossible de lire la liste des status de source");
    }

    function send(&$env, $from, $titre, $message, $dest, $captcha)
    { $env->set_out("ENVOYE", false);
      if($captcha && !file_exists($env->path("libs")."ptitcaptcha.php"))
      { $env->erreur("fichier du captcha introuvable");
        return false;
      }
      if(!$captcha || PtitCaptchaHelper::checkCaptcha())
      { if($from)
        { if($dest)
          { if
            ( mail
              ( $dest,
                $titre,
                $message,
                 "From: ".$from."\r\n"
                ."Reply-To: ".$from."\r\n"
              )
            )
            { $env->set_out("ENVOYE", true);
              return true;
            }
            else $env->erreur("Erreur &agrave; l'envoi du mail");
          }
          else $env->erreur("Impossible de trouver l'email du destinataire");
        }
        else $env->message("merci de pr&eacute;ciser un email");
      }
      else $env->message("anti-spam incorrect");
      return false;
    }

  }

?>
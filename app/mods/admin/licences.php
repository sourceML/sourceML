<?php

  class sml_admin_licences extends sml_mod
  {
    function index(&$env)
    { $data = $env->data();
      if($env->set_out("licences", $data->licences()) !== false)
      {
      }
      else $env->erreur("impossible de lire la liste des licences");
    }

    function add(&$env)
    { $data = $env->data();
      if($_POST)
      { $env->set_out("licence", $_POST);
        if($_POST["nom"])
        { if
          ( $data->add_licence
            ( $_POST["nom"],
              $_POST["url"]
            )
          )
          $env->redirect
          ( $env->url("admin/licences"),
            "la licence <strong>".$_POST["nom"]."</strong> a &eacute;t&eacute; ajout&eacute;e"
          );
          else $env->erreur("Impossible d'ajouter la licence");
        }
        else $env->message("merci de pr&eacute;ciser un nom");
      }
    }

    function edit(&$env)
    { $data = $env->data();
      if($env->set_out("licence", $data->licence($_GET[$env->param("id")])))
      { if($_POST)
        { if($_POST["nom"])
          { $licence = $env->out("licence");
            $env->set_out("licence", $_POST);
            if
            ( $data->set_licence
              ( $_GET[$env->param("id")],
                $_POST["nom"],
                $_POST["url"]
              )
            )
            $env->redirect
            ( $env->url("admin/licences"),
              "la licence <strong>".$licence["nom"]."</strong> a &eacute;t&eacute; modifi&eacute;e"
            );
            else $env->erreur("Impossible de mettre &agrave; jour la licence");
          }
          else $env->message("Merci de pr&eacute;ciser un nom");
        }
      }
      else $env->erreur("impossible de lire la licence");
    }

    function del(&$env)
    { $data = $env->data();
      if($env->set_out("licence", $data->licence($_GET[$env->param("id")])))
      { $licence = $env->out("licence");
        if(($res = $data->del_licence($_GET[$env->param("id")])) !== false)
        { if($res === 1) $env->redirect
          ( $env->url("admin/licences"),
            "Des sources sur ce site utilisent cette licence, elle n'a pas &eacute;t&eacute; supprim&eacute;e.",
            5
          );
          else $env->redirect
          ( $env->url("admin/licences"),
            "la licence <strong>".$licence["nom"]."</strong> a &eacute;t&eacute; supprim&eacute;e"
          );
        }
        else $env->erreur("Impossible de supprimer la licence");
      }
      else $env->erreur("Impossible de lire les informations de cette licence");
    }

  }

?>
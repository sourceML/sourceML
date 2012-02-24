<?php

  class sml_admin_cache extends sml_mod
  {

    function index(&$env)
    { $data = $env->data();
      if(($CONFIG = $env->get_CONFIG()) !== false)
      { $env->set_out("config", $CONFIG);
        if($_POST)
        { $env->set_out("config", $_POST);
          if(preg_match("/^[0-9]+$/", $_POST["cache_time"]))
          { $CONTINUE = true;
            if($CONTINUE && $data->set_config("cache_actif", $_POST["cache_actif"]));
            else $CONTINUE = false;
            if($CONTINUE && $data->set_config("cache_maj_auto", $_POST["cache_maj_auto"]));
            else $CONTINUE = false;
            if($CONTINUE && $data->set_config("cache_time", $_POST["cache_time"]));
            else $CONTINUE = false;
            if($CONTINUE) $env->redirect
            ( $env->url("admin/cache"),
              "la configuration du cache a &eacute;t&eacute; enregistr&eacute;e"
            );
            else $env->erreur("Impossible d'enregistrer la configuration du cache");
          }
          else $env->message("dur&eacute;e de validit&eacute; du cache doit &ecirc;tre un nombre");
        }
      }
      else $env->erreur("Impossible de lire la configuration du cache");
    }

  }

?>
<?php

  class sml_admin_maintenance extends sml_mod
  {
    function index(&$env)
    {
    }

    function empty_cache(&$env)
    { $data = $env->data();
      if($data->empty_source_cache())
      { $env->redirect
        ( $env->url("admin/maintenance"),
          "le cache a &eacute;t&eacute; vid&eacute;"
        );
      }
      else $env->erreur("Impossible de vider le cache");
    }

    function maj_all_xml(&$env)
    { $data = $env->data();
      if($data->maj_source_xml_all())
      { $env->redirect
        ( $env->url("admin/maintenance"),
          "les fichiers XML des sources ont &eacute;t&eacute; mis &agrave; jour"
        );
      }
      else $env->erreur("Impossible de mettre &agrave; jour les fichiers XML des sources");
    }

  }

?>
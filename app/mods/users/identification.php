<?php

  class sml_users_identification extends sml_mod
  {
    function index(&$env)
    {
    }

    function login(&$env)
    { if(!$env->user())
      { $data = $env->data();
        if($data->login(trim($_POST['login']), trim($_POST['pass'])))
        { $env->redirect
          ( isset($_POST["from"]) ? urldecode($_POST["from"]) : $this->env->url(),
            "Vous &ecirc;tes maintenant identifi&eacute; en tant que ".$_POST['login']
          );
        }
        else $env->message("Idantifiants incorrects");
      }
      else $env->message("Vous &ecirc;tes d&eacute;j&agrave; identifi&eacute;");
    }

    function logout(&$env)
    { $data = $env->data();
      if($data->logout())
      { $env->redirect
        ( $env->url(),
          "Vous n'&ecirc;tes plus identifi&eacute; sur le site"
        );
      }
      else $env->message("Erreur lors de la deconnection. il se peut que vous soyez encore identifi&eacute;");
    }

  }

?>
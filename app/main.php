<?php

  function sourceml($config_file, $etat = "")
  { require $config_file;
    $PATHES["app"] .= $PATHES["app"] && substr($PATHES["app"], -1) != "/" ? "/" : "";
    $PATHES["libs"] .= $PATHES["libs"] && substr($PATHES["libs"], -1) != "/" ? "/" : "";
    if($sxml_class_file = (file_exists($PATHES["libs"]."sxml.php") ? $PATHES["libs"]."sxml.php" : ""))
    { if($empty_class_file = (file_exists($PATHES["libs"]."empty_class.php") ? $PATHES["libs"]."empty_class.php" : ""))
      { if($env_class_file = (file_exists($PATHES["app"]."env/sml_env.php") ? $PATHES["app"]."env/sml_env.php" : ""))
        { require $sxml_class_file;
          require $empty_class_file;
          require $env_class_file;
          $env = new sml_env(true);
          $env->load_modules($PATHES["app"], "env/modules/");
          $env->set_config_file($config_file);
          $env->set_PATHES($PATHES);
          $env->init_plugins();
          $env->load_config($bdd, $CONFIG);
          $env->init();
          $etat = ($etat === false ? false : ($etat ? $etat : (isset($_GET[$env->param("e")]) ? $_GET[$env->param("e")] : "")));
          if($etat !== false) $env->run($etat);
          return $env;
        }
        else echo "<pre>impossible de trouver le fichier <strong>env/sml_env.php</strong></pre>";
      }
      else echo "<pre>impossible de trouver le fichier <strong>".$libs_path."empty_class.php</strong></pre>";
    }
    else echo "<pre>impossible de trouver le fichier <strong>".$libs_path."sxml.php</strong></pre>";
    return false;
  }

  function sml_display($env)
  { if($env->etat_is_valid()) $env->render_layout($env->init_layout());
  }

?>
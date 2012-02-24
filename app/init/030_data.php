<?php

  require $this->app_file("data/sml_sgbd.php");
  require $this->app_file("data/sml_data.php");
  if($this->app_file_exists("data/impl/sml_".$this->bdd("sgbd").".php"))
  { require $this->app_file("data/impl/sml_".$this->bdd("sgbd").".php");
    if(class_exists($sgbd_impl = "sml_".$this->bdd("sgbd")))
    { if(($plugins = $this->plugins("DESC")) !== false)
      { $data = new sml_data(true);
        foreach($plugins as $plugin_name => $plugin)
        { if($plugin["installed"] && $plugin["enabled"])
          { $data->load_modules($this->path("plugins").$plugin_name."/app/", "data/modules/share/");
            $data->load_modules($this->path("plugins").$plugin_name."/app/", "data/modules/".($this->bdd("sgbd") == "xml" ? "xml" : "sql")."/");
          }
        }
        $data->load_modules($this->path("app"), "data/modules/share/");
        $data->load_modules($this->path("app"), "data/modules/".($this->bdd("sgbd") == "xml" ? "xml" : "sql")."/");
        $sgbd = new sml_sgbd
        ( new $sgbd_impl
          ( $this->bdd("host"),
            $this->bdd("base"),
            $this->bdd("user"),
            $this->bdd("password")
          ),
          $this
        );
        if($sgbd->extention_ok())
        { $data->set_sgbd($sgbd);
          $data->set_env($this);
          $this->set_data($data);
        }
        else $this->erreur("L'extention php ".$this->bdd("sgbd")." n'est pas install&eacute;e", true);
      }
      else $this->erreur("Impossible de lire les plugins pour charger les modules de donnees");
    }
    else $this->erreur("Impossible de trouver la classe d'implementation du sgbd ".$this->bdd("sgbd"), true);
  }
  else $this->erreur("Impossible de trouver le fichier d'implementation du sgbd ".$this->bdd("sgbd"), true);

?>
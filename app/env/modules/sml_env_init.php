<?php

  class sml_env_init extends sml_env
  {

    function init()
    { $init_files = array();
      if(($plugins = $this->plugins()) !== false)
      { foreach($plugins as $plugin_name => $plugin)
        { $init_path = $this->path("plugins").$plugin_name."/app/init/";
          if
          (    $plugin["installed"]
            && $plugin["enabled"]
            && file_exists($init_path)
            && is_dir($init_path)
          )
          { if($dh = opendir($init_path))
            { $files = array();
              while(($file = readdir($dh)) !== false)
              { if
                (    substr($file, 0, 1) != "."
                  && !is_dir($init_path.$file)
                  && strcmp(substr($file, -4), ".php") == 0
                  && !isset($init_files[$file])
                ) $init_files[$file] = $init_path;
              }
              closedir($dh);
              
            }
            else $this->erreur("impossible d'ouvrir le dossier init du plugin ".$plugin_name, true);
          }
          if($this->check_stop()) return;
        }
        $init_path = $this->path("app")."init/";
        if
        (    file_exists($init_path)
          && is_dir($init_path)
        )
        { if($dh = opendir($init_path))
          { $files = array();
            while(($file = readdir($dh)) !== false)
            { if
              (    substr($file, 0, 1) != "."
                && !is_dir($init_path.$file)
                && strcmp(substr($file, -4), ".php") == 0
              ) $init_files[$file] = $init_path;
            }
            closedir($dh);
          }
          else $this->erreur("impossible d'ouvrir le dossier init du plugin ".$plugin_name, true);
        }
      }
      if($this->check_stop()) return;
      if($init_files)
      { ksort($init_files);
        foreach($init_files as $file => $init_path)
        { require $init_path.$file;
          if($this->check_stop()) return;
        }
      }
    }

  }

?>
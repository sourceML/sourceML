<?php

  class sml_env extends empty_class
  {

    function app_file_exists($file, $PRIORITE = "ASC")
    { $app_file = $this->_app_file($file, $PRIORITE);
      return $app_file ? true : false;
    }

    function app_file($file, $PRIORITE = "ASC")
    { $app_file = $this->_app_file($file, $PRIORITE);
      return $app_file ? $app_file : $file;
    }

    function _app_file($file, $PRIORITE = "ASC")
    { $app_file = false;
      if($PRIORITE == "ASC" && file_exists($this->path("app").$file)) return $this->path("app").$file;
      if(($plugins = $this->plugins($PRIORITE)) !== false)
      { foreach($plugins as $plugin_name => $plugin)
        { if($file && $plugin["installed"] && $plugin["enabled"] && file_exists($this->path("plugins").$plugin_name."/app/".$file))
          { $app_file = $this->path("plugins").$plugin_name."/app/".$file;
            break;
          }
        }
        if($PRIORITE == "DESC" && !$app_file)
        { if(file_exists($this->path("app").$file)) $app_file = $this->path("app").$file;
        }
      }
      return $app_file;
    }

  }

?>
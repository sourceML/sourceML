<?php

  if(($plugins = $this->plugins("DESC")) !== false)
  { foreach($plugins as $plugin_name => $plugin)
    { if($plugin["installed"] && $plugin["enabled"])
      { if(!$plugin["impl"]->init($this)) $this->erreur("erreur lors de l'initialisation du plugin ".$plugin_name, true);
      }
    }
  }
  else $this->erreur("erreur lors de l'initialisation des plugins", true);

?>
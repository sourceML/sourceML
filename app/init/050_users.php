<?php

  if($data->load_session() !== false)
  { if($data->init_user_status($this->config("user_status")) !== false)
    { if($data->init_action_status($this->config("action_status")) !== false)
      { 
      }
      else $this->erreur("Impossible de charger les statuts des actions", true);
    }
    else $this->erreur("Impossible de charger les statuts des utilisateurs", true);
  }
  else $this->erreur("Impossible de charger la session", true);

?>
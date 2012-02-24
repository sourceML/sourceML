<?php

  if(($check_instance_return = $data->check_instance()) !== false)
  { if($check_instance_return === true)
    {
    }
    else $this->erreur($check_instance_return, true);
  }
  else $this->erreur("Impossible de verifier l'integrit&eacute; de la base de donn&eacute;", true);

?>
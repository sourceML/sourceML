<?php

  class sml_env_messages extends sml_env
  {

    function erreur($message, $EXIT = false)
    { if($EXIT)
      { echo "[erreur] ".$message;
        exit();
      }
      else
      { $this->set_etat("reponses/html/erreur", false);
        $erreur = $this->out("erreur");
        if(!isset($erreur)) $erreur = array("messages" => array());
        $erreur["messages"][] = $message;
        $this->set_out("erreur", $erreur);
      }
    }

    function message($message)
    { $messages = $this->out("messages");
      if(!isset($messages)) $messages = array();
      $messages[] = $message;
      $this->set_out("messages", $messages);
    }

    function messages()
    { $messages = $this->out("messages");
      if(isset($messages)) return $messages;
      return array();
    }

  }

?>
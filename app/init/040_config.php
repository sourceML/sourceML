<?php

  if(($config = $data->config()) !== false)
  { $this->set_config($config);
    $start_action_params_config = $this->config("start_action_params") ? @unserialize($this->config("start_action_params")) : array();
    $out_config = $this->config("out");
    $out_config .= $out_config && substr($out_config, -1) != "/" ? "/" : "";
    $this->set_config
    ( array
      ( "out" => $out_config,
        "start_action_params" => $start_action_params_config
      )
    );
    if($this->set_out_config($data->out_config()) === false) $this->erreur("Impossible de lire la configuration du template");
  }
  else $this->erreur("Impossible de lire la configuration", true);

?>
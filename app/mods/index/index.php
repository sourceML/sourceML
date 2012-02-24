<?php

  class sml_index_index extends sml_mod
  {

    function index(&$env)
    { $start_action = $env->config("start_action");
      if($start_action)
      { $start_action_params = $env->config("start_action_params");
        if($start_action_params && is_array($start_action_params))
        { foreach($start_action_params as $key => $value) $_GET[$key] = $value;
        }
      }
      else $start_action = "sources/groupe";
      $env->run($start_action);
    }

  }

?>
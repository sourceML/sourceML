<?php

  class sml_env_urls extends sml_env
  {
    var $additional_get_params;

    function init_additional_get_params()
    { $this->additional_get_params = array();
      $_params = $_SERVER["QUERY_STRING"];
      $v_params = explode("&", $_params);
      foreach($v_params as $param)
      { if($param)
        { $key = strpos($param, "=") === false ? $param : substr($param, 0, strpos($param, "="));
          $value = strpos($param, "=") === false ? "" : substr($param, strpos($param, "=") + 1);
          if(!$this->is_a_param($key)) $this->additional_get_params[$key] = $value;
        }
      }
    }

    function is_a_param($key)
    { foreach($this->get_PARAMS() as $_key => $_value) if(strcmp($key, $_value) == 0) return true;
      return false;
    }

    function url($action = "", $_params = array(), $script_name = "index.php")
    { if($action) $_params["e"] = $action;
      $get_params = "";
      if(isset($this->additional_get_params)) foreach($this->additional_get_params as $key => $value) $get_params .= ($get_params ? "&amp;" : "?").$key."=".$value;
      foreach($_params as $key => $value) $get_params .= ($get_params ? "&amp;" : "?").$this->param($key)."=".$value;
      return $this->path("web").$script_name.$get_params;
    }

    function redirect($url, $message, $wait = 1)
    { $this->set_etat("reponses/html/redirect_javascript", false);
      $this->set_out
      ( "redirect",
        array
        ( "url" => str_replace("&amp;", "&", $url),
          "message" => $message,
          "wait" => $wait
        )
      );
    }

  }

?>
<?php

  class sml_data_out_config extends sml_data
  {

    function out_config()
    { $env = $this->env();
      $config = array();
      if($env->out_file_exists("config.xml"))
      { if($this->buffer = @file_get_contents($env->out_file("config.xml")))
        { if(!isset($this->sxml)) $this->sxml = new sxml();
          $this->sxml->parse($this->buffer);
          $this->buffer = $this->sxml->data["config"][0];
          if($this->buffer["subs"]) foreach($this->buffer["subs"] as $key => $value)
          { $config[$key] = array
            ( "type" => $value[0]["attrs"]["type"],
              "default" => $value[0]["attrs"]["default"],
              "text" => $value[0]["data"]
            );
          }
        }
        else $config = false;
      }
      return $config;
    }

  }

?>
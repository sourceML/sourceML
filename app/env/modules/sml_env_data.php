<?php

  class sml_env_data extends sml_env
  {
    var $data;

    function set_data(&$data) { $this->data = &$data; }

    function data()
    { return $this->data;
    }

  }

?>
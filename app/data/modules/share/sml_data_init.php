<?php

  class sml_data_init extends sml_data
  {
    var $env;
    var $sgbd;

    function env() { return $this->env; }
    function sgbd() { return $this->sgbd; }

    function set_env(&$env) { $this->env = &$env; }
    function set_sgbd(&$sgbd) { $this->sgbd = &$sgbd; }

    function table_prefix() { return false; }

  }

?>
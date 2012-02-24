<?php

  class sml_mod
  {
    function validate(&$env) { return true; }

    function prepare_inputs(&$env) { return true; }
//    function prepare_inputs(&$env) { return $env->prepare_inputs(); }

  }

?>
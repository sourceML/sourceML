<?php

  class sml_admin_index extends sml_mod
  {
    function index(&$env)
    { $env->run("admin/config");
    }

  }

?>
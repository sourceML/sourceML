<?php

  class sml_users_compte extends sml_mod
  {
    function index(&$env)
    { $env->run("users/groupes");
    }

  }

?>
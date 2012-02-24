<?php

  // error_reporting(E_ALL);

  require "app/main.php";
  if($sourceml = sourceml("config.php")) sml_display($sourceml);

?>

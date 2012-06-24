<!DOCTYPE html>
<html dir="ltr" lang="fr-FR">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </head>
  <body>
<?php

  if($this->out_file_exists($layout["content"])) require $this->out_file($layout["content"]);

?>
  </body>
</html>
<!DOCTYPE html>
<html dir="ltr" lang="fr-FR">
  <head>
    <?php require $this->out_file("views/head.php"); ?>
  </head>
  <body>

    <div id="header">
      <div class="content">
<?php require $this->out_file("views/header.php"); ?>
     </div>
    </div>

    <div id="main">
      <div class="content">
<?php require $this->out_file("views/messages.php"); ?>
<?php

  if($this->out_file_exists($layout["content"])) require $this->out_file($layout["content"]);

?>
     </div>
    </div>

    <div id="footer">
      <div class="content">
<?php require $this->out_file("views/footer.php"); ?>
     </div>
    </div>

  </body>
</html>

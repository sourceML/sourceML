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

    <div id="menu_top">
      <div class="content">
<?php if($this->out_config("navig_menu_top")) require $this->out_file("views/top_menu.php"); ?>
      </div>
    </div>

    <div class="clear"><!-- --></div>

    <div id="main">
      <div class="content">

        <div id="center" class="no_colonne">
<?php

  require $this->out_file("views/messages.php");
  if($this->out_file_exists($layout["content"])) require $this->out_file($layout["content"]);

?>
        </div>

        <div class="clear"><!-- --></div>

      </div>
    </div>

    <div id="footer">
      <div class="content">
<?php require $this->out_file("views/footer.php"); ?>
     </div>
    </div>

  </body>
</html>

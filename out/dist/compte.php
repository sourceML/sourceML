<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <?php require $this->out_file("views/head.php"); ?>
    <script type="text/javascript" src="<?= $this->path("libs") ?>tiny_mce/tiny_mce.js"></script>
    <script type="text/javascript" src="<?= $this->path("libs") ?>tiny_mce/plugins/tinybrowser/tb_tinymce.js.php"></script>
  </head>
  <body>

    <div id="header">
      <div class="content">
<?php require $this->out_file("views/header.php"); ?>
     </div>
    </div>

    <div id="main">
      <div class="content">

        <div id="colonne">
<?php require $this->out_file("views/users/colonne.php"); ?>
        </div>

        <div id="center">
<?php require $this->out_file("views/messages.php"); ?>
<?php

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

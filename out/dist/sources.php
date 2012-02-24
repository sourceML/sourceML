<?php

  $data = $this->data();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
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

<?php if($this->out_config("colonne")) : ?>
        <div id="colonne">
<?php require $this->out_file("views/sources/colonne.php"); ?>
        </div>
<?php endif; ?>

        <div id="center"<?= $this->out_config("colonne") ? "" : " class=\"no_colonne\"" ?>>
<?php require $this->out_file("views/messages.php"); ?>
<?php

  if($this->out_file_exists($layout["content"])) :

?>

<?php

  require $this->out_file($layout["content"]);
  endif;

?>

<!--[if IE]>
<script type="text/javascript" event="FSCommand(command,args)" for="player">
  eval(args);
</script>
<![endif]-->

<object id="player" type="application/x-shockwave-flash" data="<?= $this->path("libs")."player_mp3_js.swf" ?>" width="1" height="1">
  <param name="movie" value="<?= $this->path("libs")."player_mp3_js.swf" ?>" />
  <param name="AllowScriptAccess" value="always" />
  <param name="FlashVars" value="listener=player_listener&amp;interval=500" />
  <param name="bgcolor" value="#050505" />
  <param name="wmode" value="transparent" />
</object>

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
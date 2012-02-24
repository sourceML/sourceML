<?php $start_action = $this->config("start_action"); ?>
<ul class="menu navig_menu">
<?php if($start_action == "sources/groupe") : ?>
  <li><a href="<?php echo $this->url("sources/groupe"); ?>">groupes</a></li>
<?php endif; ?>
<?php

  $ALBUMS_MENU_ON = false;
  if($start_action == "sources/album/view")
  { if(isset($this->out["albums"]["total"]) && $this->out["albums"]["total"] > 1) $ALBUMS_MENU_ON = true;
  }
  else $ALBUMS_MENU_ON = true;
  if($ALBUMS_MENU_ON) :

?>
  <li><a href="<?php echo $this->url("sources/album"); ?>">albums</a></li>
<?php endif; ?>
  <li><a href="<?php echo $this->url("sources/morceau"); ?>">morceaux</a></li>
  <li><a href="<?php echo $this->url("sources/piste"); ?>">sources</a></li>
</ul>

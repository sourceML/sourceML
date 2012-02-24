<?php

  $user = $this->user();
  if($user && ($source["status"] == 2 || $source["status"] == 3)) :

?>

<ul class="menu_source">

<?php

  // si c'est un morceau
  if($source["status"] == 2) :

?>

<?php if($this->status_ok("users/morceaux/del")) : ?>
<?php if($source["id_user"] == $user["id"]) : ?>
  <li class="icon"><a href="<?= $this->url("users/morceaux/del", array("id" => $source["id"])) ?>"
                      onclick="return confirm('Supprimer ce morceau ?')"><img src="<?= $this->out_file("icons/del.gif") ?>" /></a></li>
<?php endif; ?>
<?php endif; ?>

<?php if($this->status_ok("users/morceaux/edit")) : ?>
<?php if($source["id_user"] == $user["id"]) : ?>
  <li class="icon"><a href="<?= $this->url("users/morceaux/edit", array("id" => $source["id"])) ?>"><img src="<?= $this->out_file("icons/edit.gif") ?>" /></a></li>
<?php endif; ?>
<?php endif; ?>

<?php endif; ?>


<?php

  // si c'est une piste
  if($source["status"] == 3) :

?>

<?php if($this->status_ok("users/pistes/del")) : ?>
<?php if($source["id_user"] == $user["id"]) : ?>
  <li class="icon"><a href="<?= $this->url("users/pistes/del", array("id" => $source["id"])) ?>"
                      onclick="return confirm('Supprimer cette source ?')"><img src="<?= $this->out_file("icons/del.gif") ?>" /></a></li>
<?php endif; ?>
<?php endif; ?>

<?php if($this->status_ok("users/pistes/edit")) : ?>
<?php if($source["id_user"] == $user["id"]) : ?>
  <li class="icon"><a href="<?= $this->url("users/pistes/edit", array("id" => $source["id"])) ?>"><img src="<?= $this->out_file("icons/edit.gif") ?>" /></a></li>
<?php endif; ?>
<?php endif; ?>

<?php endif; ?>


</ul>
<div class="clear"><!-- --></div>
<?php endif; ?>

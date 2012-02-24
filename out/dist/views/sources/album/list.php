<?php require $this->out_file("views/sources/album/ariane.php"); ?>

<?php if($this->out["groupes"]["total"] > 1) : ?>

<ul class="admin">
  <li>Afficher les albums</li>  
  <li>
    pour
    <select onchange="document.location=this.options[this.selectedIndex].value;">
      <option value="<?= $this->url("sources/album") ?>"<?= $_GET[$this->param("groupe")] == "0" ? " selected=\"selected\"" : "" ?>>Tous les groupes</option>
      <?php foreach($this->out["groupes"]["list"] as $id_groupe => $groupe) : ?>
      <option value="<?= $this->url("sources/album", array("groupe" => $id_groupe)) ?>"<?= $_GET[$this->param("groupe")] == $id_groupe ? " selected=\"selected\"" : "" ?>><?= $groupe["nom"] ?></option>
      <?php endforeach; ?>
    </select>
  </li>
</ul>

<?php endif; ?>

<?php if(false && $this->out["groupe"]) : ?>
<div class="description">
<?= $this->out["groupe"]["description"] ?>
</div>
<?php endif; ?>

<div class="clear"><!-- --></div>

<?php

  if($this->out["albums"]["list"]) :
  $source_status = "album";
  $url_params = array();

?>

<?php $items = "albums"; $legend = "albums"; require $this->out_file("views/navig.php"); ?>

<ul class="sources">
<?php

  foreach($this->out["albums"]["list"] as $id_source => $source)
  { $url_params["album"] = $id_source;
    $source["url"] = $this->url("sources/album/view", $url_params);
    require $this->out_file("views/sources/source.php");
  }

?>
</ul>

<?php $items = "albums"; $legend = "albums"; require $this->out_file("views/navig.php"); ?>

<?php else : ?>
<p>Aucun album pour le moment</p>
<?php endif; ?>

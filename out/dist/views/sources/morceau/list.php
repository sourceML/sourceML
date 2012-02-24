<?php require $this->out_file("views/sources/morceau/ariane.php"); ?>

<?php if($this->out["groupes"]["total"] > 0 || $this->out["albums"]["total"] > 0) : ?>

<ul class="admin">
  <li>Afficher les morceaux pour</li>  
<?php if($this->out["groupes"]["total"] > 1) : ?>
  <li>
<?php

  $url_params = array();
  if(isset($_GET[$this->param("album")]) && !$_GET[$this->param("album")]) $url_params["album"] = "";

?>
    <select onchange="document.location=this.options[this.selectedIndex].value;">
      <option value="<?= $this->url("sources/morceau", $url_params) ?>"<?= $_GET[$this->param("groupe")] == "0" ? " selected=\"selected\"" : "" ?>>Tous les groupes</option>
      <?php foreach($this->out["groupes"]["list"] as $id_groupe => $groupe) : $url_params["groupe"] = $id_groupe; ?>
      <option value="<?= $this->url("sources/morceau", $url_params) ?>"<?= $this->out["groupe"] &&  $this->out["groupe"]["id"] == $id_groupe ? " selected=\"selected\"" : "" ?>><?= $groupe["nom"] ?></option>
      <?php endforeach; ?>
    </select>
  </li>
<?php endif; ?>
<?php if($this->out["albums"]["total"] > 0) : ?>
  <li>
    <span id="album_select">
<?php

  $url_params = array();
  if(isset($this->out["groupe"])) $url_params["groupe"] = $this->out["groupe"]["id"];

?>
    <select onchange="document.location=this.options[this.selectedIndex].value;">
      <option value="<?= $this->url("sources/morceau", $url_params) ?>"<?= !isset($_GET[$this->param("album")]) ? " selected=\"selected\"" : "" ?>>Tous les morceaux</option>
<?php $url_params["album"] = ""; ?>
      <option value="<?= $this->url("sources/morceau", $url_params) ?>"<?= isset($_GET[$this->param("album")]) && !$_GET[$this->param("album")] ? " selected=\"selected\"" : "" ?>>Hors album</option>
      <?php foreach($this->out["albums"]["list"] as $id_album => $album) : $url_params["album"] = $id_album; ?>
      <option value="<?= $this->url("sources/morceau", $url_params) ?>"<?= $_GET[$this->param("album")] == $id_album ? " selected=\"selected\"" : "" ?>>Album: <?= $album["titre"] ?></option>
      <?php endforeach; ?>
    </select>
    </span>
  </li>
<?php endif; ?>
</ul>

<?php endif; ?>

<div class="clear"><!-- --></div>

<?php

  if($this->out["morceaux"]["list"]) :
  $source_status = "morceau";
  $url_params = array();
  if($this->out["groupe"]) $url_params["groupe"] = $this->out["groupe"]["id"];
  if($this->out["album"]) $url_params["album"] = $this->out["album"]["id"];

?>

<?php $items = "morceaux"; $legend = "morceaux"; require $this->out_file("views/navig.php"); ?>

<ul class="sources source_arbo">
<?php

  foreach($this->out["morceaux"]["list"] as $id_source => $source)
  { $url_params["morceau"] = $id_source;
    $source["url"] = $this->url("sources/morceau/view", $url_params);
    require $this->out_file("views/sources/source.php");
  }

?>
</ul>

<?php $items = "morceaux"; $legend = "morceaux"; require $this->out_file("views/navig.php"); ?>

<?php else : ?>
<p>Aucun morceau pour le moment</p>
<?php endif; ?>

<?php require $this->out_file("views/sources/piste/ariane.php"); ?>

<?php if($this->out["groupes"]["total"] > 0 || $this->out["albums"]["total"] > 0) : ?>

<ul class="admin">
  <li>Afficher les sources pour</li>
<?php if($this->out["groupes"]["total"] > 1) : ?>
  <li>
<?php

  $url_params = array();
  if(isset($_GET[$this->param("morceau")]) && !$_GET[$this->param("morceau")]) $url_params["morceau"] = "";

?>
    <select onchange="document.location=this.options[this.selectedIndex].value;">
      <option value="<?= $this->url("sources/piste", $url_params) ?>"<?= $_GET[$this->param("groupe")] == "0" ? " selected=\"selected\"" : "" ?>>Tous les groupes</option>
      <?php foreach($this->out["groupes"]["list"] as $id_groupe => $groupe) : $url_params["groupe"] = $id_groupe; ?>
      <option value="<?= $this->url("sources/piste", $url_params) ?>"<?= $this->out["groupe"] &&  $this->out["groupe"]["id"] == $id_groupe ? " selected=\"selected\"" : "" ?>><?= $groupe["nom"] ?></option>
      <?php endforeach; ?>
    </select>
  </li>
<?php endif; ?>
<?php if($this->out["morceaux"]["total"] > 0) : ?>
  <li>
    <span id="album_select">
<?php

  $url_params = array();
  if(isset($this->out["groupe"])) $url_params["groupe"] = $this->out["groupe"]["id"];

?>
    <select onchange="document.location=this.options[this.selectedIndex].value;">
      <option value="<?= $this->url("sources/piste", $url_params) ?>"<?= $_GET[$this->param("morceau")] == "0" ? " selected=\"selected\"" : "" ?>>Toutes les sources</option>
<?php $url_params["morceau"] = ""; ?>
      <option value="<?= $this->url("sources/piste", $url_params) ?>"<?= isset($_GET[$this->param("morceau")]) && !$_GET[$this->param("morceau")] ? " selected=\"selected\"" : "" ?>>Hors morceau</option>
      <?php foreach($this->out["morceaux"]["list"] as $id_morceau => $morceau) : $url_params["morceau"] = $id_morceau; ?>
      <option value="<?= $this->url("sources/piste", $url_params) ?>"<?= $_GET[$this->param("morceau")] == $id_morceau ? " selected=\"selected\"" : "" ?>><?= $morceau["reference"] ? $morceau["reference"]["titre"] : $morceau["titre"] ?></option>
      <?php endforeach; ?>
    </select>
    </span>
  </li>
<?php endif; ?>
</ul>

<?php endif; ?>

<div class="clear"><!-- --></div>

<?php

  if($this->out["pistes"]["list"]) :
  $source_status = "piste";
  $url_params = array();
  if($this->out["groupe"]) $url_params["groupe"] = $this->out["groupe"]["id"];
  if($this->out["morceau"]) $url_params["morceau"] = $this->out["morceau"]["id"];

?>

<?php $items = "pistes"; $legend = "sources"; require $this->out_file("views/navig.php"); ?>

<ul class="sources source_arbo">
<?php

  foreach($this->out["pistes"]["list"] as $id_source => $source)
  { $url_params["piste"] = $id_source;
    $source["url"] = $this->url("sources/piste/view", $url_params);
    require $this->out_file("views/sources/source.php");
  }

?>
</ul>

<?php $items = "pistes"; $legend = "sources"; require $this->out_file("views/navig.php"); ?>

<?php else : ?>
<p>Aucune source pour le moment</p>
<?php endif; ?>

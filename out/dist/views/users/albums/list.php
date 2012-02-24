<h2>Albums</h2>

<ul class="admin">
  <li><a class="add" href="<?= $this->url("users/albums/add") ?>">Nouvel album</a></li>
</ul>

<?php if($this->out["groupes"]["total"] > 0) : ?>

<ul class="admin">
  <li>Afficher les albums</li>  
  <li>
    pour
    <select onchange="document.location=this.options[this.selectedIndex].value;">
      <option value="<?= $this->url("users/albums") ?>"<?= $_GET[$this->param("groupe")] == "0" ? " selected=\"selected\"" : "" ?>>Tous les groupes</option>
      <?php foreach($this->out["groupes"]["list"] as $id_groupe => $groupe) : ?>
      <option value="<?= $this->url("users/albums", array("groupe" => $id_groupe)) ?>"<?= $_GET[$this->param("groupe")] == $id_groupe ? " selected=\"selected\"" : "" ?>><?= $groupe["nom"] ?></option>
      <?php endforeach; ?>
    </select>
  </li>
</ul>

<?php endif; ?>

<?php if($this->out["albums"]["list"]) : ?>

<?php $items = "albums"; $legend = "albums"; require $this->out_file("views/navig.php"); ?>

<?php

  $get_params = array();
  if(isset($_GET[$this->param("groupe")]) && $_GET[$this->param("groupe")]) $get_params["groupe"] = $_GET[$this->param("groupe")];

?>
<form name="sources_form" action="<?= $this->url("users/albums", $get_params) ?>" method="post">
<table class="admin">
  <tr>
    <th>titre</th>
    <th>ordre</th>
    <th align="center" colspan="2">actions</th>
  </tr>
<?php foreach($this->out["albums"]["list"] as $id_album => $album) : ?>
  <tr class="hl">
    <td>
<?php if($album["reference"]) : ?>
      <?= $album["reference"]["titre"] ?> <span>[ r&eacute;f&eacute;rence &raquo; <a href="<?= $album["reference"]["from"] ?>"><?= $album["reference"]["auteur"] ?></a> ]</span>
<?php else : ?>
      <?= $album["titre"] ?>
<?php endif; ?>
    </td>
    <td class="action"><input type="text" name="ordre_<?= $id_album ?>" value="<?= isset($album["ordre"]) ? $album["ordre"] : 0 ?>" size="3" /></td>
    <td class="action">
<?php if($album["permissions"]["editeur"]) : ?>
    <a href="<?= $this->url("users/albums/edit", array("id" => $id_album)) ?>"
       class="admin_link"
       title="modifier cet album"><img src="<?= $this->out_file("icons/edit.gif") ?>" /></a>
<?php else : ?>
    &nbsp;
<?php endif; ?>
    </td>
    <td class="action">
<?php if($album["permissions"]["admin"]) : ?>
    <a href="<?= $this->url("users/albums/del", array("id" => $id_album)) ?>"
       class="admin_link"
       title="supprimer cet album"><img src="<?= $this->out_file("icons/del.gif") ?>"
       onclick="return confirm('Supprimer cet album ?')"/></a>
<?php else : ?>
    &nbsp;
<?php endif; ?>
    </td>
  </tr>
<?php endforeach; ?>
</table>
  <ul class="form">
    <li class="buttons">
      <input type="submit" value="Enregistrer l'ordre" />
    </li>
  </ul>
</form>

<?php $items = "albums"; $legend = "albums"; require $this->out_file("views/navig.php"); ?>

<?php else : ?>
<p>Aucun album pour le moment</p>
<?php endif; ?>

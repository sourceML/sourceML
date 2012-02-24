<?php

  $albums = array();
  if($_GET[$this->param("groupe")] && is_array($this->out["albums"][$_GET[$this->param("groupe")]]))
  { foreach($this->out["albums"][$_GET[$this->param("groupe")]] as $id_album => $album)
    { $albums[$id_album] = $album;
    }
  }
  else
  { foreach($this->out["albums"] as $id_groupe => $_albums)
    { foreach($this->out["albums"][$id_groupe] as $id_album => $album)
      { $albums[$id_album] = $album;
      }
    }
  }

?>

<h2>Morceaux</h2>

<ul class="admin">
  <li><a class="add" href="<?= $this->url("users/morceaux/add") ?>">Nouveau morceau</a></li>
</ul>

<?php if($this->out["groupes"]["total"] > 0 || $this->out["albums"]["total"] > 0) : ?>

<ul class="admin">
  <li>Afficher les morceaux</li>  
<?php if($this->out["groupes"]["total"] > 0) : ?>
  <li>
    pour
<?php

  $url_params = array();
  if(isset($_GET[$this->param("album")]) && !$_GET[$this->param("album")]) $url_params["album"] = "";

?>
    <select onchange="document.location=this.options[this.selectedIndex].value;">
      <option value="<?= $this->url("users/morceaux", $url_params) ?>"<?= $_GET[$this->param("groupe")] == "0" ? " selected=\"selected\"" : "" ?>>Tous les groupes</option>
      <?php foreach($this->out["groupes"]["list"] as $id_groupe => $groupe) : $url_params["groupe"] = $id_groupe; ?>
      <option value="<?= $this->url("users/morceaux", $url_params) ?>"<?= $_GET[$this->param("groupe")] == $id_groupe ? " selected=\"selected\"" : "" ?>><?= $groupe["nom"] ?></option>
      <?php endforeach; ?>
    </select>
  </li>
<?php endif; ?>
<?php if($albums) : ?>
  <li>
    <span id="album_select">
<?php

  $url_params = array();
  if(isset($_GET[$this->param("groupe")])) $url_params["groupe"] = $_GET[$this->param("groupe")];

?>
    <select onchange="document.location=this.options[this.selectedIndex].value;">
      <option value="<?= $this->url("users/morceaux", $url_params) ?>"<?= !isset($_GET[$this->param("album")]) ? " selected=\"selected\"" : "" ?>>Tous les morceaux</option>
<?php $url_params["album"] = ""; ?>
      <option value="<?= $this->url("users/morceaux", $url_params) ?>"<?= isset($_GET[$this->param("album")]) && !$_GET[$this->param("album")] ? " selected=\"selected\"" : "" ?>>Hors album</option>
      <?php foreach($albums as $id_album => $album) : $url_params["album"] = $id_album; ?>
      <option value="<?= $this->url("users/morceaux", $url_params) ?>"<?= $_GET[$this->param("album")] == $id_album ? " selected=\"selected\"" : "" ?>>Album: <?= $album["reference"] ? $album["reference"]["titre"] : $album["titre"] ?></option>
      <?php endforeach; ?>
    </select>
    </span>
  </li>
<?php endif; ?>
</ul>

<?php endif; ?>

<?php if($this->out["morceaux"]["list"]) : ?>

<?php $items = "morceaux"; $legend = "morceaux"; require $this->out_file("views/navig.php"); ?>

<?php

  $get_params = array();
  if(isset($_GET[$this->param("groupe")]) && $_GET[$this->param("groupe")]) $get_params["groupe"] = $_GET[$this->param("groupe")];
  if(isset($_GET[$this->param("album")]) && $_GET[$this->param("album")]) $get_params["album"] = $_GET[$this->param("album")];

?>
<form name="sources_form" action="<?= $this->url("users/morceaux", $get_params) ?>" method="post">
<table class="admin">
  <tr>
    <th>titre</th>
    <th>ordre</th>
    <th align="center" colspan="2">actions</th>
  </tr>
<?php foreach($this->out["morceaux"]["list"] as $id_morceau => $morceau) : ?>
  <tr class="hl">
    <td>
<?php if($morceau["reference"]) : ?>
      r&eacute;f&eacute;rence &raquo; <a href="<?= $morceau["reference"]["from"] ?>"><?= $morceau["reference"]["titre"] ?> (<?= $morceau["reference"]["auteur"] ?>)</a>
<?php else : ?>
      <a href="<?= $this->url("users/morceaux/edit", array("id" => $id_morceau)) ?>"><?= $morceau["titre"] ?></a>
<?php endif; ?>
<?php if($morceau["derivations"]) : ?>
      <br />
      <span class="small">
        d&eacute;rive de &raquo;
<?php $n = 0; foreach($morceau["derivations"] as $derivation) : ?>
        <?= $n ? ", " : "" ?><a href="<?= $derivation["from"] ?>"><?= $derivation["titre"] ?> (<?= $derivation["auteur"] ?>)</a>
<?php $n++; endforeach; ?>
      </span>
<?php endif; ?>
    </td>
    <td class="action"><input type="text" name="ordre_<?= $id_morceau ?>" value="<?= isset($morceau["ordre"]) ? $morceau["ordre"] : 0 ?>" size="3" /></td>
    <td class="action">
<?php if($morceau["permissions"]["editeur"]) : ?>
    <a href="<?= $this->url("users/morceaux/edit", array("id" => $id_morceau)) ?>"
       class="admin_link"
       title="modifier ce morceau"><img src="<?= $this->out_file("icons/edit.gif") ?>" /></a>
<?php else : ?>
    &nbsp;
<?php endif; ?>
    </td>
    <td class="action">
<?php if($morceau["permissions"]["admin"]) : ?>
    <a href="<?= $this->url("users/morceaux/del", array("id" => $id_morceau)) ?>"
       class="admin_link"
       title="supprimer ce morceau"><img src="<?= $this->out_file("icons/del.gif") ?>"
       onclick="return confirm('Supprimer ce morceau ?')"/></a>
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

<?php $items = "morceaux"; $legend = "morceaux"; require $this->out_file("views/navig.php"); ?>

<?php else : ?>
<p>Aucun morceau pour le moment</p>
<?php endif; ?>

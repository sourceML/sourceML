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

  $morceaux = array();
  if($_GET[$this->param("groupe")])
  { $id_groupe = $_GET[$this->param("groupe")];
    if(is_array($this->out["morceaux"][$id_groupe]))
    { foreach($this->out["morceaux"][$id_groupe] as $id_album => $_morceaux)
      { if($_GET[$this->param("album")])
        { if($id_album == $_GET[$this->param("album")])
          { foreach($this->out["morceaux"][$id_groupe][$id_album] as $id_morceau => $morceau)
            { $morceaux[$id_morceau] = $morceau;
            }
          }
        }
        else
        { foreach($this->out["morceaux"][$id_groupe] as $id_album => $_morceaux)
          { foreach($this->out["morceaux"][$id_groupe][$id_album] as $id_morceau => $morceau)
            { $morceaux[$id_morceau] = $morceau;
            }
          }
        }
      }
    }
  }
  else
  { foreach($this->out["morceaux"] as $id_groupe => $_albums)
    { foreach($this->out["morceaux"][$id_groupe] as $id_album => $_morceaux)
      { foreach($this->out["morceaux"][$id_groupe][$id_album] as $id_morceau => $morceau)
        { $morceaux[$id_morceau] = $morceau;
        }
      }
    }
  }

?>

<h2>Sources</h2>

<ul class="admin">
  <li><a class="add" href="<?= $this->url("users/pistes/add") ?>">Nouvelle source</a></li>
</ul>

<?php if($this->out["groupes"]["total"] > 0) : ?>

<ul class="admin">
  <li>Afficher les sources pour</li>  
<?php if($this->out["groupes"]["total"] > 0) : ?>
  <li>
    <select onchange="document.location=this.options[this.selectedIndex].value;">
      <option value="<?= $this->url("users/pistes") ?>"<?= $_GET[$this->param("groupe")] == "0" ? " selected=\"selected\"" : "" ?>>Tous les groupes</option>
      <?php foreach($this->out["groupes"]["list"] as $id_groupe => $groupe) : ?>
      <option value="<?= $this->url("users/pistes", array("groupe" => $id_groupe)) ?>"<?= $_GET[$this->param("groupe")] == $id_groupe ? " selected=\"selected\"" : "" ?>><?= $groupe["nom"] ?></option>
      <?php endforeach; ?>
    </select>
  </li>
<?php endif; ?>
<?php if(false && $albums) : ?>
  <li>
    <span id="album_select">
    <select onchange="document.location=this.options[this.selectedIndex].value;">
      <option value="<?= $this->url("users/pistes") ?>"<?= $_GET[$this->param("album")] == "0" ? " selected=\"selected\"" : "" ?>>Tous les albums</option>
      <?php foreach($albums as $id_album => $album) : ?>
      <option value="<?= $this->url("users/pistes", array("album" => $id_album)) ?>"<?= $_GET[$this->param("album")] == $id_album ? " selected=\"selected\"" : "" ?>><?= $album["reference"] ? $album["reference"]["titre"] : $album["titre"] ?></option>
      <?php endforeach; ?>
    </select>
    </span>
  </li>
<?php endif; ?>

<?php if($morceaux) : ?>
  <li>
    <span id="morceau_select">
    <select onchange="document.location=this.options[this.selectedIndex].value;">
      <option value="<?= $this->url("users/pistes") ?>"<?= $_GET[$this->param("morceau")] == "0" ? " selected=\"selected\"" : "" ?>>Tous les morceaux</option>
      <?php foreach($morceaux as $id_morceau => $morceau) : ?>
      <option value="<?= $this->url("users/pistes", array("morceau" => $id_morceau)) ?>"<?= $_GET[$this->param("morceau")] == $id_morceau ? " selected=\"selected\"" : "" ?>><?= $morceau["reference"] ? $morceau["reference"]["titre"] : $morceau["titre"] ?></option>
      <?php endforeach; ?>
    </select>
    </span>
  </li>
<?php endif; ?>

</ul>

<?php endif; ?>

<?php if($this->out["pistes"]["list"]) : ?>

<?php $items = "pistes"; $legend = "sources"; require $this->out_file("views/navig.php"); ?>

<?php

  $get_params = array();
  if(isset($_GET[$this->param("groupe")]) && $_GET[$this->param("groupe")]) $get_params["groupe"] = $_GET[$this->param("groupe")];
  if(isset($_GET[$this->param("morceau")]) && $_GET[$this->param("morceau")]) $get_params["morceau"] = $_GET[$this->param("morceau")];

?>
<form name="sources_form" action="<?= $this->url("users/pistes", $get_params) ?>" method="post">
<table class="admin">
  <tr>
    <th>titre</th>
    <th>ordre</th>
    <th align="center" colspan="2">actions</th>
  </tr>
<?php foreach($this->out["pistes"]["list"] as $id_piste => $piste) : ?>
  <tr class="hl">
    <td>
<?php if($piste["reference"]) : ?>
      r&eacute;f&eacute;rence &raquo; <a href="<?= $piste["reference"]["from"] ?>"><?= $piste["reference"]["titre"] ?> (<?= $piste["reference"]["auteur"] ?>)</a>
<?php else : ?>
      <a href="<?= $this->url("users/pistes/edit", array("id" => $id_piste)) ?>"><?= $piste["titre"] ?></a>
<?php endif; ?>
<?php if($piste["derivations"]) : ?>
      <br />
      <span class="small">
        d&eacute;rive de &raquo;
<?php $n = 0; foreach($piste["derivations"] as $derivation) : ?>
        <?= $n ? ", " : "" ?><a href="<?= $derivation["from"] ?>"><?= $derivation["titre"] ?> (<?= $derivation["auteur"] ?>)</a>
<?php $n++; endforeach; ?>
      </span>
<?php endif; ?>
    </td>
    <td class="action"><input type="text" name="ordre_<?= $id_piste ?>" value="<?= isset($piste["ordre"]) ? $piste["ordre"] : 0 ?>" size="3" /></td>
    <td class="action">
<?php if($piste["permissions"]["editeur"]) : ?>
    <a href="<?= $this->url("users/pistes/edit", array("id" => $id_piste)) ?>"
       class="admin_link"
       title="modifier cette source"><img src="<?= $this->out_file("icons/edit.gif") ?>" /></a>
<?php else : ?>
    &nbsp;
<?php endif; ?>
    </td>
    <td class="action">
<?php if($piste["permissions"]["admin"]) : ?>
    <a href="<?= $this->url("users/pistes/del", array("id" => $id_piste)) ?>"
       class="admin_link"
       title="supprimer cette source"
       onclick="return confirm('Supprimer cette source ?')"><img src="<?= $this->out_file("icons/del.gif") ?>" /></a>
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

<?php $items = "pistes"; $legend = "sources"; require $this->out_file("views/navig.php"); ?>

<?php else : ?>
<p>Aucune source pour le moment</p>
<?php endif; ?>

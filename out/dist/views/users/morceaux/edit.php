<?php require $this->out_file("views/tinymce.init.js.php"); ?>

<h2>Modifier un morceau</h2>

<script type="text/javascript">

var albums = {};
<?php foreach($this->out["albums"] as $id_groupe => $albums) : ?>
albums["<?= $id_groupe ?>"] = {};
<?php foreach($albums as $id_album => $album) : ?>
albums["<?= $id_groupe ?>"]["<?= $id_album ?>"] = "<?= $album["titre"] ?>";
<?php endforeach; ?>
<?php endforeach; ?>
var derivations = {};
<?php

  $index_derivation = 1;
  foreach($this->out["morceau"]["derivations"] as $id_derivation => $derivation) :
  if($id_derivation >= $index_derivation) $index_derivation = $id_derivation + 1;

?>
derivations[<?= $id_derivation ?>] = true;
<?php endforeach; ?>
var index_derivation = <?= $index_derivation ?>;
var is_reference = <?= $this->out["morceau"]["reference"] ? "true" : "false" ?>;

</script>

<ul class="admin">
  <li><a href="<?= $this->url("users/morceaux") ?>">Retour &agrave; la liste des morceaux</a></li>
</ul>

<?php if($this->out["groupes"]["total"] > 0) : ?>

<form name="morceau_form" action="<?= $this->url("users/morceaux/edit", array("id" => $_GET[$this->param("id")])) ?>" method="post">

  <input type="hidden" name="date_inscription" value="<?= $this->out["morceau"]["date_inscription"] ?>" />

  <ul class="form">

    <li>
      <label for="id_groupe">groupe</label>
      <p>
        <select name="id_groupe" id="id_groupe" onchange="select_groupe(this.options[this.selectedIndex].value)">
        <?php

          $current_groupe = null;
          $first_groupe = null;
          foreach($this->out["groupes"]["list"] as $id_groupe => $groupe) :
          $selected = false;
          if(!isset($first_groupe)) $first_groupe = $id_groupe;
          if($this->out["groupe"]["id"] == $id_groupe)
          { $current_groupe = $id_groupe;
            $selected = true;
          }

        ?>
          <option value="<?= $id_groupe ?>"<?= $selected ? "selected=\"selected\"" : "" ?>><?= $groupe["nom"] ?></option>
        <?php

          endforeach;
          if(!isset($current_groupe)) $current_groupe = $first_groupe;

        ?>
        </select>
      </p>
    </li>

    <li>
      <div id="album_select">
        <label for="album">album</label>
        <div class="form_values"><p>
          <select name="album" id="album">
            <option value="0"<?= $_GET[$this->param("album")] == "0" ? " selected=\"selected\"" : "" ?>>hors album</option>
            <?php if($this->out["albums"][$current_groupe]) : ?>
            <?php foreach($this->out["albums"][$current_groupe] as $id_album => $album) : ?>
            <option value="<?= $id_album ?>"<?= $this->out["morceau"]["album"] == $id_album ? " selected=\"selected\"" : "" ?>><?= $album["titre"] ?></option>
            <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </p></div>
      </div>
    </li>
  </ul>

  <ul class="admin_form_head">
    <li>
      <label for="is_derivation">ce morceau est une d&eacute;rivation</label>
      <input type="checkbox" id="is_derivation" name="is_derivation"<?= $this->out["morceau"]["derivations"] ? " checked=\"checked\"" : "" ?> />
    </li>
  </ul>

  <ul class="admin_form_content" id="derivation_input"<?= $this->out["morceau"]["derivations"] ? "" : " style=\"display:none;\"" ?>>

    <li>

      <div id="derivations_list" class="form_values">

        <div id="derivations_items">
        <?php

          foreach($this->out["morceau"]["derivations"] as $id_derivation => $derivation)
          { $this->set_out
            ( "form_params",
              array
              ( "maj_url" => $this->url("users/morceaux/maj_xml", array("id" => $this->out["morceau"]["id"], "derivation" => $id_derivation, "xml" => "derivation")),
                "name" => "derivation",
                "label" => "d&eacute;rive de &raquo; ",
                "can_delete" => true,
                "id" => $id_derivation
              )
            );
            $this->set_out("xml_form_source", $derivation);
            require $this->out_file("views/content/sources/xml_form.php");
          }

        ?>
        </div>

        <ul class="admin">
          <li><a class="add" href="#" onclick="add_derivation('', false, '', false); return false;">Ajouter une source de d&eacute;rivation</a></li>
        </ul>

      </div>

    </li>

  </ul>

  <ul class="admin_form_head">
    <li>
      <label for="is_reference">ce morceau est une r&eacute;f&eacute;rence</label>
      <input type="checkbox" id="is_reference" name="is_reference"<?= $this->out["morceau"]["reference"] ? " checked=\"checked\"" : "" ?> />
    </li>
  </ul>

  <ul class="admin_form_content" id="reference_form" class="form"<?= $this->out["morceau"]["reference"] ? "" : " style=\"display: none\"" ?>>

    <li id="reference_input">

      <?php

        $this->set_out
        ( "form_params",
          array
          ( "maj_url" =>
            ( $this->out["morceau"]["reference"] && $this->config("cache_actif") ?
                $this->url("users/morceaux/maj_xml", array("id" => $this->out["morceau"]["id"], "xml" => "reference"))
              : ""
            ),
            "name" => "reference",
            "label" => "r&eacute;f&eacute;rence &raquo; ",
            "can_delete" => false
          )
        );
        $this->set_out("xml_form_source", $this->out["morceau"]["reference"]);
        require $this->out_file("views/content/sources/xml_form.php");

      ?>

    </li>

  </ul>

  <ul id="original_form" class="form"<?= $this->out["morceau"]["reference"] ? " style=\"display: none\"" : "" ?>>

    <li>
      <label for="titre">titre</label>
      <p><input type="text" class="long_text" name="titre" id="titre" value="<?= $this->out["morceau"]["titre"] ?>" /></p>
    </li>

    <li>
      <label for="licence">licence</label>
      <p>
        <select name="licence" id="licence">
        <?php foreach($this->out["licences"]["list"] as $id_licence => $licence) : ?>
          <option value="<?= $id_licence ?>"<?= $this->out["morceau"]["licence"]["id"] == $id_licence ? "selected=\"selected\"" : "" ?>><?= $licence["nom"] ?></option>
        <?php endforeach; ?>
        </select>
      </p>
    </li>

    <li>
      <label for="date_creation">date de cr&eacute;ation</label>
      <p>
<?php

  $date_creation =
  ( $this->out["morceau"]["date_creation"] ?
      explode("-", $this->out["morceau"]["date_creation"])
    : array(0 => date("Y"), 1 => date("m"), 2 => date("d"))
  );

?>
        jour
        <select name="jour_date_creation">
<?php for($j = 1; $j <=31; $j++) : ?>
          <option value="<?= $j ?>"<?= $date_creation[2] == $j ? " selected=\"selected\"" : ""?>><?= $j ?></option>
<?php endfor; ?>
        </select>
        mois
        <select name="mois_date_creation">
<?php for($m = 1; $m <=12; $m++) : ?>
         <option value="<?= $m ?>"<?= $date_creation[1] == $m ? " selected=\"selected\"" : ""?>><?= $m ?></option>
<?php endfor; ?>
        </select>
        ann&eacute;e
        <input type="text" size="4" name="annee_date_creation" value="<?= $date_creation[0] ?>">
      </p>
    </li>

    <li>
      <h3><label>Fichiers</label> <a href="#" onclick="add_document(); return false;">Ajouter un fichier</a></h3>
    </li>

    <li>
      <div id="documents">

<?php if($this->out["morceau"]) : foreach($this->out["morceau"]["documents"] as $id_document => $document) : ?>

        <div class="document" id="document_<?= $id_document ?>">
          <div class="delete"><a href="#" onclick="del_document('<?= $id_document ?>'); return false;">Enlever ce fichier</a></div>
          <label for="document_nom_<?= $id_document ?>">nom</label>
          <input type="text" class="long_text" name="document_nom_<?= $id_document ?>" id="document_nom_<?= $id_document ?>" value="<?= $document["nom"] ?>" />
          <div class="clear"><!-- --></div>
          <label for="document_url_<?= $id_document ?>">url</label>
          <input type="text" size="48" name="document_url_<?= $id_document ?>" id="document_url_<?= $id_document ?>" value="<?= $document["url"] ?>" />
        </div>
        <script type="text/javascript">if(last_document_id <= <?= $id_document ?>) last_document_id = <?= $id_document ?> + 1; </script>

<?php endforeach; endif; ?>

      </div>
    </li>

    <li>
      <label for="description">description</label>
    </li>
    <li>
      <textarea class="tinymce" cols="50" rows="10" name="description" id="description"><?= $this->out["morceau"]["description"] ?></textarea>
    </li>

  </ul>

  <ul class="form">
    <li class="buttons">
      <input type="submit" value="Enregistrer" />
    </li>
  </ul>
</form>

<?php else : ?>

<p>Vous ne g&eacute;rez aucun groupe pour le moment.</p>
<p>Pour ajouter un morceau, vous devez d'abord <a href="<?= $this->url("users/groupes/add") ?>">cr&eacute;er un groupe</a>.</p>

<?php endif; ?>

<?php require $this->out_file("views/tinymce.init.js.php"); ?>

<h2>Nouvelle source</h2>

<script type="text/javascript">

var morceaux = {};
<?php foreach($this->out["morceaux"] as $id_groupe => $albums) : ?>
morceaux["<?= $id_groupe ?>"] = {};
<?php foreach($albums as $id_album => $_morceaux) : ?>
morceaux["<?= $id_groupe ?>"]["<?= $id_album ?>"] = {};
<?php foreach($_morceaux as $id_morceau => $_morceau) : ?>
morceaux["<?= $id_groupe ?>"]["<?= $id_album ?>"]["<?= $id_morceau ?>"] = "<?= $_morceau["reference"] ? $_morceau["reference"]["titre"] : $_morceau["titre"] ?>";
<?php endforeach; ?>
<?php endforeach; ?>
<?php endforeach; ?>
var derivations = {};
<?php

  $index_derivation = 1;
  foreach($this->out["piste"]["derivations"] as $id_derivation => $derivation) :
  if($id_derivation >= $index_derivation) $index_derivation = $id_derivation + 1;

?>
derivations[<?= $id_derivation ?>] = true;
<?php endforeach; ?>
var index_derivation = <?= $index_derivation ?>;
var is_reference = <?= $this->out["piste"]["reference"] ? "true" : "false" ?>;

</script>

<ul class="admin">
  <li><a href="<?= $this->url("users/pistes") ?>">Retour &agrave; la liste des sources</a></li>
</ul>

<?php if($this->out["groupes"]["total"] > 0) : ?>

<form name="morceau_form" action="<?= $this->url("users/pistes/add") ?>" method="post">

  <ul class="form">

    <li>
      <label for="id_groupe">groupe</label>
      <p>
        <select name="id_groupe" id="id_groupe" onchange="select_morceaux_groupe(this.options[this.selectedIndex].value)">
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
      <div id="morceau_select">
        <label for="morceau">morceau</label>
        <div class="form_values"><p>
          <select name="morceau" id="morceau">
            <option value="0"<?= $_GET[$this->param("morceau")] == "0" ? " selected=\"selected\"" : "" ?>>hors morceau</option>
            <?php if($this->out["morceaux"][$current_groupe]) : ?>
            <?php foreach($this->out["morceaux"][$current_groupe] as $id_album => $album) : ?>
            <?php foreach($this->out["morceaux"][$current_groupe][$id_album] as $id_morceau => $morceau) : ?>
            <option value="<?= $id_morceau ?>"<?= $this->out["piste"]["morceau"] == $id_morceau ? " selected=\"selected\"" : "" ?>><?= $morceau["reference"] ? $morceau["reference"]["titre"] : $morceau["titre"] ?></option>
            <?php endforeach; ?>
            <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </p></div>
      </div>
    </li>
  </ul>

  <ul class="admin_form_head">
    <li>
      <label for="is_derivation">cette source est une d&eacute;rivation</label>
      <input type="checkbox" id="is_derivation" name="is_derivation"<?= $this->out["piste"]["derivations"] ? " checked=\"checked\"" : "" ?> />
    </li>
  </ul>

  <ul class="admin_form_content" id="derivation_input"<?= $this->out["piste"]["derivations"] ? "" : " style=\"display:none;\"" ?>>

    <li>

      <div id="derivations_list" class="form_values">

        <div id="derivations_items">
        <?php

          foreach($this->out["piste"]["derivations"] as $id_derivation => $derivation)
          { $this->set_out
            ( "form_params",
              array
              ( "name" => "derivation",
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
      <label for="is_reference">cette source est une r&eacute;f&eacute;rence</label>
      <input type="checkbox" id="is_reference" name="is_reference"<?= $this->out["piste"]["reference"] ? " checked=\"checked\"" : "" ?> />
    </li>
  </ul>

  <ul class="admin_form_content" id="reference_form" class="form"<?= $this->out["piste"]["reference"] ? "" : " style=\"display: none\"" ?>>

    <li id="reference_input">

      <?php

        $this->set_out
        ( "form_params",
          array
          ( "name" => "reference",
            "label" => "r&eacute;f&eacute;rence &raquo; ",
            "can_delete" => false
          )
        );
        $this->set_out("xml_form_source", $this->out["piste"]["reference"]);
        require $this->out_file("views/content/sources/xml_form.php");

      ?>

    </li>

  </ul>

  <ul id="original_form" class="form"<?= $this->out["piste"]["reference"] ? " style=\"display: none\"" : "" ?>>

    <li>
      <label for="titre">titre</label>
      <p><input type="text" class="long_text" name="titre" id="titre" value="<?= $this->out["piste"]["titre"] ?>" /></p>
    </li>

    <li>
      <label for="licence">licence</label>
      <p>
        <select name="licence" id="licence">
        <?php foreach($this->out["licences"]["list"] as $id_licence => $licence) : ?>
          <option value="<?= $id_licence ?>"<?= $this->out["piste"]["licence"]["id"] == $id_licence ? "selected=\"selected\"" : "" ?>><?= $licence["nom"] ?></option>
        <?php endforeach; ?>
        </select>
      </p>
    </li>

    <li>
      <label for="date_creation">date de cr&eacute;ation</label>
      <p>
<?php

  $date_creation =
  ( $this->out["piste"]["date_creation"] ?
      explode("-", $this->out["piste"]["date_creation"])
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

<?php if($this->out["piste"]) : foreach($this->out["piste"]["documents"] as $id_document => $document) : ?>

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
      <textarea class="tinymce" cols="50" rows="10" name="description" id="description"><?= $this->out["piste"]["description"] ?></textarea>
    </li>

  </ul>

  <ul class="form">
    <li class="buttons">
      <input type="submit" value="Ajouter" />
    </li>
  </ul>
</form>

<?php else : ?>

<p>Vous ne g&eacute;rez aucun groupe pour le moment.</p>
<p>Pour ajouter une piste, vous devez d'abord <a href="<?= $this->url("users/groupes/add") ?>">cr&eacute;er un groupe</a>.</p>

<?php endif; ?>

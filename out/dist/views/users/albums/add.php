<?php require $this->out_file("views/tinymce.init.js.php"); ?>

<h2>Nouvel album</h2>

<ul class="admin">
  <li><a href="<?= $this->url("users/albums") ?>">Retour &agrave; la liste des albums</a></li>
</ul>

<?php if($this->out["groupes"]["total"] > 0) : ?>

<form name="album_form" action="<?= $this->url("users/albums/add") ?>" method="post" enctype="multipart/form-data">
  <ul class="form">

    <li>
      <label for="id_groupe">groupe</label>
      <select name="id_groupe" id="id_groupe">
      <?php foreach($this->out["groupes"]["list"] as $id_groupe => $groupe) : ?>
        <option value="<?= $id_groupe ?>"<?= $this->out["groupe"]["id"] == $id_groupe ? "selected=\"selected\"" : "" ?>><?= $groupe["nom"] ?></option>
      <?php endforeach; ?>
      </select>
    </li>

    <li>
      <label for="titre">titre</label>
      <input type="text" class="long_text" name="titre" id="titre" value="<?= $this->out["album"]["titre"] ?>" />
    </li>

    <li>
      <label for="image">icone</label>
      <input type="file" name="image" />
    </li>

    <li>
      <label for="licence">licence</label>
      <select name="licence" id="licence">
        <option value="0"<?= $this->out["album"]["licence"]["id"] == 0 ? "selected=\"selected\"" : "" ?>>licences pr&eacute;cis&eacute;es dans le contenu</option>
      <?php foreach($this->out["licences"]["list"] as $id_licence => $licence) : ?>
        <option value="<?= $id_licence ?>"<?= $this->out["album"]["licence"]["id"] == $id_licence ? "selected=\"selected\"" : "" ?>><?= $licence["nom"] ?></option>
      <?php endforeach; ?>
      </select>
    </li>

    <li>
      <label for="date_creation">date de cr&eacute;ation</label>
<?php

  $date_creation =
  ( $this->out["album"]["date_creation"] ?
      explode("-", $this->out["album"]["date_creation"])
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

    </li>

    <li>
      <h3><label>Fichiers</label> <a href="#" onclick="add_document(); return false;">Ajouter un fichier</a></h3>
    </li>

    <li>
      <div id="documents">

<?php if($this->out["album"]) : foreach($this->out["album"]["documents"] as $id_document => $document) : ?>

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
      <h3><label for="description">description</label></h3>
    </li>
    <li>
      <textarea class="tinymce" cols="50" rows="10" name="description" id="description"><?= $this->out["album"]["description"] ?></textarea>
    </li>



    <li class="buttons">
      <input type="submit" value="Ajouter" />
    </li>
  </ul>
</form>

<?php else : ?>

<p>Vous ne g&eacute;rez aucun groupe pour le moment.</p>
<p>Pour ajouter un album, vous devez d'abord <a href="<?= $this->url("users/groupes/add") ?>">cr&eacute;er un groupe</a>.</p>

<?php endif; ?>

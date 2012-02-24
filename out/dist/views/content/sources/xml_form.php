<?php

  $xml_name = isset($this->out["form_params"]["name"]) ? $this->out["form_params"]["name"] : "sml_xn";
  $xml_label = isset($this->out["form_params"]["label"]) ? $this->out["form_params"]["label"] : "";
  $xml_maj_url = isset($this->out["form_params"]["maj_url"]) ? $this->out["form_params"]["maj_url"] : "";
  $xml_id = isset($this->out["form_params"]["id"]) ? $this->out["form_params"]["id"] : "";
  $xml_id_suffix = isset($this->out["form_params"]["id"]) ? "_".$this->out["form_params"]["id"] : "";
  $xml_can_delete = isset($this->out["form_params"]["can_delete"]) ? $this->out["form_params"]["can_delete"] : false;
  $source = $this->out["xml_form_source"];

?>
<div class="admin_source_infos" id="<?= $xml_name ?>_infos<?= $xml_id_suffix ?>">

  <?php if($xml_id) : ?>
  <input type="hidden" name="<?= $xml_name ?>_id<?= $xml_id_suffix ?>" value="on" />
  <?php endif; ?>

  <div>
    <ul class="admin_source_head">
    <?php if($source["xml"]) : ?>
      <li class="admin_form_title"><?= $xml_label ?><a href="<?= $source["from"] ?>"><strong><?= $source["titre"] ?></strong></a></li>
    <?php endif; ?>
    <?php if($xml_maj_url && $this->config("cache_actif")) : ?>
      <li><a href="<?= $xml_maj_url ?>">Recharger</a></li>
    <?php endif; ?>
    <?php if($xml_can_delete) : ?>
      <li><a href="#" onclick="del_sml_xf('<?= $xml_name ?>_infos<?= $xml_id_suffix ?>'); return false;">Enlever</a></li>
    <?php endif; ?>
    </ul>
    <div class="admin_source_url">URL du fichier XML
    : <input type="text" id="<?= $xml_name ?><?= $xml_id_suffix ?>" name="<?= $xml_name ?><?= $xml_id_suffix ?>" size="40" value="<?= $source["xml"]["url"] ?>" />
    </div>
  </div>

  <?php if($source["titre"] && $source["xml"]) : ?>
  <ul class="xml_infos">
    <li>
      source :
      <a href="<?= $source["from"] ?>">
        <?= $source["titre"] ?>
        (<?= $source["auteur"] ?>)
      </a>
    </li>
    <li>
      xml :
      <a href="<?= $source["xml"]["url"] ?>"><?= $source["xml"]["url"] ?></a>
    </li>
    <?php if($source["licence"]) : ?>
    <li>licence : <a href="<?= $source["licence"]["url"] ?>"><?= $source["licence"]["nom"] ?></a></li>
    <?php endif; ?>
    <?php if($source["documents"]) : ?>
    <li>
      fichiers :
      <ul>
      <?php foreach($source["documents"] as $id_document => $document) : ?>
        <li><a href="<?= $document["url"] ?>"><?= $document["nom"] ?></a></li>
      <?php endforeach; ?>
      </ul>
    </li>
    <?php endif; ?>
  </ul>
  <?php endif; ?>

  <div>
    <span id="<?= $xml_name ?>_edit<?= $xml_id_suffix ?>" class="xml_edit_content">
      Editer le contenu du fichier XML
      <input type="checkbox" class="use_edit_<?= $xml_name ?>_content" id="use_edit_<?= $xml_name ?>_content<?= $xml_id_suffix ?>" name="use_edit_<?= $xml_name ?>_content<?= $xml_id_suffix ?>"<?= $source["xml"]["use_edit_content"] ? " checked=\"checked\"" : "" ?> />
      <span<?= $source["xml"]["use_edit_content"] ? "" : " style=\"display:none\"" ?>>
        <textarea name="edit_<?= $xml_name ?>_content<?= $xml_id_suffix ?>" id="edit_<?= $xml_name ?>_content<?= $xml_id_suffix ?>" cols="64" rows="15" wrap="off"><?= $source["xml"]["content"] ?></textarea>
      </span>
    </span>
  </div>

</div>

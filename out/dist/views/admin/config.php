<h2>Configuration</h2>

<h3>Générale</h3>

<form name="config_form" action="<?= $this->url("admin/config") ?>" method="post">
  <ul class="form">
    <li>
      <label for="site_name">nom du site</label>
      <p>
        <input type="text" name="site_name" id="site_name" value="<?= $this->out["config"]["site_name"] ?>" />
      </p>
    </li>
    <li>
      <label for="description">description</label>
      <p>
        <textarea cols="50" rows="10" name="description" id="description"><?= $this->out["config"]["description"] ?></textarea>
      </p>
    </li>
    <li>
      <label for="start_action">Accueil du site</label>
      <p>
        <select name="start_action" id="start_action" onchange="select_start_action(this.options[this.selectedIndex].value)">
          <option value="sources/groupe"<?= "sources/groupe" == $this->out["config"]["start_action"] ? " selected" : "" ?>>Liste des groupes</option>
<?php if($this->out["groupes"]["list"]) : ?>
          <option value="sources/groupe/view"<?= "sources/groupe/view" == $this->out["config"]["start_action"] ? " selected" : "" ?>>Un groupe</option>
<?php endif; ?>
<?php if($this->out["albums"]["list"]) : ?>
          <option value="sources/album/view"<?= "sources/album/view" == $this->out["config"]["start_action"] ? " selected" : "" ?>>Un album</option>
<?php endif; ?>
<?php if(substr($this->out["config"]["start_action"], 0, 7) != "sources") : ?>
          <option value="" selected>D&eacute;fini ailleurs</option>
<?php endif; ?>
        </select>

<?php if($this->out["groupes"]["list"]) : ?>
        <select name="groupe_param" id="groupe_param"<?php if($this->out["config"]["start_action"] != "sources/groupe/view") : ?> style="display:none;"<?php endif; ?>>
        <?php foreach($this->out["groupes"]["list"] as $id_groupe => $groupe) : ?>
          <option value="<?= $id_groupe ?>"<?= $this->out["config"]["start_action_params"]["id"] == $id_groupe ? " selected=\"selected\"" : "" ?>><?= $groupe["nom"] ?></option>
        <?php endforeach; ?>
        </select>
<?php endif; ?>

<?php if($this->out["albums"]["list"]) : ?>
        <select name="album_param" id="album_param"<?php if($this->out["config"]["start_action"] != "sources/album/view") : ?> style="display:none;"<?php endif; ?>>
        <?php foreach($this->out["albums"]["list"] as $id_album => $album) : ?>
          <option value="<?= $id_album ?>"<?= $this->out["config"]["start_action_params"]["album"] == $id_album ? " selected=\"selected\"" : "" ?>><?= $album["titre"] ?></option>
        <?php endforeach; ?>
        </select>
<?php endif; ?>

      </p>
    </li>
    <li>
      <label for="contact_form">formulaire de contact</label>
      <p>
        <input type="checkbox" name="contact_form" id="contact_form"<?= $this->out["config"]["contact_form"] ? " checked=\"checked\"" : "" ?> />
      </p>
    </li>
    <li id="email_li"<?= $this->out["config"]["contact_form"] ? "" : " style=\"display:none;\"" ?>>
      <label for="email">email</label>
      <div><p>
        <input type="text" name="email" id="email" value="<?= $this->out["config"]["email"] ?>" /><br />
        <br /><input type="checkbox" name="captcha" id="captcha"<?= $this->out["config"]["captcha"] ? " checked=\"checked\"" : "" ?> /> anti-spam
      </p></div>
    </li>
    <li>
      <label for="max_list">taille maximum des listes</label>
      <p>
        <span>nombre d'&eacute;l&eacute;ments &agrave; afficher dans une liste avant de paginer :</span>
        <input type="text" name="max_list" id="max_list" value="<?= $this->out["config"]["max_list"] ?>" />
      </p>
    </li>
  </ul>

<h3>Affichage du site</h3>

  <ul class="form">


    <li>
      <label for="out">template</label>
<?php

  $template = $this->config("out");
  $FOUND = false;
  foreach($this->out["out_pathes"] as $out_path) { if($template == $out_path."/") { $FOUND = true; break; } }
  if(!$FOUND) $template = $this->path("dist_out");

?>
      <p>
<?php if($this->out["out_pathes"]) : ?>
        <select name="out" id="out">
<?php foreach($this->out["out_pathes"] as $out_path) : ?>
          <option value="<?= $out_path ?>"<?= $template == $out_path."/" ? " selected=\"selected\"" : "" ?>><?= $out_path ?></option>
<?php endforeach; ?>
        </select>
<?php else : ?>
        <strong class="warn">Aucun dossier d'affichage ! ... ?</strong>
<?php endif; ?>

      </p>
    </li>

<?php foreach($this->out["out_config"] as $key => $config) : ?>
<?php if($config["type"] == "checkbox") : ?>
    <li>
      <label for="<?= "out_".$key ?>">&nbsp;</label>
      <p>
        <input type="checkbox" name="<?= "out_".$key ?>" id="<?= "out_".$key ?>"<?= (isset($this->out["config"]["out_".$key]) ? $this->out["config"]["out_".$key] : $config["default"]) ? " checked=\"checked\"" : "" ?> />
        <?= $config["text"] ?>
      </p>
    </li>
<?php endif; ?>
<?php endforeach; ?>
    <li class="buttons">
      <input type="submit" value="Enregistrer" />
    </li>
  </ul>
</form>

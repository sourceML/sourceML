<?php require $this->out_file("views/tinymce.init.js.php"); ?>

<h2>Nouveau groupe</h2>

<ul class="admin">
  <li><a href="<?= $this->url("users/groupes") ?>">Retour &agrave; la liste des groupes</a></li>
</ul>

<form name="groupe_form" action="<?= $this->url("users/groupes/add") ?>" method="post" enctype="multipart/form-data">
  <ul class="form">
    <li>
      <label for="nom">nom</label>
      <input type="text" name="nom" id="nom" value="<?= $this->out["groupe"]["nom"] ?>" />
    </li>
    <li>
      <label for="image">logo</label>
      <input type="file" name="image" />
    </li>
    <li>
      <label for="contact_form">formulaire de contact</label>
      <p>
        <input type="checkbox" name="contact_form" id="contact_form"<?= $this->out["groupe"]["contact_form"] ? " checked=\"checked\"" : "" ?> />
      </p>
    </li>
    <li id="email_li"<?= $this->out["groupe"]["contact_form"] ? "" : " style=\"display:none;\"" ?>>
      <label for="email">email</label>
      <div><p>
        <input type="text" name="email" id="email" value="<?= $this->out["groupe"]["email"] ?>" /><br />
        <br /><input type="checkbox" name="captcha" id="captcha"<?= $this->out["groupe"]["captcha"] ? " checked=\"checked\"" : "" ?> /> anti-spam
      </p></div>
    </li>
    <li>
      <label for="description">description</label>
    </li>
    <li>
      <textarea class="tinymce" cols="50" rows="10" name="description" id="description"><?= $this->out["groupe"]["description"] ?></textarea>
    </li>
    <li class="buttons">
      <input type="submit" value="Ajouter" />
    </li>
  </ul>
</form>

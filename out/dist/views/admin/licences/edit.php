<h2>Modifier une licence</h2>

<ul class="admin">
  <li><a href="<?= $this->url("admin/licences") ?>">Retour &agrave; la liste des licences</a></li>
</ul>

<form name="licence_form" action="<?= $this->url("admin/licences/edit", array("id" => $_GET[$this->param("id")])) ?>" method="post">
  <ul class="form">
    <li>
      <label for="nom">nom</label>
      <input type="text" name="nom" id="nom" value="<?= $this->out["licence"]["nom"] ?>" size="50" />
    </li>
    <li>
      <label for="url">url</label>
      <input type="text" name="url" id="url" value="<?= $this->out["licence"]["url"] ?>" size="50" />
    </li>
    <li class="buttons">
      <input type="submit" value="Enregistrer" />
    </li>
  </ul>
</form>
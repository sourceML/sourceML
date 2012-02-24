<h2>Nouvel utilisateur</h2>

<ul class="admin">
  <li><a href="<?= $this->url("admin/users") ?>">Retour &agrave; la liste des utilisateurs</a></li>
</ul>

<form name="user_form" action="<?= $this->url("admin/users/add") ?>" method="post">
  <ul class="form">
    <li>
      <label for="login">login</label>
      <input type="text" name="login" id="login" value="<?= isset($this->out["user"]["login"]) ? $this->out["user"]["login"] : "" ?>" />
    </li>
    <li>
      <label for="status">statut</label>
      <select name="status" id="status">
      <?php foreach($this->out["status"] as $id_status => $status) : ?>
        <option value="<?= $id_status ?>"<?= $this->out["user"]["status"] == $id_status ? " selected" : "" ?>>
          <?= $status["nom"] ?>
        </option>
      <?php endforeach; ?>
      </select>
    </li>
    <li>
      <label for="email">email</label>
      <input type="text" name="email" id="email" value="<?= isset($this->out["user"]["email"]) ? $this->out["user"]["email"] : "" ?>" />
    </li>
    <li>
      <label for="password">mot de passe</label>
      <input type="password" name="password" id="password" />
    </li>
    <li>
      <label for="password_confirm">confirmer le mot de passe</label>
      <input type="password" name="password_confirm" id="password_confirm" />
    </li>
    <li class="buttons">
      <input type="submit" value="Ajouter" />
    </li>
  </ul>
</form>
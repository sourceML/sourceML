<h2>Les utilisateurs</h2>

<ul class="admin">
  <li><a class="add" href="<?= $this->url("admin/users/add") ?>">Nouvel utilisateur</a></li>
</ul>

<?php

  $users_params = array();
  if($_GET[$this->param("status")]) $users_params["status"] = $_GET[$this->param("status")];

?>
<ul class="admin">
  <li>Afficher les utilisateurs pour</li>  
  <li>
    <select onchange="document.location=this.options[this.selectedIndex].value;">
      <option value="<?= $this->url("admin/users", $users_params) ?>"<?= $_GET[$this->param("alpha")] ? "" : " selected=\"selected\"" ?>>Tous les logins</option>
      <?php for($i = 65; $i <= 90; $i++) : $users_params["alpha"] = chr($i); ?>
      <option value="<?= $this->url("admin/users", $users_params) ?>"<?= $_GET[$this->param("alpha")] == chr($i) ? " selected=\"selected\"" : "" ?>><?= chr($i) ?></option>
      <?php endfor; ?>
    </select>
  </li>
  <?php

    if($this->out["status"]) :
      if($_GET[$this->param("alpha")]) $users_params["alpha"] = $_GET[$this->param("alpha")];
      else unset($users_params["alpha"]);
      unset($users_params["status"]);

?>
  <li>
    <select onchange="document.location=this.options[this.selectedIndex].value;">
      <option value="<?= $this->url("admin/users", $users_params) ?>"<?= $_GET[$this->param("status")] ? "" : " selected=\"selected\"" ?>>Tous les status</option>
      <?php foreach($this->out["status"] as $user_status) : $users_params["status"] = $user_status["id"]; ?>
      <option value="<?= $this->url("admin/users", $users_params) ?>"<?= $_GET[$this->param("status")] == $user_status["id"] ? " selected=\"selected\"" : "" ?>><?= $user_status["nom"] ?></option>
      <?php endforeach; ?>
    </select>
  </li>
  <?php endif; ?>
</ul>




<br/><br/>

<?php if($this->out["users"]["total"] > $this->config("max_list")) : ?>

<?php $items = "users"; $legend = "utilisateurs"; require $this->out_file("views/navig.php"); ?>

<?php endif; ?>

<table class="admin">
  <tr>
    <th>login</th>
    <th>email</th>
    <th>statut</th>
    <th align="center" colspan="2">actions</th>
  </tr>
<?php if($this->out["users"]["list"]) : ?>
<?php foreach($this->out["users"]["list"] as $id_user => $user) : ?>
  <tr class="hl">
    <td><?= $user["login"] ?></td>
    <td><a href="mailto:<?= $user["email"] ?>"><?= $user["email"] ?></a></td>
    <td><?= $this->out["status"][$user["status"]]["nom"] ?></td>
    <td class="action">
    <a href="<?= $this->url("admin/users/edit", array("id" => $user["login"])) ?>"
       class="admin_link"
       title="modifier cet utilisateur"><img src="<?= $this->out_file("icons/edit.gif") ?>" /></a>
    </td>
    <td class="action">
    <a href="<?= $this->url("admin/users/del", array("id" => $user["login"])) ?>"
       class="admin_link"
       title="supprimer cet utilisateur"><img src="<?= $this->out_file("icons/del.gif") ?>"
       onclick="return confirm('Supprimer cet utilisateur ?')"/></a>
    </td>
  </tr>
<?php endforeach; ?>
<?php else : ?>
  <tr>
    <td colspan="5"><p>Aucun utilisateur pour le moment...</p></td>
  </tr>
<?php endif; ?>
</table>

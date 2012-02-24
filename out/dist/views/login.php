<div id="login_box">
<?php if(!($user = $this->user())) : ?>
<form id="login_form"
      action="<?= $this->url("users/identification/login") ?>"
      method="post">
  <div>
    <input type="hidden" name="pass" value="" />
    <input type="hidden" name="from" value="<?= isset($_POST["from"]) ? $_POST["from"] : urlencode($_SERVER["REQUEST_URI"]) ?>" />
  </div>
  <table>
    <tr>
      <th>login</th>
      <td><input type="text" name="login" size="14" maxlength="25" /></td>
    </tr>
    <tr>
      <th>pass</th>
      <td><input type="password" name="password" size="14" maxlength="16" /></td>
    </tr>
    <tr>
      <td colspan="2" align="right"><input type="submit" value="Login" onclick="prepare_password('<?= $_SESSION["id"]?>')" /></td>
    </tr>
  </table>
  <div class="clear"><!-- --></div>
</form>

<?php else : ?>
  Bienvenue <b><?= $user["login"] ?></b>
  <ul>
    <li><a href="<?= $this->url("users/compte") ?>">compte</a></li>
    <li><a href="<?= $this->url("users/identification/logout") ?>">deconnexion</a></li>
<?php if($this->status_ok("admin")) : ?>
    <li><a href="<?= $this->url("admin") ?>">admin</a></li>
<?php endif; ?>
  </ul>
<?php endif; ?>
<div class="clear"><!-- --></div>
</div>
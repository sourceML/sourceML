<p id="a_propos_links">
<?php if($this->config("contact_form")) : ?>
  <a href="<?= $this->url("forms/contact") ?>">contact</a>
<?php endif; ?>
</p>

<p>
  <?= $this->config("version") ?>
<?php if(!($user = $this->user())) : ?>
  | <a href="<?= $this->url("users/identification") ?>">s'identifier</a>
<?php else : ?>
  | Bienvenue <strong><?= $user["login"] ?></strong>
  | <a href="<?= $this->url("users/compte") ?>">compte</a>
<?php if($this->status_ok("admin")) : ?>
  | <a href="<?= $this->url("admin") ?>">admin</a>
<?php endif; ?>
  | <a href="<?= $this->url("users/identification/logout") ?>">deconnexion</a>
<?php endif; ?>
</p>

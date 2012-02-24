<?php $data = $this->data(); if(($admin_menu = $data->get_link("admin")) && $admin_menu["subs"]) : ?>
<ul class="menu">
  <?php foreach($admin_menu["subs"] as $link) : ?>
  <li><a href="<?= $link["url"] ?>"><?= $link["intitule"] ?></a></li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>

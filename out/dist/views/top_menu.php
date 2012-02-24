<?php $start_action = $this->config("start_action"); ?>

<?php $data = $this->data(); if(($menu_top = $data->get_link("menu_top")) && $menu_top["subs"]) : ?>
<ul class="menu navig_menu">
  <?php foreach($menu_top["subs"] as $link) : ?>
  <li><a href="<?= $link["url"] ?>"><?= $link["intitule"] ?></a></li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
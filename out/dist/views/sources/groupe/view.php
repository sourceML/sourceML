<div class="description">
<?= $this->out["groupe"]["description"] ?>
</div>

<div class="clear"><!-- --></div>
<br />
<?php

  if
  ( $this->out_config("groupe_view_albums")
    && $this->out["albums"]["list"]
  ) :

?>

<h3 class="menu_albums">Albums</h3>
<ul class="menu_albums">
<?php foreach($this->out["albums"]["list"] as $id_album => $album) : ?>
  <li><a href="<?= $this->url("sources/album/view", array("album" => $id_album)) ?>" title="<?= str_replace("\"", "&quot;", $album["titre"]) ?>">

  <?php

    if
    (    $album["image_uri"]
      && file_exists($album["image_uri"])
      && ($img_size = $data->img_size($album["image_uri"], 90, 90)) !== false
    ) :
    $margin_top = floor((90 - $img_size["height"]) / 2);

  ?>
    <img src="<?= $this->path("web").$album["image_uri"]; ?>"
         width="<?= $img_size["width"] ?>"
         height="<?= $img_size["height"] ?>"
         style="margin-top:<?= $margin_top ?>px"
         alt="" />
  <?php endif; ?>
  <span><?= $album["titre"] ?></span>
  </a></li>
<?php endforeach; ?>
</ul>
<div class="clear"><!-- --></div>
<?php endif; ?>

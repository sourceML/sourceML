<?php if($this->out["album"]) : $album = $this->out["album"]; ?>

<ul id="album_links">
  <li><a href="<?= $this->url("sources/album/view", array("album" => $album["id"])) ?>">album</a></li>
</ul>

<ul class="menu_albums">
  <li><a href="<?= $this->url("sources/album/view", array("album" => $album["id"])) ?>" title="<?= str_replace("\"", "&quot;", $album["titre"]) ?>">

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
         style="margin-top:<?= $margin_top ?>px" />
  <?php endif; ?>
  <span><?= $album["titre"] ?></span>
  </a></li>
</ul>

<div class="clear"><!-- --></div>

<?php endif; ?>

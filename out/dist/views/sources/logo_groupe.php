<?php

  if
  (    $this->out["groupe"]
    && $this->out["groupe"]["image_uri"]
    && file_exists($this->out["groupe"]["image_uri"])
    && ($img_size = $data->img_size($this->out["groupe"]["image_uri"], 200, 130)) !== false
  ) :
  $margin_top = floor((150 - $img_size["height"]) / 2);

?>
<div class="logo_groupe">
  <a href="<?= $this->url("sources/groupe/view", array("id" => $this->out["groupe"]["id"])) ?>">
  <img src="<?= $this->path("web").$this->out["groupe"]["image_uri"]; ?>"
       width="<?= $img_size["width"] ?>"
       height="<?= $img_size["height"] ?>"
       style="margin-top:<?= $margin_top ?>px"
       alt="" />
  </a>
</div>
<?php endif; ?>

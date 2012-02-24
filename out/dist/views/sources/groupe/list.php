<?php if($this->out["groupes"]["list"]) : $data = $this->data(); ?>

<?php $items = "groupes"; $legend = "groupes"; require $this->out_file("views/navig.php"); ?>

<ul class="groupes">
  
<?php foreach($this->out["groupes"]["list"] as $id_groupe => $groupe) : ?>
  <li>
    <h4>
      <a href="<?= $this->url("sources/groupe/view", array("id" => $id_groupe)) ?>">
        <?php

          if
          (    $groupe["image_uri"]
            && file_exists($groupe["image_uri"])
            && ($img_size = $data->img_size($groupe["image_uri"], 120, 100)) !== false
          ) :
          $margin_top = floor((100 - $img_size["height"]) / 2);

        ?>
        <img src="<?= $this->path("web").$groupe["image_uri"]; ?>"
             width="<?= $img_size["width"] ?>"
             height="<?= $img_size["height"] ?>"
             style="margin-top:<?= $margin_top ?>px"
             alt="" />
        <?php endif; ?>
        <span><?= $groupe["nom"] ?></span>
        <br class="clear" />
      </a>
    </h4>
  </li>
<?php endforeach; ?>
</ul>

<?php $items = "groupes"; $legend = "groupes"; require $this->out_file("views/navig.php"); ?>

<?php endif; ?>

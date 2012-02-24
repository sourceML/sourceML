<h2>Albums</h2>
<?php $ariane_params = array(); ?>
<?php $FIRST = true; ?>
<h2>
<?php if($this->out["groupe"]) : $ariane_params["groupe"] = $this->out["groupe"]["id"]; ?>
  <?php if($FIRST) : $FIRST = false; else : ?>&raquo;<?php endif; ?>
  <a href="<?= $this->url("sources/groupe/view", array("id" => $this->out["groupe"]["id"])) ?>"><?= $this->out["groupe"]["nom"] ?></a>
<?php endif; ?>
<?php if($this->out["album"]) : ?>
  <?php if($FIRST) : $FIRST = false; else : ?>&raquo;<?php endif; ?>
  <a href="<?= $this->url("sources/album/view", array("album" => $this->out["album"]["id"])) ?>"><?= $this->out["album"]["titre"] ?></a>
<?php endif; ?>
</h2>

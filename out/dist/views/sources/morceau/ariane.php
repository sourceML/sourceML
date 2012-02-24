<h2>Morceaux</h2>
<?php $ariane_params = array(); ?>
<?php $FIRST = true; ?>
<h2>
<?php if($this->out["groupe"]) : $ariane_params["groupe"] = $this->out["groupe"]["id"]; ?>
  <?php if($FIRST) : $FIRST = false; else : ?>&raquo;<?php endif; ?>
  <a href="<?= $this->url("sources/groupe/view", array("id" => $this->out["groupe"]["id"])) ?>"><?= $this->out["groupe"]["nom"] ?></a>
<?php endif; ?>
<?php if($this->out["album"]) : $ariane_params["album"] = $this->out["album"]["id"]; ?>
  <?php $ariane_params["album"] = $this->out["album"]["id"]; ?>
  <?php if($FIRST) : $FIRST = false; else : ?>&raquo;<?php endif; ?>
  <a href="<?= $this->url("sources/album/view", $ariane_params) ?>"><?= $this->out["album"]["titre"] ?></a>
<?php endif; ?>
<?php if($this->out["morceau"]) : $ariane_params["morceau"] = $this->out["morceau"]["id"]; ?>
  <?php if($FIRST) : $FIRST = false; else : ?>&raquo;<?php endif; ?>
  <a href="<?= $this->url("sources/morceau/view", $ariane_params) ?>"><?= $this->out["morceau"]["titre"] ?></a>
<?php endif; ?>
</h2>

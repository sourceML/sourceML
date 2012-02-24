<?php if($this->out["groupe"]) : ?>
<h2>
  <a href="<?= $this->url("sources/groupe/view", array("id" => $this->out["groupe"]["id"])) ?>">
    <?= $this->out["groupe"]["nom"] ?>
  </a>
</h2>
<?php endif; ?>

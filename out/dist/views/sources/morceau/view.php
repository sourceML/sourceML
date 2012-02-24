<h2>
<?php $ariane_params = array("morceau" => $this->out["morceau"]["id"]); ?>
<?php if($this->out["album"]) : $ariane_params["album"] = $this->out["album"]["id"]; ?>
<a href="<?= $this->url("sources/album/view", $ariane_params) ?>">
<?= $this->out["album"]["titre"] ?>
</a>
&raquo;
<?php endif; ?>

<?php if($this->out["morceau"]["reference"]) : ?>
<span>r&eacute;f&eacute;rence </span>&raquo; <a href="<?= $this->out["morceau"]["reference"]["from"] ?>"><strong><?= $this->out["morceau"]["reference"]["titre"] ?> (<?= $this->out["morceau"]["reference"]["auteur"] ?>)</strong></a>
<?php else : ?>
<a href="<?= $this->url("sources/morceau/view", $ariane_params) ?>"><?= $this->out["morceau"]["titre"] ?></a>

<?php if($this->out["morceau"]["derivations"]) : ?>
      <br />
      <span class="small">
        d&eacute;rive de &raquo;
<?php $n = 0; foreach($this->out["morceau"]["derivations"] as $derivation) : ?>
        <?= $n ? ", " : "" ?><a href="<?= $derivation["from"] ?>"><?= $derivation["titre"] ?> (<?= $derivation["auteur"] ?>)</a>
<?php $n++; endforeach; ?>
      </span>
<?php endif; ?>

<?php endif; ?>

</h2>

<div class="source_arbo">

<?php

  $morceau = $this->out["morceau"];
  $display_name = false;
  $url_params = array("morceau" => $morceau["id"]);

?>

<ul class="sources">
<?php

  $source = $morceau;
  $source["url"] = $this->url("sources/morceau/view", $url_params);
  require $this->out_file("views/sources/source.php");

?>
</ul>

<?php if(!$display_name) : ?>
<div class="description">
  <?= $morceau["description"] ?>
</div>
<?php endif; ?>
<div class="clear"><!-- --></div>

</div>

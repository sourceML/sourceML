<h2>
<?php $ariane_params = array("piste" => $this->out["piste"]["id"]); ?>
<?php if($this->out["album"]) : $ariane_params["album"] = $this->out["album"]["id"]; ?>
<a href="<?= $this->url("sources/album/view", $ariane_params) ?>">
<?= $this->out["album"]["titre"] ?>
</a>
&raquo;
<?php endif; ?>

<?php if($this->out["morceau"]) : $ariane_params["morceau"] = $this->out["morceau"]["id"]; ?>
<a href="<?= $this->url("sources/morceau/view", $ariane_params) ?>">
<?= $this->out["morceau"]["reference"] ? $this->out["morceau"]["reference"]["titre"] : $this->out["morceau"]["titre"] ?>
</a>
&raquo;
<?php endif; ?>


<?php if($this->out["piste"]["reference"]) : ?>
<span>r&eacute;f&eacute;rence </span>&raquo; <a href="<?= $this->out["piste"]["reference"]["from"] ?>"><strong><?= $this->out["piste"]["reference"]["titre"] ?> (<?= $this->out["piste"]["reference"]["auteur"] ?>)</strong></a>
<?php else : ?>
<a href="<?= $this->url("sources/piste/view", $ariane_params) ?>"><?= $this->out["piste"]["titre"] ?></a>

<?php if($this->out["piste"]["derivations"]) : ?>
      <br />
      <span class="small">
        d&eacute;rive de &raquo;
<?php $n = 0; foreach($this->out["piste"]["derivations"] as $derivation) : ?>
        <?= $n ? ", " : "" ?><a href="<?= $derivation["from"] ?>"><?= $derivation["titre"] ?> (<?= $derivation["auteur"] ?>)</a>
<?php $n++; endforeach; ?>
      </span>
<?php endif; ?>

<?php endif; ?>

</h2>

<div class="source_arbo">

<?php

  $piste = $this->out["piste"];
  $display_name = false;
  $url_params = array("piste" => $piste["id"]);

?>

<ul class="sources">
<?php

  $source = $piste;
  $source["url"] = $this->url("sources/piste/view", $url_params);
  require $this->out_file("views/sources/source.php");

?>
</ul>

<?php if(!$display_name) : ?>
<div class="description">
  <?= $piste["description"] ?>
</div>
<?php endif; ?>
<div class="clear"><!-- --></div>

</div>

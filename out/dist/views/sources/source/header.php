<?php

  if($source["reference"])
  { $groupe = array
    ( "nom" => $source["reference"]["auteur"],
      "url" => $source["reference"]["from"]
    );
  }
  else
  { $groupe = $data->get_admin_groupe($source["groupes"]);
    if($groupe) $groupe["url"] = $this->url("sources/groupe/view", array("id" => $groupe["id"]));
  }

?>

  <h5>

<?php if($groupe) : ?><a class="auteur" href="<?= $groupe["url"] ?>"><?= $groupe["nom"] ?></a><?php endif; ?>

<?php if(!isset($display_name) || $display_name) : ?>

<?php if($source["reference"]) : ?>
    <span>r&eacute;f&eacute;rence &raquo; <a href="<?= $source["reference"]["from"] ?>"><strong><?= $source["reference"]["titre"] ?> (<?= $groupe["nom"] ?>)</strong></a></span>
<?php else : ?>
    <a href="<?= $source["url"] ?>"><?= $source["titre"] ?></a>
<?php endif; ?>
<?php if($source["derivations"]) : ?>
    <br />
    <span class="small">
      d&eacute;rive de &raquo;
<?php $n = 0; foreach($source["derivations"] as $derivation) : ?>
      <?= $n ? ", " : "" ?><a href="<?= $derivation["from"] ?>"><strong><?= $derivation["titre"] ?> (<?= $derivation["auteur"] ?>)</strong></a>
<?php $n++; endforeach; ?>
    </span>
<?php endif; ?>

<?php endif; ?>
    <br class="clear" />
  </h5>

  <div class="player_progress">
    <div class="loaded"><!-- --></div>
    <div class="position"><!-- --></div>
  </div>

<?php

  $documents = $source["reference"] ? $source["reference"]["documents"] : $source["documents"];
  if($documents) :

?>


  <ul class="documents">
  <?php

    foreach($documents as $id_document => $document) :
    $mp3_url = "";
    if(strtolower(substr($document["url"], -4)) == ".mp3") $mp3_url = $document["url"];

  ?>

    <li>
      <?php if($mp3_url) : ?>
      <script type="text/javascript">tracks["<?= $source["id"]  ?>"] = "<?= $mp3_url ?>";</script>
      <div class="player_controls">
        <a class="play" href="#" onclick="play('<?= $source["id"] ?>'); return false;"><img src="<?= $this->out_file("icons/play.png") ?>" alt="play" /></a>
        <a class="pause" href="#" onclick="pause(); return false;"><img src="<?= $this->out_file("icons/pause.png") ?>" alt="pause" /></a>
        <a class="stop" href="#" onclick="stop(); return false;"><img src="<?= $this->out_file("icons/stop.png") ?>" alt="stop" /></a>
      </div>
      <?php else : ?>
      <div class="no_player"><!-- --></div>
      <?php endif; ?>
      <a href="<?= $document["url"] ?>"><?= $document["nom"]; ?></a>
    </li>

  <?php endforeach; ?>
  </ul>

  <div class="clear"><!-- --></div>

  <?php endif; ?>

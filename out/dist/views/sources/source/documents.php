<?php

  $documents = $source["reference"] ? $source["reference"]["documents"] : $source["documents"];
  if($documents) :

?>


  <ul class="documents">
  <?php

    foreach($documents as $id_document => $document) :
    $ext = "";
    $audio_type = "";
    if(($k = strrpos($document["url"], ".")) !== false) $ext = strtolower(substr($document["url"], $k + 1));
    switch($ext){
      case "ogg":
        $audio_type = "audio/ogg";
        break;
      case "mp3":
        $audio_type = "audio/mp3";
        break;
    }

  ?>

    <li id="document_<?php echo $source["id"]."_".$id_document ?>">
      <?php if($audio_type) : ?>
      <div class="player_controls player" id="player_<?php echo $source["id"]."_".$id_document ?>">
        <a class="play" href="#"><img src="<?= $this->out_file("icons/play.png") ?>" alt="play" /></a>
        <a class="pause" href="#"><img src="<?= $this->out_file("icons/pause.png") ?>" alt="pause" /></a>
        <a class="stop" href="#"><img src="<?= $this->out_file("icons/stop.png") ?>" alt="stop" /></a>
        <audio id="audio_<?= $source["id"]."_".$id_document ?>">
          <source src="<?= $document["url"] ?>" type="<?= $audio_type ?>">
        </audio>
      </div>
      <?php endif; ?>
      <div class="no_player"><!-- --></div>
      <a href="<?= $document["url"] ?>"><?= $document["nom"]; ?></a>
    </li>

  <?php endforeach; ?>
  </ul>

  <div class="clear"><!-- --></div>

  <?php endif; ?>

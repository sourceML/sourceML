<h2>
  <a href="<?= $this->url("sources/album/view", array("album" => $this->out["album"]["id"])) ?>">
    <?= $this->out["album"]["reference"] ? $this->out["album"]["reference"]["titre"] : $this->out["album"]["titre"] ?>
  </a>
  &nbsp;
</h2>

<?php

  if
  (    $this->out["album"]["image_uri"]
    && file_exists($this->out["album"]["image_uri"])
    && ($img_size = $data->img_size($this->out["album"]["image_uri"], 150, 150)) !== false
  ) :
  $margin_top = floor((150 - $img_size["height"]) / 2);
  $album_image_uri = $this->path("web").$this->out["album"]["image_uri"];
?>
<p>
  <img src="<?= $album_image_uri ?>"
       width="<?= $img_size["width"] ?>"
       height="<?= $img_size["height"] ?>"
       style="margin-top:<?= $margin_top ?>px"
       alt="" />
</p>
<?php endif; ?>


<ul class="sources">
<?php

  $source_status = "album";
  $source = $this->out["album"];
  $display_name = false;
  require $this->out_file("views/sources/source.php");

?>
</ul>

<div class="description">
<?= $this->out["album"]["description"] ?>
</div>

<div class="clear"><!-- --></div>

<?php

  if($this->out["morceaux"]["list"]) :
  $source_status = "morceau";
  $url_params = array("album" => $this->out["album"]["id"]);

?>

<h3>Morceaux</h3>

<?php

  $HAS_MP3_DOCUMENT = false;
  foreach($this->out["morceaux"]["list"] as $id_source => $source)
  { $documents = $source["reference"] ? $source["reference"]["documents"] : $source["documents"];
    if($documents)
    { foreach($documents as $id_document => $document)
      { if(strtolower(substr($document["url"], -4)) == ".mp3")
        { $HAS_MP3_DOCUMENT = true;
          break;
        }
      }
    }
    if($HAS_MP3_DOCUMENT) break;
  }
  if($HAS_MP3_DOCUMENT) :

?>

<p id="play_all"><a href="#" onclick="play_all(); return false;">Ecouter l'album</a></p>
<div class="clear"><!-- --></div>

<?php endif; ?>

<?php $items = "morceaux"; $legend = "morceaux"; require $this->out_file("views/navig.php"); ?>

<ul class="sources source_arbo">
<?php

  foreach($this->out["morceaux"]["list"] as $id_source => $source)
  { $url_params["morceau"] = $id_source;
    $source["url"] = $this->url("sources/morceau/view", $url_params);
    $display_name = true;
    require $this->out_file("views/sources/source.php");
  }

?>
</ul>

<?php $items = "morceaux"; $legend = "morceaux"; require $this->out_file("views/navig.php"); ?>

<?php endif; ?>
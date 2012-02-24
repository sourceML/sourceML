<?php if($source) : $data = $this->data(); ?>

<li class="track" id="track_<?= $source["id"] ?>">

<?php

//  require $this->out_file("views/sources/source/menu_source.php");
  require $this->out_file("views/sources/source/header.php");
  require $this->out_file("views/sources/source/metas.php");
  require $this->out_file("views/sources/source/documents.php");

?>
  <div class="clear"><!-- --></div>
<?php

  require $this->out_file("views/sources/source/arbo.php");

?>

</li>

<?php endif; ?>

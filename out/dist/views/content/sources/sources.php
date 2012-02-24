<?php

  if($sources = $this->out["sources"]["list"]) :
  $url_params = $this->out["url_params"];
  foreach($sources as $id_source => $source) :
    $url_params[$this->out["source_param"]] = $id_source;

?>
<ul class="sources">
<?php

  $source["url"] = $this->url("sources/".$this->out["source_controller"]."/view", $url_params);
  require $this->out_file("views/sources/source.php");

?>
</ul>
<?php

  endforeach;
  else : echo "<p class=\"no_source\">pas de source</p>";
  endif;

?>
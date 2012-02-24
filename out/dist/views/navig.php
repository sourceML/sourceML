<?php if($this->config("max_list") && isset($this->out[$items]["total"]) && ($this->out[$items]["total"] > $this->config("max_list"))) : ?>
<div class="navig">
<?php

  echo sml_navig
  ( $_GET[$this->param("start")],
    $this->out[$items]["total"],
    $this->config("max_list"),
    $_SERVER["REQUEST_URI"],
    $this->param("start"),
	 " ".$legend
  );

?>
</div>
<?php endif; ?>
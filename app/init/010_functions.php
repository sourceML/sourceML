<?php

  function debug($content)
  { echo "<pre class=\"debug\">\n".htmlentities(print_r($content, true))."\n</pre>\n";
  }

?>
<?php

  class sml_env_links extends sml_env
  {

    function init_links()
    { $data = $this->data();
      return $data->init_links();
    }

    function get_link($path = null)
    { $data = $this->data();
      return $data->get_link($path);
    }

    function set_link($path, $url, $intitule = "", $position = 0)
    { $data = $this->data();
      return $data->set_link($path, $url, $intitule, $position);
    }

  }

?>
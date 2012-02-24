<?php

  class sml_data_sources_xml_pathes extends sml_data
  {

    function source_xml_path($id_source)
    { $env = $this->env();
      return $env->path("content")."sources/".$id_source.".xml";
    }

    function reference_edit_xml_dir_path()
    { $env = $this->env();
      return $env->path("content")."sources/references/";
    }

    function reference_edit_xml_path($id_source)
    { $env = $this->env();
      return $this->reference_edit_xml_dir_path().$id_source.".xml";
    }

    function derivations_edit_xml_dir_path($id_source = null)
    { $env = $this->env();
      return $env->path("content")."sources/derivations/".(isset($id_source) ? $id_source."/" : "");
    }

    function derivation_edit_xml_path($id_source, $id_source_derivation)
    { $env = $this->env();
      return $this->derivations_edit_xml_dir_path($id_source).$id_source_derivation.".xml";
    }

    function source_xml_url($id_source)
    { $env = $this->env();
      return "http://".$_SERVER["SERVER_NAME"].$env->path("web").$this->source_xml_path($id_source);
    }

  }

?>
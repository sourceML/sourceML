<?php

  class sml_data_sources_xml extends sml_data
  {

    function get_source_xml_from_url($url, $IGNORE_UPDATE = false)
    { $env = $this->env();
      if($env->config("cache_actif")) return $this->get_source_xml_from_cache($url, $IGNORE_UPDATE);
      else return @file_get_contents($url);
    }

    # ----------------------------------------------------------------------------------------
    #                                                                               XML source
    #

    function set_source_xml($id, $params = array())
    { $env = $this->env();
      $OK = false;
      if(strlen($id) > 0 && ($fh = @fopen($this->source_xml_path($id), "wb")) !== false)
      { if(($content = $this->source_xml_content($id, $params)) !== false)
        { if(@fwrite($fh, $content)) $OK = true;
        }
        @fclose($fh);
      }
      return $OK;
    }

    function maj_source_xml_groupe($groupe)
    { $OK = true;
      if($groupe !== false)
      { $sgbd = $this->sgbd();
        $env = $this->env();
        if(($sources = $this->sources(array("id_groupe" => $groupe["id"]))) !== false)
        { foreach($sources["list"] as $id_source => $source)
          { if(!$this->set_source_xml($source["id"], array("source" => $source)))
            { $OK = false;
              break;
            }
          }
        }
        else $OK = false;
      }
      else $OK = false;
      return $OK;
    }

    function maj_source_xml_licence($licence)
    { $OK = true;
      if($groupe !== false)
      { $sgbd = $this->sgbd();
        $env = $this->env();
        if(($sources = $this->sources(array("id_licence" => $licence["id"]))) !== false)
        { foreach($sources["list"] as $id_source => $source)
          { if(!$this->set_source_xml($source["id"], array("source" => $source)))
            { $OK = false;
              break;
            }
          }
        }
        else $OK = false;
      }
      else $OK = false;
      return $OK;
    }

    function maj_source_xml_all()
    { $OK = true;
      if($groupe !== false)
      { $sgbd = $this->sgbd();
        $env = $this->env();
        if(($sources = $this->sources(array())) !== false)
        { foreach($sources["list"] as $id_source => $source)
          { if(!$this->set_source_xml($source["id"], array("source" => $source)))
            { $OK = false;
              break;
            }
          }
        }
        else $OK = false;
      }
      else $OK = false;
      return $OK;
    }

    function get_source_xml($id_source)
    { $source_file = $this->source_xml_path($id_source);
      if(file_exists($source_file))
      { return @file_get_contents($source_file);
      }
      return "";
    }

    function del_source_xml($id_source)
    { $env = $this->env();
      if(file_exists($this->source_xml_path($id_source)))
      { return
           $this->del_edit_derivations(array("id_source" => $id_source))
        && $this->del_edit_reference_content($id_source)
        && @unlink($this->source_xml_path($id_source));
      }
      return true;
    }

    # ----------------------------------------------------------------------------------------
    #                                                                   edition XML derivation
    #

    function set_edit_derivation_content($id_source, $id_source_derivation, $content)
    { $env = $this->env();
      $derivations_dir = $this->derivations_edit_xml_dir_path();
      if(!file_exists($derivations_dir) || !is_dir($derivations_dir)) @mkdir($derivations_dir);
      if(file_exists($derivations_dir) && is_dir($derivations_dir))
      { $derivations_dir = $this->derivations_edit_xml_dir_path($id_source);
        if(!file_exists($derivations_dir) || !is_dir($derivations_dir)) @mkdir($derivations_dir);
        if(file_exists($derivations_dir) && is_dir($derivations_dir))
        { if($fh = @fopen($this->derivation_edit_xml_path($id_source, $id_source_derivation), "w+"))
          { if(@fwrite($fh, $content))
            { @fclose($fh);
              return true;
            }
            @fclose($fh);
          }
        }
      }
      return false;
    }
    
    function get_edit_derivation_content($id_source, $id_source_derivation)
    { $env = $this->env();
      $derivation_file = $this->derivation_edit_xml_path($id_source, $id_source_derivation);
      $derivation_content = "";
      if(file_exists($derivation_file))
      { $derivation_content = @file_get_contents($derivation_file);
      }
      return $derivation_content;
    }

    function del_edit_derivation_content($id_source, $id_source_derivation)
    { $env = $this->env();
      $derivation_file = $this->derivation_edit_xml_path($id_source, $id_source_derivation);
      $OK = true;
      if(file_exists($derivation_file))
      { $OK = @unlink($derivation_file);
      }
      return $OK;
    }

    function del_edit_derivations($id_source)
    { $env = $this->env();
      $derivations_dir = $this->derivations_edit_xml_dir_path($id_source);
      $OK = true;
      if(is_dir($derivations_dir))
      { if($dh = opendir($derivations_dir))
        { while($OK && (($file = readdir($dh)) !== false))
          { if(preg_match("/^.+\.xml$/", $file))
            { $OK = @unlink($derivations_dir.$file) && $OK;
            }
          }
          closedir($dh);
          $OK = @rmdir($derivations_dir) && $OK;
        }
        else $OK = false;
      }
      return $OK;
    }

    # ----------------------------------------------------------------------------------------
    #                                                                    edition XML reference
    #

    function set_edit_reference_content($id_source, $content)
    { $env = $this->env();
      $references_dir = $this->reference_edit_xml_dir_path();
      if(!file_exists($references_dir) || !is_dir($references_dir)) @mkdir($references_dir);
      if(file_exists($references_dir) && is_dir($references_dir))
      { if($fh = @fopen($this->reference_edit_xml_path($id_source), "w+"))
        { if(@fwrite($fh, $content))
          { @fclose($fh);
            return true;
          }
          @fclose($fh);
        }
      }
      return false;
    }

    function get_edit_reference_content($id_source)
    { $env = $this->env();
      $reference_file = $this->reference_edit_xml_path($id_source);
      $reference_content = "";
      if(file_exists($reference_file))
      { $reference_content = @file_get_contents($reference_file);
      }
      return $reference_content;
    }

    function del_edit_reference_content($id_source)
    { $env = $this->env();
      $reference_file = $this->reference_edit_xml_path($id_source);
      $OK = true;
      if(file_exists($reference_file))
      { $OK = @unlink($reference_file);
      }
      return $OK;
    }

  }

?>
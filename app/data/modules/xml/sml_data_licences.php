<?php

  class sml_data_licences extends sml_data
  {

    function licences($start = null)
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $licences = array("list" => array(), "total" => 0);
      if($rst = $sgbd->open_data("licences"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { $licences["total"]++;
            if(!isset($start) || !$env->config("max_list") || ($licences["total"] > $start && $licences["total"] < ($start + $env->config("max_list"))))
            { $licences["list"][$v_rst["id"]] = $v_rst;
            }
          }
          else
          { $licences = false;
            break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $licences = false;
      return $licences;
    }

    function licence($id)
    { if($id == 0) return $id;
      $sgbd = $this->sgbd();
      return $sgbd->get_data("licences", $id);
    }

    function add_licence($nom, $url)
    { $sgbd = $this->sgbd();
      return $sgbd->add_data
      ( "licences",
        array
        ( "nom" => $nom,
          "url" => $url
        )
      );
    }

    function set_licence($id, $nom, $url)
    { if(($licence = $this->licence($id)) !== false)
      { $sgbd = $this->sgbd();
        if($nom != $licence["nom"] || $url != $licence["url"])
        { $licence["nom"] = $nom;
          $licence["url"] = $url;
          if(!$this->maj_source_xml_licence($licence)) return false;
        }
        return $sgbd->set_data
        ( "licences",
          $id,
          array
          ( "nom" => $nom,
            "url" => $url
          )
        );
      }
      return false;
    }

    function del_licence($id)
    { $OK = true;
      $USED = false;
      $sgbd = $this->sgbd();
      $env = $this->env();
      if($rst = $sgbd->open_data("sources"))
      { while($source = $sgbd->fetch_data($rst))
        { if(isset($source))
          { if($source["licence"] == $id)
            { $USED = true;
              break;
            }
          }
          else
          { $OK = false;
            break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $OK = false;
      if($OK)
      { if($USED) return 1;
        return $sgbd->del_data("licences", $id) ? true : false;
      }
      return false;
    }

  }

?>
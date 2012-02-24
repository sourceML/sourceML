<?php

  class sml_data_licences extends sml_data
  {

    function licences($start = null)
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $licences = array("list" => array(), "total" => 0);
      $SELECT = "SELECT *";
      $FROM = " FROM #--licences";
      $WHERE = "";
      $LIMIT = (isset($start) && $env->config("max_list") ? " LIMIT ".$env->config("max_list")." OFFSET ".$start : "");
      $sql = "SELECT count(*) as n FROM(".$SELECT.$FROM.$WHERE.") res";
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $licences["total"] = $v_rst["n"];
      $sgbd->free_result($rst);
      if($licences["total"] > 0)
      { $sql = "SELECT * FROM(".$SELECT.$FROM.$WHERE.$LIMIT.") res";
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return false;
        while($v_rst = $sgbd->fetch_assoc($rst)) $licences["list"][$v_rst["id"]] = $v_rst;
        $sgbd->free_result($rst);
      }
      return $licences;
    }

    function licence($id)
    { $sgbd = $this->sgbd();
      $licence = array();
      $sql = "SELECT * from #--licences WHERE id=".$this->eq($id);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $licence = $v_rst;
      $sgbd->free_result($rst);
      return $licence;
    }

    function add_licence($nom, $url)
    { $sgbd = $this->sgbd();
      $sql =
       "INSERT INTO #--licences(nom, url) VALUES"
      ."( ".$this->eq($nom)
      .", ".$this->eq($url)
      .")";
      return $sgbd->query($sql) ? true : false;
    }

    function set_licence($id, $nom, $url)
    { if(($licence = $this->licence($id)) !== false)
      { $sgbd = $this->sgbd();
        $sql =
         "UPDATE #--licences SET"
        ."  nom=".$this->eq($nom)
        .", url=".$this->eq($url)
        ." WHERE id=".$id;
        if($sgbd->query($sql))
        { if($nom != $licence["nom"] || $url != $licence["url"])
          { $licence["nom"] = $nom;
            $licence["url"] = $url;
            if(!$this->maj_source_xml_licence($licence)) return false;
          }
          return true;
        }
      }
      return false;
    }

    function del_licence($id)
    { $sgbd = $this->sgbd();
      $sql = "SELECT count(*) as n FROM #--sources WHERE licence=".$this->eq($id);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $USED = $v_rst["n"];
      $sgbd->free_result($rst);
      if($USED) return 1;
      $sql = "DELETE FROM #--licences WHERE id=".$this->eq($id);
      return $sgbd->query($sql) ? true : false;
    }

  }

?>
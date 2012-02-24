<?php

  class sml_data_groupes extends sml_data
  {

    # ----------------------------------------------------------------------------------------
    #                                                                                  groupes
    #

    function groupes($id_user = null, $start = null, $alpha = null)
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $groupes = array("list" => array(), "total" => 0);
      $SELECT = "SELECT *";
      $FROM = " FROM #--groupes";
      $WHERE = "";
      $WHERE .= (isset($id_user) ? ($WHERE ? " AND" : " WHERE")." id_user=".$id_user : "");
      $WHERE .= (isset($alpha) ? ($WHERE ? " AND" : " WHERE")." LEFT(login, 1)=".$this->eq($alpha) : "");
      $LIMIT = (isset($start) && $env->config("max_list") ? " LIMIT ".$env->config("max_list")." OFFSET ".$start : "");
      $sql = "SELECT count(*) as n FROM(".$SELECT.$FROM.$WHERE.") res";
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $groupes["total"] = $v_rst["n"];
      $sgbd->free_result($rst);
      if($groupes["total"] > 0)
      { $sql = "SELECT * FROM(".$SELECT.$FROM.$WHERE.$LIMIT.") res";
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return false;
        while($v_rst = $sgbd->fetch_assoc($rst))
        { $groupes["list"][$v_rst["id"]] = $v_rst;
          $groupes["list"][$v_rst["id"]]["image_uri"] =
          ( $v_rst["image"] ?
              $env->path("content")."uploads/".$v_rst["image"]
            : ""
          );
        }
        $sgbd->free_result($rst);
      }
      return $groupes;
    }

    function groupe($id)
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $groupe = array();
      $sql = "SELECT * from #--groupes WHERE id=".$this->eq($id);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst))
      { $groupe = $v_rst;
        $groupe["image_uri"] =
        ( $groupe["image"] ?
            $env->path("content")."uploads/".$groupe["image"]
          : ""
        );
      }
      $sgbd->free_result($rst);
      return $groupe;
    }

    function groupe_exists($nom, $other_than_id = null)
    { $sgbd = $this->sgbd();
      $EXISTS = 0;
      $sql = "SELECT count(*) as n from #--groupes WHERE nom=".$this->eq($nom);
      if(isset($other_than_id)) $sql .= " AND id!=".$this->eq($other_than_id);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $EXISTS = $v_rst["n"];
      $sgbd->free_result($rst);
      return $EXISTS;
    }

    function add_groupe($id_user, $nom, $image, $description, $email, $contact_form, $captcha)
    { $sgbd = $this->sgbd();
      $sql =
       "INSERT INTO #--groupes(id_user, nom, image, description, email, contact_form, captcha) VALUES"
      ."( ".$this->eq($id_user)
      .", ".$this->eq($nom)
      .", ".$this->eq($image)
      .", ".$this->eq($description)
      .", ".$this->eq($email)
      .", ".$this->eq($contact_form)
      .", ".$this->eq($captcha)
      .")";
      return $sgbd->query($sql);
    }

    function set_groupe($id, $nom, $image, $description, $email, $contact_form, $captcha)
    { if(($groupe = $this->groupe($id)) !== false)
      { $sgbd = $this->sgbd();
        $sql =
         "UPDATE #--groupes SET"
        ."  nom=".$this->eq($nom)
        .", image=".$this->eq($image)
        .", description=".$this->eq($description)
        .", email=".$this->eq($email)
        .", contact_form=".$this->eq($contact_form)
        .", captcha=".$this->eq($captcha)
        ." WHERE id=".$id;
        if($sgbd->query($sql))
        { if($nom != $groupe["nom"])
          { $groupe["nom"] = $nom;
            if(!$this->maj_source_xml_groupe($groupe)) return false;
          }
          return true;
        }
      }
      return false;
    }

    function del_groupe($id)
    { $sgbd = $this->sgbd();
      $sql = "SELECT count(*) as n FROM #--source_groupes WHERE id_groupe=".$this->eq($id);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $HAS_SOURCES = $v_rst["n"];
      $sgbd->free_result($rst);
      if($HAS_SOURCES) return 1;
      $sql = "DELETE FROM #--groupes WHERE id=".$this->eq($id);
      return $sgbd->query($sql);
    }

  }

?>
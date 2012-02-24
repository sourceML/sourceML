<?php

  class sml_data_source_groupes extends sml_data
  {

    function init_groupe_status()
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $groupe_status_table_exists = $sgbd->table_exists("#--groupe_status");
      if(isset($groupe_status_table_exists))
      { if($groupe_status_table_exists) return true;
        if($env->config("UPGRADE_DB"))
        { if($env->bdd("sgbd") == "mysql" || $env->bdd("sgbd") == "sqlite")
          { $res = $this->create_groupe_status();
            if($res !== true) return $res;
            $res = $this->migre_groupe_status();
            if($res !== true) return $res;
            return true;
          }
          else return "sgbd inconnu : ".$env->bdd("sgbd");
        }
        else return "la table groupe_status n'existe pas. la base de donnees doit etre mise a jour.";
      }
      else return "impossible de savoir si la table groupe_status existe";
    }

    function create_groupe_status()
    { $sgbd = $this->sgbd();
      $env = $this->env();
      if($env->bdd("sgbd") == "mysql")
      { $create_groupe_status_sql =
         "CREATE TABLE #--groupe_status"
        ."( id int(11) NOT NULL AUTO_INCREMENT"
        .", nom varchar(63) NOT NULL"
        .", PRIMARY KEY (id)"
        .")";
        if(!$sgbd->query($create_groupe_status_sql)) return "impossible de creer la table groupe_status";
        $create_source_groupes_sql =
         "CREATE TABLE #--source_groupes"
        ."( id int(11) NOT NULL AUTO_INCREMENT"
        .", id_source int(11) NOT NULL"
        .", id_groupe int(11) NOT NULL"
        .", id_groupe_status int(11) NOT NULL"
        .", PRIMARY KEY (id)"
        .")";
        if(!$sgbd->query($create_source_groupes_sql)) return "impossible de creer la table source_groupes";
        $insert_groupe_status_sql =
         "INSERT INTO #--groupe_status(id, nom) VALUES"
        ." (2, 'editeur')"
        .",(3, 'contributeur')"
        .",(1, 'admin')";
        if(!$sgbd->query($insert_groupe_status_sql)) return "impossible de renseigner la table groupe_status";
        return true;
      }
      elseif($env->bdd("sgbd") == "sqlite")
      { $create_groupe_status_sql =
         "CREATE TABLE #--groupe_status"
        ."( id INTEGER NOT NULL PRIMARY KEY"
        .", nom VARCHAR NOT NULL"
        .")";
        if(!$sgbd->query($create_groupe_status_sql)) return "impossible de creer la table groupe_status";
        $create_source_groupes_sql =
         "CREATE TABLE #--source_groupes"
        ."( id INTEGER NOT NULL PRIMARY KEY"
        .", id_source INTEGER NOT NULL"
        .", id_groupe INTEGER NOT NULL"
        .", id_groupe_status INTEGER NOT NULL"
        .")";
        if(!$sgbd->query($create_source_groupes_sql)) return "impossible de creer la table source_groupes";
        $insert_groupe_status_sql =
         "INSERT INTO #--groupe_status VALUES (2, 'editeur')";
        if(!$sgbd->query($insert_groupe_status_sql)) return "impossible de renseigner la table groupe_status";
        $insert_groupe_status_sql =
         "INSERT INTO #--groupe_status VALUES (3, 'contributeur')";
        if(!$sgbd->query($insert_groupe_status_sql)) return "impossible de renseigner la table groupe_status";
        $insert_groupe_status_sql =
         "INSERT INTO #--groupe_status VALUES (1, 'admin')";
        if(!$sgbd->query($insert_groupe_status_sql)) return "impossible de renseigner la table groupe_status";
        return true;
      }
      else return "mise a jour possible impossible (sgbd inconnu : ".$env->bdd("sgbd").")";
    }

    function migre_groupe_status()
    { $sgbd = $this->sgbd();
      $env = $this->env();
      if($env->bdd("sgbd") == "mysql" || $env->bdd("sgbd") == "sqlite")
      { 
        // mise a jour du chemin des images des groupes pour y ajouter le chemin du user
        $sql = "SELECT * FROM #--groupes";
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return false;
        while($v_rst = $sgbd->fetch_assoc($rst))
        { if($v_rst["image"] && !preg_match("/^[0-9]+\/.*$/", $v_rst["image"]))
          { $sql =
             "UPDATE #--groupes SET"
            ." image=".$this->eq($v_rst["id_user"]."/".$v_rst["image"])
            ." WHERE id=".$v_rst["id"];
            if(!$sgbd->query($sql)) return "erreur lors de la mise a jour des images des groupes (id: ".$v_rst["id"].")";
          }
        }
        $sgbd->free_result($rst);
        // mise a jour du chemin des images des sources (source_infos) pour y ajouter le chemin du user
        $sql =
         "SELECT #--source_infos.*, #--groupes.id_user"
        ." FROM #--source_infos, #--sources, #--groupes"
        ." WHERE #--source_infos.id_source=#--sources.id"
        ." AND #--sources.id_groupe=#--groupes.id";
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return "impossible de lire la table des infos de source pour modifier les chemins des images";
        while($v_rst = $sgbd->fetch_assoc($rst))
        { if($v_rst["key"] == "image" && $v_rst["value"] && !preg_match("/^[0-9]+\/.*$/", $v_rst["value"]))
          { $sql =
             "UPDATE #--source_infos SET"
            ." value=".$this->eq($v_rst["id_user"]."/".$v_rst["value"])
            ." WHERE id=".$v_rst["id"];
            if(!$sgbd->query($sql)) return "erreur lors de la mise a jour des images des sources (id: ".$v_rst["id"].")";
          }
        }
        $sgbd->free_result($rst);
        // transfere des droits des groupes dans la table source_groupes avec un statut admin
        $sql = "SELECT * FROM #--sources";
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return "impossible de lire la table des sources pour migrer les droits des groupes";
        while($v_rst = $sgbd->fetch_assoc($rst))
        { $sql =
           "INSERT INTO #--source_groupes(id_source, id_groupe, id_groupe_status) VALUES"
          ."( ".$v_rst["id"]
          .", ".$v_rst["id_groupe"]
          .", 1"
          .")";
          if(!$sgbd->query($sql)) return "erreur lors de la migration des droits de groupe (id_source: ".$v_rst["id"].")";
        }
        $sgbd->free_result($rst);
        // suppression du champ id_groupe dans la table sources
        if($env->bdd("sgbd") == "mysql")
        { $sql = "ALTER TABLE #--sources DROP id_groupe";
          if(!$sgbd->query($sql)) return "erreur lors de la suppression du champ id_groupe dans la table sources";
          // suppression du champ derivation dans la table sources
          $sql = "ALTER TABLE #--sources DROP derivation";
          if(!$sgbd->query($sql)) return "erreur lors de la suppression du champ derivation dans la table sources";
          // suppression de la table source_status_composition
          $sql = "DROP TABLE #--source_status_composition";
          if(!$sgbd->query($sql)) return "erreur lors de la suppression de la table source_status_composition";
          return true;
        }
        elseif($env->bdd("sgbd") == "sqlite")
        { $tmp_table_name = "g".substr(md5(rand()), 0, 15)."_sml_sources";
          $sql =
           "ALTER TABLE \"main\".\"sml_sources\""
          ." RENAME TO \"".$tmp_table_name."\"";
          if(!$sgbd->query($sql)) return "erreur lors de la suppression des champs 'id_groupe' et 'derivation' dans la table sources (renomage en tmp)";
          $sql =
           "CREATE TABLE \"main\".\"sml_sources\""
          ." (\"id\" INTEGER PRIMARY KEY  NOT NULL"
          ." ,\"status\" INTEGER NOT NULL"
          ." ,\"reference\" VARCHAR DEFAULT (NULL)"
          ." ,\"titre\" VARCHAR DEFAULT (NULL)"
          ." ,\"licence\" INTEGER DEFAULT (NULL)"
          ." ,\"date_creation\" text DEFAULT (NULL)"
          ." ,\"date_inscription\" text NOT NULL"
          ." )";
          if(!$sgbd->query($sql)) return "erreur lors de la suppression des champs 'id_groupe' et 'derivation' dans la table sources (creation de la nouvelle table)";
          $sql =
           "INSERT INTO \"main\".\"sml_sources\""
          ." SELECT"
          ." \"id\",\"status\",\"reference\""
          .",\"titre\",\"licence\""
          .",\"date_creation\",\"date_inscription\""
          ." FROM \"main\".\"".$tmp_table_name."\"";
          if(!$sgbd->query($sql)) return "erreur lors de la suppression des champs 'id_groupe' et 'derivation' dans la table sources (insertions dans la nouvelle table)";
          $sgbd->query("DROP TABLE \"main\".\"".$tmp_table_name."\"");
          $sgbd->query("DROP TABLE \"main\".\"source_status_composition\"");
          return true;
        }
      }
      else return "mise a jour possible impossible (sgbd inconnu : ".$env->bdd("sgbd").")";
    }

// --------------------------------------------------------------------

    function source_groupes($id_source)
    { $env = $this->env();
      $sgbd = $this->sgbd();
      $sql =
       "SELECT #--groupes.*, #--source_groupes.id_groupe_status"
      ." FROM #--groupes, #--source_groupes"
      ." WHERE #--source_groupes.id_groupe=#--groupes.id"
      ." AND #--source_groupes.id_source=".$this->eq($id_source);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      $groupes = array();
      while($v_rst = $sgbd->fetch_assoc($rst))
      { $v_rst["image_uri"] =
        ( $v_rst["image"] ?
          $env->path("content")."uploads/".$v_rst["image"]
          : ""
        );
        $groupes[$v_rst["id"]] = $v_rst;
      }
      $sgbd->free_result($rst);
      return $groupes;
    }

    function add_source_groupe($id_source, $id_groupe, $id_groupe_status)
    { $sgbd = $this->sgbd();
      $sql =
       "INSERT INTO #--source_groupes(id_source, id_groupe, id_groupe_status) VALUES"
      ."( ".$this->eq($id_source)
      .", ".$this->eq($id_groupe)
      .", ".$this->eq($id_groupe_status)
      .")";
      return $sgbd->query($sql);
    }

    function set_source_groupe($id, $id_groupe_status)
    { if(($groupe = $this->groupe($id)) !== false)
      { $sgbd = $this->sgbd();
        $sql =
         "UPDATE #--source_groupes SET"
        ."  id_groupe_status=".$this->eq($id_groupe_status)
        ." WHERE id=".$id;
        if($sgbd->query($sql)) return true;
      }
      return false;
    }

    function del_source_groupes($id_source)
    { $sgbd = $this->sgbd();
      $sql = "DELETE FROM #--source_groupes WHERE id_source=".$this->eq($id_source);
      return $sgbd->query($sql) ? true : false;
    }

    function del_source_groupe($id)
    { $sgbd = $this->sgbd();
      $sql = "DELETE FROM #--source_groupes WHERE id=".$this->eq($id);
      return $sgbd->query($sql) ? true : false;
    }

// --------------------------------------------------------------------

    function id_groupe_status_admin()        { return 1; }
    function id_groupe_status_editeur()      { return 2; }
    function id_groupe_status_contributeur() { return 3; }

    function get_admin_groupe($groupes)
    { $groupe = array();
      if(is_array($groupes)) foreach($groupes as $source_groupe)
      { if($source_groupe["id_groupe_status"] == $this->id_groupe_status_admin())
        { $groupe = $source_groupe;
          break;
        }
      }
      return $groupe;
    }

    function source_permissions($source, $id_user)
    { $permissions = array
      ( "admin" => false,
        "editeur" => false,
        "contributeur" => false
      );
      foreach($source["groupes"] as $id_groupe => $source_groupe)
      { if($source_groupe["id_user"] == $id_user)
        { if($source_groupe["id_groupe_status"] == $this->id_groupe_status_admin())
          { $permissions["admin"] = true;
            $permissions["editeur"] = true;
            $permissions["contributeur"] = true;
          }
          elseif($source_groupe["id_groupe_status"] == $this->id_groupe_status_editeur())
          { $permissions["editeur"] = true;
            $permissions["contributeur"] = true;
          }
          elseif($source_groupe["id_groupe_status"] == $this->id_groupe_status_contributeur())
          { $permissions["contributeur"] = true;
          }
        }
      }
      return $permissions;
    }

  }

?>
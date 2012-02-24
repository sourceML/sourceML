<?php

  class sml_data_sources extends sml_data
  {

    var $status;

    # ----------------------------------------------------------------------------------------
    #                                                                         status de source
    #

    function source_status()
    { if(!isset($this->status)) $this->status = $this->init_sources_status();
      return $this->status;
    }

    function init_sources_status()
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $status = array();
      $sql = "SELECT * FROM #--source_status";
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      while($v_rst = $sgbd->fetch_assoc($rst)) $status[$v_rst["id"]] = $v_rst;
      $sgbd->free_result($rst);
      return $status;
    }

    # ----------------------------------------------------------------------------------------
    #                                                                                  sources
    #

    function init_sources_table()
    { $sgbd = $this->sgbd();
      $env = $this->env();
      if($env->bdd("sgbd") == "mysql")
      { $sql = "SELECT titre FROM #--sources limit 1";
        $rst = $sgbd->query($sql);
        if(isset($rst))
        { $sgbd->free_result($rst);
          return true;
        }
        $sql = "SELECT nom FROM #--sources limit 1";
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return "impossible de lire la table des sources (recherche de 'nom' / 'titre')";
      }
      elseif($env->bdd("sgbd") == "sqlite")
      { $sql = "SELECT * FROM sqlite_master WHERE tbl_name='#--sources'";
        $rst = $sgbd->query($sql);
        if(isset($rst))
        { if($v_rst = $sgbd->fetch_assoc($rst))
          { if(strpos($v_rst["sql"], "\"titre\" VARCHAR") !== false) return true;
            if(strpos($v_rst["sql"], "\"nom\" VARCHAR") === false)
            { return "impossible de lire la table des sources (recherche de 'nom' / 'titre')";
            }
          }
          else return "impossible de lire les informations de la table sources";
        }
        else return "impossible de lire les informations de la table sources";
      }
      else return "sgbd inconnu : ".$env->bdd("sgbd");
      if($env->config("UPGRADE_DB"))
      { // renommage du champ 'nom' en 'titre' dans la table sources
        if($env->bdd("sgbd") == "mysql")
        { $sql = "ALTER TABLE #--sources CHANGE nom titre VARCHAR( 255 ) NULL DEFAULT NULL";
          if(!$sgbd->query($sql)) return "erreur lors du renommage du champ 'nom' en 'titre' dans la table sources";
        }
        elseif($env->bdd("sgbd") == "sqlite")
        { $tmp_table_name = "t".substr(md5(rand()), 0, 15)."_sml_sources";

          $sql =
           "ALTER TABLE \"main\".\"sml_sources\""
          ." RENAME TO \"".$tmp_table_name."\"";
          if(!$sgbd->query($sql)) return "erreur lors du renommage du champ 'nom' en 'titre' dans la table sources (renomage en tmp)";

          $sql =
           "CREATE TABLE \"main\".\"sml_sources\""
          ." (\"id\" INTEGER PRIMARY KEY  NOT NULL"
          ." ,\"id_groupe\" INTEGER NOT NULL"
          ." ,\"status\" INTEGER NOT NULL"
          ." ,\"reference\" VARCHAR DEFAULT (NULL)"
          ." ,\"derivation\" VARCHAR DEFAULT (NULL)"
          ." ,\"titre\" VARCHAR DEFAULT (NULL)"
          ." ,\"licence\" INTEGER DEFAULT (NULL)"
          ." ,\"date_creation\" text DEFAULT (NULL)"
          ." ,\"date_inscription\" text NOT NULL"
          ." )";
          if(!$sgbd->query($sql)) return "erreur lors du renommage du champ 'nom' en 'titre' dans la table sources (creation de la nouvelle table)";

          $sql =
           "INSERT INTO \"main\".\"sml_sources\""
          ." SELECT"
          ." \"id\",\"id_groupe\",\"status\",\"reference\""
          .",\"derivation\",\"nom\",\"licence\""
          .",\"date_creation\",\"date_inscription\""
          ." FROM \"main\".\"".$tmp_table_name."\"";
          if(!$sgbd->query($sql)) return "erreur lors du renommage du champ 'nom' en 'titre' dans la table sources (insertions dans la nouvelle table)";

          $sgbd->query("DROP TABLE \"main\".\"".$tmp_table_name."\"");
        }
        else return "sgbd inconnu : ".$env->bdd("sgbd");
      }
      else return "le champ 'nom' de la table sources devrait s'appeler 'titre'";
      return true;
    }

    function sources($params)
    { $start = isset($params["start"]) ? $params["start"] : null;
      $id_user = isset($params["id_user"]) ? $params["id_user"] : null;
      $id_groupe = isset($params["id_groupe"]) ? $params["id_groupe"] : null;
      $status = isset($params["status"]) ? $params["status"] : null;
      $id_source = isset($params["id_source"]) ? $params["id_source"] : null;
      $id_composition = isset($params["id_composition"]) ? $params["id_composition"] : null;
      $id_source_derivation = isset($params["id_source_derivation"]) ? $params["id_source_derivation"] : null;
      $id_licence = isset($params["id_licence"]) ? $params["id_licence"] : null;

      $sgbd = $this->sgbd();
      $env = $this->env();
      $sources = array("list" => array(), "total" => 0);
      $COUNT_SELECT = "SELECT count(*) as n";
      $SELECT = "SELECT #--sources.*, #--source_infos.`value` as ordre";
      $FROM = "#--sources";
      if(isset($id_user)) $FROM .= ", #--groupes, #--source_groupes";
      elseif(isset($id_groupe)) $FROM .= ", #--source_groupes";
      if(isset($id_source)) $FROM .= ", #--source_compositions";
      elseif(isset($id_composition) && $id_composition) $FROM .= ", #--source_compositions";
      if(isset($id_source_derivation)) $FROM .= ", #--source_derivations";
      $FROM =
       " FROM (".$FROM.")"
      ." LEFT JOIN #--source_infos"
      ." ON (#--source_infos.id_source=#--sources.id AND #--source_infos.`key`='ordre')";
      $WHERE = "";
      if(isset($id_user)) $WHERE .=
       ($WHERE ? " AND " : " WHERE ")
      ." #--sources.id=#--source_groupes.id_source"
      ." AND #--source_groupes.id_groupe=#--groupes.id"
      ." AND #--groupes.id_user=".$this->eq($id_user)
      ." AND #--source_groupes.id_groupe_status=".$this->id_groupe_status_admin();
      if(isset($id_groupe)) $WHERE .=
       ($WHERE ? " AND " : " WHERE ")
      ." #--sources.id=#--source_groupes.id_source"
      ." AND #--source_groupes.id_groupe=".$this->eq($id_groupe)
      ." AND #--source_groupes.id_groupe_status=".$this->id_groupe_status_admin();
      if(isset($status)) $WHERE .=
       ($WHERE ? " AND " : " WHERE ")
      ." #--sources.status=".$this->eq($status);
      if(isset($id_source)) $WHERE .=
       ($WHERE ? " AND " : " WHERE ")
      ." #--sources.id=#--source_compositions.id_composition"
      ." AND #--source_compositions.id_source=".$this->eq($id_source);
      elseif(isset($id_composition))
      { if($id_composition)
        { $WHERE .=
           ($WHERE ? " AND " : " WHERE ")
          ." #--sources.id=#--source_compositions.id_source"
          ." AND #--source_compositions.id_composition=".$this->eq($id_composition);
        }
        else
        { if(($sources_ids = $this->source_compositions(array("id_composition" => ""))) !== false)
          { if($sources_ids)
            { $NOT_IN = "";
              foreach($sources_ids as $in_source_id) $NOT_IN .= ($NOT_IN ? "," : "").$in_source_id;
              $WHERE .= ($WHERE ? " AND " : " WHERE ")." #--sources.id NOT IN(".$NOT_IN.")";
            }
          }
          else return false;
        }
      }
      if(isset($id_source_derivation)) $WHERE .=
       ($WHERE ? " AND " : " WHERE ")
      ." #--sources.id=#--source_derivations.id_source"
      ." AND #--source_derivations.derivation=".$this->eq($this->source_xml_url($id_source_derivation));
      if(isset($id_licence)) $WHERE .=
       ($WHERE ? " AND " : " WHERE ")
      ." #--sources.licence=".$this->eq($id_licence);
      $ORDER_BY = " ORDER BY #--source_infos.`value`";
      $LIMIT = (isset($start) && $env->config("max_list") ? " LIMIT ".$env->config("max_list")." OFFSET ".$start : "");
      $sql = $COUNT_SELECT.$FROM.$WHERE;
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $sources["total"] = $v_rst["n"];
      $sgbd->free_result($rst);
      if($sources["total"])
      { $sql = $SELECT.$FROM.$WHERE.$ORDER_BY.$LIMIT;
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return false;
        while($v_rst = $sgbd->fetch_assoc($rst))
        { if(($sources["list"][$v_rst["id"]] = $this->load_source($v_rst)) === false) return false;
        }
        $sgbd->free_result($rst);
      }
      return $sources;
    }

    function source($id, $load = false)
    { $sgbd = $this->sgbd();
      $sql = "SELECT * FROM #--sources WHERE id=".$this->eq($id);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      $source = array();
      if($v_rst = $sgbd->fetch_assoc($rst))
      { $source = $load ? $this->load_source($v_rst) : $this->get_source_from_v_rst($v_rst);
      }
      $sgbd->free_result($rst);
      return $source;
    }

    function get_source_from_v_rst($v_rst)
    { $sgbd = $this->sgbd();
      $source = $this->empty_source();
      foreach($v_rst as $rst_key => $rst_value) $source[$rst_key] = $rst_value;
      $si_sql = "SELECT * FROM #--source_infos WHERE id_source=".$this->eq($source["id"]);
      $si_rst = $sgbd->query($si_sql);
      if(!isset($si_rst)) return false;
      while($v_si_rst = $sgbd->fetch_assoc($si_rst))
      { if(!isset($source[$v_si_rst["key"]])) $source[$v_si_rst["key"]] = $v_si_rst["value"];
      }
      $sgbd->free_result($si_rst);
      if($source["reference"])
      { if(!is_array($source["reference"]))
        { $reference_url = $source["reference"];
          $source["reference"] = $this->empty_source();
          $source["reference"]["xml"]["url"] = $reference_url;
        }
      }
      else
      { if(!is_array($source["licence"]))
        { $source["licence"] = array
          ( "id" => $source["licence"]
          );
        }
      }
      return $source;
    }

    function load_source($source)
    { $env = $this->env();
      if(!isset($source["id"])) return false;
      $source = $this->get_source_from_v_rst($source);
      $source["xml"] = array
      ( "url" => $this->source_xml_url($id_source),
        "content" => $this->get_source_xml($source["id"])
      );
      $source["documents"] = array();
      if(($source["groupes"] = $this->source_groupes($source["id"])) === false) return false;
      $source["image_uri"] =
      ( $source["image"] ?
        $env->path("content")."uploads/".$source["image"]
        : ""
      );
      if(($source["has_sources"] = $this->has_sources($source["id"])) === false) return false;
      if(($source["has_derivations"] = $this->source_derivations(array("derivation" => $source["id"]))) === false) return false;
      if(($source["derivations"] = $this->source_derivations(array("id_source" => $source["id"]))) === false) return false;
      if(($source["reference"] = $this->source_reference($source)) === false) return false;
      if(!$source["reference"])
      { if(($source["documents"] = $this->source_documents($source["id"])) === false) return false;
      }
      return $source;
    }

    function add_source
    ( $groupes,
      $titre,
      $status,
      $licence,
      $documents = array(),
      $reference = array(),
      $derivations = array(),
      $infos = array()
    )
    { $sgbd = $this->sgbd();
      $source = array
      ( "groupes" => $groupes,
        "titre" => $reference ? null : $titre,
        "status" => $status,
        "licence" => $reference ? null : $licence,
        "reference" => $reference ? $reference : null,
        "date_creation" => isset($infos["date_creation"]) ? $infos["date_creation"] : null,
        "date_inscription" => isset($infos["date_inscription"]) ? $infos["date_inscription"] : date("Y-m-d")
      );
      $sql =
       "INSERT INTO #--sources(status, reference, titre, licence, date_creation, date_inscription)"
      ." VALUES"
      ."( ".$this->eq($source["status"])
      .", ".$this->eq($source["reference"] ? $source["reference"]["xml"]["url"] : null)
      .", ".$this->eq($source["titre"])
      .", ".$this->eq($source["licence"])
      .", ".$this->eq($source["date_creation"])
      .", ".$this->eq($source["date_inscription"])
      .")";
      if(!$sgbd->query($sql)) return false;
      $id = $sgbd->insert_id();
      foreach($source["groupes"] as $id_groupe => $groupe)
      { if($groupe["id"] && $groupe["id_groupe_status"])
        { if(!$this->add_source_groupe($id, $groupe["id"], $groupe["id_groupe_status"])) return false;
        }
        else return false;
      }
      if(isset($infos["date_creation"])) unset($infos["date_creation"]);
      if(isset($infos["date_inscription"])) unset($infos["date_inscription"]);
      foreach($infos as $key => $value)
      { $sql =
         "INSERT INTO #--source_infos(id_source, `key`,`value`)"
        ." VALUES"
        ."( ".$this->eq($id)
        .", ".$this->eq($key)
        .", ".$this->eq($value)
        .")";
        if(!$sgbd->query($sql)) return false;
      }
      foreach($derivations as $source_derivation)
      { if
        ( ( $id_source_derivation = $this->add_source_derivation
            ( $id,
              $source_derivation["xml"]["url"],
              $source_derivation["xml"]["use_edit_content"] ? $source_derivation["xml"]["content"] : ""
            )
          ) === false
        )
        { return false;
        }
      }
      if($reference)
      { if($reference["xml"]["use_edit_content"])
        { if(!$this->set_edit_reference_content($id, $reference["xml"]["content"]))
          { return false;
          }
        }
      }
      else
      { foreach($documents as $document)
        { if(!$this->add_source_document($id, $document)) return false;
        }
      }
      if(!$this->set_source_xml($id)) return false;
      return $id;
    }

    function set_source
    ( $id,
      $groupes,
      $titre,
      $status,
      $licence,
      $documents = array(),
      $reference = array(),
      $derivations = array(),
      $infos = array()
    )
    { if($source = $this->source($id))
      { $sgbd = $this->sgbd();
        $source = array
        ( "groupes" => $groupes,
          "titre" => $reference ? null : $titre,
          "licence" => $reference ? null : $licence,
          "reference" => $reference ? $reference : null,
          "date_creation" => isset($infos["date_creation"]) ? $infos["date_creation"] : null
        );
        $sql =
         "UPDATE #--sources SET"
        ."  reference=".$this->eq($source["reference"] ? $source["reference"]["xml"]["url"] : null)
        .", titre=".$this->eq($source["titre"])
        .", licence=".$this->eq($source["licence"])
        .", date_creation=".$this->eq($source["date_creation"])
        ." WHERE id=".$this->eq($id);
        if(!$sgbd->query($sql)) return false;
        if(!$this->del_source_groupes($id_source)) return false;
        foreach($source["groupes"] as $id_groupe => $groupe)
        { if($groupe["id"] && $groupe["id_groupe_status"])
          { if(!$this->add_source_groupe($id, $groupe["id"], $groupe["id_groupe_status"])) return false;
          }
          else return false;
        }
        $sql = "DELETE FROM #--source_infos WHERE id_source=".$this->eq($id);
        if(!$sgbd->query($sql)) return false;
        if(isset($infos["date_creation"])) unset($infos["date_creation"]);
        if(isset($infos["date_inscription"])) unset($infos["date_inscription"]);
        foreach($infos as $key => $value)
        { $sql =
           "INSERT INTO #--source_infos(id_source, `key`,`value`)"
          ." VALUES"
          ."( ".$this->eq($id)
          .", ".$this->eq($key)
          .", ".$this->eq($value)
          .")";
          if(!$sgbd->query($sql)) return false;
        }
        if(!$this->del_source_derivations($id)) return false;
        if(!$this->del_edit_reference_content($id)) return false;
        if(!$this->del_source_documents($id)) return false;
        foreach($derivations as $source_derivation)
        { if
          ( ( $id_source_derivation = $this->add_source_derivation
              ( $id,
                $source_derivation["xml"]["url"],
                $source_derivation["xml"]["use_edit_content"] ? $source_derivation["xml"]["content"] : ""
              )
            ) === false
          )
          { return false;
          }
        }
        if($reference)
        { if($reference["xml"]["use_edit_content"])
          { if(!$this->set_edit_reference_content($id, $reference["xml"]["content"]))
            { return false;
            }
          }
        }
        else
        { foreach($documents as $document)
          { if(!$this->add_source_document($id, $document)) return false;
          }
        }
        if(!$this->set_source_xml($id)) return false;
        return true;
      }
      return false;
    }

    function set_source_info($id_source, $key, $value)
    { $sgbd = $this->sgbd();
      $sql =
       "SELECT id FROM #--source_infos"
      ." WHERE id_source=".$this->eq($id_source)
      ." AND `key`=".$this->eq($key);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      $id = null;
      if($v_rst = $sgbd->fetch_assoc($rst)) $id = $v_rst["id"];
      $sgbd->free_result($rst);
      if(isset($id)) $sql =
       "UPDATE #--source_infos SET"
      ." `value`=".$this->eq($value)
      ." WHERE id=".$this->eq($id);
      else $sql =
       "INSERT INTO #--source_infos(id_source, `key`,`value`)"
      ." VALUES"
      ."( ".$this->eq($id_source)
      .", ".$this->eq($key)
      .", ".$this->eq($value)
      .")";
      if(!$sgbd->query($sql)) return false;
      if(!isset($id)) $id = $sgbd->insert_id();
      return $id;
    }

    function del_source($id)
    { $sgbd = $this->sgbd();
      if(!$this->del_source_compositions(array("id_source" => $id, "id_composition" => $id))) return false;
      if(!$this->del_edit_reference_content($id)) return false;
      if(!$this->del_source_derivations($id)) return false;
      if(!$this->del_source_xml($id)) return false;
      if(!$this->del_source_documents($id)) return false;
      if(!$this->del_source_groupes($id)) return false;
      $sql = "DELETE FROM #--source_infos WHERE id_source=".$this->eq($id);
      if(!$sgbd->query($sql)) return false;
      $sql = "DELETE FROM #--sources WHERE id=".$this->eq($id);
      if(!$sgbd->query($sql)) return false;
      return true;
    }

    # ----------------------------------------------------------------------------------------
    #                                                                   derivations de sources
    #

    function init_source_derivations()
    { $sgbd = $this->sgbd();
      $rst = $sgbd->table_exists("#--source_derivations");
      if(isset($rst))
      { if(!$rst)
        { return
           "table manquante"
          ."<br />"
          ."<br />"
          ."<pre>"
          ."(table_prefix)source_derivations :\n"
          ."\n"
          ."  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,\n"
          ."  id_source INT NOT NULL,\n"
          ."  derivation VARCHAR NOT NULL\n"
          ."\n"
          ."</pre>";
        }
      }
      else return "impossible de chercher la table #--source_derivations";
      return true;
    }

    function source_derivations($params)
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $derivations = array();
      // sources dont "id_source" est une derivation
      if(isset($params["id_source"]))
      { $sql =
         "SELECT *"
        ." FROM #--source_derivations"
        ." WHERE #--source_derivations.id_source=".$this->eq($params["id_source"]);
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return false;
        while($v_rst = $sgbd->fetch_assoc($rst))
        { $derivations[$v_rst["id"]] = array();
          $derivation_edit_file = $this->derivation_edit_xml_path($v_rst["id_source"], $v_rst["id"]);
          if(file_exists($derivation_edit_file))
          { if(($derivation_edit_content = $this->get_edit_derivation_content($v_rst["id_source"], $v_rst["id"])) !== false)
            { if(($derivations[$v_rst["id"]] = $this->source_xml_read($v_rst["derivation"], $derivation_edit_content)) !==false)
              { $derivations[$v_rst["id"]]["xml"] = array
                ( "url" => $v_rst["derivation"],
                  "content" => $derivation_edit_content,
                  "use_edit_content" => true
                );
              }
              else return false;
            }
            else return false;
          }
          else
          { if(($derivations[$v_rst["id"]] = $this->source_xml_read($v_rst["derivation"])) !==false)
            { $derivations[$v_rst["id"]]["id_source"] = $v_rst["id_source"];
            }
            else $derivations[$v_rst["id"]] = $this->empty_source();
          }
          $derivations[$v_rst["id"]]["id_source"] = $v_rst["id_source"];
        }
        $sgbd->free_result($rst);
        return $derivations;
      }
      // sources qui derivent de "derivation"
      elseif(isset($params["derivation"]))
      { $source_xml_url = $params["derivation"];
        if(preg_match("/^[0-9]+$/", $source_xml_url)) $source_xml_url = $this->source_xml_url($source_xml_url);
        $sql =
         "SELECT #--sources.*"
        ." FROM #--sources, #--source_derivations"
        ." WHERE #--sources.id=#--source_derivations.id_source"
        ." AND #--source_derivations.derivation=".$this->eq($source_xml_url);
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return false;
        while($v_rst = $sgbd->fetch_assoc($rst)) $derivations[$v_rst["id"]] = $v_rst;
        $sgbd->free_result($rst);
        return $derivations;
      }
      return false;
    }

    function source_derivation($id)
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $derivation = array();
      $sql =
       "SELECT *"
      ." FROM #--source_derivations"
      ." WHERE #--source_derivations.id=".$this->eq($id);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $derivation = $v_rst;
      $sgbd->free_result($rst);
      return $derivation;
    }

    function add_source_derivation($id_source, $derivation, $edit_content = "")
    { $sgbd = $this->sgbd();
      $sql =
       "INSERT INTO #--source_derivations(id_source, derivation)"
      ." VALUES"
      ."( ".$this->eq($id_source)
      .", ".$this->eq($derivation)
      .")";
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      $id_source_derivation = $sgbd->insert_id();
      if($edit_content)
      { if(!$this->set_edit_derivation_content($id_source, $id_source_derivation, $edit_content))
        { return false;
        }
      }
      return $id_source_derivation;
    }

    function set_source_derivation($id_source_derivation, $id_source, $derivation, $edit_content = "")
    { $sgbd = $this->sgbd();
      $sql =
       "UPDATE #--source_derivations SET"
      ."  id_source=".$this->eq($id_source)
      .", derivation=".$this->eq($derivation)
      ." WHERE id=".$this->eq($id_source_derivation);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($edit_content)
      { if(!$this->set_edit_derivation_content($id_source, $id_source_derivation, $edit_content))
        { return false;
        }
      }
      return true;
    }

    function del_source_derivation($id_source_derivation)
    { if(($derivation = $this->source_derivation($id_source_derivation)) !== false)
      { if(($derivations = $this->source_derivations(array("id_source" => $derivation["id_source"]))) !== false)
        { $sgbd = $this->sgbd();
          $sql = "DELETE FROM #--source_derivations WHERE id=".$this->eq($id_source_derivation);
          $rst = $sgbd->query($sql);
          if(!isset($rst)) return false;
          if(count($derivations) > 1)
          { return $this->del_edit_derivation_content($derivation["id_source"], $id_source_derivation);
          }
          else return $this->del_edit_derivations($derivation["id_source"]);
        }
      }
      return false;
    }

    function del_source_derivations($id_source)
    { $sgbd = $this->sgbd();
      $sql = "DELETE FROM #--source_derivations WHERE id_source=".$this->eq($id_source);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      return $this->del_edit_derivations($id_source);
    }

    # ----------------------------------------------------------------------------------------
    #                                                                               references
    #

    function source_reference($source)
    { $reference = array();
      if($source["reference"])
      { if(!is_array($source["reference"]))
        { $source["reference"] = array
          ( "url" => $source["reference"],
            "content" => "",
            "use_edit_content" => false
          );
        }
        $reference_edit_file = $this->reference_edit_xml_path($source["id"]);
        if(file_exists($reference_edit_file))
        { if(($reference_edit_content = $this->get_edit_reference_content($source["id"])) !== false)
          { if(($reference = $this->source_xml_read($source["reference"], $reference_edit_content)) !==false)
            { $reference["xml"] = array
              ( "url" => $source["reference"]["xml"]["url"],
                "content" => $reference_edit_content,
                "use_edit_content" => true
              );
            }
            else return false;
          }
          else return false;
        }
        else
        { if(($reference = $this->source_xml_read($source["reference"]["xml"]["url"])) ===false)
          { $reference = $this->empty_source();
          }
        }
      }
      return $reference;
    }

    # ----------------------------------------------------------------------------------------
    #                                                                                documents
    #

    function source_documents($id_source)
    { $sgbd = $this->sgbd();
      $documents = array();
      $sql = "SELECT * FROM #--source_documents WHERE id_source=".$this->eq($id_source);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      while($v_rst = $sgbd->fetch_assoc($rst)) $documents[$v_rst["id"]] = $v_rst;
      $sgbd->free_result($rst);
      return $documents;
    }

    function add_source_document($id_source, $document)
    { $sgbd = $this->sgbd();
      $sql =
       "INSERT INTO #--source_documents(id_source, nom, url)"
      ." VALUES"
      ."( ".$this->eq($id_source)
      .", ".$this->eq($document["nom"])
      .", ".$this->eq($document["url"])
      .")";
      if(!$sgbd->query($sql)) return false;
      return $sgbd->insert_id();
    }

    function del_source_documents($id_source)
    { $sgbd = $this->sgbd();
      $sql = "DELETE FROM #--source_documents WHERE id_source=".$this->eq($id_source);
      if(!$sgbd->query($sql)) return false;
      return true;
    }

    # ----------------------------------------------------------------------------------------
    #                                                                  compositions de sources
    #

    function source_compositions($params)
    { $id_source = isset($params["id_source"]) ? $params["id_source"] : null;  
      $id_composition = isset($params["id_composition"]) ? $params["id_composition"] : null;
      $sgbd = $this->sgbd();
      $env = $this->env();
      $compositions = array();
      if(isset($id_source))
      { $sql = "SELECT * FROM #--source_compositions WHERE id_source=".$this->eq($id_source);
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return false;
        while($v_rst = $sgbd->fetch_assoc($rst))
        { if(!isset($compositions[$v_rst["id_source"]])) $compositions[$v_rst["id_source"]] = array();
          $compositions[$v_rst["id_source"]][] = $v_rst["id_composition"];
        }
        $sgbd->free_result($rst);
        return $compositions;
      }
      elseif(isset($id_composition))
      { if($id_composition)
        { $sql =
           "SELECT * FROM #--source_compositions WHERE id_composition=".$this->eq($id_composition);
          $rst = $sgbd->query($sql);
          if(!isset($rst)) return false;
          while($v_rst = $sgbd->fetch_assoc($rst))
          { if(!isset($compositions[$v_rst["id_composition"]])) $compositions[$v_rst["id_composition"]] = array();
            $compositions[$v_rst["id_composition"]][] = $v_rst["id_source"];
          }
          $sgbd->free_result($rst);
          return $compositions;
        }
        else
        { $sql =
           "SELECT DISTINCT id_source FROM #--source_compositions";
          $rst = $sgbd->query($sql);
          if(!isset($rst)) return false;
          while($v_rst = $sgbd->fetch_assoc($rst)) $compositions[] = $v_rst["id_source"];
          $sgbd->free_result($rst);
          return $compositions;
        }
      }
      return false;
    }

    function set_source_composition($id_source, $id_composition)
    { $sgbd = $this->sgbd();
      $sql =
       "INSERT INTO #--source_compositions(id_source, id_composition) VALUES"
      ."( ".$this->eq($id_source)
      .", ".$this->eq($id_composition)
      .")";
      if($sgbd->query($sql)) return $this->set_source_xml($id_composition);
      return false;
    }

    function del_source_compositions($params)
    { $res = true;
      $id_source = isset($params["id_source"]) ? $params["id_source"] : null;  
      $id_composition = isset($params["id_composition"]) ? $params["id_composition"] : null;
      $to_delete = array();
      $to_update = array();
      if(isset($id_composition)) $to_update[] = $id_composition;
      $sgbd = $this->sgbd();
      if(isset($id_source))
      { $sql = "SELECT * FROM #--source_compositions WHERE id_source=".$this->eq($id_source);
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return false;
        while($v_rst = $sgbd->fetch_assoc($rst)) $to_update[] = $v_rst["id_composition"];
        $sgbd->free_result($rst);
      }
      $sql = "DELETE FROM #--source_compositions";
      $WHERE = "";
      $WHERE .= (isset($id_source) ? ($WHERE ? " OR " : " WHERE ")."id_source=".$this->eq($id_source) : "");
      $WHERE .= (isset($id_composition) ? ($WHERE ? " OR " : " WHERE ")."id_composition=".$this->eq($id_composition) : "");
      $sql .= $WHERE;
      if($sgbd->query($sql))
      { foreach($to_update as $id_source_xml)
        { if(!$this->set_source_xml($id_source_xml)) return false;
        }
        return true;
      }
      return false;
    }

    function has_sources($id_composition)
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $sql = "SELECT count(*) as n FROM #--source_compositions WHERE id_composition=".$this->eq($id_composition);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      $has_sources = 0;
      if($v_rst = $sgbd->fetch_assoc($rst)) $has_sources = $v_rst["n"];
      $sgbd->free_result($rst);
      return $has_sources;
    }

    function source_ariane($id_source, $ariane = array())
    { if(($compositions = $this->source_compositions(array("id_source" => $id_source))) !== false)
      { if(isset($compositions[$id_source]) && $compositions[$id_source])
        { foreach($compositions[$id_source] as $id_composition)
          { if(($ariane = $this->source_ariane($id_composition, $ariane)) !== false)
            { if(($ariane[$id_composition] = $this->source($id_composition)) !== false)
              {
              }
              else $ariane = false;
            }
            else $ariane = false;
            break;
          }
        }
      }
      else $ariane = false;
      return $ariane;
    }

  }

?>
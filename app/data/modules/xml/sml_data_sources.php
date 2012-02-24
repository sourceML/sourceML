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
      if($rst = $sgbd->open_data("source_status"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { $status[$v_rst["id"]] = $v_rst;
          }
          else
          { $status = false;
            break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $status = false;
      return $status;
    }

    # ----------------------------------------------------------------------------------------
    #                                                                                  sources
    #

    function init_sources_table()
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $res = false;
      $NEED_UPDATE = false;
      if($rst = $sgbd->open_data("sources"))
      { if($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(!isset($v_rst["titre"]))
            { if(isset($v_rst["nom"]))
              { $NEED_UPDATE = true;
                $res = true;
              }
              else $res = "impossible de trouver les champs 'titre' ou 'nom' dans la table des sources";
            }
            else $res = true;
          }
          else $res = "impossible de lire les informations des sources";
        }
        else $res = true;
        $sgbd->close_data($rst);
      }
      else $res = "impossible d'ouvrir la table des sources";
      if($res !== true) return $res;
      if($NEED_UPDATE)
      { $res = false;
        if($env->config("UPGRADE_DB"))
        { if($rst = $sgbd->open_data("sources"))
          { while($v_rst = $sgbd->fetch_data($rst))
            { if(isset($v_rst))
              { if(isset($v_rst["id"]) && isset($v_rst["nom"]))
                { $v_rst["titre"] = $v_rst["nom"];
                  unset($v_rst["nom"]);
                  if($sgbd->set_data("sources", $v_rst["id"], $v_rst)) $res = true;
                  else
                  { $res = "erreur lors de la mise a jour de la table des sources";
                    break;
                  }
                }
              }
              else
              { $res = "erreur lors de la mise a jour de la table des sources";
                break;
              }
            }
            $sgbd->close_data($_rst);
          }
          else $res = "impossible d'ouvrir la table des sources";
        }
        else $res = "la table des sources doit etre mise a jour (le champ 'nom' devient 'titre')";
      }
      return $res;
    }

    function sources($params)
    { $sgbd = $this->sgbd();
      $env = $this->env();

      // -------------------------------------------------------------------------------
      //                                                                         filtres

      $start = isset($params["start"]) ? $params["start"] : null;
      $id_user = isset($params["id_user"]) ? $params["id_user"] : null;
      $id_groupe = isset($params["id_groupe"]) ? $params["id_groupe"] : null;
      $status = isset($params["status"]) ? $params["status"] : null;
      $id_source = isset($params["id_source"]) ? $params["id_source"] : null;
      $id_composition = isset($params["id_composition"]) ? $params["id_composition"] : null;
      $id_source_derivation = isset($params["id_source_derivation"]) ? $params["id_source_derivation"] : null;
      $id_licence = isset($params["id_licence"]) ? $params["id_licence"] : null;

      // -------------------------------------------------------------------------------
      //                                                 infos pour verifier les filtres

      if(isset($id_user) || isset($id_groupe))
      { $in_source_ids = array();
        $in_groupe_ids = array();
        if(isset($id_groupe)) $in_groupe_ids[$id_groupe] = true;
        else
        { if(($groupes = $this->groupes($id_user)) !== false)
          { foreach($groupes["list"] as $id_groupe => $groupe) $in_groupe_ids[$id_groupe] = true;
          }
          else return false;
        }
        $OK = true;
        if($rst = $sgbd->open_data("source_groupes"))
        { while($OK && $v_rst = $sgbd->fetch_data($rst))
          { if(isset($v_rst) && isset($v_rst["id_source"]) && isset($v_rst["id_groupe"]))
            { if($in_groupe_ids[$v_rst["id_groupe"]]) $in_source_ids[$v_rst["id_source"]] = true;
            }
            else $OK = false;
          }
          $sgbd->close_data($rst);
        }
        else $OK = false;
        if(!$OK) return false;
      }

      $compositions = array();
      if(isset($id_source))
      { if(($compositions = $this->source_compositions(array("id_source" => $id_source))) === false)
        { return false;
        }
      }
      elseif(isset($id_composition))
      { if(($compositions = $this->source_compositions(array("id_composition" => $id_composition))) === false)
        { return false;
        }
      }

      $derivations = array();
      if(isset($id_source_derivation))
      { if(($derivations = $this->source_derivations($id_source_derivation)) === false)
        { return false;
        }
      }

      // -------------------------------------------------------------------------------
      //                                                               boucle principale

      $sources = array("list" => array(), "total" => 0);
      $res = array();
      if($rst = $sgbd->open_data("sources"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst)) $res[$v_rst["id"]] = $v_rst;
          else
          { $res = false;
            break;
          }
        }
        $sgbd->close_data($rst);
        if($res !== false)
        { $res = $this->ordonne($res, "ordre");

          foreach($res as $id_res => $v_rst)
          { // ------------------------------- par defaut
            $MATCH = true;

            // ------------------------------- filtre sur id_user et id_groupe

            if(isset($in_source_ids))
            { $MATCH = isset($in_source_ids[$v_rst["id"]]) && $in_source_ids[$v_rst["id"]];
            }

            // ------------------------------- filtre sur status
            if($MATCH)
            { $MATCH = !isset($status) || (isset($v_rst["status"]) && $v_rst["status"] == $status);
            }

            // -------------------------------- filtre sur id_source
            if($MATCH)
            { if(isset($id_source))
              { $MATCH = false;
                if($compositions && is_array($compositions[$id_source]))
                { foreach($compositions[$id_source] as $id_composition)
                  { if(isset($v_rst["id"]) && $v_rst["id"] == $id_composition)
                    { $MATCH = true;
                      break;
                    }
                  }
                }
              }
            }

            // -------------------------------- filtre sur id_composition
            if($MATCH)
            { if(isset($id_composition))
              { if($id_composition)
                { $MATCH = false;
                  if($compositions && is_array($compositions[$id_composition]))
                  { foreach($compositions[$id_composition] as $_id_source)
                    { if(isset($v_rst["id"]) && $v_rst["id"] == $_id_source)
                      { $MATCH = true;
                        break;
                      }
                    }
                  }
                }
                else
                { if($compositions)
                  { if($compositions[$v_rst["id"]]) $MATCH = false;
                  }
                }
              }
            }

            // -------------------------------- filtre sur id_source_derivation
            if($MATCH)
            { if(isset($id_source_derivation))
              { $MATCH = false;
                if($derivations && is_array($derivations[$id_source_derivation]))
                { foreach($derivations[$id_source_derivation] as $_id_derivation)
                  { if(isset($v_rst["id"]) && $v_rst["id"] == $_id_derivation)
                    { $MATCH = true;
                      break;
                    }
                  }
                }
              }
            }

            // -------------------------------- filtre sur la licence
            if($MATCH)
            { if(isset($id_licence))
              { $MATCH = false;
                if(isset($v_rst["licence"]) && $v_rst["licence"] == $id_licence) $MATCH = true;
              }
            }

            // -------------------------------- filtre sur quantite de resultats
            if($MATCH)
            { $sources["total"]++;
              $MATCH = !isset($start) || !$env->config("max_list") || ($sources["total"] > $start && $sources["total"] <= ($start + $env->config("max_list")));
            }

            // -------------------------------- ajout aux resultats si MATCH
            if($MATCH) $sources["list"][$v_rst["id"]] = $v_rst;
          }

        }
      }
      else return false;
      foreach($sources["list"] as $id_source => $source)
      { if(($sources["list"][$source["id"]] = $this->load_source($source)) === false) return false;
      }
      return $sources;
    }

    function source($id, $load = false)
    { $sgbd = $this->sgbd();
      if(($source = $sgbd->get_data("sources", $id)) !== false)
      { $source = $load ? $this->load_source($source) : $this->get_source_from_v_rst($source);
        return $source;
      }
      return false;
    }

    function get_source_from_v_rst($v_rst)
    { $sgbd = $this->sgbd();
      $source = $this->empty_source();
      foreach($v_rst as $rst_key => $rst_value) $source[$rst_key] = $rst_value;
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
      ( "status" => $status,
        "reference" => $reference ? $reference["xml"]["url"] : null,
        "titre" => $reference ? null : $titre,
        "licence" => $reference ? null : $licence,
        "date_creation" => isset($infos["date_creation"]) ? $infos["date_creation"] : null,
        "date_inscription" => isset($infos["date_inscription"]) ? $infos["date_inscription"] : date("Y-m-d")
      );
      foreach($infos as $key => $value) if(!isset($source[$key])) $source[$key] = $value;
      $id = $sgbd->add_data("sources", $source);
      if(!isset($id)) return false;
      foreach($groupes as $id_groupe => $groupe)
      { if($groupe["id"] && $groupe["id_groupe_status"])
        { if(!$this->add_source_groupe($id, $groupe["id"], $groupe["id_groupe_status"])) return false;
        }
        else return false;
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
        $source["reference"] = $reference ? $reference["xml"]["url"] : null;
        $source["titre"] = $reference ? null : $titre;
        $source["licence"] = $reference ? null : $licence;
        $source["date_creation"] = isset($infos["date_creation"]) ? $infos["date_creation"] : null;
        foreach($infos as $key => $value) $source[$key] = $value;
        if(!$sgbd->set_data("sources", $id, $source)) return false;
        if(!$this->del_source_groupes($id)) return false;
        foreach($groupes as $id_groupe => $groupe)
        { if($groupe["id"] && $groupe["id_groupe_status"])
          { if(!$this->add_source_groupe($id, $groupe["id"], $groupe["id_groupe_status"])) return false;
          }
          else return false;
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
      if($source = $sgbd->get_data("sources", $id_source))
      { $source[$key] = $value;
        if($sgbd->set_data("sources", $id_source, $source)) return true;
      }
      return false;
    }

    function del_source($id)
    { $sgbd = $this->sgbd();
      if(!$this->del_source_compositions(array("id_source" => $id, "id_composition" => $id))) return false;
      if(!$this->del_edit_reference_content($id)) return false;
      if(!$this->del_source_derivations($id)) return false;
      if(!$this->del_source_xml($id)) return false;
      if(!$this->del_source_documents($id)) return false;
      if(!$this->del_source_groupes($id)) return false;
      if(!$sgbd->del_data("sources", $id)) return false;
      return true;
    }

    # ----------------------------------------------------------------------------------------
    #                                                                   derivations de sources
    #

    function init_source_derivations()
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $rst = $sgbd->data_exists("source_derivations");
      if(isset($rst))
      { if(!$rst)
        { $erreur = "";
          if($env->config("UPGRADE_DB"))
          { if(!$sgbd->create_data("source_derivations")) return "impossible de créer la table #--source_derivations";
            if($rst = $sgbd->open_data("sources"))
            { while($v_rst = $sgbd->fetch_data($rst))
              { if(isset($v_rst))
                { if(isset($v_rst["id"]) && isset($v_rst["derivation"]) && $v_rst["derivation"])
                  { if
                    ( !$sgbd->add_data
                      ( "source_derivations",
                        array
                        ( "id_source" => $v_rst["id"],
                          "derivation" => $v_rst["derivation"]
                        )
                      )
                    ) { $erreur = "impossible de mettre a jour les derivations"; break; }
                  }
                }
                else { $erreur = "erreur lors de lecture des sources pour mettre a jour les derivations"; break; }
              }
              $sgbd->close_data($rst);
            }
            else $erreur = "impossible de lire la liste des sources pour mettre a jour les derivations";
          }
          else $erreur = "la base de donnees doit etre mise a jour";
          if($erreur) return $erreur;
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
      { if($rst = $sgbd->open_data("source_derivations"))
        { while($v_rst = $sgbd->fetch_data($rst))
          { if(isset($v_rst))
            { if(isset($v_rst["id"]) && isset($v_rst["id_source"]) && $v_rst["id_source"] == $params["id_source"])
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
                    else
                    { $derivations = null;
                      break;
                    }
                  }
                  else
                  { $derivations = null;
                    break;
                  }
                }
                else
                { if(($derivations[$v_rst["id"]] = $this->source_xml_read($v_rst["derivation"])) !==false)
                  { $derivations[$v_rst["id"]]["id_source"] = $v_rst["id_source"];
                  }
                  else $derivations[$v_rst["id"]] = $this->empty_source();
                }
                $derivations[$v_rst["id"]]["id_source"] = $v_rst["id_source"];
              }
            }
            else
            { $derivations = null;
              break;
            }
          }
          $sgbd->close_data($rst);
        }
        if(!isset($derivations)) return false;
        return $derivations;
      }
      // sources qui derivent de "derivation"
      elseif(isset($params["derivation"]))
      { $source_xml_url = $params["derivation"];
        if(preg_match("/^[0-9]+$/", $source_xml_url)) $source_xml_url = $this->source_xml_url($source_xml_url);
        $id_sources = array();
        if($rst = $sgbd->open_data("source_derivations"))
        { while($v_rst = $sgbd->fetch_data($rst))
          { if(isset($v_rst))
            { if(isset($v_rst["id_source"]) && isset($v_rst["derivation"]) && $v_rst["derivation"] == $source_xml_url)
              { $id_sources[$v_rst["id_source"]] = true;
              }
            }
            else { $id_sources = false; break; }
          }
          $sgbd->close_data($rst);
        }
        else $id_sources = false;
        if($id_sources === false) return false;
        if($id_sources)
        { if($rst = $sgbd->open_data("sources"))
          { while($v_rst = $sgbd->fetch_data($rst))
            { if(isset($v_rst))
              { if(isset($v_rst["id"]) && isset($id_sources[$v_rst["id"]]))
                { $derivations[$v_rst["id"]] = $v_rst;
                }
              }
              else { $derivations = false; break; }
            }
            $sgbd->close_data($rst);
          }
          else $derivations = false;
        }
        return $derivations;
      }
      return false;
    }

    function source_derivation($id)
    { $sgbd = $this->sgbd();
      return $sgbd->get_data("source_derivations", $id);
    }

    function add_source_derivation($id_source, $derivation, $edit_content = "")
    { $sgbd = $this->sgbd();
      $id_source_derivation = $sgbd->add_data
      ( "source_derivations",
        array
        ( "id_source" => $id_source,
          "derivation" => $derivation
        )
      );
      if(isset($id_source_derivation))
      { if($edit_content)
        { if(!$this->set_edit_derivation_content($id_source, $id_source_derivation, $edit_content))
          { $id_source_derivation = false;
          }
        }
      }
      else $id_source_derivation = false;
      return $id_source_derivation;
    }

    function set_source_derivation($id_source_derivation, $id_source, $derivation, $edit_content = "")
    { $sgbd = $this->sgbd();
      if
      ( ( $sgbd->set_data
          ( "source_derivations",
            $id_source_derivation,
            array
            ( "id_source" => $id_source,
              "derivation" => $derivation
            )
          )
        )
      )
      { if($edit_content)
        { if(!$this->set_edit_derivation_content($id_source, $id_source_derivation, $edit_content))
          { return false;
          }
        }
        return true;
      }
      return false;
    }

    function del_source_derivation($id_source_derivation)
    { if(($derivation = $this->source_derivation($id_source_derivation)) !== false)
      { if(($derivations = $this->source_derivations(array("id_source" => $derivation["id_source"]))) !== false)
        { $sgbd = $this->sgbd();
          if(!$sgbd->del_data("source_derivations", $id_source_derivation)) return false;
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
      $id_source_derivations = array();
      if($rst = $sgbd->open_data("source_derivations"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(isset($v_rst["id"]) && isset($v_rst["id_source"]) && $v_rst["id_source"] == $id_source)
            { $id_source_derivations[] = $v_rst["id"];
            }
          }
          else { $id_source_derivations = false; break; }
        }
        $sgbd->close_data($rst);
      }
      else $id_source_derivations = false;
      if($id_source_derivations === false) return false;
      if($id_source_derivations)
      { foreach($id_source_derivations as $id_source_derivation)
        { if(!$sgbd->del_data("source_derivations", $id_source_derivation)) return false;
        }
      }
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
      if($sgbd->data_exists("sources/".$id_source))
      { if($rst = $sgbd->open_data("sources/".$id_source))
        { $OK = true;
          while($OK && ($document = $sgbd->fetch_data($rst)))
          { if(isset($document)) $documents[$document["id"]] = $document;
            else $OK = false;
          }
          $sgbd->close_data($rst);
        }
        else $OK = false;
        if(!$OK) return false;
      }
      return $documents;
    }

    function add_source_document($id_source, $document)
    { $sgbd = $this->sgbd();
      if(!$sgbd->data_exists("sources/".$id_source))
      { if(!$sgbd->create_data("sources/".$id_source)) return false;
      }
      if
      ( !( $id_document = $sgbd->add_data
          ( "sources/".$id_source,
            array
            ( "nom" => $document["nom"],
              "url" => $document["url"]
            )
          )
        )
      ) return false;
      return $id_document;
    }

    function del_source_documents($id_source)
    { $sgbd = $this->sgbd();
      if($sgbd->data_exists("sources/".$id_source))
      { if(!$sgbd->remove_data("sources/".$id_source)) return false;
      }
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
      { if($rst = $sgbd->open_data("source_compositions"))
        { while($v_rst = $sgbd->fetch_data($rst))
          { if(isset($v_rst))
            { if(isset($v_rst["id_source"]) && isset($v_rst["id_composition"]) && $v_rst["id_source"] == $id_source)
              { if(!isset($compositions[$v_rst["id_source"]])) $compositions[$v_rst["id_source"]] = array();
                $compositions[$v_rst["id_source"]][] = $v_rst["id_composition"];
              }
            }
            else { $compositions = false; break; }
          }
          $sgbd->close_data($rst);
        }
        else $compositions = false;
      }
      elseif(isset($id_composition))
      { if($id_composition)
        { if($rst = $sgbd->open_data("source_compositions"))
          { while($v_rst = $sgbd->fetch_data($rst))
            { if(isset($v_rst))
              { if(isset($v_rst["id_source"]) && isset($v_rst["id_composition"]) && $v_rst["id_composition"] == $id_composition)
                { if(!isset($compositions[$v_rst["id_composition"]])) $compositions[$v_rst["id_composition"]] = array();
                  $compositions[$v_rst["id_composition"]][] = $v_rst["id_source"];
                }
              }
              else { $compositions = false; break; }
            }
            $sgbd->close_data($rst);
          }
          else $compositions = false;
        }
        else
        { if($rst = $sgbd->open_data("source_compositions"))
          { while($v_rst = $sgbd->fetch_data($rst))
            { if(isset($v_rst))
              { if(isset($v_rst["id_source"])) $compositions[$v_rst["id_source"]] = true;
              }
              else { $compositions = false; break; }
            }
            $sgbd->close_data($rst);
          }
          else $compositions = false;
        }
      }
      return $compositions;
    }

    function set_source_composition($id_source, $id_composition)
    { $sgbd = $this->sgbd();
      if
      ( $sgbd->add_data
        ( "source_compositions",
          array
          ( "id_source" => $id_source,
            "id_composition" => $id_composition
          )
        )
      ) return $this->set_source_xml($id_composition);
      return false;
    }

    function del_source_compositions($params)
    { $OK = true;
      $id_source = isset($params["id_source"]) ? $params["id_source"] : null;  
      $id_composition = isset($params["id_composition"]) ? $params["id_composition"] : null;
      $to_delete = array();
      $to_update = array();
      if(isset($id_composition)) $to_update[$id_composition] = true;
      $sgbd = $this->sgbd();
      if($rst = $sgbd->open_data("source_compositions"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(isset($v_rst["id"]) && isset($v_rst["id_source"]) && isset($v_rst["id_composition"]))
            { if
              (    (isset($id_source) && $v_rst["id_source"] == $id_source)
                || (isset($id_composition) && $v_rst["id_composition"] == $id_composition)
              ) $to_delete[] = $v_rst["id"];
              if(isset($id_source) && $v_rst["id_source"] == $id_source) $to_update[$v_rst["id_composition"]] = true;
            }
          }
          else { $OK = false; break; }
        }
        $sgbd->close_data($rst);
      }
      else $OK = false;
      if(!$OK) return false;
      foreach($to_delete as $id_source_composition)
      { if(!$sgbd->del_data("source_compositions", $id_source_composition)) return false;
      }
      foreach($to_update as $id_source_xml => $delete)
      { if(!$this->set_source_xml($id_source_xml)) return false;
      }
      return true;
    }

    function has_sources($id_composition)
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $has_sources = 0;
      if($rst = $sgbd->open_data("source_compositions"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(isset($v_rst["id_source"]) && isset($v_rst["id_composition"]))
            { if($v_rst["id_composition"] == $id_composition)
              { $has_sources = 1;
                break;
              }
            }
          }
          else
          { $has_sources = false;
            break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $has_sources = false;
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
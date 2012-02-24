<?php

  class sml_data_source_groupes extends sml_data
  {

    function init_groupe_status()
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $groupe_status_table_exists = $sgbd->data_exists("groupe_status");
      if(isset($groupe_status_table_exists))
      { if($groupe_status_table_exists) return true;
        if($env->config("UPGRADE_DB"))
        { if(($res = $this->create_groupe_status()) !== true) return $res;
          if(($res = $this->migre_groupe_status()) !== true) return $res;
          return true;
        }
        else return "la table groupe_status n'existe pas. la base de donnees doit etre mise a jour.";
      }
      else return "impossible de savoir si la table groupe_status existe";
    }

    function create_groupe_status()
    { $sgbd = $this->sgbd();
      if(!$sgbd->create_data("groupe_status")) return "impossible de creer la table groupe_status";
      if(!$sgbd->create_data("source_groupes")) return "impossible de creer la table source_groupes";
      if
      (    !$sgbd->add_data("groupe_status", array("nom" => "admin"))
        || !$sgbd->add_data("groupe_status", array("nom" => "editeur"))
        || !$sgbd->add_data("groupe_status", array("nom" => "contributeur"))
      )
      { return "impossible de renseigner la table groupe_status";
      }
      return true;
    }

    function migre_groupe_status()
    { $sgbd = $this->sgbd();
      $env = $this->env();

      // mise a jour du chemin des images des groupes pour y ajouter le chemin du user
      $erreur = "";
      if($rst = $sgbd->open_data("groupes"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(isset($v_rst["id"]) && isset($v_rst["id_user"]) && isset($v_rst["image"]) && $v_rst["image"])
            { if(!preg_match("/^[0-9]+\/.*$/", $v_rst["image"]))
              { $id_groupe = $v_rst["id"];
                unset($v_rst["id"]);
                $v_rst["image"] = $v_rst["id_user"]."/".$v_rst["image"];
                if
                ( !$sgbd->set_data
                  ( "groupes",
                    $id_groupe,
                    $v_rst
                  )
                ) { $erreur = "impossible de mettre a jour les images des groupes"; break; }
              }
            }
          }
          else { $erreur = "erreur lors de lecture des groupes pour mettre a jour les images"; break; }
        }
        $sgbd->close_data($rst);
      }
      else $erreur = "impossible de lire la liste des groupes pour mettre a jour les chemins des images";
      if($erreur) return $erreur;

      // mise a jour des informations des sources :
      //  - chemin des images, dans le dossier id_user
      //  - plus de champ derivation dans la table sources
      //  - plus de champ id_groupe dans la table sources
      //  - information de groupe transfere dans source_groupes

      $erreur = "";
      if($rst = $sgbd->open_data("sources"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(isset($v_rst["id"]))
            { $id_source = $v_rst["id"];
              unset($v_rst["id"]);
              $NEED_UPDATE = false;
              if(isset($v_rst["derivation"]))
              { unset($v_rst["derivation"]);
                $NEED_UPDATE = true;
              }
              $id_groupe = null;
              if(isset($v_rst["id_groupe"]))
              { $id_groupe = $v_rst["id_groupe"];
                unset($v_rst["id_groupe"]);
                $NEED_UPDATE = true;
              }
              if(isset($v_rst["image"]) && $v_rst["image"] && !preg_match("/^[0-9]+\/.*$/", $v_rst["image"]))
              { if(isset($id_groupe))
                { if(($groupe = $this->groupe($id_groupe)) !== false)
                  { $v_rst["image"] = $groupe["id_user"]."/".$v_rst["image"];
                    $NEED_UPDATE = true;
                  }
                }
              }
              if($NEED_UPDATE)
              { if
                ( $sgbd->set_data
                  ( "sources",
                    $id_source,
                    $v_rst
                  )
                )
                { if(isset($id_groupe))
                  { if
                    ( !$sgbd->add_data
                      ( "source_groupes",
                        array
                        ( "id_source" => $id_source,
                          "id_groupe" => $id_groupe,
                          "id_groupe_status" => 1
                        )
                      )
                    )
                    { $erreur = "impossible de mettre a jour les informations des sources (source_groupes)"; break;
                    }
                  }
                }
                else { $erreur = "impossible de mettre a jour les informations des sources"; break; }
              }
            }
          }
          else { $erreur = "erreur lors de lecture des sources pour mettre a jour leurs informations"; break; }
        }
        $sgbd->close_data($rst);
      }
      else $erreur = "impossible de lire la liste des sources pour mettre a jour leurs informations";
      if($erreur) return $erreur;

      // suppression de la table source_status_composition
      $sgbd->remove_data("source_status_composition");

      return true;
    }

// --------------------------------------------------------------------

    function source_groupes($id_source)
    { $env = $this->env();
      $sgbd = $this->sgbd();
      $groupes = array();
      $groupes_status = array();
      if($rst = $sgbd->open_data("source_groupes"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst) && isset($v_rst["id_groupe"]) && isset($v_rst["id_source"]) && isset($v_rst["id_groupe_status"]))
          { if($v_rst["id_source"] == $id_source) $groupes_status[$v_rst["id_groupe"]] = $v_rst["id_groupe_status"];
          }
          else
          { $groupes_status = false;
            break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $groupes_status = false;
      if($groupes_status === false) return false;
      if($rst = $sgbd->open_data("groupes"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst) && isset($v_rst["id"]))
          { if(isset($groupes_status[$v_rst["id"]]))
            { $groupes[$v_rst["id"]] = $v_rst;
              $groupes[$v_rst["id"]]["id_groupe_status"] = $groupes_status[$v_rst["id"]];
              $groupes[$v_rst["id"]]["image_uri"] =
              ( $v_rst["image"] ?
                $env->path("content")."uploads/".$v_rst["image"]
                : ""
              );
            }
          }
          else
          { $groupes = false;
            break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $groupes = false;
      return $groupes;
    }

    function add_source_groupe($id_source, $id_groupe, $id_groupe_status)
    { $sgbd = $this->sgbd();
      if
      ( $sgbd->add_data
        ( "source_groupes",
          array
          ( "id_source" => $id_source,
            "id_groupe" => $id_groupe,
            "id_groupe_status" => $id_groupe_status
          )
        )
      )
      { return true;
      }
      return false;
    }

    function set_source_groupe($id, $id_groupe_status)
    { if(($groupe = $this->groupe($id)) !== false)
      { if
        ( $sgbd->set_data
          ( "source_groupes",
            $id,
            array
            ( "id_source" => $id_source,
              "id_groupe" => $id_groupe,
              "id_groupe_status" => $id_groupe_status
            )
          )
        )
        { return true;
        }
      }
      return false;
    }

    function del_source_groupes($id_source)
    { $env = $this->env();
      $sgbd = $this->sgbd();
      $OK = true;
      if($rst = $sgbd->open_data("source_groupes"))
      { while($OK && $v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst) && isset($v_rst["id"]) && isset($v_rst["id_source"]))
          { if($v_rst["id_source"] == $id_source) if(!$sgbd->del_data("source_groupes", $v_rst["id"])) $OK = false;
          }
          else $OK = false;
        }
        $sgbd->close_data($rst);
      }
      else $OK = false;
      return $OK;
    }

    function del_source_groupe($id)
    { $sgbd = $this->sgbd();
      return $sgbd->del_data("source_groupes", $id) ? true : false;
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
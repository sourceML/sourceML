<?php

  class sml_data_groupes extends sml_data
  {

    var $groupes;

    # ----------------------------------------------------------------------------------------
    #                                                                                  groupes
    #

    function groupes($id_user = null, $start = null, $alpha = null)
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $groupes = array("list" => array(), "total" => 0);
      if(true || isset($id_user))
      { if($rst = $sgbd->open_data("groupes"))
        { while($v_rst = $sgbd->fetch_data($rst))
          { if(isset($v_rst))
            { if(!isset($alpha) || (isset($v_rst["nom"]) && substr($v_rst["nom"], 0, 1) == $alpha))
              { if(!isset($id_user) || (isset($v_rst["id_user"]) && $v_rst["id_user"] == $id_user))
                { $groupes["total"]++;
                  $groupes["list"][$v_rst["id"]] = $v_rst;
                }
              }
            }
            else
            { $groupes = false;
              break;
            }
          }
          $sgbd->close_data($rst);
          if($groupes !== false)
          { $n = -1;
            foreach($groupes["list"] as $id_groupe => $groupe)
            { $n++;
              if(isset($start) && $env->config("max_list") && ($n < $start || $n >= ($start + $env->config("max_list"))))
              { unset($groupes["list"][$id_groupe]);
              }
              else
              { $groupes["list"][$id_groupe]["image_uri"] =
                ( $groupe["image"] ?
                    $env->path("content")."uploads/".$groupe["image"]
                  : ""
                );
              }
            }
          }
        }
        else $groupes = false;
      }
      return $groupes;
    }

    function groupe($id)
    { if(!isset($this->groupes)) $this->groupes = array();
      if(isset($this->groupes[$id])) return $this->groupes[$id];
      $sgbd = $this->sgbd();
      $env = $this->env();
      if(($groupe = $sgbd->get_data("groupes", $id)) !== null)
      { $groupe["image_uri"] =
        ( $groupe["image"] ?
            $env->path("content")."uploads/".$groupe["image"]
          : ""
        );
      }
      else $groupe = false;
      if($groupe != false) $this->groupes[$id] = $groupe;
      return $groupe;
    }

    function groupe_exists($nom, $other_than_id = null)
    { $sgbd = $this->sgbd();
      $EXISTS = 0;
      if($rst = $sgbd->open_data("groupes"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(isset($v_rst["nom"]) && $v_rst["nom"] == $nom)
            { if(isset($other_than_id))
              { if($v_rst["id"] != $other_than_id) $EXISTS++;
              }
              else $EXISTS++;
            }
          }
          else
          { $EXISTS = false;
            break;
          }
        }
        $sgbd->close_data($rst);
      }
      return $EXISTS;
    }

    function add_groupe($id_user, $nom, $image, $description, $email, $contact_form, $captcha)
    { $sgbd = $this->sgbd();
      return $sgbd->add_data
      ( "groupes",
        array
        ( "id_user" => $id_user,
          "nom" => $nom,
          "image" => $image,
          "description" => $description,
          "email" => $email,
          "contact_form" => $contact_form,
          "captcha" => $captcha
        )
      );
    }

    function set_groupe($id, $nom, $image, $description, $email, $contact_form, $captcha)
    { if(($groupe = $this->groupe($id)) !== false)
      { $sgbd = $this->sgbd();
        if($nom != $groupe["nom"])
        { $groupe["nom"] = $nom;
          if(!$this->maj_source_xml_groupe($groupe)) return false;
        }
        return $sgbd->set_data
        ( "groupes",
          $id,
          array
          ( "id_user" => $groupe["id_user"],
            "nom" => $nom,
            "image" => $image,
            "description" => $description,
            "email" => $email,
            "contact_form" => $contact_form,
            "captcha" => $captcha
          )
        );
      }
      return false;
    }

    function del_groupe($id)
    { $OK = true;
      $USED = false;
      $sgbd = $this->sgbd();
      $env = $this->env();
      if($rst = $sgbd->open_data("sources"))
      { while($source = $sgbd->fetch_data($rst))
        { if(isset($source))
          { if($source["id_groupe"] == $id)
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
        return $sgbd->del_data("groupes", $id) ? true : false;
      }
      return false;
    }

  }

?>
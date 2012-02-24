<?php

  class sml_data_utils extends sml_data
  {

    function eq($content) { return (isset($content) ? "'".str_replace("'", "\'", $content)."'" : "NULL"); }

    /*
      fonction pour ordonner les resultats des requetes sur
      les donnees XML (pour faire l'equivalent d'un ORDER BY en SQL)
    */
    /*
    function ordonne($list, $key)
    { $values = array_values($list);
      $maximum = count($values);
      while($maximum > 0)
      { $maximumTemporaire = 0;
        for($i = 0; $i < $maximum; $i++)
        { if($values[$i][$key] > $values[$i + 1][$key])
          { $tmp = $values[$i];
            $values[$i] = $values[$i + 1];
            $values[$i + 1] = $tmp;
            $maximumTemporaire = $i + 1;
          }
        }
        $maximum = $maximumTemporaire;
      }
      $res = array();
      foreach($values as $value) if($value["id"]) $res[$value["id"]] = $value;
      return $res;
    }
    */

    function ordonne($list, $key, $order = "ASC")
    { $values = array_values($list);
      $maximum = count($values);
      while($maximum > 0)
      { $maximumTemporaire = 0;
        for($i = 0; $i < $maximum; $i++)
        { if
          (    ($order == "ASC" && $values[$i][$key] > $values[$i + 1][$key])
            || ($order == "DESC" && $values[$i][$key] < $values[$i + 1][$key])
          )
          { $tmp = $values[$i];
            $values[$i] = $values[$i + 1];
            $values[$i + 1] = $tmp;
            $maximumTemporaire = $i + 1;
          }
        }
        $maximum = $maximumTemporaire;
      }
      $res = array();
      foreach($values as $value) if($value["id"]) $res[$value["id"]] = $value;
      return $res;
    }

    function upload($image, $upload_dir)
    { $file = "";
      $upload_dir .= $upload_dir && (substr($upload_dir, -1) != "/") ? "/" : "";
      if($_FILES)
      { if(isset($_FILES[$image]))
        { if($_FILES[$image]["error"] == UPLOAD_ERR_OK)
          { if(move_uploaded_file($_FILES[$image]["tmp_name"], $upload_dir.$_FILES[$image]["name"]))
            { $file = $_FILES[$image]["name"];
            }
            else $file = false;
          }
          else if($_FILES[$image]["error"] != UPLOAD_ERR_NO_FILE) $file = false;
        }
        else $file = false;
      }
      return $file;
    }

  }

?>
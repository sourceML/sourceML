<?php

  class sml_data_config extends sml_data
  {

    function config($key = null)
    { $sgbd = $this->sgbd();
      $value = false;
      if($rst = $sgbd->open_data("config"))
      { if(isset($key))
        { while($v_rst = $sgbd->fetch_data($rst))
          { if(isset($v_rst))
            { if($v_rst["key"] == $key)
              { $value = $v_rst["value"];
              }
            }
            else $value = null;
          }
        }
        else
        { $value = array();
          while($v_rst = $sgbd->fetch_data($rst))
          { if(isset($v_rst))
            { if(is_array($v_rst)) foreach($v_rst as $key => $_value)
              { $value[$key] = $_value;
                break;
              }
            }
            else $value = null;
          }
        }
        $sgbd->close_data($rst);
      }
      if(!isset($value)) return false;
      return $value;
    }

    function config_exists($key)
    { $sgbd = $this->sgbd();
      $exists = 0;
      if($rst = $sgbd->open_data("config"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(isset($v_rst[$key])) $exists++;
          }
          else
          { $exists = false;
            break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $exists = false;
      return $exists;
    }

    function set_config($key, $value)
    { $sgbd = $this->sgbd();
      $FOUND = false;
      if($rst = $sgbd->open_data("config"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(array_key_exists($key, $v_rst))
            { $FOUND = $sgbd->set_data("config", $v_rst["id"], array($key => $value));
              break;
            }
          }
          else
          { $FOUND = null;
            break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $FOUND = null;
      if(isset($FOUND))
      { if($FOUND) return true;
        else
        { if($sgbd->add_data("config", array($key => $value))) return true;
        }
      }
      return false;
    }

    function del_config($key)
    { $ids = array();
      $sgbd = $this->sgbd();
      if($rst = $sgbd->open_data("config"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(isset($v_rst[$key]) && isset($v_rst["id"]))
            { $ids[] = $v_rst["id"];
            }
          }
          else $ids = false;
        }
        $sgbd->close_data($rst);
      }
      if($ids === false) return false;
      foreach($ids as $id) if(!$sgbd->del_data("config", $id)) return false;
      return true;
    }

  }

?>
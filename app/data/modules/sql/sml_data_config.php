<?php

  class sml_data_config extends sml_data
  {

    function config($key = null)
    { $sgbd = $this->sgbd();
      $value = false;
      if(isset($key))
      { $sql =
         "SELECT `value` FROM #--config"
        ." WHERE `key`=".$this->eq($key);
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return false;
        if($v_rst = $sgbd->fetch_assoc($rst)) $value = $v_rst["value"];
        else $value = "";
        $sgbd->free_result($rst);
      }
      else
      { $value = array();
        $sql =
         "SELECT * FROM #--config";
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return false;
        while($v_rst = $sgbd->fetch_assoc($rst)) $value[$v_rst["key"]] = $v_rst["value"];
        $sgbd->free_result($rst);
      }
      return $value;
    }

    function config_exists($key)
    { $sgbd = $this->sgbd();
      $exists = false;
      $sql = "SELECT count(*) as n FROM #--config"
      ." WHERE `key`=".$this->eq($key);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $exists = $v_rst["n"];
      $sgbd->free_result($rst);
      return $exists;
    }

    function set_config($key, $value)
    { $sgbd = $this->sgbd();
      if($this->config_exists($key)) $sql =
       "UPDATE #--config"
      ." SET `value`=".$this->eq($value)
      ." WHERE `key`=".$this->eq($key);
      else $sql =
       "INSERT INTO #--config"
      ." VALUES(NULL, ".$this->eq($key).", ".$this->eq($value).")";
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      return true;
    }

    function del_config($key)
    { $sgbd = $this->sgbd();
      return $sgbd->query("DELETE FROM #--config WHERE `key`=".$this->eq($key)) ? true : false;
    }

  }

?>
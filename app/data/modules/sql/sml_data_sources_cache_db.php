<?php

  class sml_data_sources_cache_db extends sml_data
  {

    function source_cache_db()
    { $sgbd = $this->sgbd();
      $cache = array();
      $sql = "SELECT * FROM #--source_cache";
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      while($v_rst = $sgbd->fetch_assoc($rst)) $cache[$v_rst["id"]] = $v_rst;
      return $cache;
    }

    function source_cache_infos_db($url)
    { $sgbd = $this->sgbd();
      $cache_infos = array();
      $sql = "SELECT * FROM #--source_cache WHERE url=".$this->eq($url);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $cache_infos = $v_rst;
      $sgbd->free_result($rst);
      return $cache_infos;
    }

    function add_source_cache_db($url, $cache_index)
    { $sgbd = $this->sgbd();
      $sql =
       "INSERT INTO #--source_cache(url, id_source, last_update) VALUES"
      ."( ".$this->eq($url)
      .", ".$cache_index
      .", '".date("Y-m-d H:i:s")."'"
      .")";
      if($sgbd->query($sql)) return true;
      return false;
    }

    function del_source_cache_db($id_cache_data)
    { $env = $this->env();
      $sgbd = $this->sgbd();
      $sql = "DELETE FROM #--source_cache WHERE id=".$this->eq($id_cache_data);
      if($sgbd->query($sql)) return true;
      return false;
    }

  }

?>
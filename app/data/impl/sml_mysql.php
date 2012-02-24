<?php

  class sml_mysql
  {
    var $link;

    var $host;
    var $base;
    var $user;
    var $password;

    var $EXTENTION_OK;

    function extention_ok(&$env) { return $this->EXTENTION_OK; }

    function sml_mysql($host, $base, $user, $password)
    { $this->host = $host;
      $this->base = $base;
      $this->user = $user;
      $this->password = $password;
      $this->EXTENTION_OK = function_exists("mysql_connect");
    }

    function connect($host, $base, $user, $password)
    { $this->link = @mysql_connect($host, $user, $password);
      if(!$this->link) return null;
      @mysql_query("SET NAMES 'utf8'");
      if($base)
      { $connected = @mysql_select_db($base, $this->link);
        if(!$connected) return null;
      }
      return true;
    }

    function select_db($db_name)
    { $this->base = $db_name;
      if(!$this->link)
      { if(!$this->connect($this->host, $this->base, $this->user, $this->password)) return null;
      }
      return $this->query("USE ".$db_name);
    }

    function table_exists($table_name)
    { $sql =  "SHOW TABLES LIKE '".$table_name."'";
      $rst = $this->query($sql);
      if(isset($rst))
      { $exists = false;
        $v_rst = $this->fetch_assoc($rst);
        if($v_rst) $exists = true;
        $this->free_result($rst);
        return $exists;
      }
      return null;
    }

    function query($query_string)
    { if(!$this->link)
      { if(!$this->connect($this->host, $this->base, $this->user, $this->password)) return null;
      }
      $result = @mysql_query($query_string, $this->link);
      if(!$result) return null;
      return $result;
    }

    function fetch_assoc($rst)
    { if($rst && $this->link) return mysql_fetch_assoc($rst);
      return null;
    }

    function insert_id()
    { if($this->link) return mysql_insert_id($this->link);
      return null;
    }

    function free_result($rst)
    { if($rst && $this->link) return mysql_free_result($rst);
      return null;
    }

    function close()
    { if($this->link) return mysql_close($this->link);
      return null;
    }

  }

?>
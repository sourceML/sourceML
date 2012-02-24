<?php

  class sml_sqlite
  {
    var $link;

    var $path;
    var $file;

    var $EXTENTION_OK;

    function extention_ok(&$env) { return $this->EXTENTION_OK; }



    function sml_sqlite($path, $file, $user = "", $password = "")
    { $this->path = $path.($path && substr($path, -1) != "/" ? "/" : "");
      $this->file = $file;
      $this->EXTENTION_OK = class_exists("PDO");
    }

    function connect($path, $file, $user = "", $password = "")
    { $this->path = $path.($path && substr($path, -1) != "/" ? "/" : "");
      $this->file = $file;
      try
      { $this->link = new PDO("sqlite:".$this->path.$this->file);
        return true;
      }
      catch(PDOException $e)
      { return null;
      }
    }

    function select_db($file)
    { return $this->close() && $this->connet($this->path, $file);
    }

    function table_exists($table_name)
    { $sql =  "SELECT * FROM sqlite_master WHERE type='table' AND name='".$table_name."'";
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
      { if(!$this->connect($this->path, $this->file)) return null;
      }
      try
      { $rst = $this->link->query(str_replace("`", "", $query_string));
        if($rst === false) $rst = null;
      }
      catch(Exception $e) { $rst = null; }
      return $rst;
    }

    function fetch_assoc($rst)
    { if($rst && $this->link)
      { try
        { $row = $rst->fetch();
          if($row) foreach($row as $key => $value)
          { if(!preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/", $key)) unset($row[$key]);
          }
        }
        catch(Exception $e) { $row = null; }
        return $row;
      }
      return null;
    }

    function insert_id()
    { if($this->link)
      { try
        { $rst = $this->link->query("SELECT last_insert_rowid() as last_insert_rowid");
          if($rst)
          { if($v_rst = $rst->fetch()) $insert_id = $v_rst["last_insert_rowid"];
            else $insert_id = null;
          }
          else $insert_id = null;
        }
        catch(Exception $e) { $insert_id = null; }
      }
      else $insert_id = null;
      return $insert_id;
    }

    function free_result($rst)
    { if($rst) return true;
      return null;
    }

    function close()
    { if(!$this->link) return null;
      $this->link = null;
      return true;
    }

  }

?>
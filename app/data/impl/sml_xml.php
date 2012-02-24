<?php

  class sml_xml
  {

    var $host;
    var $base;
    var $user;
    var $password;

    var $EXTENTION_OK;

    var $xml_data;

    var $data_handlers;
    var $last_data_handler;

    function sml_xml($host, $base, $user, $password)
    { $this->init_xml_env($host, $base, $user, $password);
      $this->EXTENTION_OK = true;
    }

    function extention_ok(&$env)
    { if($this->EXTENTION_OK)
      { if
        (    file_exists($env->app_file("data/impl/xml/sml_xml_data.php"))
          && file_exists($env->app_file("data/impl/xml/sml_xml_data_handler.php"))
        )
        { require $env->app_file("data/impl/xml/sml_xml_data.php");
          require $env->app_file("data/impl/xml/sml_xml_data_handler.php");
          if
          (    class_exists("sml_xml_data")
            && class_exists("sml_xml_data_handler")
          )
          { $this->xml_data = new sml_xml_data($this->host, $this->base);
          }
          else $this->EXTENTION_OK = false;
        }
        else $this->EXTENTION_OK = false;
      }
      return $this->EXTENTION_OK;
    }

    function init_xml_env($host, $base, $user, $password)
    { $this->host = $host.($host && substr($host, -1) != "/" ? "/" : "");
      $this->base = $base.($base && substr($base, -1) != "/" ? "/" : "");
      $this->user = $user;
      $this->password = $password;
      $this->data_handlers = array();
      $this->last_data_handler = 0;
    }

    function connect($host, $base, $user, $password)
    { if($host.$base && is_dir($host.$base) && is_writable($host.$base))
      { $this->init_xml_env($host, $base, $user, $password);
        $this->xml_data = new sml_xml_data($this->host, $this->base);
        return true;
      }
      return null;
    }

    function select_db($base)
    { $this->base = $base.($base && substr($base, -1) != "/" ? "/" : "");
      return $this->connect($this->host, $this->base, $this->user, $this->password);
    }

    function data_exists($data_path)
    { return is_dir($this->host.$this->base.$data_path);
    }

    function create_data($data_path)
    { if(!is_dir($this->host.$this->base.$data_path)) @mkdir($this->host.$this->base.$data_path);
      if(is_dir($this->host.$this->base.$data_path))
      { if($dh = $this->open_data($data_path, false))
        { $this->close_data($dh);
          return true;
        }
      }
      return null;
    }

    function get_data($data_path, $data_id)
    { $dh = ++$this->last_data_handler;
      $this->data_handlers[$dh] = new sml_xml_data_handler($this->xml_data, $data_path);
      if($this->data_handlers[$dh]->open_data(false))
      { $res = $this->data_handlers[$dh]->get_data($data_id);
        $this->close_data($dh);
        return $res;
      }
      return null;
    }

    function open_data($data_path, $FETCH = true)
    { $dh = ++$this->last_data_handler;
      $this->data_handlers[$dh] = new sml_xml_data_handler($this->xml_data, $data_path);
      if($this->data_handlers[$dh]->open_data($FETCH))
      { return $dh;
      }
      $this->close_data($dh);
      return null;
    }

    function fetch_data($dh)
    { if(isset($this->data_handlers[$dh]))
      { return $this->data_handlers[$dh]->fetch_assoc();
      }
      return false;
    }

    function add_data($data_path, $data)
    { $dh = ++$this->last_data_handler;
      $this->data_handlers[$dh] = new sml_xml_data_handler($this->xml_data, $data_path);
      if($this->data_handlers[$dh]->open_data(false))
      { $res = $this->data_handlers[$dh]->add_data($data);
        if($res) $res = $this->last_index($dh);
        $this->close_data($dh);
        return $res;
      }
      return null;
    }

    function last_index($dh)
    { if(isset($this->data_handlers[$dh]))
      { return $this->data_handlers[$dh]->last_index;
      }
      return null;
    }

    function set_data($data_path, $data_id, $data)
    { $dh = ++$this->last_data_handler;
      $this->data_handlers[$dh] = new sml_xml_data_handler($this->xml_data, $data_path);
      if($this->data_handlers[$dh]->open_data(false))
      { $res = $this->data_handlers[$dh]->set_data($data_id.".xml", $data);
        $this->close_data($dh);
        return $res;
      }
      return null;
    }

    function del_data($data_path, $data_id)
    { $dh = ++$this->last_data_handler;
      $this->data_handlers[$dh] = new sml_xml_data_handler($this->xml_data, $data_path);
      if($this->data_handlers[$dh]->open_data(false))
      { $res = $this->data_handlers[$dh]->del_data($data_id.".xml");
        $this->close_data($dh);
        return $res;
      }
      return null;
    }

    function close_data($dh)
    { if(isset($this->data_handlers[$dh]))
      { $this->data_handlers[$dh]->close_data();
        unset($this->data_handlers[$dh]);
      }
    }

    function remove_data($data_path)
    { $OK = strlen($data_path) > 0;
      if($OK && is_dir($this->host.$this->base.$data_path) && is_writable($this->host.$this->base.$data_path))
      { $data_path .= substr($data_path, -1) == "/" ? "" : "/";
        if($dh = opendir($this->host.$this->base.$data_path))
        { while($OK && ($file = readdir($dh)) !== false)
          { if(is_dir($this->host.$this->base.$data_path.$file))
            { if($file != "." && $file != "..") $OK = $this->remove_data($data_path.$file);
            }
            else $OK = @unlink($this->host.$this->base.$data_path.$file);
          }
          closedir($dh);
        }
        else $OK = null;
      }
      else $OK = null;
      if($OK) @rmdir($this->host.$this->base.$data_path);
      return $OK;
    }

  }

?>
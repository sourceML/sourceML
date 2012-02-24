<?php

  class sml_xml_data
  {
    var $host;
    var $base;

    var $sxml;

    var $cache;
    var $buffer;

    function sml_xml_data($host, $base)
    { $this->host = $host.(substr($host, -1) != "/" ? "/" : "");
      $this->base = $base.(substr($base, -1) != "/" ? "/" : "");
      $this->cache = array();
    }

    function host() { return $this->host; }
    function base() { return $this->base; }

    function use_cache() { return true; }

    function set_cache($data_name, $data, $data_id)
    { if($this->use_cache())
      { $this->cache[$data_name] = $data;
        $this->cache[$data_name]["id"] = $data_id;
      }
    }

    function get_data($data_path, $data_id)
    { $data_name = $this->data_name($data_path, $data_id);
      if(isset($this->cache[$data_name])) return $this->cache[$data_name];
      if($this->buffer = @file_get_contents($data_name))
      { if(($data = $this->parse_data()) !== false)
        { $this->set_cache($data_name, $data, $data_id);
          $data["id"] = $data_id;
          return $data;
        }
      }
      return false;
    }

    function add_data($data_path, $data_id, $data)
    { return $this->_set_data($data_path, $data_id, $data);
    }

    function set_data($data_path, $data_id, $data)
    { return $this->_set_data($data_path, $data_id, $data);
    }

    function _set_data($data_path, $data_id, $data)
    { if($fh = @fopen($this->data_name($data_path, $data_id), "w"))
      { $this->buffer = $this->serialize_data($data);
        if(@fwrite($fh, $this->buffer) !== false)
        { @fclose($fh);
          $this->buffer = null;
          $data_name = $this->data_name($data_path, $data_id);
          $this->set_cache($data_name, $data, $data_id);
          return $data_id;
        }
        else
        { @fclose($fh);
          $this->buffer = null;
        }
      }
      return null;
    }

    function del_data($data_path, $data_id)
    { $data_name = $this->data_name($data_path, $data_id);
      if(isset($this->cache[$data_name])) unset($this->cache[$data_name]);
      return @unlink($this->data_name($data_path, $data_id));
    }

    function data_name($data_path, $data_id)
    { return $this->host.$this->base.$data_path.$data_id.".xml";
    }

    function parse_data()
    { if(!isset($this->sxml)) $this->sxml = new sxml();
      $this->sxml->parse($this->buffer);
      if(isset($this->sxml->data["tuple"][0]))
      { $this->buffer = $this->sxml->data["tuple"][0];
        $v_rst = array();
        foreach($this->buffer["subs"] as $key => $value)
        { $v_rst[$key] = $value[0]["data"];
        }
        return $v_rst;
      }
      return false;
    }

    function serialize_data($data)
    { $this->buffer = "<tuple>\n";
      foreach($data as $key => $value)
      { if(isset($value)) $this->buffer .= "  <".$key."><![CDATA[".$value."]]></".$key.">\n";
      }
      $this->buffer .= "</tuple>\n";
      return $this->buffer;
    }

  }

?>
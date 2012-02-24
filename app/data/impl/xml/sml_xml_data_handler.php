<?php

  class sml_xml_data_handler
  {
    var $xml_data;
    var $data_path;
    var $data_path_handler;
    var $last_index;

    function sml_xml_data_handler($xml_data, $data_path)
    { $this->xml_data = $xml_data;
      $this->data_path = $data_path.(substr($data_path, -1) != "/" ? "/" : "");
    }

    function get_data($data_id)
    { if(file_exists($this->xml_data->host().$this->xml_data->base().$this->data_path.$data_id.".xml"))
      { return $this->xml_data->get_data($this->data_path, $data_id);
      }
      return false;
    }

    function open_data($FETCH = true)
    { clearstatcache();
      $INDEX_OK = false;
      if($this->xml_data->host() && $this->xml_data->base() && $this->data_path)
      { if(is_dir($this->xml_data->host().$this->xml_data->base().$this->data_path) && is_writable($this->xml_data->host().$this->xml_data->base().$this->data_path))
        { if(!file_exists($this->xml_data->host().$this->xml_data->base().$this->data_path.".index"))
          { if($fh = @fopen($this->xml_data->host().$this->xml_data->base().$this->data_path.".index", "w+"))
            { if(@fwrite($fh, "0"))
              { $this->last_index = 0;
                @fclose($fh);
                $INDEX_OK = true;
              }
              else @fclose($fh);
            }
          }
          else
          { if(($this->buffer = @file_get_contents($this->xml_data->host().$this->xml_data->base().$this->data_path.".index")) !== false)
            { if(preg_match("/^[0-9]+$/", $this->buffer))
              { $this->last_index = (int)$this->buffer;
                $INDEX_OK = true;
              }
            }
          }
        }
      }
      if($INDEX_OK)
      { if($FETCH)
        { if($this->data_path_handler = @opendir($this->xml_data->host().$this->xml_data->base().$this->data_path))
          { return true;
          }
          else
          { $this->close_data();
            return null;
          }
        }
        else return true;
      }
      else
      { $this->close_data();
        return null;
      }
    }

    function fetch_assoc()
    { if($this->data_path_handler)
      { $FORMAT_OK = false;
        while(!$FORMAT_OK && ($data_file = @readdir($this->data_path_handler)) !== false)
        { if(substr($data_file, 0, 1) != "." && substr($data_file, -4) == ".xml") $FORMAT_OK = true;
        }
        if($FORMAT_OK) return $this->xml_data->get_data($this->data_path, substr($data_file, 0, -4));
      }
      return false;
    }

    function add_data($data)
    { $index = $this->inc_index();
      if(isset($index))
      { if(is_array($data)) return $this->xml_data->add_data($this->data_path, $index, $data);
      }
      return null;
    }

    function inc_index()
    { clearstatcache();
      if(isset($this->last_index))
      { $index = $this->last_index + 1;
        if($fh = @fopen($this->xml_data->host().$this->xml_data->base().$this->data_path.".index", "w+"))
        { if(@fwrite($fh, (string)$index))
          { $this->last_index = $index;
            @fclose($fh);
            return $index;
          }
          else @fclose($fh);
        }
      }
      return null;
    }

    function set_data($data_file, $data)
    { if(preg_match("/^[0-9]+\.xml$/", $data_file))
      { if(is_writable($this->xml_data->host().$this->xml_data->base().$this->data_path.$data_file))
        { if(is_array($data))
          { return $this->xml_data->set_data($this->data_path, substr($data_file, 0, -4), $data);
          }
        }
      }
      return null;
    }

    function del_data($data_file)
    { if(preg_match("/^[0-9]+\.xml$/", $data_file))
      { if(is_file($this->xml_data->host().$this->xml_data->base().$this->data_path.$data_file))
        { return $this->xml_data->del_data($this->data_path, substr($data_file, 0, -4));
        }
      }
      return null;
    }

    function close_data()
    { $this->data_path= null;
      if($this->data_path_handler) @closedir($this->data_path_handler);
      $this->last_index = null;
    }

  }

?>
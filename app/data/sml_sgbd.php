<?php

  class sml_sgbd
  {
    var $sgbd_impl;
    var $env;

    function sml_sgbd($sgbd_impl, $env)
    { $this->sgbd_impl = $sgbd_impl;
      $this->env = $env;
    }

    function extention_ok() { return $this->sgbd_impl->extention_ok($this->env); }

    function connect($host, $base, $user, $password)
    { return $this->sgbd_impl->connect($host, $base, $user, $password);
    }

    function select_db($db_name)
    { return $this->sgbd_impl->select_db($db_name);
    }

    # ---------------------------------------------------------------------------------
    #                                                                               SQL
    #

    function table_exists($table_name)
    { return $this->sgbd_impl->table_exists
      ( ($prefix_codes = array_keys($this->env->bdd("table_prefix"))) ?
          str_replace($prefix_codes, array_values($this->env->bdd("table_prefix")), $table_name)
        : $table_name
      );
    }

    function query($sql)
    { return $this->sgbd_impl->query
      ( ($prefix_codes = array_keys($this->env->bdd("table_prefix"))) ?
          str_replace($prefix_codes, array_values($this->env->bdd("table_prefix")), $sql)
        : $sql
      );
    }

    function insert_id()
    { return $this->sgbd_impl->insert_id();
    }

    function fetch_assoc($rst)
    { return $this->sgbd_impl->fetch_assoc($rst);
    }

    function free_result($rst)
    { return $this->sgbd_impl->free_result($rst);
    }

    function close()
    { return $this->sgbd_impl->close();
    }

    # ---------------------------------------------------------------------------------
    #                                                                               XML
    #

    function data_exists($data_path)
    { return $this->sgbd_impl->data_exists($data_path);
    }

    function create_data($data_path)
    { return $this->sgbd_impl->create_data($data_path);
    }

    function get_data($data_path, $data_id)
    { return $this->sgbd_impl->get_data($data_path, $data_id);
    }

    function open_data($data_path, $FETCH = true)
    { return $this->sgbd_impl->open_data($data_path, $FETCH);
    }

    function fetch_data($dh)
    { return $this->sgbd_impl->fetch_data($dh);
    }

    function add_data($data_path, $data)
    { return $this->sgbd_impl->add_data($data_path, $data);
    }

    function last_index($dh)
    { return $this->sgbd_impl->last_index($dh);
    }

    function set_data($data_path, $data_id, $data)
    { return $this->sgbd_impl->set_data($data_path, $data_id, $data);
    }

    function del_data($data_path, $data_id)
    { return $this->sgbd_impl->del_data($data_path, $data_id);
    }

    function close_data($dh)
    { return $this->sgbd_impl->close_data($dh);
    }

    function remove_data($data_path)
    { return $this->sgbd_impl->remove_data($data_path);
    }

  }

?>
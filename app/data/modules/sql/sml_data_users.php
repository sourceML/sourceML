<?php

  class sml_data_users extends sml_data
  {

    var $users;
    var $_user;
    var $user_status;
    var $action_status;

    # ----------------------------------------------------------------------------------------
    #                                                                                    users
    #

    function users($start = 0, $alpha = null, $status = null)
    { $sgbd = $this->sgbd();
      $env = $this->env();
      $users = array("list" => array(), "total" => 0);
      $SELECT = "SELECT *";
      $FROM = " FROM #--users";
      $WHERE = "";
      $WHERE .= (isset($alpha) ? ($WHERE ? " AND" : " WHERE")." LEFT(login, 1)=".$this->eq($alpha) : "");
      $WHERE .= (isset($status) ? ($WHERE ? " AND" : " WHERE")." status=".$this->eq($status) : "");
      $LIMIT = ($env->config("max_list") ? " LIMIT ".$env->config("max_list")." OFFSET ".$start : "");
      $sql = "SELECT count(*) as n FROM(".$SELECT.$FROM.$WHERE.") res";
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $users["total"] = $v_rst["n"];
      $sgbd->free_result($rst);
      if($users["total"] > 0)
      { $sql = "SELECT * FROM(".$SELECT.$FROM.$WHERE.$LIMIT.") res";
        $rst = $sgbd->query($sql);
        if(!isset($rst)) return false;
        while($v_rst = $sgbd->fetch_assoc($rst)) $users["list"][$v_rst["id"]] = $v_rst;
        $sgbd->free_result($rst);
      }
      return $users;
    }

    function user_by_id($id)
    { $sgbd = $this->sgbd();
      $user = array();
      $sql = "SELECT * from #--users WHERE id=".$this->eq($id);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $user = $v_rst;
      $sgbd->free_result($rst);
      return $user;
    }

    function user($login)
    { $sgbd = $this->sgbd();
      $user = array();
      $sql = "SELECT * from #--users WHERE login=".$this->eq($login);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $user = $v_rst;
      $sgbd->free_result($rst);
      return $user;
    }

    function user_exists($login)
    { $sgbd = $this->sgbd();
      $EXISTS = 0;
      $sql = "SELECT count(*) as n from #--users WHERE login=".$this->eq($login);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $EXISTS = $v_rst["n"];
      $sgbd->free_result($rst);
      return $EXISTS;
    }

    function add_user($login, $password, $email, $status)
    { $sgbd = $this->sgbd();
      $sql =
       "INSERT INTO #--users(login, password, email, status) VALUES"
      ."( ".$this->eq($login)
      .", ".$this->eq($password)
      .", ".$this->eq($email)
      .", ".$status
      .")";
      return $sgbd->query($sql);
    }

    function set_user($id, $login, $password, $email, $status)
    { $sgbd = $this->sgbd();
      $sql =
       "UPDATE #--users SET"
      ."  login=".$this->eq($login)
      .", password=".$this->eq($password)
      .", email=".$this->eq($email)
      .", status=".$status
      ." WHERE id=".$id;
      return $sgbd->query($sql);
    }

    function del_user($login)
    { $sgbd = $this->sgbd();
      $sql = "DELETE FROM #--users WHERE login=".$this->eq($login);
      return $sgbd->query($sql);
    }

    # ----------------------------------------------------------------------------------------
    #                                                                                   status
    #

    function status()
    { if(!isset($this->user_status)) return false;
      return $this->user_status;
    }

    function init_user_status($status = array())
    { $sgbd = $this->sgbd();
      $this->user_status = array();
      $sql = "SELECT * FROM #--user_status";
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      while($v_rst = $sgbd->fetch_assoc($rst)) $this->user_status[$v_rst["id"]] = $v_rst;
      $sgbd->free_result($rst);
      return $this->user_status;
    }

    function init_action_status($status = array())
    { if(!isset($this->user_status)) return false;
      $sgbd = $this->sgbd();
      $this->action_status = array();
      $sql = "SELECT * FROM #--action_status";
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      while($v_rst = $sgbd->fetch_assoc($rst)) $this->action_status[$v_rst["id"]] = $v_rst;
      $sgbd->free_result($rst);
      return $this->action_status;
    }

    function get_user_status()
    { $user = $this->get_session_user();
      if($user && isset($user["status"])) return $user["status"];
      return 0;
    }

    function get_action_status($mod, $controller = "index", $action = "index", $set_status = array())
    { $sgbd = $this->sgbd();
      $status = array();
      $sql =
       "SELECT action, id_status"
      ." FROM #--action_status"
      ." WHERE action=".$this->eq($mod)
      ." OR action=".$this->eq($mod."/".$controller)
      ." OR action=".$this->eq($mod."/".$controller."/".$action);
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      while($v_rst = $sgbd->fetch_assoc($rst))
      { if(!isset($status[$v_rst["action"]])) $status[$v_rst["action"]] = array();
        $status[$v_rst["action"]][$v_rst["id_status"]] = true;
      }
      $sgbd->free_result($rst);
      return $status;
    }

    function creation_default_status()
    { $sgbd = $this->sgbd();
      $default_status = 0;
      $sql = "SELECT id FROM #--user_status WHERE creation_default=1 LIMIT 0,1";
      $rst = $sgbd->query($sql);
      if(!isset($rst)) return false;
      if($v_rst = $sgbd->fetch_assoc($rst)) $default_status = $v_rst["id"];
      $sgbd->free_result($rst);
      return $default_status;
    }

    # ----------------------------------------------------------------------------------------
    #                                                                             log in / out
    #

    function login($login, $password)
    { if(($user = $this->user($login)) !== false)
      { if($this->password_ok($user, $password))
        { if(!$this->set_session($user)) $user = false;
        }
        else
        { $this->clear_session();
          $user = array();
        }
      }
      return $user;
    }

    function logout()
    { return $this->clear_session();
    }

    function user_ok($user)
    { return
      strcmp(md5($user["password"].$_SESSION["id"]), $_SESSION["pass"]) == 0
      && $_SESSION["ip"] == $_SERVER["REMOTE_ADDR"];
    }

    function password_ok($user, $password)
    { return
      strcmp(md5($user["password"].$_SESSION["id"]), $password) == 0
      && $_SESSION["ip"] == $_SERVER["REMOTE_ADDR"];
    }

    # ----------------------------------------------------------------------------------------
    #                                                                                  session
    #

    function load_session()
    { session_start();
      if(!isset($_SESSION["id"])) $this->clear_session();
      if
      ( $user =
        ( isset($_COOKIE["user"]) || isset($_SESSION["user"]) ?
            $this->user(isset($_COOKIE["user"]) ? $_COOKIE["user"] : $_SESSION["user"])
          : array()
        )
      )
      { if(isset($_COOKIE["user"])) $this->set_session($user);
        if(!$this->user_ok($user))
        { $this->clear_session();
          $user = array();
        }
      }
      $this->_user = $user;
      return $user;
    }

    function set_session($user)
    { $_SESSION["user"] = $user["login"];
      $_SESSION["pass"] = md5($user["password"].$_SESSION["id"]);
      $env = $this->env();
      return setcookie("user", $user["login"], time() + (60 * 60 * 24 * 7), $env->path("web"));
    }

    function clear_session()
    { unset($_SESSION["user"]);
      unset($_SESSION["pass"]);
      $_SESSION["ip"] = $_SERVER["REMOTE_ADDR"];
      $_SESSION["id"] = md5(rand());
      $env = $this->env();
      return setcookie("user", "", 0, $env->path("web"));
    }

    function get_session_user() { return $this->_user; }

    # ----------------------------------------------------------------------------------------
    #                                                                                  uploads
    #

    function check_user_uploads_dir($user = null)
    { $env = $this->env();
      $user_dir = $env->path("content")."uploads/".(isset($user) ? $user : $this->_user["id"]);
      if(!file_exists($user_dir)) @mkdir($user_dir);
      return file_exists($user_dir);
    }

  }

?>
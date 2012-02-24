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
      $res = array();
      if($rst = $sgbd->open_data("users"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(!isset($alpha) || (isset($v_rst["login"]) && strtolower(substr($v_rst["login"], 0, 1)) == strtolower($alpha)))
            { if(!isset($status) || (isset($v_rst["status"]) && $v_rst["status"] == $status))
              { $res[$v_rst["id"]] = $v_rst;
                $users["total"]++;
              }
            }
          }
          else
          { $res = false;
            break;
          }
        }
        $sgbd->close_data($rst);
        if($res !== false)
        { $n = 0;
          foreach($res as $id_user => $user)
          { $n++;
            if(!$env->config("max_list") || ($n > $start && $n <= ($start + $env->config("max_list"))))
            { $users["list"][$user["id"]] = $user;
              if(!isset($this->users)) $this->users = array();
              $this->users[$user["id"]] = $user;
            }
          }
        }
        else $users = false;
      }
      else $users = false;
      return $users;
    }

    function user_by_id($id)
    { if(!isset($this->users)) $this->users = array();
      if(isset($this->users[$id])) return $this->users[$id];
      $sgbd = $this->sgbd();
      if(($user = $sgbd->get_data("users", $id)) !== false)
      { $this->users[$id] = $user;
      }
      return $user;
    }

    function user($login)
    { $sgbd = $this->sgbd();
      $user = array();
      if($rst = $sgbd->open_data("users"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(isset($v_rst["login"]) && $v_rst["login"] == $login)
            { $user = $v_rst;
              break;
            }
          }
          else $user = false;
        }
        $sgbd->close_data($rst);
      }
      else $user = false;
      if($user !== false)
      { if(!isset($this->users)) $this->users = array();
        $this->users[$user["id"]] = $user;
      }
      return $user;
    }

    function user_exists($login)
    { $sgbd = $this->sgbd();
      $EXISTS = 0;
      if($rst = $sgbd->open_data("users"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(isset($v_rst["login"]) && $v_rst["login"] == $login)
            { $EXISTS++;
            }
          }
          else
          { $EXISTS = false;
            break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $EXISTS = false;
      return $EXISTS;
    }

    function add_user($login, $password, $email, $status)
    { $sgbd = $this->sgbd();
      return $sgbd->add_data
      ( "users",
        array
        ( "login" => $login,
          "password" => $password,
          "email" => $email,
          "status" => $status
        )
      );
    }

    function set_user($id, $login, $password, $email, $status)
    { $sgbd = $this->sgbd();
      return $sgbd->set_data
      ( "users",
        $id,
        array
        ( "login" => $login,
          "password" => $password,
          "email" => $email,
          "status" => $status
        )
      );
    }

    function del_user($login)
    { if(($user = $this->user($login)) !== false)
      { $sgbd = $this->sgbd();
        return $sgbd->del_data("users", $user["id"]);
      }
      return false;
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
      if($rst = $sgbd->open_data("user_status"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { $this->user_status[$v_rst["id"]] = $v_rst;
          }
          else
          { $this->user_status = false;
            break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $this->user_status = false;
      if($status && $this->user_status !== false)
      { foreach($status as $new_user_status)
        { $id_status = false;
          foreach($this->user_status as $user_status) if($new_user_status["nom"] == $user_status["nom"])
          { $id_status = $user_status["id"];
            break;
          }
          if($id_status)
          { $SAME = true;
            foreach($new_user_status as $status_key => $status_value)
            { if(!isset($this->user_status[$id_status][$status_key]) || $this->user_status[$id_status][$status_key] != $status_value)
              { $SAME = false; break;
              }
            }
            if(!$SAME)
            { if($sgbd->set_data("user_status", $id_status, $new_user_status)) $this->user_status[$id_status] = $new_user_status;
              else { $this->user_status = false; break; }
            }
          }
          else
          { if($id_status = $sgbd->add_data("user_status", $new_user_status)) $this->user_status[$id_status] = $new_user_status;
            else { $this->user_status = false; break; }
          }
        }
      }
      return $this->user_status;
    }

    function init_action_status($status = array())
    { if(!isset($this->user_status)) return false;
      $sgbd = $this->sgbd();
      $this->action_status = array();
      if($rst = $sgbd->open_data("action_status"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { $this->action_status[$v_rst["id"]] = $v_rst;
          }
          else
          { $this->action_status = false;
            break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $this->action_status = false;
      if($status && $this->action_status !== false)
      { $STATUS_OK = true;
        foreach($status as $id_new_action_status => $new_action_status)
        { $FOUND = $new_action_status["id_status"] == "0";
          if(!$FOUND) foreach($this->user_status as $user_status)
          { if($new_action_status["id_status"] == $user_status["nom"])
            { $FOUND = true;
              $status[$id_new_action_status]["id_status"] = $user_status["id"];
            }
          }
          if(!$FOUND)
          { $STATUS_OK = false;
            break;
          }
        }
        if($STATUS_OK)
        { foreach($status as $new_action_status)
          { $id_status = false;
            foreach($this->action_status as $action_status)
            { if
              (    $new_action_status["action"] == $action_status["action"]
                && $new_action_status["id_status"] == $action_status["id_status"]
              )
              { $id_status = $action_status["id"];
                break;
              }
            }
            if($id_status)
            { $SAME = true;
              foreach($new_action_status as $status_key => $status_value)
              { if(!isset($this->action_status[$id_status][$status_key]) || $this->action_status[$id_status][$status_key] != $status_value)
                { $SAME = false; break;
                }
              }
              if(!$SAME)
              { if($id_status = $sgbd->add_data("action_status", $new_action_status)) $this->action_status[$id_status] = $new_action_status;
                else { $this->action_status = false; break; }
              }
            }
            else
            { if($id_status = $sgbd->add_data("action_status", $new_action_status)) $this->action_status[$id_status] = $new_action_status;
              else { $this->action_status = false; break; }
            }
          }
        }
        else $this->action_status = false;
      }
      return $this->action_status;
    }

    function get_user_status()
    { $user = $this->get_session_user();
      if($user && isset($user["status"])) return $user["status"];
      return 0;
    }

    function get_action_status($mod, $controller = "index", $action = "index", $set_status = array())
    { $sgbd = $this->sgbd();
      if($rst = $sgbd->open_data("action_status"))
      { while($status !==false && $v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst) && isset($v_rst["action"]) && isset($v_rst["id_status"]))
          { if
            (    $v_rst["action"] == $mod
              || $v_rst["action"] == $mod."/".$controller
              || $v_rst["action"] == $mod."/".$controller."/".$action
            )
            { if(!isset($status[$v_rst["action"]])) $status[$v_rst["action"]] = array();
              $status[$v_rst["action"]][$v_rst["id_status"]] = true;
            }
          }
          else $status = false;
        }
        $sgbd->close_data($rst);
      }
      else $status = false;
      if($status !== false)
      { if($set_status)
        { foreach($set_status as $new_action_status)
          { $id_status = false;
            foreach($status as $user_status) if($new_user_status["nom"] == $user_status["nom"])
            { $id_status = $user_status["id"];
              break;
            }
            if($id_status)
            { $SAME = true;
              foreach($new_user_status as $status_key => $status_value)
              { if(!isset($status[$id_status][$status_key]) || $status[$id_status][$status_key] != $status_value)
                { $SAME = false; break;
                }
              }
              if(!$SAME)
              { if($sgbd->set_data("user_status", $id_status, $new_user_status)) $status[$id_status] = $new_user_status;
                else { $status = false; break; }
              }
            }
            else
            { if($id_status = $sgbd->add_data("user_status", $new_user_status)) $status[$id_status] = $new_user_status;
              else { $status = false; break; }
            }
          }
        }
      }
      return $status;
    }

    function creation_default_status()
    { $sgbd = $this->sgbd();
      $default_status = 0;
      if($rst = $sgbd->open_data("user_status"))
      { while($v_rst = $sgbd->fetch_data($rst))
        { if(isset($v_rst))
          { if(isset($v_rst["creation_default"]) && $v_rst["creation_default"] == 1)
            { $default_status = $v_rst["id"];
              break;
            }
          }
          else
          { $default_status = false;
            break;
          }
        }
        $sgbd->close_data($rst);
      }
      else $default_status = false;
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
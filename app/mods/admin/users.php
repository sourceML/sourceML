<?php

  class sml_admin_users extends sml_mod
  {
    var $status;

    function validate(&$env)
    { $data = $env->data();
      if(($this->status = $data->status()) === false) return "impossible de lire la liste des statuts";
      return true;
    }

    function index(&$env)
    { $data = $env->data();
      if
      ( $env->set_out
        ( "users",
          $data->users
          ( $_GET[$env->param("start")] ? $_GET[$env->param("start")] : 0,
            $_GET[$env->param("alpha")],
            $_GET[$env->param("status")]
          )
        ) !== false
      )
      { if($this->status)
        { $env->set_out("status", $this->status);
        }
        else $env->erreur("impossible de lire la liste des status");
      }
      else $env->erreur("impossible de lire la liste des utilisateurs");
    }

    function add(&$env)
    { $data = $env->data();
      if($this->status)
      { $env->set_out("status", $this->status);
        $env->set_out("user", array("status" => $data->creation_default_status()));
        if($_POST)
        { $env->set_out("user", $_POST);
          if($_POST["login"])
          { if(($exists = $data->user($_POST["login"])) !== false)
            { if(!$exists)
              { $VALID = true;
                if(!$_POST["email"])
                { $env->message("merci de preciser un email");
                  $VALID = false;
                }
                if(!$_POST["password"])
                { $env->message("merci de preciser un mot de passe");
                  $VALID = false;
                }
                if($_POST["password"] != $_POST["password_confirm"])
                { $env->message("la confirmation du mot de passe est incorrecte");
                  $VALID = false;
                }
                if($VALID)
                { if
                  ( $data->add_user
                    ( $_POST["login"],
                      md5($_POST["password"]),
                      $_POST["email"],
                      $_POST["status"]
                    )
                  )
                  $env->redirect
                  ( $env->url("admin/users"),
                    "l'utilisateur <strong>".$_POST["login"]."</strong> a &eacute;t&eacute; ajout&eacute;"
                  );
                  else $env->erreur("Impossible d'ajouter l'utilisateur");
                }
              }
              else $env->message("ce login existe d&eacute;j&agrave;");
            }
            else $env->erreur("impossible de savoir si cet login existe d&eacute;j&agrave;");
          }
          else $env->message("merci de pr&eacute;ciser un login");
        }
      }
      else $env->erreur("impossible de lire la liste des status");
    }

    function edit(&$env)
    { $data = $env->data();
      if($this->status)
      { $env->set_out("status", $this->status);
        if($env->set_out("user", $data->user($_GET[$env->param("id")])))
        { if($_POST)
          { $user = $env->out("user");
            $id = $user["id"];
            $login = $user["login"];
            $password = $user["password"];
            $_POST["login"] = $login;
            $env->set_out("user", $_POST);
            $VALID = true;
            if(!$_POST["email"])
            { $env->message("merci de preciser un email");
              $VALID = false;
            }
            if(isset($_POST["change_password"]) && $_POST["change_password"])
            { if(!$_POST["password"])
              { $env->message("merci de preciser un mot de passe");
                $VALID = false;
              }
              if($_POST["password"] != $_POST["password_confirm"])
              { $env->message("la confirmation du mot de passe est incorrecte");
                $VALID = false;
              }
            }
            if($VALID)
            { if
              ( $data->set_user
                ( $id,
                  $login,
                  isset($_POST["change_password"]) && $_POST["change_password"] ? md5($_POST["password"]) : $password,
                  $_POST["email"],
                  $_POST["status"]
                )
              )
              $env->redirect
              ( $env->url("admin/users"),
                "l'utilisateur <strong>".$login."</strong> a &eacute;t&eacute; modifi&eacute;"
              );
              else $env->erreur("Impossible de mettre &agrave; jour l'utilisateur");
            }
          }
        }
        else $env->erreur("Impossible de lire les informations de cet utilisateur");
      }
      else $env->erreur("impossible de lire la liste des status");
    }

    function del(&$env)
    { $data = $env->data();
      if($env->set_out("user", $data->user($_GET[$env->param("id")])))
      { $user = $env->out("user");
        if($data->del_user($_GET[$env->param("id")])) $env->redirect
        ( $env->url("admin/users"),
          "l'utilisateur <strong>".$user["login"]."</strong> a &eacute;t&eacute; supprim&eacute;"
        );
        else $env->erreur("Impossible de supprimer l'utilisateur");
      }
      else $env->erreur("Impossible de lire les informations de cet utilisateur");
    }

  }

?>
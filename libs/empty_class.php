<?php

/*

  Cette classe aide a agreger des classes qui en heritent

  une classe qui herite de empty_class peut etre vide
  (sans attribut ni methode). une fois instanciee,
  la fonction load_modules lui ajoute les methodes
  qui sont definies dans d'autres classes qui heritent
  aussi de empty_class

  NOTE: pour la compatibilite php4 et php5,
  le mecanisme d'agregation est different selon
  la version de php

  php4: c'est la methode aggregate qui est utilisee

  php5: les classes contenant les nouvelles methodes
        sont instanciees et sont enregistrées dans
        le tableau associatif $module

  ATTENTION: les attributs de ces classes, en php5,
             on donc un contexte qui depend de
             leur classe et ne sont pas accessibles
             directement via l'instance principale

             si ces attributs doivent etre accessible
             via l'instance principale, il faut prevoir
             des accesseurs.

*/

  class empty_class
  {

    var $root_inst;
    var $modules;

    function empty_class($root_inst)
    { if($root_inst === true) $this->root_inst = $this;
      else $this->root_inst = $root_inst;
    }

    function load_modules($modules_path, $current_modules, $core_modules = null)
    { $this->_load_modules($modules_path, $current_modules, $this->root_inst, true);
      if(isset($core_modules) && $current_modules != $core_modules)
      { $this->_load_modules($modules_path, $core_modules, $this->root_inst, true);
      }
    }

    function _load_modules($modules_path, $modules_path_suffixe, $root_inst, $recursif = false)
    { if(file_exists($modules_path.$modules_path_suffixe) && $dh = opendir($modules_path.$modules_path_suffixe))
      { while(($file = readdir($dh)) !== false)
        { if(is_dir($modules_path.$modules_path_suffixe.$file))
          { if($recursif && substr($file, 0, 1) != ".")
            { $this->_load_modules($modules_path, $modules_path_suffixe.$file."/", $root_inst, $recursif);
            }
          }
          elseif(strcasecmp(substr($file, -4), ".php") == 0)
          { $this->load($modules_path.$modules_path_suffixe.$file, $root_inst);
          }
        }
        closedir($dh);
      }
    }

    function load($module_file, $root_inst)
    { if($module_file && file_exists($module_file))
      { $v_path = explode("/", $module_file);
        $file = $v_path[count($v_path) - 1];
        if(strcasecmp(substr($file, -4), ".php") == 0)
        { $class_name = substr($file, 0, -4);
          if(!class_exists($class_name))
          { require_once $module_file;
            if(version_compare(PHP_VERSION, '5.0.0', '>='))
            { if(class_exists($class_name) && !isset($this->modules[$class_name]))
              { $this->modules[$class_name] = new $class_name($root_inst);
              }
            }
            else
            { if(class_exists($class_name))
              { aggregate($this, $class_name);
              }
            }
          }
        }
      }
    }

    function __call($method_name, $arguments)
    { return $this->empty_class_call($this->root_inst, $method_name, $arguments);
    }

    function empty_class_call($inst, $method_name, $arguments)
    { $r = false;
      $args = "";
      foreach($arguments as $i => $arg) $args .= ($args ? ", " : "")."\$arguments[".$i."]";
      if(isset($inst->modules)) foreach($inst->modules as $module_name => $module)
      { if(method_exists($module, $method_name))
        { eval("\$r = \$module->".$method_name."(".$args.");");
          break;
        }
        else
        { $r = $this->empty_class_call($module, $method_name, $arguments);
          if($r !== false) break;
        }
      }
      return $r;
    }

  }

?>
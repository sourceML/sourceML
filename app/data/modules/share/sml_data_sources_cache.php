<?php

  class sml_data_sources_cache extends sml_data
  {

    /*
     * retourne le contenu XML dont l'URL d'origine est $url
     *
     * si le contenu n'est pas dans le cache, cette fonction
     * ira le lire et l'ajoutera dans le cache
     *
     * si le contenu est dans cache mais que sa date de validite
     * est depassee, le contenu sera mis a jour
     *
     * $url : l'URL d'origine du fichier XML
     *
     * $IGNORE_UPDATE : si VRAI et qu'un contenu correspondant est
     * dans le cache, c'est ce contenu qui sera fourni, meme si
     * sa date de validite est depassee
     *
     */
    function get_source_xml_from_cache($url, $IGNORE_UPDATE = false)
    { $env = $this->env();
      $sgbd = $this->sgbd();
      if(($cache_infos = $this->source_cache_infos_db($url)) !== false)
      { if($cache_infos)
        { if($env->config("cache_maj_auto"))
          { if(($need_update = $this->cache_need_update($cache_infos["last_update"], $env->config("cache_time") * 60 * 60)) !== false)
            { if(!$IGNORE_UPDATE && $need_update)
              { if($this->buffer =  @file_get_contents($url))
                { if($this->parse_source_xml($this->buffer))
                  { if($this->del_source_cache($cache_infos["id"], $cache_infos["id_source"]))
                    { if($this->add_source_cache($url, $this->buffer)) return $this->buffer;
                    }
                  }
                  else return -1;
                }
                else return -1;
              }
              else return @file_get_contents($env->path("content")."cache/sources/".$cache_infos["id_source"].".xml");
            }
          }
        }
        else
        { if($this->buffer =  @file_get_contents($url))
          { if($this->parse_source_xml($this->buffer))
            { if(!$this->add_source_cache($url, $this->buffer)) return false;
            }
            return $this->buffer;
          }
        }
      }
      return false;
    }

    /*
     * retourne 1 si $last_update + $cache_time est
     * superieur ou egal a la date courante, 0 sinon
     *
     * retourne FAUX si le formta de $last_update ou
     * de $cache_time est incorrect
     *
     * $last_update : date au format Y-m-d H:i:s
     *
     * $cache_time : duree, en secondes
     *
     */
    function cache_need_update($last_update, $cache_time)
    { $v_last_update = explode(" ", $last_update);
      if(count($v_last_update) == 2)
      { $v_date = explode("-", $v_last_update[0]);
        if(count($v_date) == 3)
        { $v_time = explode(":", $v_last_update[1]);
          if(count($v_time) == 3)
          { if
            (    preg_match("/^[0-9]+$/", $v_date[0])
              && preg_match("/^[0-9]+$/", $v_date[1])
              && preg_match("/^[0-9]+$/", $v_date[2])
              && preg_match("/^[0-9]+$/", $v_time[0])
              && preg_match("/^[0-9]+$/", $v_time[1])
              && preg_match("/^[0-9]+$/", $v_time[2])
              && preg_match("/^[0-9]+$/", $cache_time)
            )
            { return (time() - mktime($v_time[0], $v_time[1], $v_time[2], $v_date[1], $v_date[2], $v_date[0])) < $cache_time ? 0 : 1;
            }
          }
        }
      }
      return false;
    }

    /*
     * ajoute un contenu xml dans le cache
     *
     * $url : l'URL du fichier XML d'origine
     *
     * $xml_content : le contenu du fichier XML
     *
     */
    function add_source_cache($url, $xml_content)
    { $env = $this->env();
      $sgbd = $this->sgbd();
      if(($cache_index = $this->inc_sources_cache_index()) !== false)
      { if($fh = @fopen($env->path("content")."cache/sources/".$cache_index.".xml", "w+"))
        { $res = false;
          if(@fwrite($fh, $this->buffer))
          { @fclose($fh);
            $res = $this->add_source_cache_db($url, $cache_index);
          }
          @fclose($fh);
          return $res;
        }
      }
      return false;
    }

    /*
     * met a jour le cache pour le fichier XML dont l'URL d'origine est $url
     *
     * retourne :
     *
     *   VRAI si tout se passe bien
     *   -1 si l'URL est introuvable
     *   -2 si le contenu a cette URL n'est pas un fichier XML de source
     *   FAUX si le cache n'a pas pu etre mis a jour avec les nouvelles infos
     *
     */
    function maj_source_cache($url)
    { if($this->buffer =  @file_get_contents($url))
      { if($this->parse_source_xml($this->buffer))
        { if(($cache_infos = $this->source_cache_infos_db($url)) !== false)
          { if($cache_infos)
            { if($this->del_source_cache($cache_infos["id"], $cache_infos["id_source"]))
              { if($this->add_source_cache($url, $this->buffer)) return true;
              }
            }
            else
            { if($this->add_source_cache($url, $this->buffer)) return true;
            }
          }
          return false;
        }
        else return -2;
      }
      else return -1;
    }

    /*
     * vide le cache
     *
     */
    function empty_source_cache()
    { $OK = true;
      if(($cache = $this->source_cache_db()) !== false)
      { foreach($cache as $id_cache => $cache_infos)
        { if(!$this->del_source_cache($id_cache, $cache_infos["id_source"]))
          { $OK = false;
            $break;
          }
        }
      }
      else $OK = false;
      return $OK;
    }

    /*
     * efface un contenu XML du cache
     *
     * $id_cache_data : l'index des informations de cache
     * dans le dossier data
     *
     * $id_xml_cache : l'index du contenu XML dans le cache
     *
     */
    function del_source_cache($id_cache_data, $id_xml_cache)
    { $env = $this->env();
      if($this->del_source_cache_db($id_cache_data))
      { if(@unlink($env->path("content")."cache/sources/".$id_xml_cache.".xml")) return true;
      }
      return false;
    }

    /*
     * initialise le cache
     * cette fonction verifie que les dossiers du cache existent
     * et les cree sinon
     *
     */
    function init_sources_cache()
    { $env = $this->env();
      if(!is_dir($env->path("content")."cache")) @mkdir($env->path("content")."cache");
      if(is_dir($env->path("content")."cache"))
      { if(!is_dir($env->path("content")."cache/sources")) @mkdir($env->path("content")."cache/sources");
        if(is_dir($env->path("content")."cache/sources"))
        { return true;
        }
      }
      return "impossible d'initialiser le cache";
    }

    /*
     * incremente l'index du cache et retourne le nouvel index
     *
     */
    function inc_sources_cache_index()
    { clearstatcache();
      $env = $this->env();
      $cache_index = false;
      if(is_dir($env->path("content")."cache/sources") && is_writable($env->path("content")."cache/sources"))
      { if(!file_exists($env->path("content")."cache/sources/.index"))
        { if($fh = @fopen($env->path("content")."cache/sources/.index", "w+"))
          { if(@fwrite($fh, "0")) $cache_index = 0;
            @fclose($fh);
          }
        }
        else
        { if(($cache_index = @file_get_contents($env->path("content")."cache/sources/.index")) !== false)
          { if(preg_match("/^[0-9]+$/", $cache_index))
            { $cache_index = (int)$cache_index;
            }
            else $cache_index = false;
          }
        }
      }
      if($cache_index !== false)
      { $cache_index++;
        if($fh = @fopen($env->path("content")."cache/sources/.index", "w+"))
        { if(!@fwrite($fh, (string)$cache_index)) $cache_index = false;
          @fclose($fh);
        }
        else $cache_index = false;
      }
      return $cache_index;
    }

  }

?>
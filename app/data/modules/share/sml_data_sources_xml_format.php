<?php

  class sml_data_sources_xml_format extends sml_data
  {

    var $status;
    var $source;
    var $groupes;
    var $licence;
    var $sources;
    var $xml_content;

    # ----------------------------------------------------------------------------------------
    #                                                                            data vers XML
    #

    function source_xml_content($id, $params = array())
    { $env = $this->env();
      $content = false;
      $this->status = isset($this->status) ? $this->status : $this->source_status();
      if($this->status !== false)
      { $this->source = isset($params["source"]) ? $params["source"] : $this->source($id, true);
        if($this->source !== false)
        { if($this->source["groupes"]) $this->groupes = $this->source["groupes"];
          if(isset($params["groupes"])) $this->groupes = $params["groupes"];
          if(!isset($this->groupes)) $this->groupes = $this->source_groupes($id);
          if($this->groupes !== false)
          { $this->licence = isset($params["licence"]) ? $params["licence"] : $this->licence($this->source["licence"]["id"]);
            if($this->licence !== false)
            { if(($this->sources = $this->source_compositions(array("id_composition" => $id))) !== false)
              { $content = "<source>\n";
                if($this->source["derivations"])
                { foreach($this->source["derivations"] as $id_derivation => $derivation)
                  { $content .= "  <derivation><![CDATA[".$derivation["xml"]["url"]."]]></derivation>\n";
                  }
                }
                if($this->source["reference"]) $content .= "  <reference><![CDATA[".$this->source["reference"]["xml"]["url"]."]]></reference>\n";
                else
                { $content .=
                   "  <titre><![CDATA[".$this->source["titre"]."]]></titre>\n"
                  ."  <from><![CDATA[http://".$_SERVER["SERVER_NAME"].$env->url("sources/".$this->status[$this->source["status"]]["nom"]."/view", array($this->status[$this->source["status"]]["nom"] => $id))."]]></from>\n";
                  $HAS_AUTHOR = false;
                  foreach($this->groupes as $id_groupe => $groupe)
                  { if($groupe["nom"] && $groupe["id_groupe_status"])
                    { if
                      (    $groupe["id_groupe_status"] == $this->id_groupe_status_admin()
                        || $groupe["id_groupe_status"] == $this->id_groupe_status_editeur()
                      )
                      { $content .= "  <auteur><![CDATA[".$groupe["nom"]."]]></auteur>\n";
                        $HAS_AUTHOR = true;
                      }
                    }
                    else return false;
                  }
                  if(!$HAS_AUTHOR) return false;
                  foreach($this->source["documents"] as $id_document => $document)
                  { $content .=
                     "  <document>\n"
                    ."    <nom><![CDATA[".$document["nom"]."]]></nom>\n"
                    ."    <url><![CDATA[".$document["url"]."]]></url>\n"
                    ."  </document>\n";
                  }
                  if($this->licence)
                  { $content .=
                     "  <licence url=\"".$this->licence["url"]."\"><![CDATA[".$this->licence["nom"]."]]></licence>\n";
                  }
                  if(isset($this->sources[$id]))
                  { foreach($this->sources[$id] as $id_source)
                    { $content .= "  <source src=\"".$this->source_xml_url($id_source)."\" />\n";
                    }
                  }
                }
                $content .= "</source>";
              }
            }
          }
        }
      }
      return $content;
    }

    # ----------------------------------------------------------------------------------------
    #                                                                            XML vers data
    #

    function parse_source_xml($xml_content)
    { if(!isset($this->sxml)) $this->sxml = new sxml();
      $this->sxml->parse($xml_content);
      return isset($this->sxml->data["source"][0]);
    }

    function empty_source($params = array())
    { $source = array
      ( "auteurs" => array(),
        "titre" => "",
        "from" => "",
        "licence" => array
        ( "nom" => "",
          "url" => ""
        ),
        "documents" => array(),
        "derivations" => array(),
        "reference" => array(),
        "xml" => array
        ( "url" => "",
          "content" => "",
          "use_edit_content" => false
        )
      );
      foreach($params as $key => $value) $source[$key] = $value;
      return $source;
    }

    function source_xml_read($url, $xml_content = null)
    { $source = $this->empty_source();
      if($this->xml_content = (isset($xml_content) ? $xml_content : $this->get_source_xml_from_url($url)))
      { if($this->xml_content === -1) $this->xml_content = $this->get_source_xml_from_url($url, true);
        $source["xml"] = array
        ( "url" => $url,
          "content" => $this->xml_content,
          "use_edit_content" => false
        );
        if($this->parse_source_xml($this->xml_content))
        { $this->buffer = $this->sxml->data["source"][0];
          foreach($this->buffer["subs"] as $key => $value)
          { if($key == "auteur")
            { $source["auteurs"][] = array
              ( "nom" => $value[0]["data"]
              );
            }
            elseif($key == "document")
            { foreach($value as $id_document => $document)
              { $source["documents"][$id_document] = array
                ( "nom" => $document["subs"]["nom"][0]["data"],
                  "url" => $document["subs"]["url"][0]["data"]
                );
              }
            }
            elseif($key == "derivation")
            { $source["derivations"][] = array
              ( "xml" => array
                ( "url" => $value[0]["data"],
                  "content" => "",
                  "use_edit_content" => false
                )
              );
            }
            elseif($key == "reference")
            { $source["reference"] = array
              ( "xml" => array
                ( "url" => $value[0]["data"],
                  "content" => "",
                  "use_edit_content" => false
                )
              );
            }
            elseif($key == "licence")
            { $source["licence"] = array
              ( "nom" => $value[0]["data"],
                "url" => $value[0]["attrs"]["url"]
              );
            }
            else
            { $source[$key] = $value[0]["data"];
            }
          }
          $source["auteur"] = "";
          foreach($source["auteurs"] as $auteur) $source["auteur"] .= ($source["auteur"] ? ", " : "").$auteur["nom"];
        }
        else return false;
      }
      else return false;
      return $source;
    }

  }

?>
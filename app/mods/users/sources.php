<?php

  class sml_users_sources extends sml_mod
  {

    function validate(&$env)
    { return "Controleur interne - appel interne uniquement";
    }

    function get_source_from_xml(&$env, $xml_url, $xml_content, $xml_use_edit_content)
    { $data = $env->data();
      $source = $data->empty_source();
      $source_xml = array
      ( "url" => $xml_url,
        "content" => $xml_content,
        "use_edit_content" => $xml_use_edit_content
      );
      if($source_xml["url"])
      { if($source_xml["content"])
        { if
          ( ( $source = $data->source_xml_read
              ( $source_xml["url"],
                $source_xml["content"]
              )
            ) === false
          )
          { $source = $data->empty_source();
            $env->message
            ( "Code XML invalide pour :"
             ."<br /><strong>".$source_xml["url"]."</strong>"
            );
          }
        }
        else
        { if($source_xml["content"] === false)
          { $env->message
            ( "Impossible de lire le contenu du fichier XML pour :"
             ."<br /><strong>".$source_xml["url"]."</strong>"
            );
          }
          else
          { $env->message
            ( "Le contenu du fichier XML est vide pour :"
             ."<br /><strong>".$source_xml["url"]."</strong>"
            );
          }
        }
      }
      else
      { $env->message("Merci de pr&eacute;ciser l'ULR du fichier XML");
      }
      $source["xml"] = $source_xml;
      return $source;
    }

  }

?>
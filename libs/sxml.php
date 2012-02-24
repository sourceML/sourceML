<?php

/*

  from :

  http://www.shop24-7.info/32-0-simplexml-alternative-php4.html

  ajout :

-  xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
   dans la fonction parse($data) pour la prise en compte de la casse

-  if($attribs) $this->data['attrs'] = $attribs;
   dans la fonction tag_open pour la prise en compte des attributs

*/

class sxml
{
    var $parser;
    var $error_code;
    var $error_string;
    var $current_line;
    var $current_column;
    var $data;
    var $datas;
    function parse($data)
    {
//        $this->parser = xml_parser_create('UTF-8');
        $this->data = array();
        $this->datas = array();
        $this->parser = xml_parser_create();
        xml_set_object($this->parser, $this);
        xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
        xml_set_element_handler($this->parser, 'tag_open', 'tag_close');
        xml_set_character_data_handler($this->parser, 'cdata');
        if (!xml_parse($this->parser, $data))
        {
            $this->data = array();
            $this->error_code = xml_get_error_code($this->parser);
            $this->error_string = xml_error_string($this->error_code);
            $this->current_line = xml_get_current_line_number($this->parser);
            $this->current_column = xml_get_current_column_number($this->parser);
        }
        else
        {
            $this->data = $this->data['subs'];
        }
        xml_parser_free($this->parser);
    }

    function tag_open($parser, $tag, $attribs)
    {
        $this->datas[] = &$this->data;
        $this->data = &$this->data['subs'][$tag][];
        if($attribs) $this->data['attrs'] = $attribs;

    }

    function cdata($parser, $cdata)
    {
        @$this->data['data'] .= $cdata;
    }

    function tag_close($parser, $tag)
    {
        $this->data =& $this->datas[count($this->datas)-1];
        array_pop($this->datas);
    }
}

?>
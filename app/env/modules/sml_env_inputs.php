<?php

  class sml_env_inputs extends sml_env
  {

    function prepare_inputs()
    { if($_POST)
      { require_once $this->path("libs")."inputfilter.php";
        $allowed_tags = array
        ( "p", "span", "pre", "blockquote", "address", "hr", "br",
          "img",
          "strong", "em", "u", "i", "b", "s",
          "a",
          "ul", "ol", "li",
          "h1", "h2", "h3", "h4", "h5", "h6"
        );
        $allowed_attrs = array
        ( "style",
          "src", "alt", "width", "height",
          "href", "title"
        );
        $input_filter = new InputFilter($allowed_tags, $allowed_attrs);
        $_POST = $input_filter->process($_POST);
      }
      if($_FILES)
      { foreach($_FILES as $file_key => $file_infos)
        { $v_name = explode(".", $file_infos["name"]);
          $ext = strtolower($v_name[count($v_name) - 1]);
          if
          (    $ext != "png"
            && $ext != "jpg"
            && $ext != "jpeg"
            && $ext != "gif"
          ) unset($_FILES[$file_key]);
        }
      }
      return true;
    }

  }

?>
<?php

  if(get_magic_quotes_gpc())
  { $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while(list($key, $val) = each($process))
    { foreach($val as $k => $v)
      { unset($process[$key][$k]);
        if(is_array($v))
        { $process[$key][stripslashes($k)] = $v;
          $process[] = &$process[$key][stripslashes($k)];
        }
        else $process[$key][stripslashes($k)] = stripslashes($v);
      }
    }
    unset($process);
  }



  /*
   *
   * decommentez la fin du fichier pour activer le filtrage
   * des inputs (ici POST et FILES)
   *

  if($_POST)
  { require $this->path("libs")."inputfilter.php";
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

  */

?>
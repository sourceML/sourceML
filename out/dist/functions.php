<?php

  function sml_navig($current, $total, $max, $base_url, $start_param, $legende = null)
  { $navig = "";
    if($max && $total > $max)
    { if(isset($legende))
      { $navig .=
         $legende." ".($current + 1)." &agrave; "
        .(($current + $max) > $total ? $total : $current + $max)
        ." sur ".$total." - aller &agrave; la ";
      }
      $get_params = array();
      if(($q = strpos($base_url, "?")) !== false)
      { $v_query = explode("&", substr($base_url, $q + 1));
        $base_url = substr($base_url, 0, $q);
        foreach($v_query as $query)
        { if($query)
          { $v = explode("=", $query);
            $get_params[$v[0]] = $v[1];
          }
        }
      }
      if(isset($get_params[$start_param])) unset($get_params[$start_param]);
      $base_url .= "?";
      foreach($get_params as $key => $value) $base_url .= $key."=".$value."&";
      $nb_pages = ceil($total / $max);
      $navig .=
       "page : "
      ."<select onchange=\"document.location=this.options[this.selectedIndex].value;\">\n";
      $np = 1;
      $ni = 0;
      while($ni < $total)
      { $navig .=
         "  <option value=\"".$base_url.$start_param."=".$ni."\"".($current >= $ni && $current <= $ni ? " SELECTED" : "").">"
        .$np
        ."</option>\n";
        $np++;
        $ni += $max;
      }
      $navig .=
       "</select>\n";
      if($current >= $max)
      { $navig .=
          "<a href=\"".$base_url.$start_param."=".($current - $max)."\">&laquo;</a>\n";
      }
      if($current < $total - $max)
      { $navig .=
          "<a href=\"".$base_url.$start_param."=".($current + $max)."\">&raquo;</a>\n";
      }
    }
    return $navig;
  }

  function aff_date($date)
  { if(preg_match("/([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})/", $date, $regs))
    { $date = $regs[3]." ".mois($regs[2])." ".$regs[1];
    }
    return $date;
  }

  function mois($n)
  { switch($n)
    { case 1:  $mois = "jan"; break;
      case 2:  $mois = "fev"; break;
      case 3:  $mois = "mars"; break;
      case 4:  $mois = "avr"; break;
      case 5:  $mois = "mai"; break;
      case 6:  $mois = "juin"; break;
      case 7:  $mois = "juil"; break;
      case 8:  $mois = "aout"; break;
      case 9:  $mois = "sept"; break;
      case 10: $mois = "oct"; break;
      case 11: $mois = "nov"; break;
      case 12: $mois = "dec"; break;
      default: $mois = $n;
    }
    return $mois;
  }

?>
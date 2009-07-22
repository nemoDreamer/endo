<?php

function smarty_function_link($params=array(), &$smarty)
{
  require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

  $output='';
  $text='';
  $controller='';
  $action='';
  $set_gets=false;
  $extra='';

  foreach ($params as $_key => $_value) {
    switch ($_key) {
      case 'text':
      case 'controller':
      case 'action':
      case 'href':
      case 'set_gets':
        $$_key = $_value;
        break;

      case 'confirm':
        $extra .= ' onclick="return confirm(\''.str_replace_js($_value).'\');"';
        break;

      default:
        if(!is_array($_value)) {
            $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_value).'"';
        } else {
            $smarty->trigger_error("link: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
        }
        break;
    }
  }

  if (empty($text)) {
    $smarty->trigger_error("link: missing 'text' parameter", E_USER_NOTICE);
    return;
  }

  if (empty($href)) {
    if (empty($controller)) {
      $smarty->trigger_error("link: missing 'controller' parameter", E_USER_NOTICE);
      return;
    }

    $output .= '<a href="'.DS.$controller;

    if (!empty($action)) {
      $output .= DS.$action;
    }
  } else {
    $output .= '<a href="';
    if (preg_match('/.*@.*\..{2,5}/', $href)) {
      $output .= 'mailto:';
    }
    $output .= $href;
  }


  if ($set_gets!=false) {
    require_once $smarty->_get_plugin_filepath('function','set_gets');
    $output .= smarty_function_set_gets(is_bool($set_gets) ? array() : array('vars' => $set_gets), $smarty);
  }

  $output .= '"'.$extra.'>'.$text.'</a>';

  return $output;

}

?>
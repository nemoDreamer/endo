<?php

function smarty_function_set_gets($params=array(), &$smarty)
{
  require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

  $output='';
  $vars = 'filter';
  $extra='';

  foreach ($params as $_key => $_value) {
    switch ($_key) {
      case 'vars':
        $vars = $vars.','.$_value;
        break;
    }
  }

  $vars = explode(',', $vars);

  foreach ($vars as $_key) {
    $_value = array_get($smarty->_tpl_vars, $_key, false);
    switch ($_key) {
      case 'filter':
        if ($_value->value!=false) {
          $output .= $_key."=".(($_value->short) ? $_value->value : $_value->where);
        }
        break;
      default:
        $output .= $_key."=".smarty_function_escape_special_chars($_value);
        break;
    }
    $output .= "&"; // add separator
  }

  $output = preg_replace('/&$/U', '', $output); // remove trailing separator

  if (strlen($output)>0) {
    $output = '?'.$output; // build output
  }

  return $output;

}

?>
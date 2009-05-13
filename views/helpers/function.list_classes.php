<?php

function smarty_function_list_classes($params, &$smarty)
{
  require_once $smarty->_get_plugin_filepath('function','cycle');

  $output = '';

  // get name
  if (!array_key_exists('name', $params) || empty($params['name'])) {
      $smarty->trigger_error("list_classes: missing 'name' parameter in '{$smarty->_smarty_debug_info[0]['filename']}'", E_USER_ERROR);
      return;
  } else {
    $name = $params['name'];
  }

  // get loop type
  if (!array_key_exists('type', $params)) {
    $type = 'foreach';
  } else {
    $type = $params['type'];
  }

  // odd/even
  $output .= smarty_function_cycle(array_merge($params, array('values' => 'odd,even')), &$smarty);

  // first/last
  $loop = $smarty->_foreach[$name];
  if ($loop['iteration']==1) {
    $output .= ' first';
  } elseif($loop['iteration']==$loop['total']) {
    $output .= ' last';
  }

  return $output;
}

?>
<?php

function smarty_function_admin_link($params=array(), &$smarty)
{
  require_once $smarty->_get_plugin_filepath('function','link');

  if (array_key_exists('controller', $params)) {
    $params['controller'] = ADMIN_ROUTE.DS.$params['controller'];
  } else {
    $smarty->trigger_error("admin_link: missing 'text' parameter", E_USER_NOTICE);
    return;
  }

  return smarty_function_link($params, $smarty);
}

?>
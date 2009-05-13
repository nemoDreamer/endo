<?php
function smarty_function_endo_errors($params=array(), &$smarty)
{
  if (!array_key_exists('key', $params)) {
    $params['key'] = 'default';
  }
  if (!array_key_exists('layout', $params)) {
    $params['layout'] = 'default';
  }

  return Error::output($params['key'], $params['layout']);
}
?>
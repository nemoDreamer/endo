<?php
function smarty_function_errors($params=array(), &$smarty)
{
  if (!array_key_exists('key', $params)) {
    $params['key'] = 'default';
  }
  if (!array_key_exists('layout', $params)) {
    $params['layout'] = 'default';
  }

  return Error::Output($params['key'], $params['layout']);
}
?>
<?php

function smarty_function_header($params, &$smarty)
{
  if (isset($params['name']) && isset($params['value'])) {
    header("{$params['name']}: {$params['value']};");
  } else {
    return false;
  }
}

?>
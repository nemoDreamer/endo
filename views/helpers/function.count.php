<?php

function smarty_function_count($params, &$smarty)
{
  return count(array_get($params, 'array', array()));
}

?>
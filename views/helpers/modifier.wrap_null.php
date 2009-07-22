<?php

function smarty_modifier_wrap_null ($string, $before='', $after='')
{
  if (!isset($string) || !$string)
    return '';
  else
    return $before.$string.$after;
}

?>
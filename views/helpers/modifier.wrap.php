<?php

function smarty_modifier_wrap ($string, $before='', $after='')
{
  if (!isset($string) || $string === '')
      return $string;
  else
      return $before.$string.$after;
}

?>
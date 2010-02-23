<?php

function smarty_modifier_inflect($string, $method='pluralize')
{
  return AppInflector::$method($string);
}

?>
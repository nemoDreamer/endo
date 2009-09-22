<?php

function smarty_modifier_humanize($string)
{
  return ucwords(preg_replace('/[_-]/', ' ', $string));
}

?>
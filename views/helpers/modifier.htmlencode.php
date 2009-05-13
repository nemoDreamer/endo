<?php

function smarty_modifier_htmlencode($string)
{
  if(!is_array($string)) {
    $string = htmlentities($string, ENT_QUOTES, NULL, false);
  }
  return $string;
}

?>
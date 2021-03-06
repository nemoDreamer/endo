<?php

function smarty_modifier_textile ($string, $lite=false, $restricted=false)
{
  if (!isset($string) || $string === '') {
    return $string;
  } else {
    Globe::Load('Textile/library/Vendor/Textile', 'package');
    $textile = new Textile();
    return $restricted ? $textile->TextileRestricted($string, $lite) : $textile->TextileThis($string, $lite);
  }
}

?>
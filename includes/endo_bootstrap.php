<?php

/**
 * EndoBootstrap
 * Globally used functions and stuff...
 *
 * @author Philip Blyth
 */

function str_replace_js($string='')
{
  $search = array(
    '\'',
    "\n"
  );
  $replace = array(
    "\'",
    '\n'
  );

  return str_replace($search, $replace, $string);
}

?>
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

function array_get($array, $key, $default=null)
{
  return array_key_exists($key, $array) ? $array[$key] : $default;
}

?>
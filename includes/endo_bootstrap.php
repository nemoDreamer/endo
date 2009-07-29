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

function array_get(&$array, $key, $default=null)
{
  return array_key_exists($key, $array) ? $array[$key] : $default;
}

function array_extract(&$array, $keys)
{
  $output = array();
  foreach ($keys as $key) {
    $output[$key] = array_get($array, $key);
  }
  return $output;
}

function to_array($obj)
{
  $output = array();
  foreach ($obj as $key => $value) {
    $output[$key] = $value;
  }
  return $output;
}

// --------------------------------------------------
// SORTING CALLBACKS
// --------------------------------------------------

function sort_name($a, $b) {
  return strnatcasecmp($a->name, $b->name);
}

?>
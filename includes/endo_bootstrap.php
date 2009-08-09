<?php

/**
 * EndoBootstrap
 * Globally used functions and stuff...
 *
 * @author Philip Blyth
 */

// --------------------------------------------------
// STRING
// --------------------------------------------------

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

function camelcase($str='')
{
  return str_replace(' ', '', titlecase(preg_replace('/\W/', ' ', $str)));
}

function titlecase($str='')
{
  return ucwords((string) $str);
}

function wrap($string, $before='', $after='', $default=false)
{
  $string = trim($string);
  if (($no=$string!='') || $default!=false) {
    return $before.($no ? $string : $default).$after;
  } else {
    return '';
  }
}

// --------------------------------------------------
// ARRAY
// --------------------------------------------------

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

function add_all($array)
{
  $tmp = array(0 => 'All');
  foreach ($array as $key => $value) {
    $tmp[$key] = $value;
  }
  return $tmp;
}

// --------------------------------------------------
// SORTING CALLBACKS
// --------------------------------------------------

function sort_name($a, $b) {
  return strnatcasecmp($a->name, $b->name);
}

?>
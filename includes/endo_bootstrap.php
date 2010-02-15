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

function array_get(&$array, $key, $default=null, $allow_false=true)
{
  return (!is_array($array) || !array_key_exists($key, $array) || (!$allow_false && !$array[$key])) ? $default : $array[$key];
}

function array_extract(&$array, $keys, $unset=false)
{
  if (is_string($keys)) {
    $keys = array($keys);
  }
  $output = array();
  foreach ($keys as $key) {
    $output[$key] = is_array($array) ? array_get($array, $key) : object_get($array, $key);
    if ($unset) {
      unset($array[$key]);
    }
  }
  return $output;
}

function add_all($array, $label='All')
{
  $tmp = array(0 => $label);
  foreach ($array as $key => $value) {
    $tmp[$key] = $value;
  }
  return $tmp;
}

function array_wrap($array, $before=null, $after=null)
{
  foreach ($array as $key => $value) {
    $array[$key] = $before.$value.$after;
  }
  return $array;
}

function random_get($array, $elements=1)
{
  $output = array();
  for ($i=0; $i < $elements; $i++) {
    if (empty($array)) break;
    $key = array_rand($array);
    $output[$key] = $array[$key];
    unset($array[$key]);
  }
  return $output;
}

function array_empty($array)
{
  return count($array)===0 || (count($array)==1 && $array[0]==='');
}

// --------------------------------------------------
// COLLECTION
// --------------------------------------------------

/*
 * use to format array for html_options helper
 */
function to_array($obj)
{
  $output = array();
  foreach ($obj as $key => $value) {
    $output[$key] = $value;
  }
  return $output;
}

define('COLLECTION_INDEX_NO_GROUP', '__ungroupable__');

function collection_group($array, $group_bys)
{
  if (is_array($group_bys)) {
    $group_by = array_shift($group_bys);
  } else {
    $group_by = (string) $group_bys;
    $group_bys = null;
  }

  $tmp = array();
  foreach ($array as $index => $row) {
    $group_name = !isset($row->$group_by) ? COLLECTION_INDEX_NO_GROUP : $row->$group_by;
    if (!array_key_exists($group_name, $tmp)) {
      $tmp[$group_name] = array();
    }
    $tmp[$group_name][$index] = $row;
  }

  if (is_array($group_bys) && !empty($group_bys)) {
    foreach ($tmp as $key => $value) {
      $tmp[$key] = collection_group($value, $group_bys);
    }
  }

  return $tmp;
}

function collection_reindex($array=array(), $index='id')
{
  $tmp = array();
  foreach ($array as $key => $value) {
    if (!is_array($value)) {
      $value = (array)$value;
    }
    $tmp[$value[$index]] = $value;
  }
  return $tmp;
}

function build_options($array, $null=false)
{
  foreach ($array as $key => $value) {
    if (is_numeric($key) && is_string($value)) {
      unset($array[$key]);
      $array[htmlentities($value)] = $null ? null : $value;
    }
  }
  return $array;
}

function collection_slice($array, $id, $offset, $length, $preserve_keys=false)
{
  // prepare
  $array_keys = array_keys($array);
  $array_keys = array_pad($array_keys, -(abs($offset)+count($array_keys)), null); // left pad array to allow too small/large offset
  $array_keys = array_pad($array_keys, abs($offset)+count($array_keys), null); // right pad
  $id_as_index = array_search($id, $array_keys);
  $array_slice = array_slice($array_keys, $id_as_index + $offset, $length);
  // replicate
  $output = array();
  for ($i=0; $i < $length; $i++) {
    $index = $preserve_keys ? $array_slice[$i] : $i;
    $output[(string)$index] = array_get($array, $array_slice[$i]);
  }
  return $output;
}

function collection_extract($array, $keys)
{
  $output = array();
  foreach ($array as $key => $value) {
    $output[$key] = array_extract($value, $keys);
  }
  return $output;
}

// --------------------------------------------------
// OBJECT
// --------------------------------------------------

function object_get(&$obj, $key, $default=null, $allow_false=true)
{
  return (!is_object($obj) || !isset($obj->$key) || (!$allow_false && !$obj->$key)) ? $default : $obj->$key;
}

// --------------------------------------------------
// VARIABLES
// --------------------------------------------------

function get_default(&$variable, $default=null)
{
  if (isset($variable)) {
    return !$variable ? $default : $variable;
  } else {
    return $default;
  }
}

function dual(&$variable, $true, $false)
{
  if ($variable!==$true) {
    $variable = $false;
    return false;
  } else {
    return true;
  }
}

// --------------------------------------------------
// SORTING CALLBACKS
// --------------------------------------------------

function sort_name($a, $b) {
  return strnatcasecmp($a->name, $b->name);
}

?>
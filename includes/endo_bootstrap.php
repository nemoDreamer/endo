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

function wrap($string, $before='', $after='', $default=false)
{
  $string = trim($string);
  if (($no=$string!='') || $default!=false) {
    return $before.($no ? $string : $default).$after;
  } else {
    return '';
  }
}

function fancyize($parts)
{
  $output = '';
  $i = 0; // don't work w/ key, because you never know if you're passed a numeric array
  foreach ($parts as $part) {
    $output .= wrap($part, ($i!=1?' ':'').'<span class="part part_'.$i.'">', '</span> ');
    $i++;
  }
  return $output;
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

function build_options($collection, $null=false)
{
  foreach ($collection as $key => $value) {
    if (is_numeric($key) && is_string($value)) {
      unset($collection[$key]);
      $collection[htmlentities($value)] = $null ? null : $value;
    }
  }
  return $collection;
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

// --------------------------------------------------
// FILESYSTEM
// use with caution...
// --------------------------------------------------

function emptyResource($path)
{
  if (removeResource($path, false)) {
    createEmptyFile($path);
  }
}

function removeResource( $_target, $remove_last=true ) {

  if (!file_exists($_target)) {
    return false;
  }

  //file?
  if( is_file($_target) ) {
    if( is_writable($_target) ) {
      if( @unlink($_target) ) {
        return true;
      }
    }
  }

  //dir?
  if( is_dir($_target) ) {
    if( is_writeable($_target) ) {
      foreach( new DirectoryIterator($_target) as $_res ) {
        if( $_res->isDot() ) {
          unset($_res);
          continue;
        }

        if( $_res->isFile() ) {
          removeResource( $_res->getPathName() );
        } elseif( $_res->isDir() ) {
          removeResource( $_res->getRealPath() );
        }

        unset($_res);
      }

      if ( $remove_last ) {
        if( @rmdir($_target) ) {
          return true;
        }
      } else {
        return true;
      }
    }
  }
  return false;
}

function createEmptyFile($path, $name='empty')
{
  if (substr($path, -1)!=DS) {
    $path .= DS;
  }
  $handle = fopen($path.$name, 'w');
  fclose($handle);
}

?>
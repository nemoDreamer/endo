<?php

/**
 * GetSetter
 * Framework for Predefined Global Variables
 *
 * @package default
 * @author Philip Blyth
 */
class GetSetter {

  static function get(&$source, $variable, $default=null)
  {
    return array_get($source, $variable, $default);
  }

  static function set(&$source, $variable, $data=null)
  {
    return $source[$variable] = $data;
  }

  static function update(&$source, $variable, $data=array(), $unique=false)
  {
    if (!is_array($data)) {
      $data = array($data);
    }
    if (!is_array($tmp=self::get($source, $variable))) {
      $tmp = $tmp==null ? array() : array($tmp);
    }
    $tmp = array_merge($tmp, $data);
    if ($unique) {
      $tmp = array_unique($tmp);
    }
    return self::set($source, $variable, $tmp);
  }

  static function remove(&$source, $variable, $data=array())
  {
    if (!is_array($data)) {
      $data = array($data);
    }
    foreach ($data as $value) {
      if (array_key_exists($value, $source[$variable])) {
        unset($source[$variable][$value]);
      }
    }
  }

}

?>
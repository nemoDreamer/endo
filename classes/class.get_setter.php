<?php

/**
 * GetSetter
 * Framework for Predefined Global Variables
 *
 * @package default
 * @author Philip Blyth
 */
class GetSetter {

  static function Get(&$source, $variable, $default=null)
  {
    return array_get($source, $variable, $default);
  }

  static function Set(&$source, $variable, $data=null)
  {
    return $source[$variable] = $data;
  }

  static function Update(&$source, $variable, $data=array(), $unique=false)
  {
    if (!is_array($data)) {
      $data = array($data);
    }
    if (!is_array($tmp=self::Get($source, $variable))) {
      $tmp = $tmp==null ? array() : array($tmp);
    }
    $tmp = array_merge($tmp, $data);
    if ($unique) {
      $tmp = array_unique($tmp);
    }
    return self::Set($source, $variable, $tmp);
  }

  static function Remove(&$source, $variable, $data=array())
  {
    if (!is_array($data)) {
      $data = array($data);
    }
    foreach ($data as $key => $value) {
      $value = is_numeric($key) ? $value : $key;
      if (array_key_exists($value, $source[$variable])) {
        unset($source[$variable][$value]);
      }
    }
  }

}

?>
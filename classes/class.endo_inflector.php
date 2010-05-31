<?php

require_once(ENDO_PACKAGES_ROOT.'AkelosInflector/Inflector.php'); // can't use Globe::Load, since loaded before Globe...

class EndoInflector extends Inflector {

  static $pluralize_exceptions = array(
    'missing' => 'missing', // keep this for internal
    'execute' => 'execute'  // keep this for internal
  );

  static function GetPluralizeExceptions()
  {
    return array_merge(EndoInflector::$pluralize_exceptions, AppInflector::$pluralize_exceptions);
  }

  // --------------------------------------------------
  // PLURALIZE / SINGULARIZE
  // --------------------------------------------------

  static function pluralize($str='')
  {
    $str = strtolower($str);
    if (array_key_exists($str, $tmp=self::GetPluralizeExceptions())) {
      return $tmp[$str];
    } else {
      return parent::pluralize($str);
    }
  }

  static function singularize($str='')
  {
    $str = strtolower($str);
    if (array_key_exists($str, $tmp=array_flip(self::GetPluralizeExceptions()))) {
      return $tmp[$str];
    } else {
      return parent::singularize($str);
    }
  }

  // --------------------------------------------------
  // ENDO-SPECIFIC
  // --------------------------------------------------

  static function classify($name='', $type='class')
  {
    $type = strtolower($type);
    $name = AppInflector::underscore($name);

    switch ($type) {
      case 'model':
        $name = AppInflector::singularize($name);
        break;
      case 'controller':
        $name = AppInflector::pluralize(AppInflector::singularize($name));
        if (!preg_match('/Controller$/i', $name)) {
          $name .= 'Controller';
        }
        break;
    }

    return AppInflector::camelize($name);
  }

  static function fileize($name='', $type='class')
  {
    $type = strtolower($type);
    $name = AppInflector::underscore($name);

    switch ($type) {
      case 'controller':
        $name = AppInflector::pluralize(AppInflector::singularize(preg_replace('/_?Controller$/i', '', $name)));
        break;
    }
    return $name;
  }

  static function tableize($class_name)
  {
    return strtolower(AppInflector::camelize(AppInflector::singularize($class_name)));
  }

}

?>
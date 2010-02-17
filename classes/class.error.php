<?php

class Error
{
  static $errors = array();

  static function Set($message='', $key='default')
  {
    if (!array_key_exists($key, self::$errors)) {
      self::$errors[$key] = array();
    }

    array_push(self::$errors[$key], $message);
  }

  static function Output($key='default', $layout='default')
  {
    $View = new AppView();

    if (array_key_exists($key, self::$errors)) {

      $output = '';

      foreach (self::$errors[$key] as $index => $message) {
        $View->assign('key', $key);
        $View->assign('index', $index);
        $View->assign('message', $message);
        $output .= $View->fetch(Globe::GetTemplate($layout, 'errors', Url::$data['type']))."\n";
      }

      unset(self::$errors[$key]);

      return $output;
    } else {
      return false;
    }
  }

  static function HasErrors()
  {
    return count(self::$errors)!=0;
  }

  static function IsFatal()
  {
    return self::Is('fatal');
  }

  static function Is($key)
  {
    return array_key_exists($key, self::$errors);
  }
}

?>
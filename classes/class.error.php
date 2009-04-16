<?php

class Error
{
  static $errors = array();

  static function set($message='', $key='default')
  {
    if (!array_key_exists($key, Error::$errors)) {
      Error::$errors[$key] = array();
    }

    array_push(Error::$errors[$key], $message);
  }

  static function output($key='default', $layout='default')
  {
    $View = new AppView();

    if (array_key_exists($key, Error::$errors)) {

      $output = '';

      foreach (Error::$errors[$key] as $index => $message) {
        $View->assign('key', $key);
        $View->assign('index', $index);
        $View->assign('message', $message);
        $output .= $View->fetch(Globe::get_template($layout, 'errors', Url::$data['type']));
      }

      unset(Error::$errors[$key]);

      return $output;
    } else {
      return false;
    }
  }

  static function has_errors()
  {
    return count(Error::$errors)!=0;
  }
}

?>
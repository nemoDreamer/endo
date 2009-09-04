<?php

/**
 * Url
 * Handles parsing or the url string.
 *
 * @package default
 * @author Philip Blyth
 */
class Url {

  static $data=array(
    '_url' => null,
    'url' => null,
    'model' => null,
    'modelName' => null,
    'controller' => null,
    'controllerName' => null,
    'action' => null,
    'type' => DEFAULT_REQUEST_TYPE,
    'params' => array(),
    'is_admin' => false,
    'is_subdomain' => false
  );

  static function parse($url)
  {
    // save original
    Url::$data['_url'] = Url::$data['url'] = preg_replace('/\/$/', '', $url);

    // get extension
    preg_match('/(.*)\.(\w{1,4})$/Ui', Url::$data['url'], $matches);
    if (!empty($matches)) {
      Url::$data['url'] = $matches[1];
      Url::$data['type'] = $matches[2];
    }

    // get parts
    $parts = explode(DS, Url::$data['url']);

    // is subdomain?
    // - because of tilde
    if (substr($parts[0],0,1)==SUBDOMAIN_PREFIX) {
      Url::_set_subdomain(substr(array_shift($parts),1));
    }
    // - because of domain
    elseif (count($host_parts=explode('.', $_SERVER['HTTP_HOST'])) > 2) {
      Url::_set_subdomain(array_shift($host_parts));
    }
    // - anything left?
    if (Url::$data['is_subdomain'] && count($parts)===0) {
      $parts = explode(DS, SUBDOMAIN_DEFAULT_URL);
    }

    // is admin because of path?
    if ($parts && $parts[0]==ADMIN_ROUTE) {
      // set is_admin
      Url::$data['is_admin'] = true;
      array_shift($parts); // dump into nothingness...
      // anything left?
      if (count($parts)===0) {
        $parts = array(ADMIN_DEFAULT_CONTROLLER, ADMIN_DEFAULT_ACTION);
      }
    }

    // is execute?
    if (Url::$data['type']=='php') {
      Url::$data['model'] = 'app';
      Url::$data['modelName'] = 'AppModel';
      Url::$data['controller'] = EXECUTE_CONTROLLER;
      Url::$data['controllerName'] = ucfirst(EXECUTE_CONTROLLER).'Controller';
      Url::$data['action'] = '_include_to_buffer';
      // set filename
      Url::$data['params'] = array(
        'filename' => implode(DS, $parts).'.php'
      );
    } else {

      // set controller
      Url::$data['controller'] = array_shift($parts);
      Url::$data['controllerName'] = Globe::make_class_name(Url::$data['controller'], 'controller');

      // set model
      Url::$data['model'] = Globe::singularize(Url::$data['controller']);
      Url::$data['modelName'] = ucfirst(Url::$data['model']);

      // set action
      Url::$data['action'] = ($action=array_shift($parts)) != null ? $action : 'index';

      // is admin because of action?
      if (strpos($action, ADMIN_PREFIX)===0) {
        // set is_admin
        Url::$data['is_admin'] = true;
      } elseif(Url::$data['is_admin']) {
        // add prefix to admin actions
        Url::$data['action'] = ADMIN_PREFIX.Url::$data['action'];
      }

      // set params
      while (($value=array_shift($parts))!=null) {
        Url::$data['params'][] = $value;
      }
    }

    // set request
    Url::$data['request'] = $_REQUEST;

  }

  ##
  # get data
  #
  static function data($data, $variable, $default=null)
  {
    return array_get(array_get(Url::$data, $data, array()), $variable, $default);
  }

  ##
  # get param
  #
  static function param($variable, $default=null)
  {
    return Url::data('params', $variable, $default);
  }

  ##
  # get request
  #
  static function request($variable, $default=null)
  {
    return Url::data('request', $variable, $default);
  }

  static function _set_subdomain($subdomain)
  {
    Url::$data['is_subdomain'] = true;
    Url::$data['subdomain'] = $subdomain;
    $host_parts = explode('.', $_SERVER['HTTP_HOST']);
    if ($host_parts[0] != $subdomain) {
      array_unshift($host_parts, $subdomain);
    }
    Url::$data['host'] = 'http'.(array_get($_SERVER, 'HTTPS') ? 's' : '').'://'.implode('.', $host_parts);
  }

}

?>
<?php

/**
 * Url
 * Handles parsing or the url string.
 *
 * @package default
 * @author Philip Blyth
 */
class Url {

  // --------------------------------------------------
  // DEFAULTS
  // --------------------------------------------------

  static $data = array(
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

  // --------------------------------------------------
  // ROUTES
  // --------------------------------------------------

  static $routes = array(
    // tilde
    '~(.*)/(.*)' => array(
      'replace' => '$2',
      'is_subdomain' => true,
      'subdomain' => '$1',
      'continue' => true
    ),
    // login
    'login(.*)' => array(
      'replace' => 'users/login'
    ),
    // logout
    'logout(.*)' => array(
      'replace' => 'users/logout'
    )
  );

  // --------------------------------------------------
  // PARSE
  // --------------------------------------------------

  static function Parse($url)
  {
    // save 'original'
    Url::$data['_url'] = $url;

    // send through routes
    Url::$data['url'] = Url::DoRoutes($url);

    // get extension
    preg_match('/(.*)\.(\w{1,4})$/Ui', Url::$data['url'], $matches);
    if (!empty($matches)) {
      Url::$data['url'] = $matches[1];
      Url::$data['type'] = $matches[2];
    }

    // get parts
    $parts = explode(DS, Url::$data['url']);

    // is subdomain?
    // if (count($host_parts=explode('.', $_SERVER['HTTP_HOST'])) > 2 && $host_parts[0]!='www') {
    if (($subdomain=Url::GetSubdomain()) != null) {
      Url::$data['is_subdomain'] = true;
      Url::$data['subdomain'] = $subdomain;
    }
    Url::$data['host'] = 'http'.(array_get($_SERVER, 'HTTPS') ? 's' : '').'://'.wrap($subdomain,'','.').DOMAIN;

    // d($url);d_arr($parts);d_arr(Url::$data);die;

    // nothing left?
    if (array_empty($parts)) {
      if (Url::GetData('is_admin')) {
        // is admin
        $parts = array(ADMIN_DEFAULT_CONTROLLER, ADMIN_DEFAULT_ACTION);
      } else{
        // is default
        $parts = explode(DS, Url::GetData('is_subdomain') ? SUBDOMAIN_DEFAULT_URL : DEFAULT_URL);
      }
    }

    // is execute?
    if (Url::$data['type']=='php') {
      Url::$data['model'] = 'app';
      Url::$data['modelName'] = 'AppModel';
      Url::$data['controller'] = EXECUTE_CONTROLLER;
      Url::$data['controllerName'] = ucfirst(EXECUTE_CONTROLLER).'Controller';
      Url::$data['action'] = 'include_to_buffer';
      // set filename
      Url::$data['params'] = array(
        'filename' => implode(DS, $parts).'.php'
      );
    } else {

      // set controller
      Url::$data['controller'] = array_shift($parts);
      Url::$data['controllerName'] = AppInflector::classify(Url::$data['controller'], 'controller');

      // set model
      Url::$data['model'] = AppInflector::singularize(Url::$data['controller']);
      Url::$data['modelName'] = AppInflector::classify(Url::$data['model'], 'model');
      // set action
      Url::$data['action'] = ($action=array_shift($parts)) != null ? $action : 'index';

      // add prefix to admin actions
      if(Url::GetData('is_admin')) {
        Url::$data['action'] = ADMIN_PREFIX.Url::$data['action'];
      }

      // set params
      while (($value=array_shift($parts))!=null) {
        Url::$data['params'][] = $value;
      }
    }

    // set request
    Url::$data['request'] = $_REQUEST;

    // set domain
    Url::$data['domain'] = DOMAIN;

    // d_arr(Url::$data);die;

  }

  // --------------------------------------------------
  // ROUTES
  // --------------------------------------------------

  static function DoRoutes($url)
  {
    $routes = Url::GetRoutes();

    // add slash
    $url = preg_replace('/([^\/])$/', '${1}/', $url);

    foreach ($routes as $pattern => $data) {
      $pattern = "%^$pattern$%Uis"; // add pattern defaults & flags
      if (preg_match($pattern, $url)!==0) {
        // redirect?
        if ($redirect = array_get($data, 'redirect')) {
          header('Location: '.str_replace('DOMAIN', DOMAIN, $redirect));
          die();
        }
        // params
        $params = array_extract($data, array('replace', 'continue'), true);
        $url = preg_replace($pattern, $params['replace'], $url);
        Url::$data = array_merge(Url::$data, $data);
        // break?
        if (!$params['continue']) break;
      }
    }
    // d($url);d_arr(Url::$data);die;

    // clean and return
    return preg_replace('/\/$/', '', strtolower($url));
  }

  static function GetRoutes()
  {
    $include = array();
    // admin route
    $include[ADMIN_ROUTE.'/(.*)'] = array(
      'replace' => '$1',
      'is_admin' => true,
      'continue' => true
    );
    return array_merge($include, AppUrl::$routes, Url::$routes);
  }

  // --------------------------------------------------
  // HELPERS
  // --------------------------------------------------

  static function GetData($variable, $default=null, $allow_false=true)
  {
    return array_get(Url::$data, $variable, $default, $allow_false);
  }

  static function GetDataArray($variable, $key, $default=null)
  {
    $tmp = array_get(Url::$data, $variable, array()); // fix 'only variables can be passed by reference' bug...
    return array_get($tmp, $key, $default);
  }

  static function GetParam($variable, $default=null) { return Url::GetDataArray('params', $variable, $default); }
  static function GetRequest($variable, $default=null) { return Url::GetDataArray('request', $variable, $default); }

  static function HasData($variable)
  {
    return array_key_exists($variable, Url::$data);
  }

  static function HasDataArray($variable, $key)
  {
    return array_key_exists($key, array_get(Url::$data, $variable, array()));
  }

  static function HasParam($variable) { return Url::HasDataArray('params', $variable); }
  static function HasRequest($variable) { return Url::HasDataArray('request', $variable); }

  static function GetSubdomain()
  {
    preg_match_all('/^(?:www\.)?(.+)(?:\:\d+)?/i', $_SERVER['HTTP_HOST'], $matches, PREG_SET_ORDER);
    return substr(str_replace(DOMAIN, '', $matches[0][1]), 0, -1);
  }

}

?>
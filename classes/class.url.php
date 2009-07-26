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

    // is execute?
    if (Url::$data['type']=='php') {
      Url::$data['model'] = 'app';
      Url::$data['modelName'] = 'AppModel';
      Url::$data['controller'] = EXECUTE_CONTROLLER;
      Url::$data['controllerName'] = ucfirst(EXECUTE_CONTROLLER).'Controller';
      Url::$data['action'] = '_include_to_buffer';

      // is subdomain?
      if (substr($tmp_url=Url::$data['url'],0,1)==SUBDOMAIN_PREFIX) {
        // set subdomain
        $tmp_pos = !($tmp_pos = strpos($tmp_url, DS)) ? -1 : $tmp_pos;
        Url::_set_subdomain(substr($tmp_url,1,$tmp_pos-strlen(DS))); // get from after SUBDOMAIN_PREFIX to before first DS
        // continue w/ rest of url
        $tmp_url = substr($tmp_url,$tmp_pos+strlen(DS));
      }

      // is admin?
      if (strpos($tmp_url, ADMIN_ROUTE)===0) {
        // set is_admin
        Url::$data['is_admin'] = true;
        // remove route
        $tmp_url = preg_replace('%^'.ADMIN_ROUTE.DS.'%U', '', $tmp_url);
      }

      // set filename
      Url::$data['params'] = array(
        'filename' => $tmp_url.'.php'
      );
    } else {
      // get parts
      $parts = explode(DS, Url::$data['url']);

      // is subdomain?
      if (substr($parts[0],0,1)==SUBDOMAIN_PREFIX) {
        // set subdomain
        Url::_set_subdomain(substr(array_shift($parts),1)); // save & dump into nothingness...
        // anything left?
        if (count($parts)===0) {
          $parts = explode(DS, SUBDOMAIN_DEFAULT_URL);
        }
      }

      // is admin because of path?
      if ($parts[0]==ADMIN_ROUTE) {
        // set is_admin
        Url::$data['is_admin'] = true;
        array_shift($parts); // dump into nothingness...
        // anything left?
        if (count($parts)===0) {
          $parts = array(ADMIN_DEFAULT_CONTROLLER, ADMIN_DEFAULT_ACTION);
        }
      }

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

  }

  ##
  # get param
  #
  static function param($variable, $default=null)
  {
    return array_get(Url::$data['params'], $variable, $default);
  }

  static function _set_subdomain($subdomain)
  {
    Url::$data['is_subdomain'] = true;
    Url::$data['subdomain'] = $subdomain;
    Url::$data['_subdomain'] = DS.SUBDOMAIN_PREFIX.$subdomain;
  }

}

?>
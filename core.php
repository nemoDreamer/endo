<?php

/**
 * Core
 * handles the entire framework, from request to render.
 *
 * @author Philip Blyth
 */

// $time = microtime(true);

// --------------------------------------------------
// INIT
// --------------------------------------------------

require_once(ENDO_ROOT.'configure.php');
require_once(ENDO_ROOT.INCLUDES_DIR.'initialize.php');
require_once(APP_ROOT.INCLUDES_DIR.'initialize.php');

// Sessions
session_start();

// --------------------------------------------------
// URL
// --------------------------------------------------

Url::parse(array_get($_REQUEST, 'url'));

// --------------------------------------------------
// Controller
// --------------------------------------------------

$Controller = Globe::init(Url::$data['controller'], 'controller');

if (get_class($Controller)=='stdClass') {
  Error::set("Create Controller '".Url::$data['controllerName']."'!", 'fatal');
  $Controller = Globe::init('missing', 'controller');
}

// --------------------------------------------------
// Action
// --------------------------------------------------

// go through filters
$Controller->_beforeFilter();
$Controller->_call(Url::$data['action'], Url::$data['params'], Url::$data['type']);
$Controller->_beforeRender();
if (!Error::is_fatal()) {
  $Controller->_render();
}
$Controller->_afterRender();
$Controller->_afterFilter();

// --------------------------------------------------
// Debug
// --------------------------------------------------

$debug_dump = '';
$debug_dump .= d_pre('Url::$data', false).d_arr(Url::$data, false);
$debug_dump .= d_pre('$Controller->LoggedIn', false).d_arr($Controller->LoggedIn, false);
// $debug_dump .= d_pre('Error::$errors').d_arr(Error::$errors, false);
$debug_dump .= d_pre('$_SESSION', false).d_arr($_SESSION, false);
// $debug_dump .= d_pre('$_SERVER', false).d_arr($_SERVER, false);
// $debug_dump .= d_pre('CONSTANTS', false).d_arr(get_constants(), false);


// --------------------------------------------------
// Output
// --------------------------------------------------

// create View
$View = new AppView();

// assign standards
$View->assign(array(
  // 'id' => Url::$data['controller'].'_'.Url::$data['action'],
  'id' => $Controller->name.'_'.$Controller->action,
  'url' => Url::$data,
  'has_errors' => Error::has_errors(),
  'debug_dump' => $debug_dump
));

// assign content if no fatal
$View->assign('content', !Error::is_fatal() || DEBUG!=0 ? $Controller->output : null);

// assign case-specific
$View->assign(Globe::$variables_for_layout);

// remove junk from xml/json/...
if ($Controller->type!=DEFAULT_REQUEST_TYPE) {
  $View->debugging = false;
  $View->error_reporting = false;
}

// echo
$View->display(Globe::get_template($Controller->layout, 'layouts', $Controller->type));

// d_pre("execution time:".(microtime(true) - $time));

?>
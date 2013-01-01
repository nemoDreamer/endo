<?php

/**
 * ENDO Configure
 *
 * @author Philip Blyth
 */

// --------------------------------------------------
// ROOTS
// --------------------------------------------------

ddefine('ENDO_ROOT',                        dirname(__FILE__).DS);
ddefine('APP_PACKAGES_ROOT',                APP_ROOT.'packages'.DS);
ddefine('ENDO_PACKAGES_ROOT',               ENDO_ROOT.'packages'.DS);
ddefine('TMP_ROOT',                         ROOT.'tmp'.DS);

// --------------------------------------------------
// ROUTES
// --------------------------------------------------

ddefine('DEFAULT_URL',                      'pages/home');
ddefine('DEFAULT_REQUEST_TYPE',             'html');
ddefine('DEFAULT_LAYOUT',                   'default');
ddefine('DEFAULT_ACTION',                   'default');

// --------------------------------------------------
// SUBDOMAIN
// --------------------------------------------------

ddefine('SUBDOMAIN_PREFIX',                 '~');
ddefine('SUBDOMAIN_DEFAULT_URL',            DEFAULT_URL);

// --------------------------------------------------
// ADMIN
// --------------------------------------------------

ddefine('ADMIN_ROUTE',                      '__admin');
ddefine('ADMIN_PREFIX',                     'admin_');
ddefine('ADMIN_DEFAULT_CONTROLLER',         '---'); // should be overwritten in APP Configure
ddefine('ADMIN_DEFAULT_ACTION',             'dashboard');

// --------------------------------------------------
// USERS
// --------------------------------------------------

ddefine('CLASS_USER_MEMBER',                'Member');
ddefine('LOGIN_REDIRECT',                   DS);

// --------------------------------------------------
// FORMATTING
// --------------------------------------------------

ddefine('DATE_FORMAT',                      'D, M jS \'y g:ia');
ddefine('DATE_FORMAT_JS',                   'D, j M Y H:i:s');

// --------------------------------------------------
// FOLDERS
// --------------------------------------------------

ddefine('INCLUDES_DIR',                     'includes'.DS);
ddefine('CLASSES_DIR',                      'classes'.DS);
ddefine('MODELS_DIR',                       'models'.DS);
ddefine('CONTROLLERS_DIR',                  'controllers'.DS);
ddefine('BEHAVIORS_DIR',                    'behaviors'.DS);
ddefine('EXECUTE_DIR',                      'execute'.DS);
ddefine('CACHES_DIR',                       'caches'.DS);
ddefine('PAGES_CONTROLLER',                 'pages');
ddefine('EXECUTE_CONTROLLER',               'execute');

// --------------------------------------------------
// STRINGS (CLASS-NAMES / PRE/SUFFIXES)
// --------------------------------------------------

define('STR_BEHAVIOR',                      'Behavior');

// --------------------------------------------------
// LOGS
// --------------------------------------------------

ddefine('STR_LOG',                          TMP_ROOT.'log.txt');
ddefine('STR_FINDCACHE',                    'find');

// --------------------------------------------------
// EVENTS
// --------------------------------------------------

ddefine('EVENT_ADD',                        'added');
ddefine('EVENT_EDIT',                       'edited');
ddefine('EVENT_REMOVE',                     'removed');
ddefine('EVENT_SHOW',                       'viewed');
ddefine('EVENT_LOGIN',                      'logged in');
ddefine('EVENT_LOGOUT',                     'logged out');
ddefine('EVENT_SIGNUP',                     'signed up');
ddefine('EVENT_FORGOT_PASSWORD',            'forgot their password');
ddefine('EVENT_RESET_PASSWORD',             'reset their password');

// --------------------------------------------------
// SMARTY
// --------------------------------------------------

ddefine('SMARTY_TEMPLATE_DIR',              'views');
ddefine('SMARTY_PLUGINS_DIR',               'helpers');
ddefine('SMARTY_SCAFFOLD_DIR',              SMARTY_TEMPLATE_DIR.DS.'scaffolds');
ddefine('SMARTY_CONFIG_DIR',                'config');
ddefine('SMARTY_COMPILE_DIR',               APP_ROOT.CACHES_DIR.'views_compiled');
ddefine('SMARTY_CACHE_DIR',                 APP_ROOT.CACHES_DIR.'views_cached');
ddefine('SMARTY_TEMPLATE_EXT',              'tpl');

// ddefine('SMARTY_CACHING',                   DEBUG==0 ? true : false);
ddefine('SMARTY_CACHING',                   false);
ddefine('SMARTY_COMPILE_CHECK',             true);
ddefine('SMARTY_DEBUG_OUTPUT',              null);

// --------------------------------------------------
// DB
// --------------------------------------------------

ddefine('MYACTIVERECORD_CONNECTION_STR',    'mysql://root:root@host/database');
ddefine('MYACTIVERECORD_CACHE_SQL',         true);

// --------------------------------------------------
// FUNCTIONS
// --------------------------------------------------

function ddefine($constant, $value)
{
  if (!defined($constant)) {
    define($constant, $value);
  }
}

?>

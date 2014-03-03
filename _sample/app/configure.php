<?php

/**
 * APP Configure
 * Define application-specific constants
 * (can override ENDO constants)
 *
 * @author Philip Blyth
 */

define('LOCAL',                             strpos($_SERVER['SERVER_NAME'], 'localhost') !== false); // change this to match your environment
define('STAGING',                           strpos($_SERVER['HTTP_HOST'], 'staging') !== false); // change this to match your environment

define('DEBUG',                             LOCAL ? 2 : (STAGING ? 1: 0)); // 0:none | 1:basic | 2:basic+smarty

// --------------------------------------------------
// LAYOUT
// --------------------------------------------------

define('SITE_NAME',                         'My Awesome App');
define('FOOTER',                            'Joyfully produced by <a href="mailto:me@19my-awesome-app.com">me</a><br/>
                                             Running on Endo by <a href="http://nemoDreaming.com">nemoDreaming.com</a>');


// --------------------------------------------------
// ROOTS && DB
// --------------------------------------------------

// define('ENDO_ROOT',                         ROOT.'endo'.DS);
define('ENDO_ROOT',                         ROOT.'../../endo'.DS); // adjusted for _sample symlink...

if (LOCAL) {
  define('DOMAIN',                          'localhost');
  define('MYACTIVERECORD_CONNECTION_STR',   'mysql://root:root@localhost/endo_sample');
} else {
  include(APP_ROOT.'domain.inc');
}

// --------------------------------------------------
// ADMIN
// --------------------------------------------------

define('ADMIN_DEFAULT_CONTROLLER',          'posts');

?>

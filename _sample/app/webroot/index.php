<?php

/**
 * Index
 * gets called first, via .htaccess
 *
 * this file needs to be overwriteable by any ulterior version!
 * (use bootstrap for app-specific additions)
 *
 * @author Philip Blyth
 */

define('DS', DIRECTORY_SEPARATOR); // do not change!

// --------------------------------------------------
// ROOTS
// --------------------------------------------------

define('WEB_ROOT', dirname(__FILE__).DS); // do not change!
define('APP_ROOT', dirname(dirname(__FILE__)).DS); // do not change!
define('ROOT', dirname(dirname(dirname(__FILE__))).DS); // do not change!

// --------------------------------------------------
// CONFIG
// --------------------------------------------------

require_once(APP_ROOT.'configure.php');

// --------------------------------------------------
// CORE will handle the rest...
// --------------------------------------------------

require_once(ENDO_ROOT.'core.php');

?>

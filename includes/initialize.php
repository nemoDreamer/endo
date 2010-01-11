<?php

/**
 * initialize.php
 * includes all required files, classes & packages
 * initializes Sessions, Smarty & MyActiveRecord
 *
 * @author Philip Blyth
 */

require_once(ENDO_ROOT.INCLUDES_DIR.'debug.php');
require_once(ENDO_ROOT.CLASSES_DIR.'class.error.php');
require_once(ENDO_ROOT.CLASSES_DIR.'class.globe.php');

// --------------------------------------------------
// INCLUDES
// --------------------------------------------------

Globe::load(array(
  'endo_bootstrap',
  'app_bootstrap',
), 'include');

// --------------------------------------------------
// PACKAGES
// --------------------------------------------------

Globe::load(array(
  'MyActiveRecord/MyActiveRecord.0.4',
  'Smarty/Smarty.class'
), 'package');

// --------------------------------------------------
// CLASSES
// --------------------------------------------------

Globe::load(array(
  'endo_view',
  'app_view',
  'url', // endo url
  'app_url'
));

// --------------------------------------------------
// MODELS
// --------------------------------------------------

Globe::load(array(
  'endo',
  'app',
  'user',
  'admin'
), 'model');

// --------------------------------------------------
// CONTROLLERS
// --------------------------------------------------

Globe::load(array(
  'endo',
  'app'
), 'controller');

?>
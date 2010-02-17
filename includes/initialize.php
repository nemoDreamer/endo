<?php

/**
 * Endo initialize.php
 * includes all required files, classes & packages
 * initializes Sessions, Smarty & MyActiveRecord
 *
 * @author Philip Blyth
 */

require_once(ENDO_ROOT.INCLUDES_DIR.'debug.php');
require_once(ENDO_ROOT.CLASSES_DIR.'class.log.php');
require_once(ENDO_ROOT.CLASSES_DIR.'class.error.php');
require_once(ENDO_ROOT.CLASSES_DIR.'class.globe.php');

// --------------------------------------------------
// INCLUDES
// --------------------------------------------------

Globe::Load(array(
  'endo_bootstrap',
  'app_bootstrap',
), 'include');

// --------------------------------------------------
// PACKAGES
// --------------------------------------------------

Globe::Load(array(
  'MyActiveRecord/MyActiveRecord.0.4',
  'Smarty/Smarty.class'
), 'package');

// --------------------------------------------------
// CLASSES
// --------------------------------------------------

Globe::Load(array(
  'log',
  'endo_view',
  'app_view',
  'url', // endo url
  'app_url'
));

// --------------------------------------------------
// MODELS
// --------------------------------------------------

Globe::Load(array(
  'endo',
  'app',
  'event',
  'user',
  'app_user',
  'admin'
), 'model');

// --------------------------------------------------
// CONTROLLERS
// --------------------------------------------------

Globe::Load(array(
  'endo',
  'app'
), 'controller');

// --------------------------------------------------
// BEHAVIORS
// --------------------------------------------------

Globe::Load(array(
  'endo',
  'app'
), STR_BEHAVIOR);

?>
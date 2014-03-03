<?php

require_once(ENDO_ROOT.CLASSES_DIR.'class.endo_inflector.php'); // can't use Globe::Load, since loaded before Globe...

class AppInflector extends EndoInflector {

  static $pluralize_exceptions = array(
    // 'app' => 'appz'
  );

}

?>

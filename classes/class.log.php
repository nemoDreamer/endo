<?php

Log::$filepath = STR_LOG;

class Log {

  /*
    TODO make Log non-static
  */

  static $filepath = '';

  // --------------------------------------------------
  // PUBLIC METHODS
  // --------------------------------------------------

  static function write($text='', $full=false)
  {
    $handle = fopen(Log::$filepath, 'a');
    fwrite($handle, $text.($full ? ' | '.date('D, d M Y H:i:s') : null)."\n");
    fclose($handle);
    return $text;
  }

}

?>
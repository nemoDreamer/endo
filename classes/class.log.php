<?php

Log::$filepath = STR_LOG;

class Log {

  static public $filepath = '';

  // --------------------------------------------------
  // PUBLIC METHODS
  // --------------------------------------------------

  static public function ToFile($text='', $full=false)
  {
    $handle = fopen(self::$filepath, 'a');
    fwrite($handle, $text.($full ? ' | '.date('D, d M Y H:i:s') : null)."\n");
    fclose($handle);
    return $text;
  }

}

?>
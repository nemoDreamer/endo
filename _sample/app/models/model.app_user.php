<?php

class AppUser extends User {

  static $levels = array(
    'Guest', 'Author', 'Admin'
  );

  // --------------------------------------------------
  // PUBLIC METHODS
  // --------------------------------------------------

  public function is_guest() { return $this->is_class('Guest'); }
  public function is_author() { return $this->is_class('Author'); }

}

?>

<?php

class User extends AppModel {

  var $name = 'user';
  var $description_fields = array('username');

  const REMEMBER_ME = 'remember_me';

  function validate($password)
  {
    return $this->password == $this->_salt_it($password);
  }

  // --------------------------------------------------
  // HOOKS
  // --------------------------------------------------

  function _beforeSave()
  {
    if (!empty($this->password)) { // changing password?
      // valid?
      if (isset($this->old_password) && !$this->validate($old_password)) {
        return Error::set("Old Password invalid!", 'validation');
      }
      // confirmed?
      if ($this->password != $this->password_confirm) {
        return Error::set("Password &amp; confirm don't match!", 'validation');
      }
      $this->_new_password($this->password);
    } else {
      $this->password = User::FetchPassword($this->username);
    }
    return parent::_beforeSave();
  }

  // --------------------------------------------------
  // STATIC METHODS
  // --------------------------------------------------

  static function Login($username, $password)
  {
    if (($user = AppModel::FindFirst('User', true, array('username' => $username))) && $user->validate($password)) {
      return User::Clean($user);
    } else {
      return Error::set("Invalid Username and/or Password", 'validation');
    }
  }

  static function FetchPassword($username)
  {
    return array_pop(mysql_fetch_row(AppModel::query(AppModel::Prepare("SELECT `password` FROM `user` WHERE `username`='%s' LIMIT 1", $username))));
  }

  static function Clean($user)
  {
    if (is_a($user, 'User')) {
      unset($user->salt);
      unset($user->password);
    }
    return $user;
  }

  // --------------------------------------------------
  // PRIVATE METHODS
  // --------------------------------------------------

  private function _salt_it($password)
  {
    return md5($this->salt.$password);
  }

  private function _new_password($password)
  {
    $this->salt = md5(rand());
    $this->password = $this->_salt_it($password);
  }

}

/*
  CREATE TABLE `user` (
    `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    `name` VARCHAR( 64 ) NULL DEFAULT NULL ,
    `username` VARCHAR( 64 ) NULL DEFAULT NULL ,
    `password` VARCHAR( 256 ) NULL DEFAULT NULL ,
    `salt` VARCHAR( 256 ) NULL DEFAULT NULL ,
    `class` VARCHAR( 16 ) NULL DEFAULT NULL ,
    `created` DATETIME NULL DEFAULT NULL ,
    `modified` DATETIME NULL DEFAULT NULL
  ) ENGINE = MYISAM ;
*/

?>
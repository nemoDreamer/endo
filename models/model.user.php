<?php

class User extends AppModel {

  var $name = 'user';

  var $name_fields = array('email');
  var $description_fields = array('class');
  var $order_by = 'email';

  var $level = 0;

  static $levels = array(
    'User', 'Member', 'Editor', 'Publisher', 'Admin'
  );

  // --------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------

  const REMEMBER_ME = 'remember_me';
  const SESSION_KEY = 'user';

  // --------------------------------------------------
  // PUBLIC METHODS
  // --------------------------------------------------

  public function validate($password)
  {
    return $this->password == $this->_salt_it($password);
  }

  public function is_class($class=null)
  {
    return $this->class == $class;
  }

  public function is_admin() { return $this->is_class('Admin'); }
  public function is_guest() { return $this->is_class(); }

  // --------------------------------------------------
  // HOOKS
  // --------------------------------------------------

  /**
   * This hook handles password encoding.
   * FIXME _pb: It includes and exception for admin pages (which might need some more work)
   */
  function _beforeSave()
  {
    if (!empty($this->password)) { // changing password?
      // valid?
      if (isset($this->old_password) && !$this->validate($old_password)) {
        Error::set("Old Password invalid!", 'validation');
        return false;
      }
      // confirmed?
      if ($this->password != $this->password_confirm) {
        Error::set("Password &amp; confirm don't match!", 'validation');
        return false;
      }
      $this->_new_password($this->password);
    } else {
      $this->password = $this->__previous_data['password'];
    }
    return parent::_beforeSave();
  }

  // --------------------------------------------------
  // STATIC METHODS
  // --------------------------------------------------

  /**
   * This is basically the 'Login' function.
   * It cycles through different possible locations for the user,
   * lastly checking if a login is occurring.
   *
   * @return User object. FALSE on fail.
   */
  static function GetCurrent()
  {
    // session?
    if ($user=array_get($_SESSION, User::SESSION_KEY, false)) {
      return $user;
    }
    // cookie?
    elseif ($cookie=array_get($_COOKIE, User::REMEMBER_ME, false)) {
      list($email, $password) = explode('|', $cookie);
      // valid?
      if (is_a($user=User::FetchUser($email), 'User') && $user->password == $password) {
        return User::SetCurrent($user);
      }
    }
    // data?
    elseif (Url::request('check_data', false) && ($email=Url::request('email', false)) && ($password=Url::request('password', false))) {
      // valid?
      if (($user = User::FetchUser($email)) && $user->validate($password)) {
        return User::SetCurrent($user);
      } else {
        Error::set("Invalid Email and/or Password", 'validation');
      }
    }
    return AppModel::Create(User::$levels[0]);
  }

  /**
   * Saves the logged-in User into Session and Cookie
   */
  static function SetCurrent($user)
  {
    // cookie
    if (Url::request(User::REMEMBER_ME, false)) {
      setcookie(User::REMEMBER_ME, $user->email.'|'.$user->password, time()+60*60*24*30);
    }
    // session
    return $_SESSION[User::SESSION_KEY] = User::Clean($user);
  }

  /**
   * Removes the logged-in user from Session and expires Cookie,
   * effectively logging out...
   */
  static function UnsetCurrent()
  {
    // session
    unset($_SESSION[User::SESSION_KEY]);
    // cookie
    setcookie(User::REMEMBER_ME, '', time()-3600);
  }

  static function FetchUser($email)
  {
    return AppModel::FindFirst('User', false, array('email' => $email));
  }

  static function FetchPassword($email)
  {
    return array_pop(mysql_fetch_row(AppModel::query(AppModel::Prepare("SELECT `password` FROM `user` WHERE `email`='%s' LIMIT 1", $email))));
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

  ALTER TABLE `user` DROP `name` ;
  ALTER TABLE `user` CHANGE `username` `email` VARCHAR( 128 ) ;
*/

?>
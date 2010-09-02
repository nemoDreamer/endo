<?php

class User extends AppModel {

  var $name = 'user';

  var $name_fields = array('last_name', 'first_name', 'email');
  var $description_fields = array('class');
  var $order_by = 'last_name, first_name, email';

  var $level = 0;

  static $levels = array(
    'Guest', 'Member', 'Editor', 'Publisher', 'Admin'
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
    return $this->crypted_password == $this->_salt_it($password);
  }

  public function is_class($class=null)
  {
    return $this->class == $class;
  }

  public function is_admin() { return $this->is_class('Admin'); }

  public function store_tag()
  {
    return $this->email.'|'.$this->crypted_password;
  }

  // --------------------------------------------------
  // GET/SETTERS
  // --------------------------------------------------

  public function get_full_name($fancy=false)
  {
    $parts = $this->get_full_name_parts();
    return $fancy ? fancyize($parts) : implode(' ', $parts); // TODO move fancyize to Inflector
  }

  public function get_full_name_parts()
  {
    return array($this->first_name, $this->last_name);
  }

  public function get_full_email()
  {
    return $this->get_full_name()." <{$this->email}>";
  }

  // --------------------------------------------------
  // HOOKS
  // --------------------------------------------------

  /**
   * This hook handles password encoding.
   */
  protected function _beforeSave()
  {
    if (!empty($this->password)) { // changing password?
      // valid?
      if (isset($this->old_password) && !$this->validate($this->old_password)) {
        Error::Set("Old Password invalid!");
        return false;
      }
      // confirmed?
      if ($this->password != $this->password_confirm) {
        Error::Set("Password &amp; confirm don't match!");
        return false;
      }
      $this->_new_password($this->password);
    }
    return parent::_beforeSave();
  }

  // --------------------------------------------------
  // STATIC METHODS
  // --------------------------------------------------

  // Current
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
    if ($session=AppUser::GetSession()) {
      if ($user=AppUser::FetchFromString($session)) {
        return $user;
      }
    }
    // cookie?
    elseif ($cookie=AppUser::GetCookie()) {
      if ($user=AppUser::FetchFromString($cookie)) {
        return $user;
      }
    }
    // data?
    elseif (Url::GetRequest('check_data', false) && ($email=Url::GetRequest('email', false)) && ($password=Url::GetRequest('password', false))) {
      // valid?
      if (($user = AppUser::FetchUser($email)) && $user->validate($password)) {
        return AppUser::SetCurrent($user);
      } else {
        Error::Set("Invalid Email and/or Password", 'validation');
      }
    }
    // create Guest...
    return AppModel::Create(AppUser::$levels[0]);
  }

  /**
   * Saves the logged-in User into Session and Cookie
   * @param boolean $soft do soft log-in (resets cookie if exists)
   */
  static function SetCurrent($user, $soft=false)
  {
    // correct class & extend
    $user = AppUser::FindById($user->class, $user->id, true, true);
    // cookie
    if (Url::GetRequest(AppUser::REMEMBER_ME, false) || ($soft && AppUser::GetCookie())) {
      AppUser::SetCookie($user);
    }
    // session
    AppUser::SetSession($user);
    // return
    return AppUser::Clean($user);
  }

  /**
   * Removes the logged-in user from Session and expires Cookie,
   * effectively logging out...
   */
  static function UnsetCurrent()
  {
    AppUser::UnsetSession();
    AppUser::UnsetCookie();
  }

  // Session
  // --------------------------------------------------

  static function GetSession()
  {
    return array_get($_SESSION, AppUser::SESSION_KEY, false);
  }

  static function SetSession($user=false)
  {
    $value = !$user ? false : $user->store_tag();
    $_SESSION[AppUser::SESSION_KEY] = $value;
  }

  static function UnsetSession()
  {
    unset($_SESSION[AppUser::SESSION_KEY]);
  }

  // Cookie
  // --------------------------------------------------

  static function GetCookie()
  {
    return array_get($_COOKIE, AppUser::REMEMBER_ME, false);
  }

  static function SetCookie($user=false)
  {
    $time = !$user ? time()-3600 : time()+60*60*24*30;
    $value = !$user ? '' : $user->store_tag();
    setcookie(AppUser::REMEMBER_ME, $value, $time, DS);
  }

  static function UnsetCookie()
  {
    AppUser::SetCookie(false);
  }

  // Fetch
  // --------------------------------------------------

  static function FetchUser($email)
  {
    return AppModel::FindFirst('User', false, array('email' => $email));
  }

  static function FetchPassword($email)
  {
    return array_pop(mysql_fetch_row(AppModel::query(AppModel::Prepare("SELECT `password` FROM `user` WHERE `email`='%s' LIMIT 1", $email))));
  }

  static function FetchFromString($string='')
  {
    list($email, $crypted_password) = explode('|', $string);
    // valid?
    if (($user=AppUser::FetchUser($email)) instanceof User && $user->crypted_password == $crypted_password) {
      return AppUser::SetCurrent($user);
    } else {
      return false;
    }
  }

  // Tools
  // --------------------------------------------------

  static function Clean($user)
  {
    if ($user instanceof User || is_subclass_of($user, 'User')) {
      unset($user->salt);
      unset($user->password);
      unset($user->crypted_password);
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
    $this->crypted_password = $this->_salt_it($password);
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
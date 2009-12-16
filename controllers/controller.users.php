<?php

class UsersController extends AppController
{
  var $name = 'users';

  function signup()
  {
    if ($this->data) {
      if (!$this->data('username') || !$this->data('password')) {
        return Error::set("Fields blank!", 'validation');
      }
      $this->Model = AppModel::Create('User', $this->data);
      if ($this->Model->save()) {
        $this->_redirect('/users/login');
      }
    } else {
      $this->_assign(array(
        'redirect_to' => null,
        'name' => null,
        'username' => null
      ));
    }
  }

  // if ($cookie = array_get($_COOKIE, User::REMEMBER_ME, false)) {
  // }

  function login() {
    if ($this->data) {
      if ($this->user = User::Login($this->data('username'), $this->data('password'))) {
        if ($this->data('remember_me', false)) {
          setcookie(User::REMEMBER_ME, $user->username.'|'.$user->salt, time()+60*60*24*30);
        }
        $this->_redirect($this->data('redirect_to'));
      }
    } else {
      $this->_assign(array(
        'redirect_to' => Url::request('redirect_to', '/'),
        'username' => Url::request('username')
      ));
    }
  }

}

?>
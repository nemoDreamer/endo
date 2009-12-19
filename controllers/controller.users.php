<?php

class UsersController extends AppController
{
  var $name = 'users';

  function signup()
  {
    if ($this->data) {
      if (!$this->data('email') || !$this->data('password')) {
        Error::set("Fields blank!", 'validation');
        return false;
      }
      $this->Model = AppModel::Create('User', $this->data);
      if ($this->Model->save()) {
        $this->_redirect('/users/login');
      }
    } else {
      $this->_assign(array(
        'redirect_to' => null,
        'email' => null
      ));
    }
  }

  function login() {
    if ($this->LoggedIn) {
      $this->_redirect($this->data('redirect_to', '/'));
    } else {
      $this->_assign(array(
        'redirect_to' => Url::request('redirect_to', '/'),
        'email' => Url::request('email')
      ));
    }
  }

  function logout()
  {
    User::UnsetCurrent();
    $this->_redirect('/', true);
  }

}

?>
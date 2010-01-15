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
      // create
      $this->Model = AppModel::Create(CLASS_USER_MEMBER, $this->data);
      // valid?
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

  function login($root=DS) {
    if (!$this->LoggedIn->is_guest()) {
      $this->_redirect($this->data('redirect_to', $root));
    } else {
      $this->_assign(array(
        'redirect_to' => Url::request('redirect_to', $root),
        'email' => Url::request('email')
      ));
    }
  }

  function logout($root=DS)
  {
    AppUser::UnsetCurrent();
    $this->_redirect($root);
  }

  // --------------------------------------------------
  // ADMIN
  // --------------------------------------------------

  function admin_login()  { $this->login(DS.ADMIN_ROUTE); }
  function admin_logout() { $this->logout(DS.ADMIN_ROUTE); }

  function admin_add()
  {
    parent::admin_add();
    $this->_feed_form();
  }

  function admin_edit($id)
  {
    parent::admin_edit($id);
    $this->_feed_form();
  }

  // --------------------------------------------------
  // PRIVATE METHODS
  // --------------------------------------------------

  private function _feed_form()
  {
    $this->_assign(array(
      'levels' => build_options(AppUser::$levels),
      'class' => $this->Model->class
    ));
  }
}

?>
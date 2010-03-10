<?php

class UsersController extends AppController
{
  var $name = 'users';

  public function signup()
  {
    if ($this->data) {
      if (!$this->data('email') || !$this->data('password')) {
        Error::Set("Fields blank!", 'validation');
        return false;
      }
      // create
      $this->Model = AppModel::Create(CLASS_USER_MEMBER, $this->data);
      // valid?
      if ($this->Model->save()) {
        $this->redirect('/users/login');
      }
    } else {
      $this->assign(array(
        'redirect_to' => null,
        'email' => null
      ));
    }
  }

  public function login($root=LOGIN_REDIRECT) {
    $this->is_login = true;
    if (!Url::$data['is_admin']) {
      $this->layout = 'login';
    }
    if (!$this->LoggedIn->is_guest()) {
      Event::Set($this->LoggedIn, 'login');
      $this->redirect($this->data('redirect_to', $root));
    } else {
      $this->assign(array(
        'redirect_to' => Url::GetRequest('redirect_to', $root),
        'email' => Url::GetRequest('email')
      ));
    }
  }

  public function logout($root=DS)
  {
    Event::Set($this->LoggedIn, 'logout');
    AppUser::UnsetCurrent();
    $this->redirect($root);
  }

  // --------------------------------------------------
  // ADMIN
  // --------------------------------------------------

  public function admin_login()  { $this->login(DS.ADMIN_ROUTE); }
  function admin_logout() { $this->logout(DS.ADMIN_ROUTE); }


  // --------------------------------------------------
  // FORM DATA
  // --------------------------------------------------

  public function form_levels()
  {
    return build_options(AppUser::$levels);
  }
}

?>
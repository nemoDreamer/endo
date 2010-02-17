<?php

class SettingsController extends AppController {

  var $name = 'settings';

  public function admin_index()
  {
    $this->assign('groups', $groups=Setting::Groups());//array('featured_lessons')));
  }

}

?>
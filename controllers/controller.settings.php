<?php

class SettingsController extends AppController {

  var $name = 'settings';

  function admin_index()
  {
    $this->View->assign('groups', $groups=Setting::Groups());//array('featured_lessons')));
  }

}

?>
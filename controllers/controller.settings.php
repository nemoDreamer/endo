<?php

class SettingsController extends AppController {

  var $name = 'settings';

  function admin_index()
  {
    $this->_assign('groups', $groups=Setting::Groups());//array('featured_lessons')));
  }

}

?>
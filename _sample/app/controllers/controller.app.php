<?php

class AppController extends EndoController
{
  var $name = 'app';

  protected function _beforeRender()
  {
    // --------------------------------------------------
    // NAVIGATION
    // --------------------------------------------------

    /*
      TODO create much better nav/subnav code...
    */
    if (empty($this->nav)) {
      $this->nav = Url::GetData('is_admin') ? array(
        'Dashboard' => DS.ADMIN_ROUTE,
        'Posts' => DS.ADMIN_ROUTE.DS.'posts',
        'FAQs' => DS.ADMIN_ROUTE.DS.'faqs',
        'Tags' => DS.ADMIN_ROUTE.DS.'tags',
        'Import' => DS.ADMIN_ROUTE.DS.'posts/import.php',
        'Settings' => DS.ADMIN_ROUTE.DS.'settings',
        'Admins' => DS.ADMIN_ROUTE.DS.'admins'
      ) : array(
        'Home' => '/',
        'Posts' => '/blogs',
        'Ask an Expert' => '/faqs',
        'Buy the Book' => '/store/catalog.php',
        'About Us' => '/pages/about'
      );
    }

    parent::_beforeRender();
  }
}

?>

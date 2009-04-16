<?php

class PagesController extends AppController
{
  var $name = 'pages';

  function _call($action, $params)
  {
    $this->action = $action;

    $this->View->assign($params);
  }
}

?>
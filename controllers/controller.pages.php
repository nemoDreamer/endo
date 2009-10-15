<?php

class PagesController extends AppController
{
  var $name = 'pages';
  var $has_model = false;

  function _call($action, $params)
  {
    $this->action = $action;

    $this->_assign($params);
  }
}

?>
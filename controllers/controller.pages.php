<?php

class PagesController extends AppController
{
  var $name = 'pages';
  var $has_model = false;

  public function call($action, $params)
  {
    $this->action = $action;

    $this->assign($params);
  }
}

?>
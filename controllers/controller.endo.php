<?php

class EndoController
{
  var $name = 'endo';

  var $output = '';
  var $data = array();

  var $View;

  var $template; // keep NULL!!!
  var $layout = 'default';
  var $action = 'default';
  var $type = DEFAULT_REQUEST_TYPE;

  // --------------------------------------------------
  // CONSTRUCTOR
  // --------------------------------------------------

  function __construct()
  {
    // View
    $this->View =& new AppView();

    if (Url::$data['controller']!=PAGES_CONTROLLER && Url::$data['controller']!=EXECUTE_CONTROLLER) {
      // load Model class
      $this->Model =& Globe::init(Url::$data['modelName'], 'model', false);
      if (get_class($this->Model)!='stdClass') {
        $this->{Url::$data['modelName']} =& AppModel::Create(Url::$data['modelName']);
        $this->Model =& $this->{Url::$data['modelName']}; // reference for ease
      }
    }
  }

  // --------------------------------------------------
  // CALL
  // --------------------------------------------------

  function _call($action='', $arguments=array(), $type=null)
  {
    $this->action = $action;
    $this->type = $type;

    // security
    if (!method_exists($this, $action) || (substr($action, 0, 1)=='_' && Url::$data['controller']!=EXECUTE_CONTROLLER)) {
      Error::set("Missing Action '$action()' in Controller '".get_class($this)."'", 'fatal');
    } else {
      call_user_func_array(array($this, $action), $arguments);
    }
  }

  // --------------------------------------------------
  // FILTERS
  // --------------------------------------------------

  function _beforeFilter() {
    // data
    $this->data = $_POST;

    // admin?
    if (Url::$data['is_admin']) {
      $this->layout = 'admin';
    }
  }

  function _afterFilter() {}

  function _beforeRender() {
    // used by all
    /*
      TODO _pb: maybe simply access through 'register object' in smarty
    */
    $this->View->assign('url', Url::$data);

    Globe::for_layout('sitename', SITE_NAME);
    Globe::for_layout('footer', FOOTER);

    // admin stuff
    if (Url::$data['is_admin']) {
      $this->View->assign('ADMIN_ROUTE', ADMIN_ROUTE);
    }
  }

  function _afterRender() {}

  function _set($variable, $value=null)
  {
    $this->View->assign($variable, $value);
  }

  // --------------------------------------------------
  // OUTPUT
  // --------------------------------------------------

  function _include_to_buffer($filename)
  {
    if($filepath=Globe::find($filename, array(APP_ROOT.EXECUTE_DIR, ENDO_ROOT.EXECUTE_DIR))) {
      ob_start();
      include($filepath);
      $this->output = ob_get_contents();
      ob_end_clean();
    } else {
      Error::set("File '$filepath' could not be found!", 'fatal');
    }
  }

  function _get_template()
  {
    if ($this->template!=null) {
      if ($this->type!=null) {
        return $this->type.DS.$this->template;
      }
      return $this->template;
    } else {
      return Globe::get_template($this->action, $this->name, $this->type);
    }
  }

  function _render()
  {
    // assign data
    $this->View->assign($this->data);
    // de-activate debug
    if ($this->type!=DEFAULT_REQUEST_TYPE) {
      $this->View->debugging = false;
      $this->View->error_reporting = false;
    }
    // render!
    if (($template = $this->_get_template()) != false) {
      return $this->output = $this->View->fetch($template);
    } else {
      Error::set('Couldn\'t render!');
      return false;
    }
  }

  function _redirect($url='')
  {
    header('Location: '.$url);
  }

  // --------------------------------------------------
  // ADMIN SCAFFOLDING
  // --------------------------------------------------

  function admin_index()
  {
    $prep_filter = $this->_prep_filter();
    // get
    $this->View->assign(
      'items',
      $items=AppModel::FindAll(
        Url::$data['modelName'],
        false, // extend
        $prep_filter['where'], // where
        '`'.$this->Model->name_fields[0].'` ASC' // order
        // '`modified` DESC' // order
      )
    );
  }

  // ADD
  // --------------------------------------------------

  function admin_add()
  {
    $prep_filter = $this->_prep_filter();
    if ($this->data!=null) {
      // create
      $this->Model = AppModel::create(Url::$data['modelName'], $this->data);
      // save & redirect?
      if ($this->Model->save()) {
        $this->_redirect(DS.ADMIN_ROUTE.DS.$this->name);
      }
    } else {
      // create empty
      $this->Model = AppModel::create(Url::$data['modelName'], array($prep_filter['parent_field'] => $prep_filter['filter']));
    }
    $this->View->assign('item', $this->Model);
  }

  // EDIT
  // --------------------------------------------------

  function admin_edit($id)
  {
    $this->_prep_filter();
    if ($this->data!=null) {
      // save & redirect?
      if (AppModel::Update(Url::$data['modelName'], $this->data['id'], $this->data)) {
        $this->_redirect(DS.ADMIN_ROUTE.DS.$this->name);
      }
    }
    $this->View->assign('item', AppModel::FindById(Url::$data['modelName'], $id, true));
  }

  // SHOW
  // --------------------------------------------------

  function admin_show($id)
  {
    $this->View->assign('item', AppModel::FindById(Url::$data['modelName'], $id, true));
  }

  // REMOVE
  // --------------------------------------------------

  function admin_remove($id)
  {
    // load
    $this->Model = AppModel::FindById(Url::$data['modelName'], $id, true);

    // destroy
    if ($success = $this->Model->destroy(true)) {
      // redirect
      $this->_redirect(DS.ADMIN_ROUTE.DS.$this->name);
    }
  }

  private function _prep_filter()
  {
    // filter?
    $filter = array_get($_GET, 'filter', null);
    // parents?
    $parents = ($parent = array_get($this->Model->get_parents, 0, false)) ? array_merge(array(0 => 'All'), AppModel::FindAllAssoc($parent)) : false;
    // short filter?
    if (is_numeric($filter) && $filter!=false) {
      Globe::load($parent, 'model');
      $parent_field = AppModel::Class2Table($parent).'_id';
      $where = "$parent_field=$filter";
    } else {
      $parent_field = false; // TODO _pb: improve!!!
      $where = $filter;
    }
    $this->View->assign('filter', $filter); // field, value
    $this->View->assign('parents', $parents);

    return array(
      'where' => $where,
      'filter' => $filter,
      'parents' => $parents,
      'parent_field' => $parent_field
    );
  }

}

?>
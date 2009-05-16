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
    $filter = $this->_get_filter();
    // get
    $this->View->assign(
      'items',
      AppModel::FindAll(
        Url::$data['modelName'],
        true, // extend
        $filter->where, // where
        '`'.$this->Model->name_fields[0].'` ASC' // order
      )
    );
  }

  // ADD
  // --------------------------------------------------

  function admin_add()
  {
    $filter = $this->_get_filter();
    if ($this->data!=null) {
      // create
      $this->Model = AppModel::create(Url::$data['modelName'], $this->data);
      // save & redirect?
      if ($this->Model->save()) {
        $this->_redirect(DS.ADMIN_ROUTE.DS.$this->name);
      }
    } else {
      // pre-data?
      $pre_data = ($filter->field) ? array($filter->field => $filter->value) : null;
      // create empty
      $this->Model = AppModel::create(Url::$data['modelName'], $pre_data);
    }
    $this->View->assign('item', $this->Model);
  }

  // EDIT
  // --------------------------------------------------

  function admin_edit($id)
  {
    $this->_get_filter();
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

  private function _get_filter()
  {
    // filter?
    $value = array_get($_GET, 'filter', null);

    // parents?
    $parents = ($parent = array_get($this->Model->get_parent, 0, false)) ? array_merge(array(0 => 'All'), AppModel::FindAllAssoc($parent)) : false;

    // short filter?
    if ($short = is_numeric($value)) {
      if ($parent) {
        Globe::load($parent, 'model');
        $field = AppModel::Class2Table($parent).'_id';
        if ($value!=false) {
          $where = "$field=$value";
        } else {
          $where = null;
        }
      } else {
        $field = $where = null;
      }
    } else {
      $field = preg_replace('/=.*/U', '', $value);
      $where = $value;
    }

    $this->View->assign('filter', $filter = (object) array(
      'short' => $short,
      'where' => $where,
      'field' => $field,
      'value' => $value,
      'parents' => $parents
    ));

    return $filter;
  }

}

?>
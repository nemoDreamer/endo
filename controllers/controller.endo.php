<?php

class EndoController
{
  var $name = 'endo';

  var $has_model = true;

  var $output = '';
  var $data = array();
  var $filter = null;
  var $nav = array();
  var $nav_prepend = array();
  var $nav_append = array();

  var $View;
  var $Model = false;

  var $template; // keep NULL!!!
  var $layout = DEFAULT_LAYOUT;
  var $action = DEFAULT_ACTION;
  var $type = DEFAULT_REQUEST_TYPE;

  // --------------------------------------------------
  // CONSTRUCTOR
  // --------------------------------------------------

  function __construct()
  {
    // View
    $this->View =& new AppView();

    if ($this->has_model) {
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
      $this->_set_filter();
    }
  }

  function _afterFilter() {}

  function _beforeRender() {

    // URL
    // --------------------------------------------------

    /*
      TODO _pb: maybe simply access through 'register object' in smarty
    */
    $this->_assign('url', Url::$data);
    Globe::for_layout('url', Url::$data);

    // SITE
    // --------------------------------------------------

    Globe::for_layout('sitename', SITE_NAME);
    Globe::for_layout('footer', FOOTER);
    Globe::for_layout('time', time());

    // ADMIN
    // --------------------------------------------------

    if (Url::$data['is_admin']) {
      $this->_assign('ADMIN_ROUTE', ADMIN_ROUTE);
    }

    // SUBDOMAIN
    // --------------------------------------------------

    if (Url::$data['is_subdomain'] && $this->layout == DEFAULT_LAYOUT) {
      $this->layout = 'subdomain';
    }

    // --------------------------------------------------
    // NAVIGATION
    // --------------------------------------------------

    // pre/append
    $this->nav = array_merge(
      $this->nav_prepend,
      $this->nav,
      $this->nav_append
    );

    // save
    Globe::for_layout('nav', $this->nav);

    // active?
    Globe::for_layout('nav_active', '');
    foreach ($this->nav as $label => $link) {
      if (strpos(DS.Url::$data['_url'], $link)===0) {
        Globe::for_layout('nav_active', $label);
        Globe::for_layout('title', $label!='LiveIt! Lessons' ? trim(str_replace('LiveIt!', '', $label)) : 'Lesson '.$this->View->_tpl_vars['lesson']);
      }
    }

  }

  function _afterRender() {}

  // --------------------------------------------------
  // OUTPUT
  // --------------------------------------------------

  function _assign($variable, $value=null)
  {
    return $this->View->assign($variable, $value);
  }

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
    $this->_assign($this->data);
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

  function _display($resource_name, $cache_id = null, $compile_id = null)
  {
    $this->View->display($resource_name, $cache_id, $compile_id);
  }

  function _redirect($url='', $do_die=true)
  {
    if (DEBUG) {
      $this->layout = 'redirect';
      Globe::for_layout('redirect', $url);
    } else {
      header('Location: '.$url);
      if ($do_die) {
        die();
      }
    }
  }

  // --------------------------------------------------
  // ADMIN SCAFFOLDING
  // --------------------------------------------------

  function admin_index()
  {
    // items
    $this->_assign(
      'items',
      AppModel::FindAll(
        Url::$data['modelName'],
        true, // extend
        $this->filter->where, // where
        '`'.$this->Model->name_fields[0].'` ASC' // order
      )
    );
    // options
    $this->_assign('is_publishable', $this->Model->is_publishable());
  }

  // ADD
  // --------------------------------------------------

  function admin_add()
  {
    if ($this->data!=null) {
      // create
      $this->Model = AppModel::create(Url::$data['modelName'], $this->data);
      // save & redirect?
      if ($this->Model->save()) {
        $this->_redirect(DS.ADMIN_ROUTE.DS.$this->name, false);
      }
    } else {
      // pre-data?
      $pre_data = ($this->filter->field) ? array($this->filter->field => $this->filter->value) : null;
      // create empty
      $this->Model = AppModel::create(Url::$data['modelName'], $pre_data);
    }
    $this->_assign('item', $this->Model);
  }

  // EDIT
  // --------------------------------------------------

  function admin_edit($id)
  {
    if ($this->data!=null) {
      // save & redirect?
      if (AppModel::Update(Url::$data['modelName'], $this->data['id'], $this->data)) {
        $this->Model = AppModel::FindById(Url::$data['modelName'], $this->data['id']);
        $this->_redirect(DS.ADMIN_ROUTE.DS.$this->name, false);
      }
    }
    $this->_assign('item', AppModel::FindById(Url::$data['modelName'], $id, true));
  }

  // SHOW
  // --------------------------------------------------

  function admin_show($id)
  {
    $this->_assign('item', AppModel::FindById(Url::$data['modelName'], $id, true));
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
      $this->_redirect(DS.ADMIN_ROUTE.DS.$this->name, false);
    }
  }

  // --------------------------------------------------
  // PRIVATE METHODS
  // --------------------------------------------------

  private function _set_filter()
  {
    // filter?
    $value = Url::request('filter', null);

    if ($this->name != 'execute') {
      // parents?
      $parents = ($parent = array_get($this->Model->get_parent, 0, false)) ? add_all(AppModel::FindAllAssoc_options($parent)) : false;
    } else {
      // no model on ExecuteController...
      $parents = $parent = false;
    }

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
      $field = preg_replace('/=.+$/U', '', $value);
      $where = $value;
    }

    $this->_assign('filter', $this->filter = (object) array(
      'short' => $short,
      'where' => $where,
      'field' => $field,
      'value' => $value,
      'parent' => $parent,
      'parents' => $parents
    ));
  }

}

?>
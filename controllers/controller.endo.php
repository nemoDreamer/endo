<?php

class EndoController
{
  var $name = 'endo';

  var $has_model = true;

  var $output = '';
  var $data = array();
  var $filter = null;
  var $has_redirected = false;
  var $nav = array();
  var $nav_prepend = array();
  var $nav_append = array();

  var $View;
  var $Model = false;
  var $User;

  var $template; // keep NULL!!!
  var $layout = DEFAULT_LAYOUT;
  var $action = DEFAULT_ACTION;
  var $type = DEFAULT_REQUEST_TYPE;

  var $is_ajax = false;

  // ACL
  var $allow = array();
  var $deny = array();

  // --------------------------------------------------
  // CONSTRUCTOR
  // --------------------------------------------------

  public function __construct()
  {
    // Data
    $this->data = $_POST; // TODO clean up data for SQL here?

    // View
    $this->View =& new AppView();

    // Model
    if ($this->has_model) {
      // load Model class
      if (Globe::Load(Url::$data['model'], 'model')) {
        $this->Model = $this->{Url::$data['modelName']} = AppModel::Create(Url::$data['modelName']); // reference for ease
      }
    }
  }

  // --------------------------------------------------
  // CALL
  // --------------------------------------------------

  public function call($action='', $arguments=array(), $type=null)
  {
    $this->action = $action;
    $this->type = $type;

    // ACL
    if (!$this->is_allowed(Url::$data['action'])) {
      $this->redirect(DS.(Url::GetData('is_admin') ? ADMIN_ROUTE.DS : null).'login?redirect_to='.DS.Url::$data['_url'], true, false);
    }

    // security
    if (!method_exists($this, $action) || (substr($action, 0, 1)=='_' && Url::$data['controller']!=EXECUTE_CONTROLLER)) { // FIXME get 'controller' from $this
      Error::Set("Missing Action '$action()' in Controller '".get_class($this)."'", 'fatal');
    } else {
      call_user_func_array(array(&$this, $action), $arguments);
    }
  }

  // --------------------------------------------------
  // FILTERS
  // --------------------------------------------------

  public function call_beforeFilter() { $this->_beforeFilter(); }
  public function call_afterFilter()  { $this->_afterFilter(); }

  protected function _beforeFilter() {

    // User
    // --------------------------------------------------
    $this->LoggedIn = AppUser::GetCurrent();

    // is Admin?
    // --------------------------------------------------
    if (Url::GetData('is_admin')) {
      $this->layout = 'admin';
      $this->_set_filter();
    }

    // ACL
    // --------------------------------------------------
    $admin_actions = array('admin_dashboard', 'admin_index', 'admin_add', 'admin_edit', 'admin_show', 'admin_remove');
    $this->deny( $admin_actions);
    $this->allow($admin_actions, 'Admin');
  }

  protected function _afterFilter() {}

  public function call_beforeRender() { $this->_beforeRender(); }
  public function call_afterRender()  { $this->_afterRender(); }

  protected function _beforeRender() {

    // URL
    // --------------------------------------------------

    /*
      FIXME replace with single 'registered object' in smarty!
    */
    $this->assign('this', $this);
    $this->assign('url', Url::$data);
    Globe::ForLayout('url', Url::$data);

    // SITE
    // --------------------------------------------------

    Globe::ForLayout('sitename', SITE_NAME);
    Globe::ForLayout('footer', FOOTER);
    Globe::ForLayout('time', time());

    // ADMIN
    // --------------------------------------------------

    if (Url::GetData('is_admin')) {
      $this->assign('ADMIN_ROUTE', ADMIN_ROUTE);
    }

    // SUBDOMAIN
    // --------------------------------------------------

    if (Url::GetData('is_subdomain') && $this->layout == DEFAULT_LAYOUT) {
      $this->layout = 'subdomain';
    }

    // NAVIGATION
    // --------------------------------------------------

    // pre/append
    $this->nav = array_merge(
      $this->nav_prepend,
      $this->nav,
      $this->nav_append
    );

    // save
    Globe::ForLayout('nav', $this->nav);

    // active?
    Globe::ForLayout('nav_active', '');
    foreach ($this->nav as $label => $link) {
      if (strpos(DS.Url::$data['_url'], $link)===0) {
        Globe::ForLayout('nav_active', $label);
        // FIXME what the hell is this doing in endo?!
        Globe::ForLayout('title', $label!='LiveIt! Lessons' ? trim(str_replace('LiveIt!', '', $label)) : 'Lesson '.$this->View->_tpl_vars['lesson']);
      }
    }

    // AJAX
    // --------------------------------------------------

    $this->assign('is_ajax', $this->is_ajax());

    // ACL
    // --------------------------------------------------

    Globe::ForLayout('LoggedIn', $this->assign('LoggedIn', AppUser::Clean($this->LoggedIn)));
  }

  protected function _afterRender() {}

  // --------------------------------------------------
  // OUTPUT
  // --------------------------------------------------

  public function assign($variable, $value=null)
  {
    return $this->View->assign($variable, $value);
  }

  public function include_to_buffer($filename)
  {
    if($filepath=Globe::Find($filename, array(APP_ROOT.EXECUTE_DIR, ENDO_ROOT.EXECUTE_DIR))) {
      ob_start();
      include($filepath);
      $this->output = ob_get_contents();
      ob_end_clean();
    } else {
      Error::Set("File '$filepath' could not be found!", 'fatal');
    }
  }

  public function get_template()
  {
    if ($this->template!=null) {
      if ($this->type!=null) {
        return $this->type.DS.$this->template;
      }
      return $this->template;
    } else {
      return Globe::GetTemplate($this->action, $this->name, $this->type);
    }
  }

  public function render()
  {
    // assign data
    $this->assign($this->data);
    // de-activate debug
    if ($this->type!=DEFAULT_REQUEST_TYPE) {
      $this->View->debugging = false;
      $this->View->error_reporting = false;
    }
    // render!
    if (($template = $this->get_template()) != false) {
      return $this->output = $this->View->fetch($template);
    } else {
      Error::Set('Couldn\'t render!');
      return false;
    }
  }

  public function display($resource_name, $cache_id = null, $compile_id = null)
  {
    $this->View->display($resource_name, $cache_id, $compile_id);
  }

  public function redirect($url='', $do_die=true, $wait=true)
  {
    $this->has_redirected = true;
    if (DEBUG && $wait) {
      $this->layout = 'redirect';
      Globe::ForLayout('redirect', $url);
    } else {
      header('Location: '.$url);
      if ($do_die) {
        die();
      }
    }
  }

  public function data($variable, $default=null)
  {
    return array_get($this->data, $variable, $default, true);
  }

  // --------------------------------------------------
  // ADMIN SCAFFOLDING
  // --------------------------------------------------

  public function admin_login()
  {
    $this->redirect(DS.ADMIN_ROUTE.DS.'login');
  }

  public function admin_index()
  {
    // ajax?
    if ($this->is_ajax) {
      // page?
      $page = $this->assign(
        'page',
        (integer) Url::GetRequest('page', 1)
      );
      // limit
      $limit = 10;
      $offset = ($page-1) * $limit;
      // page count
      $page_count = $this->assign(
        'page_count',
        ceil(count(AppModel::FindAllSearched(
          Url::$data['modelName'],
          Url::GetRequest('search', null), // search
          $this->filter->where // where
        )) / $limit)
      );
      // items
      $this->assign(
        'items',
        AppModel::FindAllSearched(
          Url::$data['modelName'],
          Url::GetRequest('search', null), // search
          $this->filter->where, // where
          null, // automatic order
          $limit,
          $offset
        )
      );
    } else {
      // items
      $this->assign(
        'items',
        AppModel::FindAll(
          Url::$data['modelName'],
          false, // extend
          $this->filter->where, // where
          '`'.$this->Model->name_fields[0].'` ASC' // order
        )
      );
    }

    // options
    $this->assign('is_publishable', $this->Model->is_publishable());
  }

  // ADD
  // --------------------------------------------------

  public function admin_add()
  {
    if ($this->data!=null) {
      // create
      $this->Model = AppModel::create(Url::$data['modelName'], $this->data);
      // save & redirect?
      if ($this->Model->save()) {
        $this->redirect(DS.ADMIN_ROUTE.DS.Url::$data['controller'], false);
      }
    } else {
      // pre-data?
      $pre_data = ($this->filter->field) ? array($this->filter->field => $this->filter->value) : null;
      // create empty
      $this->Model = AppModel::create(Url::$data['modelName'], $pre_data);
    }
    $this->assign('item', $this->Model);
  }

  // EDIT
  // --------------------------------------------------

  public function admin_edit($id)
  {
    if ($this->data!=null) {
      // save & redirect?
      if (AppModel::Update(Url::$data['modelName'], $this->data['id'], $this->data)) {
        $this->Model = AppModel::FindById(Url::$data['modelName'], $this->data['id']);
        $this->redirect(DS.ADMIN_ROUTE.DS.Url::$data['controller'], false);
      }
    }
    $this->assign('item', $this->Model = AppModel::FindById(Url::$data['modelName'], $id, true));
  }

  // SHOW
  // --------------------------------------------------

  public function admin_show($id)
  {
    $this->assign('item', $this->Model = AppModel::FindById(Url::$data['modelName'], $id, true));
  }

  // REMOVE
  // --------------------------------------------------

  public function admin_remove($id)
  {
    // load
    $this->Model = AppModel::FindById(Url::$data['modelName'], $id, true);

    // destroy
    if ($success = $this->Model->destroy(true)) {
      // redirect
      $this->redirect(DS.ADMIN_ROUTE.DS.Url::$data['controller'], false);
    }
  }

  // --------------------------------------------------
  // AJAX
  // --------------------------------------------------

  public function is_ajax($action=null)
  {
    return $this->is_ajax && (in_array(is_null($action) ? $this->action : $action, $this->is_ajax) || in_array('*', $this->is_ajax));
  }

  // --------------------------------------------------
  // ACL
  // --------------------------------------------------

  public function allow($strOrArray, $levels='*') { $this->_allow_or_deny('allow', $strOrArray, $levels); }
  public function deny($strOrArray, $levels='*') { $this->_allow_or_deny('deny', $strOrArray, $levels); }

  private function _allow_or_deny($which='allow', $strOrArray, $levels='*')
  {
    if ($which=='allow') {
      $other = 'deny';
    } else {
      $which = 'deny';
      $other = 'allow';
    }

    $array = is_array($strOrArray) ? $strOrArray : array_map('trim', explode(',', $strOrArray));
    $levels = $this->_get_levels($levels);

    foreach ($levels as $level) {
      $this->{$which}[$level] = array_unique(array_merge(array_get($this->{$which}, $level, array()), $array));
      foreach ($array as $action) {
        if (array_key_exists($level, $this->{$other}) && ($tmp_pos=array_search($action, $this->{$other}[$level]))!==false) {
          unset($this->{$other}[$level][$tmp_pos]);
        }
      }
    }
  }

  public function is_allowed($action)
  {
    return !in_array($action, array_get($this->deny, $this->LoggedIn->class, array()));
  }

  private function _get_levels($levels)
  {
    if (is_array($levels)) {
      return $levels;
    } elseif ($levels=='*') {
      return AppUser::$levels;
    } else {
      return explode(',', (string) $levels);
    }
  }

  // --------------------------------------------------
  // PRIVATE METHODS
  // --------------------------------------------------

  private function _set_filter()
  {
    // filter?
    $value = Url::GetRequest('filter', null);

    // parents?
    $parents = $this->Model ? $this->Model->get_first_parent() : false;
    if (!empty($parents)) {
      $parent = $parents['key'];
      $parent_params = $parents['value'];
      AppModel::RelationNameParams($parent, $parent_params);
      $parents = add_all(AppModel::FindAllAssoc_options($parent));
    } else {
      $parents = $parent = false;
    }

    // short filter?
    if ($short = is_numeric($value)) {
      if ($parent) {
        Globe::Load($parent, 'model');
        $field = array_get($parent_params, 'foreignKey', AppInflector::tableize($parent).'_id');
        if ($value!=false) {
          $where = "$field=$value";
        } else {
          $where = null;
        }
      } else {
        $field = $where = null;
      }
    } else {
      if (!empty($value)) {
        preg_match_all('/\s*([^=]+)\s*/', $value, $parts); // explode & trim!
        $field = array_shift($parts[0]);
        $value = implode('=', $parts[0]);
        $where = "$field='$value'";
      } else {
        $field = $where = null;
      }
    }

    $this->assign('filter', $this->filter = (object) array(
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
<?php

/**
 * Smarty extension.
 *
 * @author Philip Blyth
 **/
class EndoView extends Smarty
{

  function __construct()
  {
    $this->template_dir = SMARTY_TEMPLATE_DIR;
    $this->compile_dir = SMARTY_COMPILE_DIR;
    $this->config_dir = SMARTY_CONFIG_DIR;
    $this->cache_dir = SMARTY_CACHE_DIR;
    $this->plugins_dir = array_merge(
      array(
        APP_ROOT.SMARTY_TEMPLATE_DIR.DS.SMARTY_PLUGINS_DIR,
        ENDO_ROOT.SMARTY_TEMPLATE_DIR.DS.SMARTY_PLUGINS_DIR
      ),
      $this->plugins_dir
    );

    $this->caching = SMARTY_CACHING;
    $this->compile_check = SMARTY_COMPILE_CHECK;
    $this->use_sub_dirs = true;

    $this->default_template_handler_func = 'template_handler';

    $this->debugging = DEBUG==2 ? true : false;
    $this->error_reporting = DEBUG>=1 ? true : false;

    $this->assign('_smarty_debug_output', SMARTY_DEBUG_OUTPUT);

    $this->autoload_filters = array(
      'pre' => array('fix_literal')
    );
  }

  function assign_from($other_view)
  {
    return $this->assign($other_view->_tpl_vars);
  }

  function assign($tpl_var, $value = null)
  {
    parent::assign($tpl_var, $value);
    return is_array($tpl_var) ? $tpl_var : $value;
  }

}

function template_handler($resource_type, $resource_name, &$source_content, &$source_timestamp, &$smarty)
{
  if ($filepath=Globe::find($resource_name, array(APP_ROOT.SMARTY_TEMPLATE_DIR.DS, ENDO_ROOT.SMARTY_TEMPLATE_DIR.DS))) {
    return set_resource($filepath, &$source_content, &$source_timestamp, &$smarty);
  } else {
    // not found. set error...
    Error::set("Template '".Globe::clean_dir($filepath)."' not found!");
    return false;
  }
}

function set_resource($filepath, &$source_content, &$source_timestamp, &$smarty)
{
  $source_content = $smarty->_read_file($filepath);
  $source_timestamp = filemtime($filepath);
  return true;
}

?>
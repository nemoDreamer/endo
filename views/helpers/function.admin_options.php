<?php

function smarty_function_admin_options($params=array(), &$smarty)
{
  require_once $smarty->_get_plugin_filepath('function','admin_link');
  // require_once $smarty->_get_plugin_filepath('function','html_image');

  /*
    TODO get controller from object, once name conversion function's optimized for speed
  */
  $controller=null;
  $object=null;
  $wrap=false;
  $show_label=false;
  $links=array();
  $output='';

  foreach ($params as $_key => $_value) {
    switch ($_key) {
      default:
        $$_key = $_value;
        break;
    }
  }

  if (empty($controller)) {
    $smarty->trigger_error("admin_options: missing 'controller' parameter", E_USER_NOTICE);
    return;
  }

  if (empty($object)) {
    $smarty->trigger_error("admin_options: missing 'object' parameter", E_USER_NOTICE);
    return;
  }

  $links['remove'] = smarty_function_admin_link(array(
    'controller' => $controller,
    'action' => 'remove'.DS.$object->id,
    'alt' => $alt = 'remove entry',
    'text' => '<img src="/assets/images/admin/silk/delete.png" width="16" height="16" alt="'.$alt.'" />'.($show_label?" $alt":''),
    'confirm' => "Are you sure you want to remove\n\n'{$object->display_field('name', false)}'\n\nand all child entries? (This action is permanent!)",
    'set_gets' => true
  ), $smarty);

  $links['show'] = smarty_function_admin_link(array(
    'controller' => $controller,
    'action' => 'show'.DS.$object->id,
    'alt' => $alt = 'show entry',
    'text' => '<img src="/assets/images/admin/silk/magnifier.png" width="16" height="16" alt="'.$alt.'" />'.($show_label?" $alt":''),
    'set_gets' => true
  ), $smarty);

  $links['edit'] = smarty_function_admin_link(array(
    'controller' => $controller,
    'action' => 'edit'.DS.$object->id,
    'alt' => $alt = 'edit entry',
    'text' => '<img src="/assets/images/admin/silk/page_white_edit.png" width="16" height="16" alt="'.$alt.'" />'.($show_label?" $alt":''),
    'set_gets' => true
  ), $smarty);

  foreach ($links as $class => $link) {
    $output .= "<li class=\"$class\">$link</li>\n";
  }

  if ($wrap) {
    $output = '<ul>'.$output.'</ul>';
  }

  return $output;
}

?>
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

  $links[] = smarty_function_admin_link(array(
    'controller' => $controller,
    'action' => 'remove'.DS.$object->id,
    'text' => '<img src="/images/admin/silk/delete.png" width="16" height="16" alt="Remove">',
    'confirm' => "Are you sure you want to remove\n\n'{$object->display_field('name', false)}'\n\nand all child entries? (This action is permanent!)",
    'set_gets' => true
  ), $smarty);

  $links[] = smarty_function_admin_link(array(
    'controller' => $controller,
    'action' => 'show'.DS.$object->id,
    'text' => '<img src="/images/admin/silk/magnifier.png" width="16" height="16" alt="Show">',
    'set_gets' => true
  ), $smarty);

  $links[] = smarty_function_admin_link(array(
    'controller' => $controller,
    'action' => 'edit'.DS.$object->id,
    'text' => '<img src="/images/admin/silk/page_white_edit.png" width="16" height="16" alt="Edit">',
    'set_gets' => true
  ), $smarty);

  foreach ($links as $link) {
    $output .= "<li>$link</li>\n";
  }

  if ($wrap) {
    $output = '<ul>'.$output.'</ul>';
  }

  return $output;
}

?>
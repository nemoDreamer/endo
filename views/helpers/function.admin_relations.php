<?php

function smarty_function_admin_relations($params=array(), &$smarty)
{
  require_once $smarty->_get_plugin_filepath('function','admin_link');

  $relations = array(
    'parent' =>   'edit ',
    'children' => 'add/edit '
    // 'attached' => array('attached ',  '', 'Globe::pluralize') // TODO filter: support for 'attached' filtering...
  );

  $object=null;
  $wrap=false;
  $links=array();
  $output='';

  foreach ($params as $_key => $_value) {
    $$_key = $_value;
  }

  if (empty($object)) {
    $smarty->trigger_error("admin_relations: missing 'object' parameter", E_USER_NOTICE);
    return;
  }

  // cycle relations
  foreach ($relations as $relation_name => $relation_prefix) {
    // cycle object's 'get_' variables
    foreach ($object->{'get_'.$relation_name} as $model_index => $model_name) {
      // get controller
      $controller = Globe::init(Globe::pluralize($model_name), 'controller');
      // action & text
      switch ($relation_name) {
        case 'parent':
          $action = 'edit'.DS.$object->{$model_name}->id;
          $text = $model_name;
          $image = 'page_white_edit';
          break;
        case 'children':
          $prefix = $model_index==0 ? '' : AppModel::Class2Table(get_class($object)).'_id=';
          $action = '?filter='.$prefix.$object->id;
          $text = Globe::pluralize($model_name, true);
          $image = 'magnifier';
          break;
        default:
          $action = '';
          $text = $model_name;
          $image = 'magnifier';
          break;
      }
      // build link
      $links[] =  smarty_function_admin_link(array(
        'controller' => $controller->name,
        'action' => $action,
        'text' => "<span>$relation_prefix$text</span>".' <img src="/assets/images/admin/silk/'.$image.'.png" width="16" height="16">'
      ), $smarty);
    }
  }

  foreach ($links as $link) {
    $output .= "<li>$link</li>\n";
  }

  if ($wrap) {
    $output = '<ul class="relations">'.$output.'</ul>';
  }

  return $output;
}

?>
<?php

function smarty_function_admin_relations($params=array(), &$smarty)
{
  require_once $smarty->_get_plugin_filepath('function','admin_link');

  $relations = array(
    'parent' =>   'edit ',
    'children' => 'add/edit '
    // 'attached' => array('attached ',  '', 'AppInflector::pluralize') // TODO filter: support for 'attached' filtering...
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
    $i=0;
    foreach ($object->{'get_'.$relation_name} as $model_name => $model_params) {
      AppModel::RelationNameParams($model_name, $model_params);
      // get controller
      $controller = Globe::init($model_name, 'controller'); // TODO replace by ::singleton, find others
      // action & text
      switch ($relation_name) {
        case 'parent':
          $action = ($model=object_get($object, $model_name)) ? 'edit'.DS.$model->id : null;
          $text = ucwords(AppInflector::titleize($model_name));
          $image = 'page_white_edit';
          break;
        case 'children':
          $prefix = $i==0 ? '' : AppInflector::tableize(get_class($object)).'_id=';
          $action = '?filter='.$prefix.$object->id;
          $text = ucwords(AppInflector::pluralize(AppInflector::titleize($model_name)));
          $image = 'magnifier';
          break;
        default:
          $action = '';
          $text = AppInflector::titleize($model_name);
          $image = 'magnifier';
          break;
      }
      // build link
      $links[] =  smarty_function_admin_link(array(
        'controller' => AppInflector::fileize(get_class($controller), 'controller'),
        'action' => $action,
        'text' => "<span>$relation_prefix$text</span>".' <img src="/assets/images/admin/silk/'.$image.'.png" width="16" height="16">'
      ), $smarty);

      $i++;
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
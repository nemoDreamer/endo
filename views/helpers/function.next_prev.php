<?php

function smarty_function_next_prev($params, &$smarty)
{
  // --------------------------------------------------
  // PARAMETERS
  // --------------------------------------------------

  $object = array_get($params, 'object'); // required
  if (!$object) $smarty->trigger_error("next_prev: missing 'object' array parameter");

  $base = array_get($params, 'base', '');
  $id = array_get($params, 'id', '');
  $class = array_get($params, 'class', '');
  $show_name = array_get($params, 'show_name', false);

  // --------------------------------------------------
  // DEPENDENCIES
  // --------------------------------------------------

  require_once $smarty->_get_plugin_filepath('function','link');
  require_once $smarty->_get_plugin_filepath('modifier','truncate');

  // --------------------------------------------------
  // DO!
  // --------------------------------------------------

  $both = $object->acts_as_sortable->get_both(true);
  $parent = $object->acts_as_sortable->get_parent();

  $output  = "<ul id=\"$id\" class=\"next_prev $class\">";

  foreach (array('prev', 'next') as $i => $label) {

    $href = false;
    $name = '';

    if ($curr_obj = $both[$i]) {
      $href = $curr_obj->{$parent}->id.DS.$curr_obj->id;
      $name = $curr_obj->name;
    }

    $name = ' <span class="name">'.($show_name ? ($href ? expand_name($curr_obj, $parent) : 'You have reached the end.') : null).'</span>';
    $text = ($i==0 ? '<span class="aquo">&laquo;</span> ' : null) . '<span class="label">'.ucfirst($label).'</span>' . ($i==1 ? ' <span class="aquo">&raquo;</span>' : null);

    $output .= "<li class=\"$label\">";
    $output .= $href ? smarty_function_link(array('href' => $base.$href, 'text' => $text.$name), &$smarty) : "<span class=\"disabled\">$text $name</span>";
    $output .= '</li>';
  }

  $output .= '</ul>';

  return $output;
}

function expand_name($curr_obj, $parent)
{
  $truncated = smarty_modifier_truncate($curr_obj->name, 50, ' [...]');
  return '<span class="number">'.$curr_obj->{$parent}->position.'.'.$curr_obj->position.'</span> '.$truncated;
}

?>

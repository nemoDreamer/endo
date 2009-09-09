<?php

class Setting extends EndoModel {

  var $name_fields = array('variable');
  var $description_fields = array('value');
  var $order_by = 'variable';

  function Group($group)
  {
    return AppModel::FindAll('Setting', false, "`group`='{$group}'", '`variable` ASC');
  }

  function Get($variable, $group = 'default', $value_only = true)
  {
    $tmp = AppModel::FindFirst('Setting', false, "`variable`=\"{$variable}\" AND `group`='{$group}'");
    if (!$tmp) {
      return null;
    }
    return !$value_only ? $tmp : $tmp->value;
  }

}

?>
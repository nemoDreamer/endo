<?php

class Setting extends EndoModel {

  function Group($group)
  {
    return AppModel::FindAll('Setting', false, "`group`='{$group}'", '`variable` ASC');
  }

  function Get($variable, $group = 'default', $value_only = true)
  {
    $tmp = AppModel::FindFirst('Setting', false, "`variable`=\"{$variable}\" AND `group`='{$group}'");
    return !$value_only ? $tmp : $tmp->value;
  }

}

?>
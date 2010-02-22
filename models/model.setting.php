<?php

class Setting extends AppModel {

  var $name_fields = array('label', 'variable');
  var $description_fields = array('value');
  var $order_by = 'variable';

  static function Group($group, $reindex=false)
  {
    $tmp = self::FindAll('Setting', false, "`group`='$group'", '`variable` ASC');
    return $reindex ? AppModel::CollectionIndexBy($tmp, 'variable') : $tmp;
  }

  static function Get($variable, $group = 'default', $value_only = true)
  {
    $tmp = self::FindFirst('Setting', false, "`variable`=\"$variable\" AND `group`='$group'");
    if (!$tmp) {
      return null;
    }
    return !$value_only ? $tmp : $tmp->value;
  }

  static function Groups($exclude=array())
  {
    if (!empty($exclude)) {
      $where = array();
      foreach ($exclude as $value) {
        array_push($where, "`group`!='$value'");
      }
      $where = implode(' AND ', $where);
    } else {
      $where = null;
    }
    return AppModel::CollectionGroupBy(self::FindAll('Setting', false, $where), 'group');
  }

}

?>
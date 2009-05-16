<?php

class EndoModel extends MyActiveRecord
{
  var $name_fields = array('name');
  var $description_fields = array('description');

  var $get_attached = array();
  var $get_children = array();
  var $get_parents = array();

  // --------------------------------------------------
  // STORAGE
  // --------------------------------------------------

  function save()
  {
    // --------------------------------------------------
    // Extend w/ datetime stamps
    // --------------------------------------------------
    if (!isset($this->id)) {
      $this->set_datetime('created');
    }
    $this->set_datetime('modified');

    // go through filters
    if (!$this->_beforeSave()) return false;
    if (!parent::save()) return false;
    if (!$this->_afterSave()) return false;

    // success!
    return true;
  }

  /*
    TODO 'update' wrapper
  */

  // --------------------------------------------------
  // RETRIEVAL
  // --------------------------------------------------

	function date($strKey, $for_js=false)
	{
    return date($for_js ? DATE_FORMAT_JS : DATE_FORMAT, $this->get_timestamp($strKey));
	}

  function AddRelated ($relation, &$objects=array())
  {
    // set defaults
    switch ($relation) {
      case 'attached':
      case 'children':
      case 'parent':
        break;
      default:
        $relation = 'attached';
        break;
    }

    $get_related = "get_$relation";
    $find_related = "find_$relation";

    // get related
    foreach ($objects as $key => $object) {
      foreach ($object->$get_related as $related_name) {
        Globe::load($related_name, 'model');
        $objects[$key]->$related_name = $object->$find_related($related_name);
      }
    }
    return $objects;
  }

  function AddAllRelated(&$objects=array())
  {
    AppModel::AddRelated('attached', $objects);
    AppModel::AddRelated('children', $objects);
    return $objects;
  }

  function FindAll($strClass, $extend=FALSE, $mxdWhere=NULL, $strOrderBy='`id` ASC', $intLimit=10000, $intOffset=0)
  {
    $objects = parent::FindAll($strClass, $mxdWhere, $strOrderBy, $intLimit, $intOffset);
    if ($extend) {
      AppModel::AddAllRelated($objects);
    }
    return $objects;
  }

  function FindById( $strClass, $mxdID, $extend=FALSE )
  {
    $objects = array(parent::FindById($strClass, $mxdID));
    if ($extend) {
      AppModel::AddAllRelated($objects);
    }
    return $objects[0];
  }

  /**
   * return array of frequency of links of one model to another
   *
   * @param string $strColumn
   * @param string $linkClass
   * @param string $toClass
   * @return array
   * @author Philip Blyth
   */
  function FindAllFreq($ofClass, $toClass, &$min=null, &$max=null)
  {
    Globe::load(array($ofClass, $toClass), 'model');

    $ofTable = EndoModel::Class2Table($ofClass);
    $strColumn = strtolower($ofClass.'_id');
    $linkTable = EndoModel::getLinkTable($ofClass, $toClass);

    $sql = "SELECT $ofTable.*, count(*) AS frequency FROM `$linkTable` INNER JOIN `$ofTable` ON id=$strColumn GROUP BY $strColumn ORDER BY $strColumn";

    $output = EndoModel::FindBySql($ofClass, $sql);

    $max=0;
    foreach ($output as $key => $object) {
      if ($object->frequency > $max) {
        $max = $object->frequency;
      }
    }
    $min=$max;
    foreach ($output as $key => $object) {
      if ($object->frequency < $min) {
        $min = $object->frequency;
      }
    }

    return $output;
  }

  function FindAllAssoc($strClass, $strWhere=null, $strIndexBy='id')
  {
    // get table
    // $table = AppModel::Class2Table($strClass); // TODO work on Globe::make_class_name...
    $table = strtolower($strClass);

    // load class file
    $class = Globe::init($table, 'model');

    // get name field
    $name_field = implode($class->name_fields, '`, `');

    // where?
    $strWhere = $strWhere!=null ? "WHERE {$strWhere}" : '';

    // get collection
    $collection = AppModel::FindBySql($strClass, "SELECT `id`, `{$name_field}` FROM `{$table}` {$strWhere} ORDER BY `{$name_field}`", $strIndexBy);

    // associate id => name
    foreach ($collection as $key => $object) {
      $collection[$key] = $object->display_field('name', false);
    }

    // return
    asort($collection); // re-sort by display-name
    return $collection;
  }

  // --------------------------------------------------
  // DESTRUCTION
  // --------------------------------------------------

  function destroy( $extend=false )
  {
    // destroy?
    if (($success = parent::destroy()) && $extend) {
      // remove attachments
      foreach ($this->get_attached as $model) {
        foreach ($this->$model as $attached) {
          $attached->detach();
        }
      }

      // destroy children
      foreach ($this->get_children as $model) {
        foreach ($this->$model as $child) {
          $child->destroy();
        }
      }
    }

    return $success;
  }

  // --------------------------------------------------
  // FILTERS
  // --------------------------------------------------

  function _beforeSave() {
    return true;
  }
  function _afterSave() {
    return true;
  }

  // --------------------------------------------------
  // SCAFFOLD TOOLS
  // --------------------------------------------------

  function display_field($scaffold_name, $span_wrap=true, $separator=', ')
  {
    $output = '';
    foreach ($this->{$scaffold_name.'_fields'} as $key => $value) {
      if ($this->$value!=null) {
        $output .= $span_wrap ? '<span class="part_'.$key.'">'.$this->$value.'</span> ' : $this->$value.($key<count($this->{$scaffold_name.'_fields'})-1 ? $separator : '');
      }
    }
    return trim($output);
  }

}

?>
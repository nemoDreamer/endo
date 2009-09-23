<?php

class EndoModel extends MyActiveRecord
{
  var $name_fields = array('name');
  var $description_fields = array('description');
  var $order_by = 'name';

  var $get_attached = array();
  var $get_children = array();
  var $get_parent = array();
  var $file_uploads = array();

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
        $model = Globe::init($related_name, 'model');
        $objects[$key]->$related_name = $object->$find_related($related_name, null, $model->order_by);
      }
    }
    return $objects;
  }

  function AddAllRelated(&$objects=array())
  {
    AppModel::AddRelated('attached', $objects);
    AppModel::AddRelated('children', $objects);
    AppModel::AddRelated('parent', $objects);
    return $objects;
  }

  function FindAll($strClass, $extend=FALSE, $mxdWhere=NULL, $strOrderBy='`id` ASC', $intLimit=10000, $intOffset=0)
  {
    if (!AppModel::_smartLoadModel($strClass)) return false;

    $objects = parent::FindAll($strClass, $mxdWhere, $strOrderBy, $intLimit, $intOffset);
    if ($extend) {
      AppModel::AddAllRelated($objects);
    }
    return $objects;
  }

  function FindById( $strClass, $mxdID, $extend=FALSE )
  {
    if (!AppModel::_smartLoadModel($strClass)) return false;

    $objects = array(parent::FindById($strClass, $mxdID));
    if ($extend) {
      AppModel::AddAllRelated($objects);
    }
    return $objects[0];
  }

  function FindFirst($strClass, $extend=false, $strWhere=null, $strOrderBy='id ASC')
  {
    if (!AppModel::_smartLoadModel($strClass)) return false;

    if ($object = parent::FindFirst($strClass, $strWhere, $strOrderBy)) {
      $objects = array($object);
      if ($extend) {
        AppModel::AddAllRelated($objects);
      }
      return $objects[0];
    } else {
      return false;
    }
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
    if (!AppModel::_smartLoadModel(array($ofClass, $toClass))) return false;

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

  function FindAllAssoc($strClass, $extend=FALSE, $mxdWhere=NULL, $strIndexBy='id', $strOrderBy='`id` ASC', $intLimit=10000, $intOffset=0)
  {
    if (!AppModel::_smartLoadModel($strClass)) return false;

    // get collection
    $collection = AppModel::FindAll($strClass, $extend, $mxdWhere, $strOrderBy, $intLimit, $intOffset);

    // associate label
    $output = array();
    foreach ($collection as $key => $object) {
      $output[$object->{$strIndexBy}] = $object;
    }

    return $output;
  }

  function FindAllAssoc_options($strClass, $mxdWhere=null, $strIndexBy='id')
  {
    if (!AppModel::_smartLoadModel($strClass)) return false;

    // get collection
    $collection = AppModel::FindAllAssoc($strClass, false, $mxdWhere, $strIndexBy);

    // only keep display-name
    foreach ($collection as $key => $object) {
      $collection[$key] = $object->display_field('name', false);
    }

    // sort by display-name
    asort($collection);

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
    return $this->_handle_file_uploads();
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

  function is_publishable()
  {
    return array_key_exists('is_published', $this);
  }

  function is_published()
  {
    return !$this->is_publishable() || ($this->is_publishable() && $this->is_published);
  }

  function _for_show()
  {
    $output = clone $this;
    foreach ($output as $key => $value) {
      if (preg_match('/^get_/', $key) || preg_match('/_id$/', $key)) {
        unset($output->$key);
      }
    }
    return $output;
  }

  function __toString()
  {
    return $this->display_field('name', false);
  }

  // --------------------------------------------------
  // SMART LOAD
  // --------------------------------------------------

  private function _smartLoad($strClass, $type='model')
  {
    return Globe::load($strClass, $type) != false;
  }

  private function _smartLoadModel($strClass)       { return AppModel::_smartLoad($strClass, 'model'); }
  private function _smartLoadController($strClass)  { return AppModel::_smartLoad($strClass, 'controller'); }

  // --------------------------------------------------
  // UPLOADS
  // --------------------------------------------------

  function _handle_file_uploads()
  {
    $success = true;
    foreach ($this->file_uploads as $field => $params) {
      $success = $success && $this->_handle_file_upload($field, $params);
    }
    return $success;
  }

  function _handle_file_upload($field=null, $params=array())
  {
    require_once(PACKAGES_ROOT.'VerotUpload'.DS.'class.upload.php');

    // TODO _pb: add 'delete-checkbox' functionality

    if (!empty($_FILES)) {

      // defaults
      $path = array_get($params,'path','assets');
      $allowed = array_get($params,'allowed','image');
      $options = array_get($params,'options',array());

      $file = new Upload($_FILES[$field]);
      if ($file->uploaded) {
        // path
        $path = 'uploads'.DS.Globe::pluralize(get_class($this)).DS.$path.DS.$this->id.DS;
        $folder = WEB_ROOT.$path;

        // settings
        if ($allowed=='image') {
          $file->allowed = array('image/gif','image/jpg','image/jpeg','image/png','image/bmp');
          // defaults
          if (!array_get($options, 'width') && !array_get($options, 'height')) {
            $options = array_merge(array('width' => 100), $options);
          }
          // resize
          $file->image_max_pixels = 1000000;
          $file->image_resize = true;
          $file->image_convert = 'jpg';
          // determine method
          if (array_get($options, 'width') && !array_get($options, 'height')) {
            // width only
            $file->image_x = $options['width'];
            $file->image_ratio_y = true;
          } elseif(!array_get($options, 'width') && array_get($options, 'height')) {
            // height only
            $file->image_y = $options['height'];
            $file->image_ratio_x = true;
          } else {
            // width and height
            $file->image_x = $options['width'];
            $file->image_y = $options['height'];
            // crop?
            if (array_get($options, 'crop')) {
              $file->image_ratio_crop = true;
            } else {
              $file->image_ratio = true;
            }
          }
        } elseif(is_array($allowed)) {
          $file->allowed = $allowed;
        }

        // process!
        $file->Process($folder);

        // success?
        if ($file->processed) {
          // remove old file?
          @unlink($folder.str_replace(DS.$path,'',$this->{$field.'_old'}));

          $file->Clean();
          $this->{$field} = DS.$path.$file->file_dst_name;
          return parent::save();
        } else {
          // TODO _pb: add endo form error message
          echo 'ERROR: ' . $file->error;
          return false;
        }
      }

    }

    return true;
  }

  // --------------------------------------------------
  // DATA CHECKING
  // --------------------------------------------------

  /*
   * can take either:
   *  - string: variable-name
   *  - array: array of variable-names
   *  - array: 0: array of variable-names, 1: default
   */
  function has_($arrayOrVar, &$scope=false)
  {
    if (!$scope) {
      $scope =& $this;
    }
    $array = !is_array($arrayOrVar) ? array($arrayOrVar) : $arrayOrVar;
    if (is_array($array[0])) {
      $default = $array[1]; // save default
      if (!is_array($default)) {
        $default = array($default);
      }
      $array = $array[0];
    } else {
      $default = array();
    }
    $has = false;
    foreach ($array as $var) {
      $has = $has || (isset($scope->$var) && !in_array($scope->$var, $default));
    }
    return $has;
  }

}

?>
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

  var $do_handle_attachments = true;

  var $acts_as = array();

  /*
    TODO add 'acts_as_publishable' & 'acts_as_datable' used all over Endo...
    TODO better 'name_fields', with sprintf support!
  */

  // --------------------------------------------------
  // CONSTRUCTOR
  // --------------------------------------------------

  public function __construct()
  {
    // Behaviors
    // --------------------------------------------------
    foreach ($this->acts_as as $behavior => $config) {
      if (Globe::Load($behavior, STR_BEHAVIOR)) {
        $class_name = $behavior.STR_BEHAVIOR;
        $this->{'acts_as_'.$behavior} = new $class_name($this, $config);
      }
    }
  }

  // --------------------------------------------------
  // CREATION
  // --------------------------------------------------

  static function &Create($strClass, $arrVals = null)
  {
    AppModel::_smartLoadModel($strClass);
    $obj = parent::Create($strClass, $arrVals);
    if (array_key_exists('class', $obj) && empty($obj->class)) {
      $obj->class = $strClass;
    }
    return $obj;
  }

  static function Update( $strClass, $id, $properties )
  {
    // make sure we're not loading from cache!
    $cache_reset = rand();
    $object = array_shift(AppModel::FindAll($strClass, true, array('id' => $id, "'$cache_reset'" => $cache_reset), 'id', 1));
    // are we allowed to make these changes?
    if (!$object->_validate_changes($properties)) {
      return false;
    }
    $object->populate($properties);
    return $object->save();
  }

  // --------------------------------------------------
  // RELATIONS
  // --------------------------------------------------

  static function AddRelated ($relation, &$objects=array())
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
      foreach ($object->$get_related as $related_name => $related_params) {
        AppModel::RelationNameParams($related_name, $related_params);
        // d($find_related.': '.$related_name.' '.print_r($related_params, true));
        $model = Globe::Init($related_name, 'model');
        $strForeignKey = array_get($related_params, 'foreignKey', null);
        // TODO hook in 'where' from behavior:
        $strWhere = null;
        $array = $relation=='parent' ? array($related_name, $strForeignKey) : array($related_name, $strWhere, $model->order_by, 10000, 0, $strForeignKey);
        $objects[$key]->$related_name = call_user_func_array(array($object, $find_related), $array);
      }
    }
    return $objects;
  }

  static function AddAllRelated(&$objects=array())
  {
    AppModel::AddRelated('attached', $objects);
    AppModel::AddRelated('children', $objects);
    AppModel::AddRelated('parent', $objects);
    return $objects;
  }

  static function RelationNameParams(&$related_name, &$related_params)
  {
    if (is_numeric($related_name)) {
      $related_name = $related_params;
      $related_params = array();
    }
  }

  // --------------------------------------------------
  // FIND
  // --------------------------------------------------

  static function FindAll($strClass, $extend=false, $mxdWhere=null, $strOrderBy=null, $intLimit=null, $intOffset=null)
  {
    if (!AppModel::_smartLoadModel($strClass)) return false;

    $model = AppModel::Create($strClass);

    $intLimit = get_default($intLimit, 10000);
    $intOffset = get_default($intOffset, 0);
    $strOrderBy = get_default($strOrderBy, $model->order_by);

    $objects = parent::FindAll($strClass, $mxdWhere, $strOrderBy, $intLimit, $intOffset);
    if ($extend) {
      AppModel::AddAllRelated($objects);
    }
    return $objects;
  }

  static function FindById( $strClass, $mxdID, $extend=FALSE )
  {
    if (!AppModel::_smartLoadModel($strClass)) return false;

    $objects = array(parent::FindById($strClass, $mxdID));
    if ($extend) {
      AppModel::AddAllRelated($objects);
    }
    return $objects[0];
  }

  static function FindFirst($strClass, $extend=false, $strWhere=null, $strOrderBy=null)
  {
    if (!AppModel::_smartLoadModel($strClass)) return false;

    $strOrderBy = get_default($strOrderBy, AppModel::Create($strClass)->order_by);

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
  static function FindAllFreq($ofClass, $toClass, &$min=null, &$max=null)
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

  static function FindAllAssoc($strClass, $extend=FALSE, $mxdWhere=NULL, $strIndexBy='id', $strOrderBy=null, $intLimit=10000, $intOffset=0)
  {
    if (!AppModel::_smartLoadModel($strClass)) return false;

    $model = AppModel::Create($strClass); // TODO replace by ::singleton

    $strOrderBy = get_default($strOrderBy, $model->order_by);

    // get collection
    $collection = AppModel::FindAll($strClass, $extend, $mxdWhere, $strOrderBy, $intLimit, $intOffset);

    // associate label
    $output = array();
    foreach ($collection as $key => $object) {
      $output[$object->{$strIndexBy}] = $object;
    }

    return $output;
  }

  static function FindAllAssoc_options($strClass, $mxdWhere=null, $strIndexBy='id')
  {
    if (!AppModel::_smartLoadModel($strClass)) return false;

    // get collection
    $collection = AppModel::FindAllAssoc($strClass, false, $mxdWhere, $strIndexBy);

    return AppModel::CollectionToOptions($collection);
  }

  static function FindAllSearched($strClass, $search='', $extend=false, $strWhere=NULL, $strOrderBy=null, $intLimit=null, $intOffset=null)
  {
    if (!AppModel::_smartLoadModel($strClass)) return false;

    $Model = new $strClass();

    $strOrderBy = get_default($strOrderBy, '`display_name` ASC');
    $intLimit = get_default($intLimit, 10000);
    $intOffset = get_default($intOffset, 0);

    $table = AppModel::Class2Table($strClass);

    $where = array('1=1');
    // where?
    if ($strWhere) {
      array_push($where, $strWhere);
    }
    // search?
    if ($search) {
      $search_pattern = implode(' ', array_wrap(explode(' ', $search), '+')).'*';
      $match = 'MATCH ('.implode(', ', $Model->name_fields).', '.implode(', ', $Model->description_fields).") AGAINST('".$search_pattern."' IN BOOLEAN MODE)";
      array_push($where, $match);
    }
    $where = implode(' AND ', $where);

    $display_name = "TRIM(CONCAT_WS(' ', `".implode('`, `', $Model->name_fields).'`)) AS `display_name`';

    $query = "SELECT *, $display_name FROM `$table` WHERE $where ORDER BY $strOrderBy LIMIT $intOffset,$intLimit";

    $objects = AppModel::FindBySql($strClass, $query);
    if ($extend) {
      AppModel::AddAllRelated($objects);
    }
    return $objects;
  }

  static function PureSql( $strSQL )
  {
    static $cache = array();
    $md5 = md5($strSQL);

    if( isset( $cache[$md5] ) && defined('MYACTIVERECORD_CACHE_SQL') && MYACTIVERECORD_CACHE_SQL )
    {
      return $cache[$md5];
    }
    else
    {
      if( $rscResult = MyActiveRecord::query($strSQL) )
      {
        $arrObjects = array();
        while( $arrVals = mysql_fetch_assoc($rscResult) )
        {
          array_push($arrObjects, (object) $arrVals);
        }
        mysql_free_result($rscResult);
        return $cache[$md5] = $arrObjects;
      }
      else
      {
        trigger_error("MyActiveRecord::FindBySql() - SQL Query Failed: $strSQL", E_USER_ERROR);
        return $cache[$md5] = false;
      }
    }
  }

  // --------------------------------------------------
  // COLLECTION MANIPULATION
  // --------------------------------------------------

  const COLLECTION_INDEX_NO_GROUP = '__ungroupable__';

  /**
   * @static
   * @param Array of Model Objects (usually returned by a 'Find')
   * @param String or array of keys (if array, will group recursively cycling through keys)
   */
  static function CollectionGroupBy($collection, $group_bys)
  {
    if (is_array($group_bys)) {
      $group_by = array_shift($group_bys);
    } else {
      $group_by = (string) $group_bys;
      $group_bys = null;
    }

    $tmp = array();
    foreach ($collection as $index => $row) {
      $group_name = !isset($row->$group_by) ? AppModel::COLLECTION_INDEX_NO_GROUP : $row->$group_by;
      if (!array_key_exists($group_name, $tmp)) {
        $tmp[$group_name] = array();
      }
      $tmp[$group_name][$index] = $row;
    }

    if (is_array($group_bys) && !empty($group_bys)) {
      foreach ($tmp as $key => $value) {
        $tmp[$key] = AppModel::CollectionGroupBy($value, $group_bys);
      }
    }

    return $tmp;
  }

  static function CollectionIndexBy($collection=array(), $index='id')
  {
    $tmp = array();
    foreach ($collection as $key => $value) {
      $tmp[$value->$index] = $value;
    }
    return $tmp;
  }

  static function CollectionSlice($collection, $id, $offset, $length, $preserve_keys=false)
  {
    // prepare
    $collection_keys = array_keys($collection);
    $collection_keys = array_pad($collection_keys, -(abs($offset)+count($collection_keys)), null); // left pad array to allow too small/large offset
    $collection_keys = array_pad($collection_keys, abs($offset)+count($collection_keys), null); // right pad
    $id_as_index = array_search($id, $collection_keys);
    $collection_slice = array_slice($collection_keys, $id_as_index + $offset, $length);
    // replicate
    $output = array();
    for ($i=0; $i < $length; $i++) {
      $index = $preserve_keys ? $collection_slice[$i] : $i;
      $output[(string)$index] = array_get($collection, $collection_slice[$i]);
    }
    return $output;
  }

  static function CollectionExtract($collection, $keys)
  {
    $output = array();
    foreach ($collection as $key => $value) {
      $output[$key] = array_extract($value, $keys);
    }
    return $output;
  }

  static function CollectionClone($collection)
  {
    $output = array();
    foreach ($collection as $key => $value) {
      $output[$key] = clone $value;
    }
    return $output;
  }

  static function CollectionToOptions($collection, $sort=true, $fancy=false)
  {
    if (!is_array($collection)) {
      return array();
    }
    // only keep display-name
    foreach ($collection as $key => $object) {
      $collection[$key] = $object->display_field('name', $fancy);
    }

    if ($sort) {
      // sort by display-name
      asort($collection);
    }

    return $collection;
  }

  // --------------------------------------------------
  // SAVE
  // --------------------------------------------------

  public function save()
  {
    /*
      TODO implement class change
    */
    // CLASS CHANGE
    // --------------------------------------------------
    // if ($this->class != get_class($this)) {
    //   # code...
    // }

    // Extend w/ datetime stamps
    // --------------------------------------------------
    /*
      TODO implement hooks in filters for behaviors
    */
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

  // --------------------------------------------------
  // POPULATE
  // --------------------------------------------------

  public function populate($arrVals)
  {
    $success = parent::populate($arrVals);

    // attachments
    foreach ($this->get_attached as $model) {
      if (!isset($this->$model)) {
        $this->$model = array();
      }
    }

    return $success;
  }

  // --------------------------------------------------
  // DESTRUCTION
  // --------------------------------------------------

  public function destroy( $extend=false )
  {
    // destroy?
    if (($success = parent::destroy()) && $extend) {
      // remove attachments
      foreach ($this->get_attached as $model) {
        foreach ($this->$model as $attached) {
          $attached->detach($this);
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

  protected function _beforeSave() {
    return true;
  }
  protected function _afterSave() {
    return $this->_handle_file_uploads() && $this->_handle_attachments();
  }

  // --------------------------------------------------
  // SCAFFOLD TOOLS
  // --------------------------------------------------

  public function display_field($scaffold_name, $fancy=true, $separator=', ')
  {
    $parts = array();
    foreach ($this->{$scaffold_name.'_fields'} as $field) {
      if ($fancy || $this->$field!=null) {
        array_push($parts, $this->$field);
      }
    }
    return $fancy ? fancyize($parts) : implode($separator, $parts);
  }

  // TODO move to dateable
  public function date($strKey, $for_js=false)
  {
    return date($for_js ? DATE_FORMAT_JS : DATE_FORMAT, $this->get_timestamp($strKey));
  }

  public function relative_date($strKey) {
    $time=$this->get_timestamp($strKey);
    $diff = time() - $time;

    if ($diff < 1) {
      return 'just now';
    }

    $a = array(
      12 * 30 * 24 * 60 * 60  =>  'year',
           30 * 24 * 60 * 60  =>  'month',
                24 * 60 * 60  =>  'day',
                     60 * 60  =>  'hour',
                          60  =>  'minute',
                           1  =>  'second'
    );

    foreach ($a as $seconds => $str) {
      $d = $diff / $seconds;
      if ($d > 1 && $str=='year') {
        return date(DATE_FORMAT, $time);
      } elseif ($d >= 1) {
        $r = round($d);
        return $r.' '.$str.($r > 1 ? 's' : '').' ago';
      }
    }
  }

  // TODO move to publishable
  public function is_publishable()
  {
    return array_key_exists('is_published', $this);
  }

  public function is_published()
  {
    return !$this->is_publishable() || ($this->is_publishable() && $this->is_published);
  }

  public function _for_show()
  {
    $output = clone $this;
    foreach ($output as $key => $value) {
      if (preg_match('/^get_/', $key) || preg_match('/_id$/', $key)) {
        unset($output->$key);
      }
    }
    return $output;
  }

  public function get_first_parent()
  {
    reset($this->get_parent);
    return each($this->get_parent);
  }

  // --------------------------------------------------
  // CONVERSION
  // --------------------------------------------------

  public function __toString()
  {
    return $this->display_field('name', false);
  }

  // --------------------------------------------------
  // SMART LOAD
  // --------------------------------------------------

  private function _smartLoad($strClass, $type='model')
  {
    return Globe::Load($strClass, $type) != false;
  }

  private function _smartLoadModel($strClass)       { return AppModel::_smartLoad($strClass, 'model'); }
  private function _smartLoadController($strClass)  { return AppModel::_smartLoad($strClass, 'controller'); }

  // --------------------------------------------------
  // UPLOADS
  // --------------------------------------------------

  private function _handle_file_uploads()
  {
    $success = true;
    foreach ($this->file_uploads as $field => $params) {
      $success = $success && $this->_handle_file_upload($field, $params);
    }
    return $success;
  }

  private function _handle_file_upload($field=null, $params=array())
  {
    require_once(ENDO_PACKAGES_ROOT.'VerotUpload'.DS.'class.upload.php');

    // TODO add 'delete-checkbox' functionality

    if (!empty($_FILES)) {

      // defaults
      $path = array_get($params,'path','assets');
      $allowed = array_get($params,'allowed','image');
      $options = array_get($params,'options',array());

      $file = new Upload($_FILES[$field]);
      if ($file->uploaded) {
        // path
        $path = 'uploads'.DS.AppInflector::pluralize(get_class($this)).DS.$path.DS.$this->id.DS;
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
          // TODO add endo form error message
          echo 'ERROR: ' . $file->error;
          return false;
        }
      }

    }

    return true;
  }

  // --------------------------------------------------
  // ATTACHMENTS
  // --------------------------------------------------

  private function _handle_attachments()
  {
    if (!$this->do_handle_attachments) {
      return true;
    }
    // cycle Associations
    foreach ($this->get_attached as $class) {
      // save passed
      $tmp = isset($this->$class) ? $this->$class : array();
      // get all objects
      $Objects = AppModel::FindAll($class, false); // only one query
      // detach
      foreach ($this->find_attached($class) as $Object) {
        $this->detach($Object);
      }
      // attach
      if (!empty($tmp)) {
        foreach ($tmp as $objectOrId) {
          $Object = is_numeric($objectOrId) ? $Objects[$objectOrId] : $objectOrId;
          $this->attach($Object);
        }
      } else {
        $this->$class = array();
      }
    }
    return true;
  }


  private function _attach_to_all($class)
  {
    Globe::Load($class, 'model');
    // get all objects:
    $objects = Listing::FindAll($class, true);
    // cycle listings
    foreach ($objects as $object) {
      // attach & save
      $object->attach($this);
    }
  }

  private function _detach_from_all($class)
  {
    Globe::Load($class, 'model');
    // get all objects:
    $objects = Listing::FindAll($class, true);
    // cycle listings
    foreach ($objects as $object) {
      // attach & save
      $object->detach($this);
    }
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
  public function has_($arrayOrVar, &$scope=false)
  {
    if (!$scope) {
      $scope =& $this;
    }
    $array = !is_array($arrayOrVar) ? array($arrayOrVar) : $arrayOrVar;
    if (is_array($array[0])) {
      $default = $array[1]; // save default
      if (!is_array($default)) {
        $default = array($default, null);
      } else {
        array_push($default, null);
      }
      $array = $array[0];
    } else {
      $default = array(null);
    }
    $has = false;
    foreach ($array as $var) {
      $has = $has || (isset($scope->$var) && !in_array($scope->$var, $default));
    }
    return $has;
  }

  // --------------------------------------------------
  // VALIDATION
  // --------------------------------------------------

  protected function _validate_changes($new_data)
  {
    return true;
  }

}

?>
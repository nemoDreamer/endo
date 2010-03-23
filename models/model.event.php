<?php

class Event extends AppModel {

  var $order_by = 'timestamp DESC';

  /*
   * i'm using populate because i can't use the
   * automatic AddRelations (since added model-classes are dynamic)
   */
  public function populate($data)
  {
    $success = parent::populate($data);

    $success = $success && ($this->Subject = AppModel::FindById($this->subject_class, $this->subject_id, false)) != false;
    if ($this->object_class) {
      if ($this->object_id > 0) {
        $success = $success && ($this->Object = AppModel::FindById($this->object_class, $this->object_id, false)) != false;
      } else {
        $this->Object = new StringEvent($this->object_class);
        $this->object_class = $this->Object->class;
      }
    }
    $this->action_full = defined($constant='EVENT_'.strtoupper($this->action)) ? constant($constant) : null;

    return $success;
  }

  public function populate_alt($arrVals)
  {
    if( is_array($arrVals) )
    {
      foreach($arrVals as $key=>$val)
      {
        $this->$key=$val;
      }
      return true;
    }
    else
    {
      return false;
    }
  }

  // --------------------------------------------------
  // STATIC METHODS
  // --------------------------------------------------

  static function Set($Subject, $action, $Object=false, $object_is_string=false)
  {
    if (!$Subject || !$action) {
      return false;
    }

    $Event = new Event();

    $Event->populate_alt(array_merge(
      array(
        'subject_class' => get_class($Subject),
        'subject_id' => $Subject->id,
        'action' => $action
      ),
      $Object!=false ? array(
        'object_class' => $object_is_string ? $Object : get_class($Object),
        'object_id' => !$object_is_string ? $Object->id : null
      ) : array()
    ));
    $Event->set_datetime('timestamp');

    return $Event->save();
  }

  static function Exists($action, $Subject, $Object=false)
  {
    $object_where = ($Object!=false) ? "AND `object_class`='".get_class($Object)."' AND `object_id`=$Object->id" : null;
    $tmp=AppModel::PureSQL(
      "SELECT * FROM `event`
      WHERE
        `action`='$action'
        AND `subject_class`='".get_class($Subject)."' AND `subject_id`=$Subject->id
        $object_where
      "
    );
    return !empty($tmp);
  }

}

/*
 * used when object is a string
 */
class StringEvent {

  var $class = '';
  var $string = '';
  var $id = 0;
  const STRING_SEP = '|';

  public function __construct($string)
  {
    list($this->class, $this->string) = explode(self::STRING_SEP, $string);
  }

  public function display_field()
  {
    return fancyize(array($this->string));
  }
}

?>
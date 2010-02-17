<?php

class Event extends AppModel {

  var $order_by = 'timestamp';

  /*
   * i'm using populate because i can't use the
   * automatic AddRelations (since added model-classes are dynamic)
   */
  public function populate($data)
  {
    $success = parent::populate($data);

    $success = ($success && ($this->Subject = AppModel::FindById($this->subject_class, $this->subject_id, false))!=false);
    $success = (!$this->object_class || ($success && ($this->Object = AppModel::FindById($this->object_class, $this->object_id, false))!=false));

    $this->action_full = defined($constant='EVENT_'.strtoupper($this->action)) ? constant($constant) : null;

    return $success;
  }

  // --------------------------------------------------
  // STATIC METHODS
  // --------------------------------------------------

  static function Set($Subject, $action, $Object=false)
  {
    $Event = AppModel::Create('Event', array_merge(
      array(
        'subject_class' => get_class($Subject),
        'subject_id' => $Subject->id,
        'action' => $action
      ),
      $Object!=false ? array(
        'object_class' => get_class($Object),
        'object_id' => $Object->id
      ) : array()
    ));
    $Event->set_datetime('timestamp');
    return $Event->save();
  }

}

?>
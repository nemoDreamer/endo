<?php

/**
* Sortable
* extends the model with previous/next & re-position methods
*/
class SortableBehavior extends AppBehavior
{
  static $name = 'sortable';

  public $parent, $parent_obj, $parent_behavior = false;
  private $next, $previous = false;

  protected $defaults = array(
    'group_by_parent' => false
  );

  public $config = array();

  const ORDER_BY_DEFAULT = 'position ASC';

  // --------------------------------------------------
  // CONSTRUCTOR
  // --------------------------------------------------

  public function __construct($root, $config=array())
  {
    parent::__construct($root, $config);

    // set default order_by?
    if ($this->root->order_by=='name') {
      $this->root->order_by = SortableBehavior::ORDER_BY_DEFAULT;
    }
    // get position_field & order_direction
    $tmp = explode(' ', $this->root->order_by);
    $this->position_field = strtolower($tmp[0]);
    $this->order_direction = strtoupper(array_get($tmp, 1, 'asc'));
    // has parent_position field?
    if (isset($this->root->parent_position)) {
      $this->root->order_by = 'parent_position ASC '.$this->root->order_by;
    }
  }

  // --------------------------------------------------
  // PARENT
  // --------------------------------------------------

  public function get_parent()
  {
    if (!$this->parent && $this->config['group_by_parent'] && !empty($this->root->get_parent)) {
      $this->parent = $this->root->get_parent[0];
    }
    return $this->parent;
  }

  public function get_parent_obj()
  {
    if (!$this->parent_obj && $this->parent) {
      $this->parent_obj = object_get($this->root, $this->parent);
      if (!$this->parent_obj) {
        Globe::Load($this->parent, 'model');
        $this->parent_obj = $this->root->{$this->parent} = $this->root->find_parent($this->parent);
      }
    }
    return $this->parent_obj;
  }

  public function get_parent_behavior()
  {
    if (!$this->parent_behavior && $this->parent_obj) {
      $this->parent_behavior = object_get($this->parent_obj, 'acts_as_'.self::$name);
    }
    return $this->parent_behavior;
  }

  public function get_parent_position($recursive=false)
  {
    if (isset($this->root->parent_position) && $this->root->parent_position!=null) {
      return $this->root->parent_position;
    } elseif ($this->get_parent() && $this->get_parent_obj() && $this->get_parent_behavior()) {//
      return $this->parent_behavior->get_position($recursive);
    } else {
      return false;
    }
  }

  // --------------------------------------------------
  // NEXT/PREVIOUS
  // --------------------------------------------------

  private function get_($previousNext, $extend=false)
  {
    $previousNext_b = dual($previousNext, 'previous', 'next');
    if ($this->order_direction=='DESC') {
      $previousNext_b = !$previousNext_b; // invert sort order
    }

    if (!$this->{$previousNext}) {
      $class_name = get_class($this->root);
      $ltGt = $previousNext_b ? '<' : '>';
      $ascDesc = $previousNext_b ? 'DESC' : 'ASC';
      $parent_where = $this->get_parent() ? " AND `{$this->get_parent()}_id`={$this->get_parent_obj()->id}" : null;
      $where = "`{$this->position_field}` $ltGt '{$this->get_position()}'".$parent_where;

      // is first/last?
      if((!$object = AppModel::FindFirst(
        $class_name,
        $extend,
        $where,
        "`{$this->position_field}` $ascDesc"
      )) && $this->parent) {
        $parent_behavior = $this->get_parent_behavior();
        if ($adjacent_parent = AppModel::FindFirst(
          $this->parent,
          false, // don't extent
          "`{$parent_behavior->position_field}` $ltGt '{$parent_behavior->get_position()}'",
          "`{$parent_behavior->position_field}` $ascDesc"
        )) {
          $object = AppModel::FindFirst($class_name, $extend, "`{$this->parent}_id`=$adjacent_parent->id", "`{$this->position_field}` $ascDesc");
        }
      }

      return $this->{$previousNext} = $object;
    } else {
      return $this->{$previousNext};
    }
  }

  public function get_previous($extend=false)  { return $this->get_('previous',  $extend); }
  public function get_next($extend=false)      { return $this->get_('next',      $extend); }

  public function get_both($extend=false)
  {
    return array(
      $this->get_previous($extend),
      $this->get_next($extend)
    );
  }

  // --------------------------------------------------
  // DATA
  // --------------------------------------------------

  public function get_position($recursive=false)
  {
    $parent_position = ($recursive && ($tmp=$this->get_parent_position())!==false) ? $tmp.'.' : null;
    return $parent_position.$this->root->{$this->position_field};
  }

}

?>
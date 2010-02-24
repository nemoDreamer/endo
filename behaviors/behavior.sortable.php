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

  // --------------------------------------------------
  // CONSTRUCTOR
  // --------------------------------------------------

  public function __construct($root, $config=array())
  {
    parent::__construct($root, $config);
    if ($this->root->order_by=='name') {
      $this->root->order_by = 'position ASC';
    }

    $tmp = explode(' ', $this->root->order_by);
    $this->position_field = strtolower($tmp[0]);
    $this->order_direction = strtoupper(array_get($tmp, 1, 'asc'));
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

  public function get_position()
  {
    return $this->root->{$this->position_field};
  }
}


?>
<?php

/**
* Sortable
* extends the model with previous/next & re-position methods
*/
class SortableBehavior extends AppBehavior
{
  public $parent, $parent_obj = false;
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
    $this->root->order_by = 'position ASC';
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
    if ($this->parent) {
      $this->parent_obj = object_get($this->root, $this->parent);
    }
    return $this->parent_obj;
  }

  // --------------------------------------------------
  // NEXT/PREVIOUS
  // --------------------------------------------------

  private function get_($previousNext, $extend=false)
  {
    $previousNext_b = dual($previousNext, 'previous', 'next');

    if (!$this->{$previousNext}) {
      $class_name = get_class($this->root);
      $ltGt = $previousNext_b ? '<' : '>';
      $ascDesc = $previousNext_b ? 'DESC' : 'ASC';
      $parent_where = $this->get_parent() ? " AND `{$this->get_parent()}_id`={$this->get_parent_obj()->id}" : null;
      $where = "`position` $ltGt {$this->root->position}".$parent_where;

      // is first/last?
      if(!$object = AppModel::FindFirst($class_name, $extend, $where, "`position` $ascDesc")) {
        if ($adjacent_parent = AppModel::FindFirst($this->parent, false, "`position` $ltGt {$this->parent_obj->position}", "`position` $ascDesc")) {
          $object = AppModel::FindFirst($class_name, $extend, "`{$this->parent}_id`=$adjacent_parent->id", "`position` $ascDesc");
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
}


?>
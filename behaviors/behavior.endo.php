<?php

class EndoBehavior {

  protected $root;
  protected $defaults = array();
  public $config = array();
  public $inited = false;

  public function __construct($root, $config)
  {
    $this->root = $root;
    $this->config = array_merge($this->defaults, $config);
  }

  public function initialize() {
    if (!$this->inited) {
      $this->init();
      $this->inited = true;
    }
  }

  public function init() {}

}

?>
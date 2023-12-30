<?php

class Category {
    public $id;
    public $name;
    public $parent;
    public $children;

    function __construct($id, $name, $parent=null) {
        $this->id = $id;
        $this->name = $name;
        $this->parent = $parent;
        $this->children = [];
    }

    function display() {
        if ($this->parent == null) {
            return $this->name;
        }
        return $this->parent->display() . ' -> ' . $this->name;
    }
}

?>
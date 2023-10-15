<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

abstract class idg_declaration_type extends idg_object_type {
    /* Must return true, else traversal will fail! */

    function add_token(&$tree, &$depth, &$path) {
        return true;
    }
}

class idg_datasource_declaration_type extends idg_declaration_type {

    function __construct() {
        parent::__construct();

        $this->set_known('name');
        $this->set_known('class');
        $this->set_mandatory('class');
    }
}

class idg_datasource_declaration extends idg_tree_node {

    function __construct() {
        parent::__construct();
        $this->set_idg_type('datasource');
    }

    /** @todo: make this an object's functionality */
    function create_instance(&$datasource = null) {
        if (!$class_name = $this->get_property('class'))
            return;
        
        $object = new $class_name($this->get_parameters());
        $object->parent = $this;

        return $object;
    }
}

class idg_renderer_declaration_type extends idg_declaration_type {

    function __construct() {
        parent::__construct();

        $this->set_known('slot');
        $this->set_known('class');
        $this->set_known('source');
        $this->set_known('nullable');

        $this->set_known('anchor');
        $this->set_known('name');
        $this->set_known('tag');

        $this->set_mandatory('slot');
        $this->set_mandatory('class');
        $this->set_mandatory('source');
    }
}

class idg_renderer_declaration extends idg_tree_node {

    function __construct() {
        parent::__construct();

        $this->set_idg_type = 'renderer';
    }

    function create_instance(&$datasource = null) {
        if (!$class_name = @$this->get_property('class'))
            diag($this, "$obj_name: 'class' property missing");

        $nullable = $this->get_property('nullable') == "yes";

        if (class_exists($class_name)) {
            $object = new $class_name($this);
            $object->datasource = $datasource;
            $object->anchor = $this->get_property('anchor');

            return $object;
        }

        if (!$nullable)
            diag($this, "$name: unknown class '$class'");
    }

    function add_token(&$tree, &$depth, &$path) {
        if (($anchor = $this->get_property('anchor'))) {
            $prop = array();
            $prop['type'] = 'anchor';
            $prop['anchor'] = $anchor;
            $prop['name'] = $this->get_property('name');

            $tree->tokens[] = $prop;
        }

        return true;
    }
}

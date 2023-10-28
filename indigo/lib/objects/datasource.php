<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/**
 * Type information for idg_datasource.
 */
class idg_datasource_type extends idg_type {

    function __construct() {
        parent::__construct();

        $this->register_property('name');
        $this->register_property('class');
        $this->set_property_mandatory('class');
    }
}

/**
 * The source of all data.
 */
class idg_datasource extends idg_leafnode implements idg_parameterized {

    use idg_parameters;

    function __construct() {
        parent::__construct();
        $this->set_element_name('datasource');
    }
}

/**
 * An object instance of a data source.
 * Actual classes need to derive from this one since they do not get type information.
 */
class idg_datasource_object implements Iterator, idg_parameterized {
    use idg_parameters;

    private int $position = 0;
    private array $tokens = [];
    
    private idg_treenode $parent;
    
    function __construct(idg_treenode $parent) {
        $this->parent = $parent;
        $this->parametes = [];
        
        if (method_exists($parent, 'get_parameters'))
            $this->parameters = $parent->get_parameters();    
    }
    
    function add_token($token) {
        $this->tokens[] = $token;
    }
    
    function rewind(): void {
        $this->position = 0;
    }

    function current() {
        return $this->tokens[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next(): void {
        ++$this->position;
    }

    function valid(): bool {
        return isset($this->tokens[$this->position]);
    }
}

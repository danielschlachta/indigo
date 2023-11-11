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
 * A data source.
 */
class idg_datasource extends idg_leafnode implements idg_parameterized {

    use idg_parameters;

    function __construct() {
        parent::__construct('datasource');
    }

    /**
     * Needs to be implemented for idg_site\get_datasource() but does nothing.
     * @param idg_datasource $datasource The datasource being created
     * @return <code>true</code>
     */
    protected function _add_token(idg_datasource_implementation $datasource): bool {
        return true;
    }
}

/**
 * An object instance of a data source.
 * Retrieves parameters from the declaration, hence the constructor.
 */
class idg_datasource_implementation extends idg_object_implementation implements
Iterator, idg_parameterized {
    use idg_parameters;

    private int $position = 0;
    private array $tokens = [];

    /**
     * A constructor. 
     * Copies parameters from the parent.
     * @param idg_leafnode $parent The declaration
     */
    function __construct(idg_leafnode $parent) {
        parent::__construct($parent);
        if (method_exists($parent, 'get_parameters')) 
            $this->parameters = $parent->get_parameters();
    }

    function add_token($token): void {
        $this->tokens[] = $token;
    }

    function rewind(): void {
        $this->position = 0;
    }

    function current(): mixed {
        return $this->tokens[$this->position];
    }

    function key(): int {
        return $this->position;
    }

    function next(): void {
        ++$this->position;
    }

    function valid(): bool {
        return isset($this->tokens[$this->position]);
    }

    function reverse(): void {
        if ($this->tokens)
            $this->tokens = array_reverse($this->tokens);
    }

    function truncate(int $count): void {
        if ($this->tokens)
            while (count($this->tokens) > $count)
                array_pop($this->tokens);
    }
}

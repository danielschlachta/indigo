<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://openslot.org/license/mit/
 */

/**
 * Type information for idg_fragment.
 */
class idg_fragment_type extends idg_view_element_type {

    function __construct() {
        parent::__construct();
        $this->child_types = [];

        $this->register_property('class');
        $this->set_property_mandatory('class');
        $this->register_property('source');
    }
}

/**
 * A fragment produces output, has access to attributes and can also have a data source.
 * @see idg_fragment_implementation
 */
class idg_fragment extends idg_view_element {

    function __construct() {
        parent::__construct();
        $this->set_element_name('fragment');
    }

    function _render(idg_document $document, idg_view $view): void {
        if (!$class_name = $this->get_property('class'))
            return;
        
        if (!class_exists($class_name))
            idg_diag($this, "class '$class_name' does not exist");

        if (!is_subclass_of($class_name, 'idg_fragment_implementation'))
            idg_diag($this, "class '$class_name' is not a subclass of idg_fragment_object");

        $datasource = null;
        if (($source_name = $this->get_property('source')))
            $datasource = $document->get_datasource($source_name);

        $object = new $class_name($this, $datasource);
        $object->_render($document, $view);
    }
}

/**
 * An object instance of a fragment.
 * Actual classes need to derive from this one since they do not get type information.
 */
abstract class idg_fragment_implementation {

    private idg_view_element $parent;
    private ?idg_datasource_implementation $datasource;

    public function __construct(idg_view_element $parent,
        ?idg_datasource_implementation $datasource = null) {
        $this->parent = $parent;
        $this->datasource = $datasource;
    }

    /**
     * Returns the parent of the object, i.e. the fragment as it was declared.
     * @return idg_view_element The idg_fragment object in question
     */
    function get_parent(): idg_view_element {
        return $this->parent;
    }
    
    /**
     * Returns the idg_id of the fragment, as set in the parent.
     * @see idg_object
     * @return string The id
     */
    function get_idg_id(): string {
        return $this->parent->get_idg_id();
    }
    
    /**
     * Returns the property with the given name of the fragment, as set in the parent.
     * @see idg_object
     * @param string $name The name of the property
     * @return string|null The value of the property
     */
    function get_property(string $name): ?string {
        return $this->parent->get_property($name);
    }
    
    /**
     * Returns an attribute with the given name and scope, obtained from the parent.
     * @see idg_leafnode
     * @param string $name The name of the attribute
     * @param string $scope The scope of the attribute
     * @return idg_attribute The attribute
     */
    function get_attribute(string $name, string $scope = null): idg_attribute {
        return $this->parent->get_attribute($name, $scope);
    }
    
    /*
     * Returns the datasource declared in the <code>source</code> property if there
     * is one.
     * @todo this does not actually work at the moment
     */
    function get_datasource(): ?idg_datasource_implementation {
        return $this->datasource;
    }
}


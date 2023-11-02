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
class idg_fragment extends idg_view_element implements idg_parameterized {

    use idg_parameters;

    function __construct() {
        parent::__construct('fragment');
    }

    function _render(idg_document $document, idg_view $view): void {
        if (!$class_name = $this->get_property('class'))
            return;

        if (!class_exists($class_name))
            idg_diag($this, "class '$class_name' does not exist");

        if (!is_subclass_of($class_name, 'idg_fragment_implementation'))
            idg_diag($this,
                "class '$class_name' is not a subclass of idg_fragment_implementation");

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
abstract class idg_fragment_implementation extends idg_object_implementation {

    private ?idg_datasource_implementation $datasource;

    public function __construct(idg_view_element $parent,
        ?idg_datasource_implementation $datasource = null) {
        parent::__construct($parent);
        $this->datasource = $datasource;
    }

    /**
     * Returns a parameter with the given name and scope, obtained from the parent.
     * @see idg_fragment
     * @param string $name The name of the parameter
     * @return string|null The value of the parameter
     */
    function get_parameter(string $name): ?string {
        return $this->get_declaration()->get_parameter($name);
    }

    /**
     * Returns an attribute with the given name (including scope), filled with
     * all options of all compatible attributes in the document.
     * 
     * @param string $name The name of the attribute
     * @return idg_attribute The attribute
     */
    function fetch_attribute(string $name, idg_document $document): idg_attribute {
        $attribute = $this->get_declaration()->get_or_create_attribute($name);

        for ($i = $document; $i; $i = $i->get_parent())
            if (method_exists($i, 'get_or_create_attribute') &&
                (($object = $i->get_or_create_attribute($name)))) 
                if (($parameters = $object->get_parameters()))
                    foreach ($parameters as $parameter => $value)
                        if (!$attribute->get_parameter($parameter))
                            $attribute->set_parameter($parameter, $value);
        
        return $attribute;
    }

    /*
     * Returns the datasource.
     * @return string|null idg_datasource_implementation The datasource
     */
    function get_datasource(): ?idg_datasource_implementation {
        return $this->datasource;
    }
}

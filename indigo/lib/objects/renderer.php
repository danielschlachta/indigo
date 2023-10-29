<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/**
 * Type information for idg_renderer.
 */
class idg_renderer_type extends idg_type {

    function __construct() {
        parent::__construct();

        $this->register_property('slot');
        $this->register_property('class');
        $this->register_property('source');
        $this->register_property('nullable');

        $this->register_property('anchor');
        $this->register_property('name');
        $this->register_property('tag');

        $this->set_property_mandatory('slot');
        $this->set_property_mandatory('class');
        $this->set_property_mandatory('source');
    }
}

/**
 * A renderer.
 */
class idg_renderer extends idg_leafnode {

    function __construct() {
        parent::__construct();

        $this->set_idg_type = 'renderer';
    }

    /**
     * Produces instance of a renderer object if possible.
     * If the <code>nullable</code> property of the renderer is set this
     * can fail without producing an error.
     * @param idg_datasource $datasource The renderers datasource, if there is one
     * @return object|null The renderer
     */
    function create_instance(idg_datasource $datasource = null): ?object {
        if (!$class_name = @$this->get_property('class'))
            idg_diag($this, "$obj_name: 'class' property missing");

        $nullable = $this->get_property('nullable') == "yes";

        if (class_exists($class_name)) {
            $object = new $class_name($this);
            $object->datasource = $datasource;
            $object->anchor = $this->get_property('anchor');

            return $object;
        }

        if (!$nullable)
            idg_diag($this, "unknown class '$class_name'");

        return null;
    }

    /**
     * Produces a token meant for the site's built-in datasource.
     * @param idg_datasource $datasource The datasource
     * @return <code>true</code>
     */
    function _get_sitemap(idg_datasource_implementation $datasource): bool {
        if (($anchor = $this->get_property('anchor'))) {
            $token = [];
            $token['type'] = 'anchor';
            $token['anchor'] = $anchor;
            $token['name'] = $this->get_property('name');

            $datasource->add_token($token);
        }

        return true;
    }
}

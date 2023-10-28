<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://openslot.org/license/mit/
 */

/**
 * Type information for idg_container.
 */
class idg_container_type extends idg_view_element_type {

    function __construct() {
        parent::__construct();

        $this->child_types = [
            'idg_container',
            'idg_fragment',
            'idg_slot'
        ];

        $this->register_property('filter');
        $this->register_property('anchor');
    }
}

/**
 * A container can contain containers as well as other elements.
 */
class idg_container extends idg_view_element {

    function __construct() {
        parent::__construct();
        $this->set_element_name('container');
    }

    function _render(idg_document $document, idg_view $view): void {

        $idg_id = $this->get_idg_id();

        $css = '';

        foreach (idg_view_element_type::CSS_PROPERTIES as $property => $style)
            if (($value = $this->get_property($property)))
                $css .= "	div#$idg_id {\n		$value\n	}\n\n";

        $view->stream_append('css', $css);

        if ($style_print = $this->get_property('style-print')) {
            $css_print = "div#$idg_id { $style_print }\n";
            $view->stream_append('css-print', $css_print);
        }

        if ($css != '' || $css_print != '')
            $view->stream_append('html-body', "  <div id=\"$idg_id\">\n");

        $filter = $this->get_property('filter');
        if ($filter)
            $view->add_filter($filter);

        if (($children = $this->get_children()))
            foreach ($children as $child)
                $child->_render($document, $view);

        if ($filter)
            $view->enable_filter($filter, false);

        if ($css != '' || $css_print != '')
            $view->stream_append('html-body', "  </div>\n");
    }
}

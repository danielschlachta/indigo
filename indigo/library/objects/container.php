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
            'idg_slot',
            'idg_filter'
        ];

        $this->register_property('anchor');
    }
}

/**
 * A container can contain containers as well as other elements.
 */
class idg_container extends idg_view_element {

    function __construct() {
        parent::__construct('container');
    }

    function _render(idg_document $document, idg_view $view): void {
        $idg_id = $this->get_idg_id();

        if (($anchor = $this->get_property('anchor')))
            $view->stream_append('html-body', "<span id=\"$anchor\"></span>");
        
        if (($has_css = $view->render_css($this, "div#$idg_id")))
            $view->stream_append('html-body', "<div id=\"$idg_id\">\n");

        if (($children = $this->get_children())) {
            foreach ($children as $child)
                if ($child instanceof idg_filter)
                    $child->_apply_filter($view);

            foreach ($children as $child)
                if (!($child instanceof idg_filter))
                    $child->_render($document, $view);

            foreach ($children as $child)
                if ($child instanceof idg_filter)
                    $view->remove_filter($child->get_property('name'));
        }

        if ($has_css)
            $view->stream_append('html-body', "</div>\n");
    }
}

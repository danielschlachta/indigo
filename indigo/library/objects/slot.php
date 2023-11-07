<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://openslot.org/license/mit/
 */

/**
 * Type information for idg_slot.
 */
class idg_slot_type extends idg_view_element_type {

    function __construct() {
        parent::__construct();

        $this->child_types = [
            'idg_filter'
        ];
        $this->set_property_mandatory('name');
    }
}

/**
 * A slot is where the output produced by a renderer has its place inside the view.
 */
class idg_slot extends idg_view_element {

    function __construct() {
        parent::__construct('slot');
    }

    function _render(idg_document $document, idg_view $view): void {
        if (($children = $this->get_children()))
            foreach ($children as $child)
                if ($child->get_element_name() == 'filter')
                    $child->_apply_filter($view);

        $idg_id = $this->get_idg_id();
        $name = $this->get_property('name');

        $view->render_css($this, "div#$idg_id");
        $view->stream_append('html-body', "<div id=\"$idg_id\">\n");


        if (($renderers = $document->get_renderers($name)))
        foreach ($renderers as $renderer) {
            if (($anchor = $renderer->get_anchor())) 
                $view->stream_append('html-body', "<span id=\"$anchor\"></span>");
                
            $renderer->_render($document, $view);
        }

        $view->stream_append('html-body', "</div>\n");

        if ($children)
            foreach ($children as $child)
                if ($child->get_element_name() == 'filter')
                    $view->remove_filter($child->get_property('name'));
    }
}

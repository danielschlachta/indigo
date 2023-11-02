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

        $this->child_types = [];
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
        $idg_id = $this->get_idg_id();
        $name = $this->get_property('name');

        $renderers = $document->get_renderers($name);

        foreach ($renderers as $renderer)
            if (($anchor = $renderer->get_property('anchor')))
                $has_anchors = true;

        $view->render_css($this, "div#$idg_id");
        $view->stream_append('html-body', "<div id=\"$idg_id\">\n");

        foreach ($renderers as $renderer)
            $renderer->_render($document, $view);

        $view->stream_append('html-body', "</div>\n");
    }
}

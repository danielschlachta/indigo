<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\View;

/**
 * A simple one with the scroll bar always visible
 */
class simple extends \idg_view_implementation {

    function _render(\idg_document $document, \idg_view $view): void {

        if (!($children = $this->get_children()))
            return;

        $idg_id = $this->get_idg_id();
        $style_body = $view->get_property('style');

        $this->stream_append('css', "body { padding: 0; margin: 0; "
            . "width: 100%; height: 100%; "
            . "overflow-x: hidden; $style_body }\n"
            . "div#$idg_id { position: relative; top: 0; left: 0; "
            . "overflow-y: hidden; }\n");

        $this->stream_append('html-body', "<div id=\"$idg_id\">\n");

        parent::_render($document, $view);
        
        $this->stream_append('html-body', "</div>\n");
    }
}

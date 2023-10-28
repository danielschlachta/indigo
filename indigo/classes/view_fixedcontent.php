<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\View;

/**
 * A simple one with the scroll bar always visible
 */
class fixedcontent extends \idg_view {

    function _render(\idg_document $document): void {
        $style = $this->get_property('style');
        $style_print = $this->get_property('style-print');

        print_r($this->get_children());
        die('');

        $children = $this->get_children();

        if (!$children || count($children) < 1)
            diag($this, 'template must have at least one child');

        $idg_id = $this->get_idg_id();
        $fixed = $children[0];

        $css = "	body { padding: 0; margin: 0; "
            . "width: 100%; height: 100%; "
            . "overflow-x: hidden; $style }\n"
            . "div#$idg_id { position: relative; top: 0; left: 0; "
            . "z-index: 130; }\n";

        $css_print = "div#$idg_id { overflow-y: hidden; $style_print }\n";

        $this->stream_append('css', $css);
        $this->stream_append('css-print', $css_print);

        $body = "<div id=\"$idg_id\">\n";
        $this->stream_append('html-body', $body);

        $fixed->_render($document, $view);

        $body = "</div>\n";
        $this->stream_append('html-body', $body);

        for ($i = 1; $i < count($children); $i++)
            $children[$i]->_render($document, $view);
    }
}

?>

<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Fancy */

namespace Indigo\Design\Fancy;

class header extends \idg_fragment_implementation {

    function _render(&$document, &$view) {
        $image = '../docs/views/fancy/caption.png';
        $idg_id = $this->get_idg_id();

        $css = "#caption { margin: 0; padding: 0.1em; }\n";

        if (@$image)
            $css .= "div#$idg_id { margin: 0.5em;"
                . " background-image: url($image);"
                . " background-repeat: no-repeat;"
                . " background-position: right center; }\n";

        $view->stream_append('css', $css);
        
        $view->render_css($this->get_declaration(), 'header');

        $caption = $document->get_property('description');
        $view->stream_append('html-body', "<div id=\"$idg_id\">"
            . "<h1 id=\"caption\">$caption</h1></div>");
    }
}

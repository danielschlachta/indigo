<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Fancy */

namespace Indigo\Design\Fancy;

class footer extends \idg_fragment_implementation {

    function _render(&$document, &$view) {
        $image = '../docs/views/fancy/elements/postmark.png';

        $css = "footer { height: 4em; padding: 1.6em 15px 1.6em 8em; ";

        if ($image) {
            $css .= "background-image: url($image);"
                . " background-position: left center;"
                . " background-repeat: no-repeat;"
                . " text-align: right; ";
        }

        $css .= "}\n";

        $view->stream_append('css', $css);
        $view->render_css($this->get_declaration(), "footer");

        if (($text = $this->get_declaration()->get_text()))
            $view->stream_append('html-body', $text);
    }
}

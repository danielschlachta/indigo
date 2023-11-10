<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Blocks */

namespace Indigo\Design\Blocks;

/**
 * Displays an image in a frame.
 * Looks for attribute <code>blocks::image</code>, which can have the following options:
 *  - `src`
 * 
 *      The URL of the image to display, must be specified.
 * 
 *  - `top`, `left`
 * 
 *      The position in pixels, without the <code>px</code>, defaults to <code>0</code>.
 */
class imageframe extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view): void {
        $idg_id = $this->get_idg_id();

        $attribute = $this->fetch_attribute('blocks::image', $document);

        $src = $attribute->get_parameter('src');
        $top = $attribute->get_parameter('top', 0);
        $left = $attribute->get_parameter('left', 0);

        $width = $attribute->get_parameter('width', 90);
        $height = $attribute->get_parameter('height', 90);

        $imgtop = round((110 - $height) / 2) + 6 + $top;
        $imgleft = round((110 - $width) / 2) + 6 + $left;

        $bg_col = '#8a94b6';
        $bo_col = '#23314f';

        $view->stream_append('html-body',
            "<div id=\"$idg_id-frame\"></div>\n"
            . "<div id=\"$idg_id-image\">"
            . "<img src=\"$src\" width=\"$width\" height=\"$height\" alt=\"\"></div>\n");

        $view->stream_append('css',
            "div#$idg_id-frame { position: fixed; top: {$top}px; left: {$left}px; "
            . "background: $bg_col; border: 1px solid $bo_col; width: 120px; "
            . "height: 120px; opacity:0.5; }\n"
            . "div#$idg_id-image { position: fixed;"
            . " top: {$imgtop}px; left: {$imgleft}px; }\n"
            . "div#$idg_id-image img { opacity: 0.8; }\n");

        $view->stream_append('css-print',
            "div#$idg_id-frame { display: none; }\n"
            . "div#$idg_id-image { display: none; }\n");
    }
}

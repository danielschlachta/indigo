<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Design\Blocks;

/**
 * Displays an image in a frame.
 * Looks for attribute <code>blocks::image</code>, which can have the following options:
 * @param src The <code>img src</code> part, mandatory
 * @param top, left The position in pixels, default to 0
 */

class imageframe extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view): void {
        $idg_id = $this->get_idg_id();

        $attr = $this->fetch_attribute('blocks::image', $document);
        
        $img = $attr->get_parameter('src');
        $top = $attr->get_parameter('top', 0);
        $left = $attr->get_parameter('left', 0);
        
        $width = $attr->get_parameter('width', 90);
        $height = $attr->get_parameter('height', 90);
        
        $imgtop = round((110 - $height) / 2) + 6 + $top;
        $imgleft = round((110 - $width) / 2) + 6 + $left;
      
        $bg_col = '#8a94b6';
        $bo_col = '#23314f';

        $body = "<div id=\"$idg_id-frame\"></div>\n"
            . "<div id=\"$idg_id-image\">"
            . "<img src=\"$img\" width=\"$width\" height=\"$height\" alt=\"\"></div>\n";

        $view->stream_append('html-body', $body);

        $css = "div#$idg_id-frame { position: fixed; top: ${top}px; left: ${left}px; "
            . "background: $bg_col; border: 1px solid $bo_col; width: 120px; "
            . "height: 120px; opacity:0.5;  }\n"
            . "div#$idg_id-image { position: fixed;"
            . " top: ${imgtop}px; left: ${imgleft}px; }\n"
            . "div#$idg_id-image img { opacity: 0.8; }\n";

        $view->stream_append('css', $css);

        $css_print = "div#$idg_id-frame { display: none; }\n"
            . "div#$idg_id-image { display: none; }\n";

        $view->stream_append('css-print', $css_print);
    }
}

?>
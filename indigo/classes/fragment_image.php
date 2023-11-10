<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Fragment;

class image extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view) {
        if (!($img_src = $this->get_parameter('src')))
            return;

        $width = $this->get_parameter('width');
        $height = $this->get_parameter('height');
        $alt_text = $this->get_parameter('alt');

        $idg_id = $this->get_idg_id();
        $style = $this->get_property('style');
        $width = "	width: $width;";
        $height = $height ? " height: $height;\n" : "";
        $alt_text = $alt_text ? " alt=\"$alt_text\"" : "";

        $view->stream_append('html-body', "<img id=\"$idg_id\" src=\"$img_src\""
            . "width=\"$width\" height=\"$height\"$alt_text>\n");
        $view->stream_append('css', "img#$idg_id { $style }");
    }
}

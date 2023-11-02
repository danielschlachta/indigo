<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Design\Blocks;

class footer extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view): void {
        $idg_id = $this->get_idg_id();
        $tag = $this->get_property('tag');
        $style = $this->get_property('style');
        $last_change = substr($document->get_property('last-change'), 0, 10);
  
        $css = "div#$idg_id-content { background: #b4d2b0; "
            . "text-align: right; $style }\n"
            . "div#$idg_id-content p { margin: 0.3em 0 0 0; }\n"
            . "span#$idg_id-top { float: left; position: relative;"
            . " top: 0; left: 0; }\n";

        $style = \idg_view::CSS_PROPERTIES;
        unset($style['style']);
        $view->render_css($this->get_declaration(), "div#$idg_id-content", $style);
        
        $url = $document->get_site()->get_absolute_url();

        $body = "<div id=\"$idg_id-content\">\n"
            . "Last change: $last_change\n"
            . "<p>\n<span id=\"$idg_id-top\"><a href=\"#top\">"
			. "top</a></span>\n"
            . " $tag\n"
            . "</p>\n</div>\n";

        $css_print =  "div#$idg_id-content { padding: 0; }\n"
            . "div#$idg_id-content span { display: none; }\n";

        $view->stream_append('css', $css);
        $view->stream_append('css-print', $css_print);
        $view->stream_append('html-body', $body);
    }
}

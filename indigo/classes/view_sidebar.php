<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\View;

/**
 * A fixed div to the left or right.
 *
 * Takes at least two containers: one is the fixed sidebar (either left or
 * right), the other is the scrollable content. If a third container is present
 * it will occupy the content area behind the second one.
 */
class sidebar extends \idg_view_implementation {

    function _render(\idg_document $document, \idg_view $view): void {
        if (!($children =  $this->get_children()) || count($children) < 2)
            idg_diag($this, 'must have two or three children');

        $fixed = $children[0];

        $idg_id = $this->get_idg_id();
        $fixed_id = $idg_id . '-fixed';

        $style_body = $this->get_property('style');
       
        $style = \idg_view::CSS_PROPERTIES;
        unset($style['style']);
        $view->render_css($this->get_declaration(), '', $style);
        
        $this->stream_append('css', "body { padding: 0; margin: 0; width: 100%;"
            . " overflow-x: hidden; $style_body }\n"
            . "div#$fixed_id { overflow: hidden;"
            . " position: fixed; top: 0; left: 0;"
            . " height: 100%; width: 100px;"
            . "overflow: hidden; }\n"
            . "div#$idg_id { overflow-y: hidden;"
            . " margin-left: 100px; }\n");
        
        $view->render_css($fixed, "div#$fixed_id");
        
        $this->stream_append('html-body', "<div id=\"$fixed_id\">\n");
        $fixed->_render($document, $view);
        $this->stream_append('html-body', "</div>\n");

        $this->stream_append('html-body', "<div id=\"$idg_id\">\n");
        
        for ($i = 1; $i < count($children); $i++) {
            $main = $children[$i];
            
            //$view->render_css($main, "div#$idg_id-$i");
            
            $this->stream_append('html-body', "<div id=\"$idg_id-$i\">\n");
            $main->_render($document, $view);
            $this->stream_append('html-body', "</div>\n");
        }
        
        $this->stream_append('html-body', "</div>\n");
    }
}


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

    function _render(\idg_document $document): void {
        $view->render_css($this->get_declaration());
        
        $children = $this->get_children();
        $style = $this->get_property('style');
        
        if (!$children || count($children) < 2)
            idg_diag($this, 'must have two or three children');

        $fixed = $children[0];
        $fixed_id = $fixed->get_idg_id() . '-part';
        $style_fixed = $fixed->get_property('style');

        $main = $children[1];
        $main_id = $main->get_idg_id() . '-part';
        $style_main = $main->get_property('style');

        if ($bg = $children[2]) {
            $bg_id = $bg->get_idg_id() . '-part';
            $style_bg = $bg->get_property('style');
        }

        $fixed_width = $this->parameters['fixed-width'];
        $fixed_position = $this->parameters['fixed-position'];
        $attach_right = ($fixed_position == 'right');

        /**
         * @todo We need a mechanism for this.
         */
        $style = $this->get_property('style');

        $css = "	body { padding: 0; margin: 0; width: 100%; "
            . "overflow-x: hidden; $style; }\n"
            . "div#$fixed_id { overflow: hidden; "
            . "position: fixed; top: 0; $fixed_position: 0; "
            . "height: 100%; width: $fixed_width; "
            . "overflow: hidden; $style_fixed }\n"
            . "div#$main_id { overflow-y: hidden;"
            . " margin-$fixed_position: $fixed_width; $style_main }\n";

        if (@$style_bg)
            $css .= "div#$bg_id { $style_bg }\n";

        $this->stream_append('css', $css);

        if (@$bg) {
            $body = "<div id=\"$bg_id\">\n";

            $this->stream_append('html-body', $body);
            $bg->_render($document, $view);

            $body = "</div>\n";
        } else
            $body = '';

        $body .= "<div id=\"$fixed_id\">\n";

        $this->stream_append('html-body', $body);
        $fixed->_render($document, $view);

        $body = "</div>\n";
        $body .= "<div id=\"$main_id\">\n";

        $this->stream_append('html-body', $body);
        $main->_render($document, $view);

        $body = "</div>\n";
        $this->stream_append('html-body', $body);
    }
}


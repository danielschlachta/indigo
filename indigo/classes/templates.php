<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: parts.php - basic layouts
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

/**
 * A fixed div to the left or right.
 *
 * Takes at least two containers: one is the fixed sidebar (either left or
 * right), the other is the scrollable content. If a third container is present
 * it will occupy the content area behind the second one.
 */
class idg_view_fixedbar extends idg_view {

    function _render(idg_document $document): void {
        if ($this->get_child_count() < 2 ||
            $this->get_child_count() > 3)
            diag($this, get_class($this) . ' must have two or three children');

        $fixed = $this->get_child(0);
        $fixed_id = $fixed->get_idg_id() . '-part';
        $style_fixed = $fixed->get_property('style');

        $main = $this->get_child(1);
        $main_id = $main->get_idg_id() . '-part';
        $style_main = $main->get_property('style');

        if ($bg = $this->get_child(2)) {
            $bg_id = $bg->get_idg_id() . '-part';
            $style_bg = $bg->get_property('style');
        }

        $fixed_width = $this->parameters['fixed-width'];
        $fixed_position = $this->parameters['fixed-position'];
        $attach_right = ($fixed_position == 'right');

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

/**
 * A simple one with the scroll bar always visible
 */
class idg_view_fixedcontent extends idg_view {

    function _render(idg_document $document): void {
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

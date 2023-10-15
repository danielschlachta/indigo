<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

class idg_view_html_renderer_blocks_footer extends idg_tree_node_instance {

    function __construct(&$parent) {
        parent::__construct($parent);
    }

    function render(&$document, &$view) {
        global $font_blocks;
        global $elements;

        $idg_id = $this->get_idg_id();
        $tag = $this->get_property('tag');
        $style = $this->get_property('style');
        $last_change = substr($document->get_property('last-change'), 0, 10);
        $style_heading = $this->get_property('style-heading');
        $style_link = $this->get_property('style-link');
        $style_link_hover = $this->get_property('style-link-hover');
        $style_image = $this->get_property('style-image');

        $css = "div#$idg_id-content { background: #b4d2b0; "
            . "text-align: right; $style }\n"
            . "div#$idg_id-content p { margin: 0.3em 0 0 0; }\n"
            . "span#$idg_id-top { float: left; position: relative;"
            . " top: 0; left: 0; z-index: 220; }\n";

        if ($style_heading)
            $css .= "div#$idg_id-content h1 { $style_heading }\n";
        if ($style_link)
            $css .= "div#$idg_id-content a { $style_link }\n";
        if ($style_link_hover)
            $css .= "div#$idg_id-content a:hover { $style_link_hover }\n";
        if ($style_image)
            $css .= "div#$idg_id-content img { $style_image }\n";

        $url = $document->get_site()->get_site_url();

        $body = "<div id=\"$idg_id-content\">\n"
            . "Last change: $last_change\n"
            . "<p>\n<span id=\"$idg_id-top\"><a href=\"#top\">"
			. "top</a></span>\n"
            . " $tag |"
            . " <a href=\"https://validator.w3.org/nu/?doc=$url\">"
			. "HTML5</a>\n"
            . "</p>\n</div>\n";

        $css_print =  "div#$idg_id-content { padding: 0; }\n"
            . "div#$idg_id-content span { display: none; }\n";

        $view->stream_append('css', $css);
        $view->stream_append('css-print', $css_print);
        $view->stream_append('html-body', $body);
    }
}

?>
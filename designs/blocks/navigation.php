<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

class idg_view_html_renderer_blocks_navigation extends idg_tree_node_implementation {

    function __construct(&$parent) {
        parent::__construct($parent);
    }

    function render(&$document, &$view) {
        global $font_blocks;
        global $elements;

        $idg_id = $this->get_idg_id();
        $tag = $this->get_property('tag');

        $doc_path = $document->get_path();

        $this->datasource->rewind();

        $body = '';

        while ($node = $this->datasource->get_token()) {
            if (($node['type'] == 'folder') && (@$node['id'] == $this->tag)) {
                $name = @$node['name'];
                $body .= "<div id=\"$idg_id-box\">\n"
                    . "<div id=\"$idg_id-top\"></div>\n"
                    . "<div id=\"$idg_id-body\">\n"
                    . "<div id=\"$idg_id-content\">\n"
                    . "<h2>$name</h2>\n<ul id=\"$idg_id-navigation\">\n";
                $is_first = true;
                $close_doc = false;
                while ($node = $this->datasource->get_token()) {
                    if ($node['type'] == 'folder')
                        break;

                    if ($node['type'] == 'document') {
                        if (!$is_first)
                            $body .= "\n  </ul>\n";
                        if ($close_doc)
                            $body .= "  </li>\n";
                        $close_doc = true;
                        $name = $node['name'];
                        $path = $node['path'];
                        $url = $node['url'];
                        if ($path == $doc_path)
                            $body .= "  <li id=\"current\">"
                                . "<div><a href=\"#top\">$name</a></div>";
                        else
                            $body .= "  <li><div><a href=\"$url\">$name</a></div>";
                        $is_first = true;
                    } else if ($node['type'] == 'anchor') {
                        if ($is_first) {
                            $body .= "\n    <ul>\n";
                            $is_first = false;
                        }
                        $anchor = $node['anchor'];
                        $name = $node['name'];
                        $body .= "      <li><a href=\"$url#$anchor\">$name</a></li>\n";
                    }
                }
                if (!$is_first)
                    $body .= "\n</ul>\n";
                if ($close_doc)
                    $body .= "</li>";
                break;
            }
        }

        if ($body == '')
            return;

        $body .= "\n</ul>\n</div>\n</div>\n"
            . "<div id=\"$idg_id-bottom\"></div>\n</div>\n";

        $fg_col = '#23314f';
        $hi_col = '#414d67';
        $bg_col = '#9095c6';
        $ct_col = '#a7aac6';
        $ac_col = '#9ca0c6';

        $style = @$this->get_property('style');

        $css = "div#$idg_id-box { width: 248px; font-size: 63%;"
            . " $font_blocks color: $fg_col; z-index: 210; $style }\n"
            . "div#$idg_id-top { width: 250px; height: 16px;"
            . " background: url($elements/navigation/nav_top.png)"
            . " no-repeat; background-position: bottom left; }\n"
            . "div#$idg_id-body { width: 100%; background: $bg_col;"
            . " border: 1px solid $fg_col; border-width: 0px 1px 0px 1px; }\n"
            . "div#$idg_id-content { width: 218px; padding: 1px 5px 1px 5px;"
            . " margin: 0 10px 0 10px; background: $ct_col "
            . " url($elements/navigation/compass.png) no-repeat;"
            . " background-position: top right; }\n"
            . "div#$idg_id-content h2 { font-size: 100%; font-weight: bold;"
            . " padding: 0 0 0 20px; margin: 0;"
            . " background: url($elements/navigation/arrow_right.png)"
            . " left center no-repeat; opacity: 0.8; }\n"
            . "div#$idg_id-content ul { margin: 0.5em 0 0 0;"
            . " padding: 0 4px 0 2px; list-style: none; }\n"
            . "div#$idg_id-content li { width: 100%;"
            . " padding: 2px 0 1px 0; margin: 0; clear: left; }\n"
            . "div#$idg_id-content li div { font-size: 120%;"
            . " background: url($elements/navigation/link.png) no-repeat;"
            . " background-position: left center; opacity: 0.8; }\n"
            . "div#$idg_id-content li:hover div,"
            . " div#$idg_id-content li.over div { padding: 1px 0 2px 1px;"
            . " background: $bg_col url($elements/navigation/link_h.png)"
            . " no-repeat; background-position: left center; opacity: 0.8; }\n"
            . "div#$idg_id-content li#current div {"
            . " background: url($elements/navigation/link_c.png)"
            . " no-repeat; background-position: left center; opacity: 0.8; }\n"
            . "div#$idg_id-content li#current:hover div,"
            . " div#$idg_id-content li#current.over div {"
            . " padding: 1px 0 2px 1px; "
            . " background: $bg_col url($elements/navigation/link_h.png)"
            . " no-repeat; background-position: left center; opacity: 0.8; }\n"
            . "div#$idg_id-content a { display: block; padding-left: 18px; "
            . "text-decoration: none; color: $fg_col; }\n"
            . "div#$idg_id-content li#current a { display: block; "
            . "padding-left: 18px; color: $hi_col; }\n"
            . "div#$idg_id-content li#current a:hover {"
            . " padding-left: 18px; color: $fg_col; }\n"
            . "div#$idg_id-content li ul { display: none; margin: 0;"
            . " padding: 0; font-size: 110%; }\n"
            . "div#$idg_id-content li:hover ul,"
            . " div#$idg_id-content li.over ul { display: block; }\n"
            . "div#$idg_id-content li:hover ul a { color: $hi_col;"
            . " padding-left: 19px; padding-bottom: 2px; }\n"
            . "div#$idg_id-content li.over ul a { width: 100%; color: $hi_col;"
            . " padding-left: 19px; padding-bottom: 2px; }\n"
            . "div#$idg_id-content li:hover ul a:hover,"
            . " div#$idg_id-content li.over ul a:hover { color: $fg_col;"
            . " background: $ac_col url($elements/navigation/link_b.png)"
            . " no-repeat; background-position: left center; opacity: 0.8; }\n"
            . "div#$idg_id-bottom { width: 250px; height: 16px;"
            . " background: url($elements/navigation/nav_bottom.png)"
            . " no-repeat; background-position: top right; }\n";

        $css_print = "div#$idg_id-box { display: none }\n";

        $view->stream_append('css', $css);
        $view->stream_append('css-print', $css_print);
        $view->stream_append('html-body', $body);
    }
}

?>
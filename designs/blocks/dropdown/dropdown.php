<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Blocks */

namespace Indigo\Design\Blocks;

/**
 * A navigation box with drop down capabilities for anchors.
 * Accepts the <code>style</code> property for setting the font.
 */
class dropdown extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view): void {
        $elements = $view->template()->get_uri('dropdown/elements');

        if (!$source = $this->get_property('source'))
            idg_diag($this, "property 'source' not set");
        
        $datasource = $document->get_datasource($source);
        
        $idg_id = $this->get_idg_id();
        $tag = $this->get_property('tag');

        $attribute = $this->fetch_attribute('blocks::dropdown', $document);
        $bg_url = $attribute->get_parameter('bg-url');

        $doc_path = $document->get_path();

        $body = '';

        while ($datasource->valid()) {
            $token = $datasource->current();
            $datasource->next();

            if (($token['type'] == 'folder') && (@$token['id'] == $tag)) {
                $name = @$token['name'];
                $body .= "<div id=\"$idg_id-box\">\n"
                    . "<div id=\"$idg_id-top\"></div>\n"
                    . "<div id=\"$idg_id-body\">\n"
                    . "<div id=\"$idg_id-content\">\n"
                    . "<h2>$name</h2>\n<ul id=\"$idg_id-dropdown\">\n";
                $is_first = true;
                $close_doc = false;

                while ($datasource->valid()) {
                    $token = $datasource->current();
                    $datasource->next();
                    

                    if ($token['type'] == 'folder')
                        break;

                    if ($token['type'] == 'document') {
                        if (!$is_first)
                            $body .= "\n  </ul>\n";
                        if ($close_doc)
                            $body .= "  </li>\n";
                        $close_doc = true;
                        $name = $token['name'];
                        $path = $token['path'];
                        $uri = $view->template()->get_full_uri($path);
                        
                        if ($path == $doc_path)
                            $body .= "  <li id=\"current\">"
                                . "<div><a href=\"#top\">$name</a></div>";
                        else
                            $body .= "  <li><div><a href=\"$uri\">$name</a></div>";
                        $is_first = true;
                    } else if ($token['type'] == 'anchor') {
                        if ($is_first) {
                            $body .= "\n    <ul>\n";
                            $is_first = false;
                        }
                        $anchor = $token['anchor'];
                        $name = $token['name'];
                        $body .= "      <li><a href=\"?display=$path#$anchor\">$name</a></li>\n";
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
            . " color: $fg_col; $style }\n"
            . "div#$idg_id-top { width: 250px; height: 16px;"
            . " background: url($elements/nav_top.png)"
            . " no-repeat; background-position: bottom left; }\n"
            . "div#$idg_id-body { width: 100%; background: $bg_col;"
            . " border: 1px solid $fg_col; border-width: 0px 1px 0px 1px; }\n"
            . "div#$idg_id-content { width: 218px; padding: 1px 5px 1px 5px;"
            . " margin: 0 10px 0 10px; background: $ct_col";

        if ($bg_url)
            $css .= " url($bg_url) no-repeat;"
                . " background-position: top right;";
        else
            $css .= ';';

        $css .= " }\n"
            . "div#$idg_id-content h2 { font-size: 110%; font-weight: bold;"
            . " padding: 0.3em 0 0 20px; margin: 0;"
            . " background: url($elements/arrow_right.png)"
            . " left center no-repeat; opacity: 0.8; }\n"
            . "div#$idg_id-content ul { margin: 0.5em 0 0 0;"
            . " padding: 0 4px 0 2px; list-style: none; }\n"
            . "div#$idg_id-content li { width: 100%;"
            . " padding: 2px 0 1px 0; margin: 0; clear: left; }\n"
            . "div#$idg_id-content li div { font-size: 120%;"
            . " background: url($elements/link.png) no-repeat;"
            . " background-position: left center; opacity: 0.8; }\n"
            . "div#$idg_id-content li:hover div,"
            . " div#$idg_id-content li.over div { padding: 1px 0 2px 1px;"
            . " background: $bg_col url($elements/link_h.png)"
            . " no-repeat; background-position: left center; opacity: 0.8; }\n"
            . "div#$idg_id-content li#current div {"
            . " background: url($elements/link_c.png)"
            . " no-repeat; background-position: left center; opacity: 0.8; }\n"
            . "div#$idg_id-content li#current:hover div,"
            . " div#$idg_id-content li#current.over div {"
            . " padding: 1px 0 2px 1px; "
            . " background: $bg_col url($elements/link_h.png)"
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
            . " background: $ac_col url($elements/link_b.png)"
            . " no-repeat; background-position: left center; opacity: 0.8; }\n"
            . "div#$idg_id-bottom { width: 250px; height: 16px;"
            . " background: url($elements/nav_bottom.png)"
            . " no-repeat; background-position: top right; }\n";

        $css_print = "div#$idg_id-box { display: none }\n";

        $view->stream_append('css', $css);
        $view->stream_append('css-print', $css_print);
        $view->stream_append('html-body', $body);
    }
}

<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Minimal */

namespace Indigo\Design\Minimal;

/**
 * The navigation - use only in conjunction with footer!
 */
class navigation extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view) {
        $datasource = $this->get_datasource();
        $doc_title = $document->get_property('name');

        $attribute = $this->fetch_attribute('minimal::navigation', $document);
        $image = $attribute->get_parameter('image');
        $title = $attribute->get_parameter('title');
        $subtitle = $attribute->get_parameter('subtitle');

        $view->stream_append('html-body',
            "<div id=\"Logo\"><img src=\"$image\" alt=\"\"></div>\n"
            . "<div id=\"Header\">\n"
            . "<div id=\"HeaderBar\"><div id=\"HeaderText\"><i>$title" .
            " &ndash; <b>$doc_title</b></i>" . "</div></div>\n"
            . "<div id=\"HeaderNav\">\n<div id=\"HeaderBtn\">\n");

        $body = '';

        foreach ($datasource as $count => $node)
            if ($node['type'] == 'document') {
                $name = $node['name'];
                $url = $view->template()->get_full_uri($node['path']);
                $body .= "<a href=\"$url\">&middot; $name</a>\n";
            }

        $view->stream_append('html-body', $body
            . "</div>\n<div id=\"HeaderNavTxt\">$subtitle</div>\n"
            . "</div>\n</div>\n<div id=\"TextBody\">\n");
    }
}

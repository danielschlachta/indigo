<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Gradient */

namespace Indigo\Design\Gradient;

class navigation extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view) {
        $elements = $view->template()->get_uri('elements');

        $style = $this->get_property('style');
        $idg_id = $this->get_idg_id();
        $datasource = $this->get_datasource();

        $view->stream_append('css',
            "div#$idg_id-img { background: #53538a url($elements/gradient.png)"
            . " bottom left fixed repeat-x; height: 100%; }\n"
            . "div#$idg_id { padding-top: 5px; padding-left: 2px; height: 100%;"
            . " font-size: 90%; }\n"
            . "div#$idg_id a { display: block; width: 98%;"
            . " padding: 10px 5px; margin: 0 0 2px; border-width: 0;"
            . " text-decoration: none; color: #c2c2c2; font-weight: bold; }\n"
            . "div#$idg_id a span { display: none; }\n"
            . "div#$idg_id a:hover { background: url($elements/bg_gradient.png) top left"
            . " fixed repeat-x; color: #53538a; }\n"
            . "div#$idg_id a:hover span { display: block; position: relative;"
            . " width: 100px; top: 0px; left: 0px; padding-top: 5px;"
            . " font-size: 80%; font-style: italic; font-weight: normal;"
            . " z-index: 100; }\n");

        $body = "<div id=\"$idg_id-img\">\n<div id=\"$idg_id\">\n";

        foreach ($datasource as $count => $node) {
            $path = @$node['path'];

            if ($path) {
                $name = $node['name'];
                $uri = $view->template()->get_full_uri($path);

                if (!($comment = @$node['navigation-comment']))
                    $comment = @$node['description'];
                if (!$comment)
                    $comment = @$node['name'];

                $body .= "<a href=\"$uri\">$name<span>$comment</span></a>\n";
            }
        }

        $body .= "</div>\n</div>\n";

        $view->stream_append('html-body', $body);
    }
}

?>

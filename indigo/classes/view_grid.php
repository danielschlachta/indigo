<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\View;

/**
 * A view using grid.
 * @todo docu
 */

class grid extends \idg_view_implementation {

    function _render(\idg_document $document, \idg_view $view): void {

        $idg_id = $this->get_idg_id();
   
        $nav = $view->get_child_by_key('name', 'nav');
        $header = $view->get_child_by_key('name', 'header');
        $aside = $view->get_child_by_key('name', 'aside');
        $article = $view->get_child_by_key('name', 'article');
        $footer = $view->get_child_by_key('name', 'footer');

        $css = "body { font-size: 100%; margin: 0; padding: 1em 0 1em 20px; }\n"
            . "*, *:before, *:after { box-sizing: border-box; }\n"
            . "div#$idg_id { max-width: 940px; width: 66%;"
            . " float: left;" 
            . " display: grid; grid-template-columns: min-content 1fr; grid-gap: 10px;"
            . " margin-bottom: 1em; }\n"
            . "@supports (display: grid) {\n"
            . " #$idg_id > * { width: auto; }\n"
            . "}\n";

        $view->stream_append('css', $css);

        $view->render_css($this->get_declaration());
        
        $view->stream_append('html-body', "<div id=\"$idg_id\">\n");

        // header
        if ($header) {
            $view->stream_append('css',
                "header { grid-column: 1 / -1; clear: both; float: right; "
                . " width: 79.7872%; }\n");

            $view->stream_append('html-body', "<header>\n");
            $header->_render($document, $view);
            $view->stream_append('html-body', "</header>\n");
        }

        // sidebar
        if ($aside) {
            $view->stream_append('css',
                "aside { float: left; width: 19.1489%; min-width: 1.5em; }\n");
        
            $view->stream_append('html-body', "<aside>\n");
            $aside->_render($document, $view);
            $view->stream_append('html-body', "</aside>\n");
        }

        // content
        $view->stream_append('css', "article { float: right; width: 79.7872%; }\n");
        
        $view->stream_append('html-body', "<article>\n");

        if ($article)
            $article->_render($document, $view);
        else
            $view->stream_append('html-body', 
                "<code>This page intentionally left blank.</code>\n");

        $view->stream_append('html-body', "</article>\n");

        // footer
        if ($footer) {
            $view->stream_append('css',
                "footer { float: right; width: 79.7872%;"
                . "	grid-column: 1 / -1; clear: both; }\n");

            $view->stream_append('html-body', "<footer>\n");
            $footer->_render($document, $view);
            $view->stream_append('html-body', "\n</footer>\n");
        }

        // navigation
        if ($nav) {
            $view->stream_append('css',
                "nav { position: fixed; top: 0; left: 0; }\n");
            $view->stream_append('css-print',
                "nav { display: none; }\n");

            $view->stream_append('html-body', "<nav>\n");
            $nav->_render($document, $view);
            $view->stream_append('html-body', "</nav>\n");
        }
    }
}

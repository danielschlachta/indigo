<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\View;

class grid extends \idg_view_implementation {

    function _render(\idg_document $document, \idg_view $view): void {
        
        $idg_id = $this->get_idg_id();
        $stylesheet = '../designs/fancy/default.css';

        $head = " <link rel=\"stylesheet\" href=\"$stylesheet\">\n";

        $view->stream_append('html-head', $head);

        $nav = $view->get_child_by_key('name', 'nav');
        $header = $view->get_child_by_key('name', 'header');    
        $aside = $view->get_child_by_key('name', 'aside');    
        $article = $view->get_child_by_key('name', 'article');
        $footer = $view->get_child_by_key('name', 'footer');
        
        //$idg_id = $view->idg_id;

        $css = "	body {\n"
            . "		font-family: Liberation Serif', sans-serif;\n"
            . "		font-size: 110%;\n"
            . "		margin: 0;\n"
            . "		padding: 0;\n"
            . "	}\n\n"
            . "	*, *:before, *:after {\n"
            . "		box-sizing: border-box;\n"
            . "	}\n\n"
            . "	div#$idg_id {\n"
            . "		max-width: 940px;\n"
            . "		width: 66%;\n"
            . "		margin: 2em;\n"
            . "		float: left;\n"
            . "		display: grid;\n"
            . "		grid-template-columns: min-content 1fr;\n"
            . "		grid-gap: 10px;\n"
            . "	}\n\n"
            . "	div#$idg_id > * {\n"
            . "		padding: 20px;\n"
            . "		margin-bottom: 10px;\n"
            . "		border-radius: 5px;\n"
            . "	}\n\n"
            . "	article {\n"
            . "		float: right;\n"
            . "		width: 79.7872%;\n"
            . "		padding-top: 0.9em;\n"
            . "		padding-bottom: 0.5em;\n"
            . "		padding-right: 1.5em;\n"
            . "	}\n\n"
            . "	footer {\n"
            . "		float: right;\n"
            . "		width: 79.7872%;\n"
			. "		grid-column: 1 / -1;\n"
			. "		clear: both;\n"
            . "	}\n\n";

        $view->stream_append('css', $css);

        
        
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
            $view->stream_append('html-body', "<aside>\n");
            $view->stream_append('css', "aside { float: left; width: 19.1489%; }\n");
            $aside->_render($document, $view);
             $view->stream_append('html-body', "</aside>\n");
        }
        
        // content
        $view->stream_append('html-body', "<article>\n");

        if ($article)
            $article->_render($document, $view);
        else
            $view->stream_append('html-body', "<code>This page intentionally left blank.</code>\n");

        $view->stream_append('html-body', "</article>\n");

        // footer
        if ($footer) {
            $view->stream_append('html-body', "<footer>\n");
            $footer->_render($document, $view);
            $view->stream_append('html-body', "\n</footer>\n");
        }

        // navigation
        if ($nav) {
            $view->stream_append('css-print', "nav { display: none; }\n");

            $view->stream_append('html-body', "	<nav>\n");
            $nav->_render($document, $view);
            $view->stream_append('html-body', "	</nav>\n");
        }

        $view->stream_append('css',
            "	@supports (display: grid) {\n"
            . "		#$idg_id > * {\n"
            . "			width: auto;\n"
            . "			margin: 0;\n"
            . "		}\n"
            . "	}\n\n");
        
        // page map
        \Indigo\Module\Pagemap\add_to_view($view,
            'bottom: 0', 'right: 0', '25%', '93%');
    }
}

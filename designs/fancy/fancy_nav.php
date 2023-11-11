<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Fancy */

namespace Indigo\Design\Fancy;

class nav extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view): void {
        $datasource = $this->get_datasource();
        
        $view->stream_append('css', 
            "body { margin-top: 2em; }\n"
            . "nav { padding: 1em 1em 0.5em 1em; margin: 0; top: -0.5em; left: 20px;" 
            . " font-family: 'Noto Serif'; }\n"
            . ".navlink, .navlink-selected { color: black; text-decoration: none;" 
            . " padding: 0.5em; }\n"
            . ".navlink:hover { text-decoration: underline; }\n"
            . ".navlink-selected { font-weight: bold; }\n");

        $view->render_css($this->get_declaration(), "nav");
        
        $document_path = $document->get_path();
        
        foreach ($datasource as $number => $node) {
            $name = $node['name'];
            if (!($path = @$node['path']))
                continue;
            
            $class = $path == $document_path ? 
                'navlink-selected' : 'navlink';
            
            /**
             * @todo This ugly hack must go!
             */
            
            $view->stream_append('html-body', 
            "<a class=\"$class\" href=\"?view=fancy&display=$path\">" 
            . " <span>$name</span></a>\n");
        }
    }
}

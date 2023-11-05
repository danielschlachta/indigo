<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Design\Fancy;

class fancy_navigation extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view): void {
        if (!$datasource = $this->get_datasource())
            return;

        $css = "	body {\n"
            . "	}\n\n"
            . "	nav {\n"
            . "		position: fixed;\n"
            . "		border-radius: 1em;\n"
            . "		top: 0; left: 0;\n"
            . "		height: 4em; width: 95%;\n"
            . "	}\n\n"
            . "	.navlink, .navlink-selected {\n"
            . "		color: black;\n"
            . "		padding: 0 1em 0 1em;\n"
            . "		text-decoration: none;\n"
            . "	}\n\n"
            . "	.navlink:hover {\n"
            . " 		text-decoration: underline;\n"
            . "	}\n\n"
            . "	.navlink-selected {\n"
            . "		font-weight: bold;\n"
            . "	}\n\n";

        $view->stream_append('css', $css);

        /** @todo FIXTHIS PRONTO * */
        $document_path = $document->get_path();

        foreach ($datasource as $number => $node) {
            $name = $node['name'];
            if (!($path = @$node['url']))
                continue;
            
            $class = str_replace('?display=', '', $path) == $document_path ? 
                'navlink-selected' : 'navlink';
            
            $view->stream_append('html-body', 
            "<a class=\"$class\" href=\"?design=fancy&display=$path\">" 
            . " <span>$name</span></a>\n");
        }
    }
}

?>

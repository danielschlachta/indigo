<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Fancy */

namespace Indigo\Design\Fancy;

class aside extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view): void {
        if (!($datasource = $this->get_datasource()))
            return;

        $doc_path = $document->get_path();
        $path = null;
        $body = '';

        foreach ($datasource as $count => $node) {
            if (@$node['path']) {
                $path = $node['path'];
                continue;
            }
            
            $anchor = @$node['anchor'];
            $name = @$node['name'];
            
            if ($path == $doc_path && $anchor) {
                $body .= "  <li><div><a href=\"#$anchor\">$name</a></div>\n";
            }
        }
        
        $view->render_css($this->get_declaration(), 'aside');    
        $view->stream_append('html-body', $body);
    }
}
    
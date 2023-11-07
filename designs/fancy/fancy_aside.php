<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Design\Fancy;

class fancy_aside extends \idg_fragment_implementation {

    function _render(&$document, &$view) {
        if (!($datasource = $this->get_datasource()))
            return;

        $doc_path = $document->get_path();
        $url = false;

        return;
        $body = "<ul>\n";

        foreach ($datasource as $count => $node) {
            if (($path = @$token['path'])) {
                if ($path != $doc_path)
                    continue;

                if ($node['type'] != 'anchor')
                    continue;

                $name = $node['name'];
                $anchor = $node['anchor'];

                $body .= "  <li><div><a href=\"$url#$anchor\">$name</a></div>\n";
            }

            $body .= "</ul>\n";

            if ($body != "<ul>\n</ul>\n")
                $view->stream_append('html-body', "$body\n");
        }
    }
}
    
<?php

namespace Indigo\Module\Markdown;

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

$parsedown_main = __DIR__ . '/parsedown/Parsedown.php';

if (file_exists($parsedown_main)) {
    require_once($parsedown_main);
} else {
    idg_complain_module('parsedown', $parsedown_main,
        'https://github.com/erusev/parsedown.git');
    exit;
}

/**
 * Support for .md files via parsedown.
 * Use datasource_textfile as data source.
 */
class renderer extends \idg_renderer_implementation {

    function _render(\idg_document $document, \idg_view $view): void {
        $datasource = $this->get_datasource();
        $Parsedown = new \Parsedown();

        $datasource->rewind();
 
        if (($content = $datasource->current())) {
            $view->stream_append('html-body', $Parsedown->text($content));
        
            $datasource->next();
            if (($last_change = $datasource->current()))
                $document->set_last_change($last_change);
        }
    }
}

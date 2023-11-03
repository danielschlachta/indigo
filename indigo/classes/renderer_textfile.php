<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Renderer;

class textfile extends \idg_renderer_implementation {

    function _render(\idg_document $document, \idg_view $view) {
        $datasource = $this->get_datasource();
        
        $datasource->rewind();
        
        $content = $datasource->current();
        $view->stream_append('html-body', $content);
        $datasource->next();
        $last_change = $datasource->current();
        $document->set_last_change($last_change);
    }
}

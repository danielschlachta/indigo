<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Renderer;

class text extends \idg_renderer_implementation {

    function _render(\idg_document $document, \idg_view $view) {
        $datasource = $this->get_datasource();
        
        $datasource->rewind();
        $content = $datasource->current();
        $view->stream_append('html-body', $content);
        //$document->set_last_change(time());
    }
}

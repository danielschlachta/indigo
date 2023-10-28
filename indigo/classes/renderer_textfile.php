<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Renderer;

class textfile extends \idg_renderer {

    function _render(\idg_document $document, \idg_view $view) {
        $this->datasource->rewind();
        $content = $this->datasource->get_token();
        $view->stream_append('html-body', $content);
        $last_change = $this->datasource->get_token();
        $document->set_last_change($last_change);
    }
}

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
    complain_module('parsedown', $parsedown_main,
        'https://github.com/erusev/parsedown.git');
    exit;
}


/**
 * Support for .md files via parsedown.
 * Use datasource_textfile as data source.
 */
class renderer extends \idg_renderer {

    function _render(idg_document $document, idg_view $view): void {
        $this->datasource->rewind();
        $content = $this->datasource->get_token();

        $Parsedown = new Parsedown();

        $view->stream_append('html-body', $Parsedown->text($content));
        $last_change = $this->datasource->get_token();
        $document->set_last_change($last_change);
    }
}

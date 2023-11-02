<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Fragment;

class text extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view) {
        $idg_id = $this->get_declaration()->get_idg_id();

        if (!$text = $this->get_declaration()->get_text())
            return;

        $vars = array();

        while (preg_match('/(\{[a-z0-9\-]+\})/', $text, $match)) {
            $name = substr($match[0], 1, strlen($match[0]) - 2);
            $vars[$name] = '';
            $text = preg_replace($match[0], '', $text);
        }

        $vars = $document->get_properties($vars);

        $text = "<span id=\"$idg_id\">" . $this->get_declaration()->get_text() . "</span>";
        
        foreach ($vars as $name => $value) {
            $text = preg_replace("/\{$name\}/", $value, $text);
        }

        $view->render_css($this->get_declaration(), "span#$idg_id");
        $view->stream_append('html-body', $text);
    }
}

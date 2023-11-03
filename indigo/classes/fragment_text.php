<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Fragment;

/**
 * Inserts the character data into a <code>&lt;span&gt;</code>, supports all styles.
 */
class text extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view) {
        $idg_id = $this->get_declaration()->get_idg_id();

        if (!$text = $this->get_declaration()->get_text())
            return;

        if (($properties = $document->get_properties()))
            foreach ($properties as $name => $value) {
                $text = preg_replace("/\{$name\}/", $value, $text);
            }

        $view->render_css($this->get_declaration(), "span#$idg_id");
        $view->stream_append('html-body', "<span id=\"$idg_id\">$text</span>");
    }
}

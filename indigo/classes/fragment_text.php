<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Fragment;

class text extends \idg_fragment_object {

    function _render(\idg_document $document, \idg_view $view) {
        $idg_id = $this->get_parent()->get_idg_id();

        if (!$text = $this->get_parent()->get_text())
            return;

        $css = '';

        if ($style = $this->get_parent()->get_property('style'))
            $css .= "	span#$idg_id {\n		$style\n	}\n\n";

        if ($style_link = $this->get_parent()->get_property('style-link'))
            $css .= "	span#$idg_id a {\n		$style_link\n	}\n\n";

        if ($style_link_hover = $this->get_parent()->get_property('style-link-hover'))
            $css .= "	span#$idg_id a:hover {\n		$style_link_hover\n	}\n\n";

        if ($css != '')
            $view->stream_append('css', $css);

        $vars = array();

        while (preg_match('/(\{[a-z0-9\-]+\})/', $text, $match)) {
            $name = substr($match[0], 1, strlen($match[0]) - 2);
            $vars[$name] = '';
            $text = preg_replace($match[0], '', $text);
        }

        $vars = $document->get_properties($vars);

        if ($css != '') {
            $text = "<span id=\"$idg_id\">"
                . $this->get_parent()->get_text() . "</span>";
        } else
            $text = $this->get_parent()->get_text();

        foreach ($vars as $name => $value) {
            $text = preg_replace("/\{$name\}/", $value, $text);
        }

        $view->stream_append('html-body', $text);
    }
}

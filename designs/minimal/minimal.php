<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Minimal */

namespace Indigo\Design\Minimal;

require_once 'minimal_navigation.php';
require_once 'minimal_footer.php';

class view extends \idg_view_implementation {

    function _render(\idg_document $document, \idg_view $view): void {
        
        parent::_render($document, $view);

        $default = $view->core()->get_template_uri('elements') . '/default.css';
        $view->stream_append('html-head', 
            "<link rel=\"stylesheet\" href=\"$default\">\n");
        
        if (($tag = $this->get_property('tag')))
            $view->stream_append('html-head', 
                "<link rel=\"stylesheet\" href=\"$tag\">\n");
    }
}

function testcard(\idg_document $document, \idg_view $view, \idg_fragment $text): void {
    $view->set_properties([
        'class' => $view->core()->qualify('view'),
    ]);

    $navigation = new \idg_fragment;
    $navigation->set_properties([
        'class' => $view->core()->qualify('navigation'),
        'source' => '_site'
    ]);
    $view->add_child($navigation);

    $view->add_child($text);

    $footer = new \idg_fragment;
    $footer->set_properties([
        'class' => $view->core()->qualify('footer')
    ]);
    $view->add_child($footer);
}

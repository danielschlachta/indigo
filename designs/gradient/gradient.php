<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Gradient */

namespace Indigo\Design\Gradient;

require_once('gradient_navigation.php');

class view extends \Indigo\View\sidebar {
    
}

function testcard(\idg_document $document, \idg_view $view, \idg_fragment $text): void {
    $view->set_properties([
        'class' => $view->template()->qualify('view'),
        'style' => "background: #d8d8e9;"
    ]);
    
    $navigation = new \idg_fragment;
    $navigation->set_properties([
        'class' => $view->template()->qualify('navigation'),
        'source' => '_site'
    ]);
    $view->add_child($navigation);
    
    $content = new \idg_container;
    $content->set_properties([
        'style' => "padding: 1em; margin-top: -1.5em;"
    ]);
    $view->add_child($content);
    
    $content->add_child($text);
}
<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Mobile */

namespace Indigo\Design\Mobile;

require_once('mobile_navigation.php');

class view extends \Indigo\View\simple {
    
}

function testcard(\idg_document $document, \idg_view $view, \idg_fragment $text): void {
    $view->set_properties([
        'class' => $view->template()->qualify('view'),
        'style' => 'padding-left: 2%; width: 96%; '
    ]);

    $navigation = new \idg_fragment;
    $navigation->set_properties([
        'class' => $view->template()->qualify('navigation'),
        'source' => '_site',
        'style' => 'font-size: 180%; padding-top: 30px; margin-bottom: -46px;'
    ]);
    $view->add_child($navigation);
    
    $view->add_child($text);
}
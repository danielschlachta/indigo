<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Fancy */

namespace Indigo\Design\Fancy;

class view extends \Indigo\View\grid {
    function _render(\idg_document $document, \idg_view $view): void {
        parent::_render($document, $view);
       
        $view->stream_append('css', "li { padding-bottom: 0.7em }\n" 
            . "li > p { margin: 0.3em 0 0 0; }\n"
            . "pre, code { font-family: 'Liberation Mono'; }\n");
        
        \Indigo\Module\Pagemap\add_to_view($view,
            'bottom: 20px', 'right: 20px', '25%', '93%');
    }
}

/**
 * $@todo docu
 */
require_once 'fancy_nav.php';
require_once 'fancy_header.php';
require_once 'fancy_aside.php';
require_once 'fancy_footer.php';

function testcard(\idg_document $document, \idg_view $view, \idg_fragment $text): void {
    $view->set_properties([
        'class' => $view->template()->qualify('view')
    ]);

    $navigation = new \idg_fragment;
    $navigation->set_properties([
        'name' => 'nav',
        'class' => $view->template()->qualify('nav'),
        'style' => 'background-color: #e0e0e0;',
        'source' => '_site'
    ]);
    $view->add_child($navigation);

    $header = new \idg_fragment;
    $header->set_properties([
        'name' => 'header',
        'class' => $view->template()->qualify('header'),
        'style' => 'background-color: #e0e0e0;'
    ]);
    $view->add_child($header);

    $aside = new \idg_fragment;
    $aside->set_properties([
        'name' => 'aside',
        'class' => $view->template()->qualify('aside'),
        'style' => 'background-color: #e0e0e0;',
        'source' => '_site'
    ]);
    $view->add_child($aside);

    $article = new \idg_container;
    $article->set_properties([
        'name' => 'article',
        'style' => 'background-color: #e0e0e0; padding: 0 1em 1em 1em;'
    ]);
    $view->add_child($article);
    
    $article->add_child($text);

    $footer = new \idg_fragment;
    $footer->set_properties([
        'name' => 'footer',
        'class' => $view->template()->qualify('footer'),
        'style' => 'background-color: #e0e0e0;'
    ]);
    $footer->set_text('set this with set_text()');
    $view->add_child($footer);
}

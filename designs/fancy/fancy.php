<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Design\Fancy;

class view extends \Indigo\View\grid {
    
}

/**
*  #todo docu
 */

require_once 'fancy_nav.php';
require_once 'fancy_header.php';
require_once 'fancy_aside.php';
require_once 'fancy_footer.php';

function testcard(\idg_document $document, \idg_view $view, \idg_fragment $text): void {
    $view->set_property('class', $view->core()->qualify('view'));
    
    $navigation = new \idg_fragment;
    $navigation->set_properties([
        'name' => 'nav',
        'class' => $view->core()->qualify('fancy_nav'),
        'source' => '_site'
    ]);
    $view->add_child($navigation);

    $header = new \idg_fragment;
    $header->set_properties([
        'name' => 'header',
        'class' => $view->core()->qualify('fancy_header')
    ]);
    $view->add_child($header); 
    
    $aside = new \idg_fragment;
    $aside->set_properties([
        'name' => 'aside',
        'class' => $view->core()->qualify('fancy_aside'),
        'source' => '_site'
    ]);
    $view->add_child($aside);
     
    $article = new \idg_container;
    $article->set_properties([
        'name' => 'article'
    ]);
    $article->add_child($text);
    $view->add_child($article);
    
    $footer = new \idg_fragment;
    $footer->set_properties([
        'class' => $view->core()->qualify('fancy_footer'),
        'name' => 'footer',
        'tag' => '&lt;footer&gt;'
    ]);
    $view->add_child($footer);
}
<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Design\Fancy;

class view extends \Indigo\View\grid {
    
}

/**
 * #todo docu
 */

require_once 'fancy_navigation.php';
require_once 'fancy_caption.php';
require_once 'fancy_sidebar.php';
require_once 'fancy_footer.php';

function testcard(\idg_document $document, \idg_view $view, \idg_fragment $text): void {
    
    $navigation = new \idg_fragment;
    $navigation->set_properties([
        'name' => 'nav',
        'class' => $view->qualify('fancy_navigation'),
        'source' => '_site'
    ]);
    $view->add_child($navigation);

    $header = new \idg_fragment;
    $header->set_properties([
        'name' => 'header',
        'class' => $view->qualify('fancy_header')
    ]);
    $view->add_child($header); 
    
    $sidebar = new \idg_fragment;
    $sidebar->set_properties([
        'name' => 'aside',
        'class' => '\Indigo\Fragment\text'
    ]);
    $sidebar->set_text('&lt;aside&gt;');
    $view->add_child($sidebar);
     
    $article = new \idg_container;
    $article->set_properties([
        'name' => 'article'
    ]);
    $article->add_child($text);
    $view->add_child($article);
    
    $footer = new \idg_fragment;
    $footer->set_properties([
        'class' => $view->qualify('fancy_footer'),
        'name' => 'footer',
        'tag' => '&lt;footer&gt;'
    ]);
    $view->add_child($footer);
}
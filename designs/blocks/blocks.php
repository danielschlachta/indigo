<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Design\Blocks;

include 'tabs/tabs.php';
include 'imageframe/imageframe.php';
include 'dropdown/dropdown.php';
include 'infobox/infobox.php';
include 'footer/footer.php';

class view extends \Indigo\View\simple {
    
}

function testcard(\idg_document $document, \idg_view $view, \idg_fragment $text): void {
    $imageframe = new \idg_fragment;
    $imageframe->set_properties([
        'class' => 'Indigo\Design\Blocks\imageframe'
    ]);
    $attr = $imageframe->create_attribute('blocks::image');
    $attr->set_parameter('src', 'https://thispersondoesnotexist.com/');
    $attr->set_parameter('top', 15);
    $attr->set_parameter('left', 15);
    $view->add_child($imageframe);

    $datasource = new \idg_datasource();
    $datasource->set_properties([
        'class' => '\Indigo\Datasource\rss',
        'name' => 'cnn'
    ]);
    $datasource->set_parameter('url', 'http://rss.cnn.com/rss/cnn_latest.rss');
    $datasource->set_parameter('max-items', '4');
    $document->add_child($datasource);

    $infobox = new \idg_fragment;
    $infobox->set_properties([
        'class' => 'Indigo\Design\Blocks\infobox',
        'source' => 'cnn'
    ]);
    $attr = $infobox->create_attribute('blocks::infobox');
    $attr->set_parameter('caption', 'News from CNN');
    $view->add_child($infobox);

    $dropdown = new \idg_fragment;
    $dropdown->set_properties([
        'class' => '\Indigo\Design\Blocks\dropdown',
        'source' => '_site',
        'tag' => 'folder-2',
        'style' => 'position: fixed; top: 15px; right: 30px;'
    ]);
    $view->add_child($dropdown);

    $centercol = new \idg_container;
    $centercol->set_properties([
        'style' => 'margin-left: 15px; padding: 50px 300px 0 0'
    ]);
    $view->add_child($centercol);

    $tabs = new \idg_fragment;
    $tabs->set_properties([
        'class' => '\Indigo\Design\Blocks\tabs',
        'source' => '_site',
        'tag' => 'folder-1'
    ]);
    $centercol->add_child($tabs);

    $centercol_body = new \idg_container;
    $centercol_body->set_properties([
        'style' => "background: #b4d2b0; padding: 0 1em 0 1em;"
        . 'border: solid black; border-width: 0 1px 1px 1px'
    ]);
    $centercol->add_child($centercol_body);

    $centercol_body->add_child($text);

    $footer = new \idg_fragment;
    $footer->set_properties([
        'class' => '\Indigo\Design\Blocks\footer',
        'tag' => '[tag]',
        'style' => 'margin-top: 10px; padding: 10px 1em 10px 1em;'
    ]);
    $centercol->add_child($footer);
}

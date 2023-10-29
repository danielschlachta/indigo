<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Design\Blocks;

$idg_design_namespace['blocks'] = '\Indigo\Design\Blocks';

require_once('blocks/tabs.php');
require_once('blocks/imageframe.php');
require_once('blocks/dropdown.php');
require_once('blocks/infobox.php');
require_once('blocks/footer.php');

class view extends \Indigo\View\fixedcontent {
    
}

function make_testcard(\idg_document $document, \idg_view $view, \idg_fragment $article)
: void {
    $imageframe = new \idg_fragment;
    $imageframe->set_properties(array(
        'class' => 'Indigo\Design\Blocks\imageframe'
    ));
    $attr = $imageframe->create_attribute('image', 'blocks');
    $attr->set_parameter('src', 'https://thispersondoesnotexist.com/');
    $attr->set_parameter('top', 15);
    $attr->set_parameter('left', 15);
    $view->add_child($imageframe);

    $infobox = new \idg_fragment;
    $infobox->set_properties(array(
        'class' => 'Indigo\Design\Blocks\infobox',
        'source' => 'infobox'
    ));
    $view->add_child($infobox);

    $dropdown = new \idg_fragment;
    $dropdown->set_properties(array(
        'class' => '\Indigo\Design\Blocks\dropdown',
        'source' => '_site',
        'tag' => 'folder-2',
        'style' => 'position: fixed; top: 15px; right: 30px;'
    ));
    $view->add_child($dropdown);

    $centercol = new \idg_container;
    $centercol->set_properties(array(
        'style' => 'margin-left: 7px; padding: 2em 300px 0 0'
    ));
    $view->add_child($centercol);

    $tabs = new \idg_fragment;
    $tabs->set_properties(array(
        'class' => '\Indigo\Design\Blocks\tabs',
        'source' => '_site',
        'tag' => 'folder-1'
    ));
    $centercol->add_child($tabs);

    $centercol_body = new \idg_container;
    $centercol_body->set_properties(array(
        'style' => "background: #b4d2b0; padding: 0 1em 0 1em;"
        . 'border: solid black; border-width: 0 1px 1px 1px'
    ));
    $centercol->add_child($centercol_body);

    $centercol_body->add_child($article);

    $footer = new \idg_fragment;
    $footer->set_properties(array(
        'class' => '\Indigo\Design\Blocks\footer',
        'tag' => 'footer tag',
        'style' => 'margin-top: 10px; padding: 10px 1em 10px 1em;'
    ));
    $centercol->add_child($footer);
}

?>
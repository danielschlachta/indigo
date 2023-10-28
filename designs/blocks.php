<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

require_once('blocks/tabs.php');
require_once('blocks/imageframe.php');
require_once('blocks/dropdown.php');
require_once('blocks/infobox.php');
require_once('blocks/footer.php');

/* @todo Get rid of this */

if (@$font_blocks == null)
    $font_blocks = 'font-family: verdana, sans-serif;';

$elements = '../designs/blocks/elements';

class idg_view_blocks extends idg_view_fixedcontent {

    function __construct(&$parent) {
        parent::__construct($parent);
    }
}

function idg_view_blocks_make_testcard($document, $view, $text) {
    $centercol = new idg_container;
    $centercol->set_properties(array(
        'style' => 'margin: 15px; padding-top: 2em;'
    ));
    $view->add_child($centercol);

    $imageframe = new idg_fragment;
    $imageframe->set_properties(array(
        'class' => 'blocks_imageframe'
    ));
    $attr = $imageframe->create_attribute('image', 'blocks');
    $attr->set_parameter('src', 'https://thispersondoesnotexist.com/');
    $attr->set_parameter('top', 15);
    $attr->set_parameter('left', 15);
    $view->add_child($imageframe);
    
    $infobox = new idg_fragment;
    $infobox->set_properties(array(
        'class' => 'blocks_infobox',
        'source' => '_site'
    ));
    $view->add_child($infobox);

    $dropdown = new idg_fragment;
    $dropdown->set_properties(array(
        'class' => 'blocks_dropdown',
        'source' => '_site',
        'tag' => 'folder-2',
        'style' => 'position: fixed; top: 20px; right: 20px;'
    ));
    $view->add_child($dropdown); 
    
    $tabs = new idg_fragment;
    $tabs->set_properties(array(
        'class' => 'blocks_tabs',
        'source' => '_site',
        'tag' => 'folder-1'
    ));
    $centercol->add_child($tabs);

    $centercol_body = new idg_container;
    $centercol_body->set_properties(array(
        'style' => "background: #b4d2b0; padding: 15px;"
        . 'border: solid black; border-width: 0 1px 1px 1px'
    ));
    $centercol->add_child($centercol_body);

    $centercol_body->add_child($text);
/*
    $footer = new idg_outlet;
    $footer->set_properties(array(
        'class' => 'blocks_footer',
        'tag' => 'footer tag',
        'style' => 'margin-top: 10px; padding: 10px;'
    ));
    $centercol->add_child($footer);
*/
   
}

?>
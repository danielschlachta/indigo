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

class idg_view_html_part_blocks extends idg_view_html_part_fixedcontent {

    function __construct(&$parent) {
        parent::__construct($parent);
    }
}

function idg_view_blocks_make_testcard(&$document, &$view, $text) {
    $part = new idg_view_html_part;
    $part->set_properties(array(
        'class' => 'blocks',
        'style' => 'background: #a8bdff; margin-top: 70px;'
    ));
    $view->add_child($part);

    $imageframe = new idg_view_html_renderer;
    $imageframe->set_properties(array(
        'class' => 'blocks_imageframe'
    ));
    $part->add_child($imageframe);

    $centercol = new idg_view_html_container;
    $centercol->set_properties(array(
        'name' => 'center',
        'style' => 'margin: 2em;'
    ));
    $part->add_child($centercol);

    $tabs = new idg_view_html_renderer;
    $tabs->set_properties(array(
        'class' => 'tabs',
        'source' => '_site',
        'tag' => 'folder-1'
    ));
    $centercol->add_child($tabs);

    $centercol_body = new idg_view_html_container;
    $centercol_body->set_properties(array(
        'name' => 'center_body',
        'style' => "background: #b4d2b0; padding: 15px; "
        . 'border: solid black; border-width: 0 1px 1px 1px'
    ));

    $centercol->add_child($centercol_body);

    $centercol_body->add_child($text);

    $footer = new idg_view_html_renderer;
    $footer->set_properties(array(
        'class' => 'blocks_footer',
        'tag' => 'footer tag',
        'style' => 'margin-top: 10px; padding: 10px;'
    ));
    $centercol->add_child($footer);

    $imageframe = new idg_view_html_renderer;
    $imageframe->set_properties(array(
        'class' => 'blocks_imageframe'
    ));
    $attr = $imageframe->create_attribute('image');
    $attr->set_option('src', 'https://thispersondoesnotexist.com/');
    $attr->set_option('top', 15);
    $attr->set_option('left', 15);
    $part->add_child($imageframe);

    $infobox = new idg_view_html_renderer;
    $infobox->set_properties(array(
        'class' => 'blocks_infobox'
    ));
    $part->add_child($infobox);

    $navig = new idg_view_html_renderer;
    $navig->set_properties(array(
        'class' => 'blocks_navigation',
        'source' => '_site',
        'tag' => 'folder-2',
        'style' => 'position: fixed; top: 20px; right: 20px;'
    ));
    $part->add_child($navig);
}

?>
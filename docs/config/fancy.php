<?php

$view = new idg_view_html;
$view->set_properties(array(
	'name' => 'fancy',
	'icon' => 'favicon.png'
));

// #DACCB0
// #BDD5C4

// Create the layout and add it to the view

$part = new idg_view_html_part;
$part->set_properties(array(
	'class' => 'fancy'
));
$view->add_child($part);

// Add the navigation

$navigation = new idg_view_html_renderer;
$navigation->set_properties(array(
	'class' => 'fancy_navigation',
	'source' => '_site'
));
$part->add_child($navigation);

// Insert the caption

$caption = new idg_view_html_container;
$caption->set_properties(array(
	'name' => 'caption',
));
$part->add_child($caption);

$caption_text = new idg_view_html_slot;
$caption_text->set_properties(array(
	'name' => 'caption'
));
$caption->add_child($caption_text);

// Create a canvas for the body

$body = new idg_view_html_container;
$body->set_properties(array(
	'name' => 'body',
	'filter' => 'fancy_filter_eat_caption',
));
$part->add_child($body);

// Insert the page text

$main_text = new idg_view_html_slot;
$main_text->set_properties(array(
	'name' => 'main-text',
	'list-style-image' => 'elements/fancy/list-image.png',
	'filter' => 'fancy_filter_typo'
	// -- ugly, do not use: 'list-style-position' => 'inside'
));
$body->add_child($main_text);

?>

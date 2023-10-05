<?php

if ($view_xml == null) 
	$idg_path = '../indigo';

require_once("$idg_path/startup.php");

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

// Create a canvas for the body

$body = new idg_view_html_container;
$body->set_properties(array(
	'name' => 'body',
	'filter' => 'fancy_filter_typo',
));

$part->add_child($body);

// Insert the page text

$main_text = new idg_view_html_slot;
$main_text->set_properties(array(
	'name' => 'main-text',
	'list-style-image' => 'elements/fancy/images/list-image.png'
	// -- ugly, do not use: 'list-style-position' => 'inside'
));
$body->add_child($main_text);

// --------------------------------------------------

if ($view_xml !== null) {
    $file = $view_xml;
    $view->write_xml($view_xml);
} else {
    $file = 'blocks.xml';
    $view->check_all();
    $view->print_debug();
    $view->write_xml($file);
    echo "View configuration written to $file\n";
}

?>

<?php

$view = new idg_view_html;
$view->set_properties(array(
	'name' => 'fancy',
	'icon' => 'favicon.png'
));

// Create the layout and add it to the view

$part = new idg_view_html_part;
$part->set_properties(array(
	'class' => 'fancy'
));
$view->add_child($part);

// Add the navigation first as it normally appears like that on
// the screen. The order does not really matter here, 
// the html part will take care of that.

$navigation = new idg_view_html_renderer;
$navigation->set_properties(array(
	'name' => 'navigation',
	'class' => 'fancy_navigation',
	'source' => '_site'
));
$part->add_child($navigation);

// Add the header, connect it to the caption slot from the site

$header = new idg_view_html_container;
$header->set_properties(array(
	'name' => 'header',
));

// Use the first heading for the caption

$caption = new idg_view_html_slot;
$caption->set_properties(array(
	'name' => 'caption'
));
$header->add_child($caption);

$part->add_child($header);

// Add a container for the content

$content = new idg_view_html_container;
$content->set_properties(array(
	'name' => 'content',
	'filter' => 'fancy_filter_eat_caption', // we don't want it twice
));

// Substitute the page's description for the first heading

$heading = new idg_view_html_item;
$heading->set_properties(array(
    'class' => 'text',
    'style' => 'margin-top: -1.2em;'
));
$heading->set_text('&lt;h1&gt;{description}&lt;/h1&gt;');
$content->add_child($heading);

// Insert the actual page text into the container and give it some style

$main_text = new idg_view_html_slot;
$main_text->set_properties(array(
	'name' => 'main-text',
	'list-style-image' => 'elements/fancy/list-image.png',
	'filter' => 'fancy_filter_typo'
));
$content->add_child($main_text);

$part->add_child($content);

// Create a footer programmatically

$footer = new idg_view_html_item;
$footer->set_properties(array(
	'name' => 'footer',
    'class' => 'text',
    'style' => 'font-size: 130%; font-style: italic;'
));
 
$footer->set_text('gremp');
 
$part->add_child($footer);

?>

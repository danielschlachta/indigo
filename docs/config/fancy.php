<?php

$view = new idg_view_html;
$view->set_properties(array(
	'name' => 'fancy',
	'icon' => 'favicon.png',
	'filter' => 'fancy_filter_typo'
));

// Create the layout and add it to the view

$part = new idg_view_html_part;
$part->set_properties(array(
	'class' => 'fancy',
));

$part_opts = array(
	'stylesheet' => 'elements/fancy/style.css',
	'font-family' => 'Enriqueta',
	'background-color' => '#fff6e8',
);

//$part->set_options($part_opts);

$view->add_child($part);

/*
 *  Add the navigation first as it normally appears like that on
 *  the screen. The order does not really matter here,
 *  the html part will take care of that.
 */

$navigation = new idg_view_html_renderer;
$navigation->set_properties(array(
	'name' => 'navigation',
	'class' => 'fancy_navigation',
	'source' => '_site'
));

$navigation_opts = array(
	'font-family' => 'Merriweather'
);
//$navigation->set_options($navigation_opts);

$part->add_child($navigation);

// Insert the caption

$caption = new idg_view_html_renderer;
$caption->set_properties(array(
	'name' => 'header',
	'class' => 'fancy_caption',
	'source' => 'null',
	//'style' => 'font-style: italic;'
));

$caption_opts = array(
	'image' => 'elements/fancy/caption.png',
	'font-family' => 'Quintessential',
);
//$caption->set_options($caption_opts);

$part->add_child($caption);

// Add a container for the sidebar

$sidebar = new idg_view_html_renderer;

$sidebar->set_properties(array(
	'name' => 'sidebar',
	'class' => 'fancy_sidebar',
	'source' => '_site'
));

$part->add_child($sidebar);

// Add a container for the content

$content = new idg_view_html_container;
$content->set_properties(array(
	'name' => 'content'
));

//$content->set_option('font-family', 'Lekton'); // As mentioned in style.css ... UGLY HACK

// Insert the actual page text into the container

$main_text = new idg_view_html_slot;
$main_text->set_properties(array(
	'name' => 'main-text',
	'list-style-image' => 'elements/fancy/list-image.png'
));
$content->add_child($main_text);

$part->add_child($content);

// Create a footer programmatically

$footer = new idg_view_html_renderer;
$footer->set_properties(array(
	'name' => 'footer',
    'class' => 'fancy_footer',
    'source' => 'null' // gotta have one
));

$footer_opts = array(
	'image' => 'elements/fancy/postmark.png',
	'font-family' => 'Special Elite',
	'background-color' => '#a2acbd'
);
//$footer->set_options($footer_opts);

$part->add_child($footer);

?>

<?php

if ($view_xml == null) 
	$idg_path = '../indigo';

require_once("$idg_path/startup.php");

$view = new idg_view_html;
$view->set_properties(array(
	'name' => 'minimal',
	'icon' => 'favicon.png',
	'tag' => 'indigo'
));

$part = new idg_view_html_part;
$part->set_properties(array(
	'class' => 'minimal'
));
$view->add_child($part);

$body = new idg_view_html_container;
$body->set_properties(array(
	'name' => 'body'
));

$part->add_child($body);

$navi = new idg_view_html_renderer;
$navi->set_properties(array(
	'class' => 'minimal_navigation',
	'source' => '_site'
));
$body->add_child($navi);

$text = new idg_view_html_container;
$text->set_properties(array(
	'name' => 'text'
));

$body->add_child($text);

$footer = new idg_view_html_renderer;
$footer->set_properties(array(
	'class' => 'minimal_footer',
	'source' => '_site'
));
$body->add_child($footer);

$main_text = new idg_view_html_slot;
$main_text->set_properties(array(
	'name' => 'main-text',
));
$text->add_child($main_text);

// --------------------------------------------------

if ($view_xml !== null) {
    $file = $view_xml;
    $view->write_xml($view_xml);
} else {
    $file = 'minimal.xml';
    $view->check_all();
    $view->print_debug();
    $view->write_xml($file);
    echo "View configuration written to $file\n";
}

?>

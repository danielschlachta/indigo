<?php

if ($view_xml == null) 
	$idg_path = '../indigo';

require_once("$idg_path/startup.php");

$view = new idg_view_html;
$view->set_properties(array(
	'name' => 'fancy',
	'icon' => 'favicon.png'
));

// Create the layout and add it to the view

$part = new idg_view_html_part;
$part->set_properties(array(
	'class' => 'grid_bottomnav'
));
$view->add_child($part);

// Add the navigation container to the part

$navigation = new idg_view_html_renderer;
$navigation->set_properties(array(
	'class' => 'null',
	'source' => '_site'
));

$part->add_child($navigation);




/*


$centercol_body = new idg_view_html_container;
$centercol_body->set_properties(array(
	'name' => 'center_body'
));

$centercol->add_child($centercol_body);

$footer = new idg_view_html_renderer;
$footer->set_properties(array(
	'class' => 'footer',
	'source' => '_site'
));
$centercol->add_child($footer);
*/

$main_text = new idg_view_html_slot;
$main_text->set_properties(array(
	'name' => 'main-text',
));
//$centercol_body->add_child($main_text);


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

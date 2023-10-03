<?php

$idg_path = '../../../indigo';

require_once("$idg_path/startup.php");

$site = new idg_site;
$site->read_xml('site.xml');
$site->check_all();  // comment this out for production version

$view = new idg_view_html;
$view->read_xml('scatter.xml');
$view->check_all(); // comment this out for production version

if (!($page = $site->get_document())) {
    die('Page not found.');
} 

$view->render($page);
$view->printout();

?>

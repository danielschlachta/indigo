<?php

$idg_path = '../../../indigo';

require_once("$idg_path/startup.php");

$site = new idg_site;
$site->read_xml('site.xml');
$site->check_all();  // comment this out for production version

$view = new idg_view_html;

if (@$_GET['view'] == 'strip')
    include('strip.php');
 else
    $view->read_xml('scatter.xml');
  
    
$view->check_all(); // comment this out for production version

if (!($page = $site->get_document())) {
    include('error.php');
} 

$view->render($page);
$view->printout();
?>

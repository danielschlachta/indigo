<?php

$idg_path = '../../../indigo';

require_once("$idg_path/startup.php");

require_once('navigation.php');
require_once('renderers.php');
require_once('datasource_imagelist.php');

$site = new idg_site;
$site->read_xml('site.xml');

$view = new idg_view_html;

$display = @$_GET['view'];

if ($display)
    setcookie('demo-view', $display);
else
    $display = @$_COOKIE['demo-view'];
    
if (@!$display || !in_array($display, array('scatter', 'strip')))
    $display = 'scatter';

$view->read_xml("view-$display.xml");
    
if (!($page = $site->get_document())) {
    include('error.php');
} 

$view->render($page);
$view->printout();

?>

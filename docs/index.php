<?php

/* ========================================================================
 * indigo documentation
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once '../indigo/startup.php';

function isMobile() {
    return preg_match('/\b(?:a(?:ndroid|vantgo)|b(?:lackberry|olt|o?ost)'
        . '|cricket|docomo|hiptop|i(?:emobile|p[ao]d)|kitkat|m(?:ini|obi)'
        . '|palm|(?:i|smart|windows )phone|symbian|up\.(?:browser|link)|tablet'
        . '(?: browser| pc)|(?:hp-|rim |sony )tablet|w(?:ebos|indows ce|os))/i',
        @$_SERVER["HTTP_USER_AGENT"]);
}

$site = new idg_site;
$site->read_xml('site.xml');
$site->write_xml('/tmp/site.xml');
$site->check();

if (@$_GET['sitemap'] == 'xml')
    die($site->get_sitemap());


$design = @$_GET['view'];

if ($design)
    setcookie("view", $design);
else if (isMobile())
    $design = 'mobile';
else
    $design = @$_COOKIE['view'];

if ($design == 'mobile')
    $site->set_property('title-reverse-order', 'no');

if (!($document = $site->get_document())) {
    die('operation broken error');
    require_once 'error/error.php';
    $document = get_error_document();
}

// $view->write_xml($view_xml);

if (!$design)
    $design = 'blocks';

$view = new idg_view;
if (!$view->load_template($design, '../designs'))
    die('could not load design');

$view->read_xml("views/$design/$design.xml");
//require_once "config/$design.php";
//configure_view($view);


$view_preload = "config/$design.preload.php";
if (file_exists($view_preload))
    require_once($view_preload);

$view_postload = "config/$design.postload.php";
if (file_exists($view_postload))
    require_once($view_postload);

$view->check();
$view->render($document);
$view->print();

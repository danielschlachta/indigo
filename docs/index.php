<?php

/* ========================================================================
 * indigo documentation
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once '../indigo/startup.php';

function is_mobile() {
    return preg_match('/\b(?:a(?:ndroid|vantgo)|b(?:lackberry|olt|o?ost)'
        . '|cricket|docomo|hiptop|i(?:emobile|p[ao]d)|kitkat|m(?:ini|obi)'
        . '|palm|(?:i|smart|windows )phone|symbian|up\.(?:browser|link)|tablet'
        . '(?: browser| pc)|(?:hp-|rim |sony )tablet|w(?:ebos|indows ce|os))/i',
        @$_SERVER["HTTP_USER_AGENT"]);
}

$site = new idg_site;
$site->read_xml('site.xml');
$site->check();

if (@$_GET['sitemap'] == 'xml')
    die($site->get_sitemap());

if (($design = @$_GET['view']))
    setcookie("view", $design);
else if (is_mobile())
    $design = 'mobile';
else
    $design = @$_COOKIE['view'];

if (!$design)
    $design = 'blocks';

if ($design == 'mobile')
    $site->set_property('title-reverse-order', 'no');

$core = new idg_core($design, "../designs/$design");

if (!($doc_1 = $site->get_document($core->get_request_document())))
    die('oknodoc');

$view = new idg_view($core);

$view->read_xml("views/$design/$design.xml");
$view->check();
$view->render($doc_1);
$view->print();

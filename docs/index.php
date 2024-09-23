<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

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

$template = new idg_template($design, "../designs/$design");

if (!($document = $site->get_document($template->get_request_document()))) {
    require 'error/error.php';
    $document = get_error_document();
}

$view = new idg_view($template);
$view->set_reload_policy(idg_reload_policy::RELOAD_ALWAYS);
$view->read_xml("views/$design/$design.xml");
$view->check();
$view->render($document);
$view->emit();

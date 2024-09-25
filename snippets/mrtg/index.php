<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once '../../indigo/startup.php';

define('MRTG_DIR', '/var/www/html/mrtg/');
define('MRTG_URL', '/mrtg');

function is_mobile() {
    return preg_match('/\b(?:a(?:ndroid|vantgo)|b(?:lackberry|olt|o?ost)'
        . '|cricket|docomo|hiptop|i(?:emobile|p[ao]d)|kitkat|m(?:ini|obi)'
        . '|palm|(?:i|smart|windows )phone|symbian|up\.(?:browser|link)|tablet'
        . '(?: browser| pc)|(?:hp-|rim |sony )tablet|w(?:ebos|indows ce|os))/i',
        @$_SERVER["HTTP_USER_AGENT"]);
}

function get_imgname($ifname, $part) {
    return MRTG_URL . "/$ifname-$part.png";
}

function add_if($parent, $count, $ifname) {
    $document = new idg_document;
    $document->set_properties([
        'id' => "doc-$count",
        'name' => "$ifname",
        'title' => "$ifname",
        'navigation-comment' => "Document is in Folder 1"
    ]);

    $datasource = new idg_datasource();

    $datasource->set_properties([
        'name' => "if-$count-src",
        'class' => 'Indigo\Datasource\text'
    ]);

    $text = "<h1 style=\"margin-top: -0.8em;\">$ifname</h1>";
    
    $ifsrc = MRTG_DIR . $ifname . '.html';
    
    $src = file_get_contents($ifsrc);
    
    $style = '';
    if (is_mobile())
        $style = ' style="width: 100%;"';
        
    $match = [];
    
    $regex = '/.*last updated (.* at [^\.]*)/';
    
    if (!str_contains($src, ', at which'))
        $regex = '/.*last updated (.* at [^\.]*)\<\/strong/';
    
    if (preg_match($regex, $src, $match))
        $text .= "<p>Last updated $match[1].</p>\n";
    
    $text .= "<p><b>Daily graph (5 min. average)</b><br><img src=\"" 
        . get_imgname($ifname, 'day') . "\"$style ></p>\n";
    
    $text .= "<p><b>Weekly graph (30 min. average)</b><br><img src=\"" 
        . get_imgname($ifname, 'week') . "\"$style ></p>\n";
    
    $text .= "<p><b>Monthly graph (2 hr. average)</b><br><img src=\"" 
        . get_imgname($ifname, 'month') . "\"$style ></p>\n";
    
    $text .= "<p><b>Yearly graph (1 day average)</b><br><img src=\"" 
        . get_imgname($ifname, 'year') . "\"$style ></p>\n";
    
    $datasource->set_text($text);
    
    $document->add_child($datasource);

    $renderer = new idg_renderer();
    $renderer->set_properties([
        'slot' => 'main-text',
        'class' => 'Indigo\Renderer\text',
        'source' => "if-$count-src"
    ]);
    $document->add_child($renderer);

    $parent->add_child($document);
}

$site = new idg_site;
$site->set_property('index-document', 'doc-1');
$directory = scandir(MRTG_DIR);

$count = 1;

for ($i = 0; $i < count($directory); $i++) {
    $filename = $directory[$i];
    
    if (in_array($directory[$i], ['.', '..']) || 
        pathinfo($filename, PATHINFO_EXTENSION) != 'html')
        continue;
   
    add_if($site, $count++, substr($filename, 0, strlen($filename) - 5));
}

$template = is_mobile() ? 'mobile' : 'blocks';

$view = new idg_view(new idg_template($template, "../../designs/$template"));
$view->set_reload_policy(idg_reload_policy::RELOAD_ALWAYS);
$view->read_xml("$template.xml");
$view->check();

if (!($document = $site->get_document($view->template()->get_request_document()))) {
    die('document not found');
}

$view->render($document);
$view->emit();

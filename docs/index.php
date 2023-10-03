<?php

/* ========================================================================
 * indigo documentation
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

function isMobile() {
    return preg_match('/\b(?:a(?:ndroid|vantgo)|b(?:lackberry|olt|o?ost)' 
        . '|cricket|docomo|hiptop|i(?:emobile|p[ao]d)|kitkat|m(?:ini|obi)' 
        . '|palm|(?:i|smart|windows )phone|symbian|up\.(?:browser|link)|tablet' 
        . '(?: browser| pc)|(?:hp-|rim |sony )tablet|w(?:ebos|indows ce|os))/i', 
        $_SERVER["HTTP_USER_AGENT"]);
}

$idg_path = '../indigo';
require_once("$idg_path/startup.php");

$site = new idg_site;
$site->read_xml('config/site.xml');
// $site->check_all();    

if (@$_GET['sitemap'] == 'xml') {
    die($site->get_sitemap());
}

$design = @$_GET['view'];

if ($design) { 
    setcookie("view", $design);
} 
else {
    if (isMobile()) {
        $design = 'mobile';
    } else {
        $design = @$_COOKIE['view'];
    }
}
    
if (!$design || !file_exists("../designs/$design.php"))
	$design = 'blocks';

require_once("../designs/$design.php");
require_once($idg_path . '/modules/format_source.php');
require_once($idg_path . '/modules/format_markdown.php');

if (@!$page = $site->get_document()) {
	require_once('error/error.php');
	$page = get_error_page();
}

$view_xml = "config/cache/$design.xml";
$generator = "config/$design.php";

if (!file_exists($view_xml) || filemtime($generator) > filemtime($view_xml)) {
	if (!$fc = @fopen($view_xml, "w")) {
			die("Unable to create file '$view_xml' " 
				. "- please check if the containing directory exists and has the correct permissions.");
	} else {
		fclose($fc);
	}
			
	require_once($generator);
}

$view = new idg_view_html;
$view->read_xml($view_xml);
		
// $view->check_all();

$view->render($page);
$view->printout();

?>

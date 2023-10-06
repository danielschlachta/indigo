<?php

/* ========================================================================
 * indigo documentation
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */


$idg_path = '../indigo';
require_once("$idg_path/startup.php");

$cache_dir = getcwd() . '/cache';

// ---------------------------------------------------------------------


function isMobile() {
    return preg_match('/\b(?:a(?:ndroid|vantgo)|b(?:lackberry|olt|o?ost)' 
        . '|cricket|docomo|hiptop|i(?:emobile|p[ao]d)|kitkat|m(?:ini|obi)' 
        . '|palm|(?:i|smart|windows )phone|symbian|up\.(?:browser|link)|tablet' 
        . '(?: browser| pc)|(?:hp-|rim |sony )tablet|w(?:ebos|indows ce|os))/i', 
        $_SERVER["HTTP_USER_AGENT"]);
}

function complain_cache($dir, $file) {
	global $idg_path;
	
	echo "<html><body><h1>Cache directory is not writable</h1>"
		. "The directory <blockquote><code>$dir"
		. "</blockquote></code> seems not to be writable by the web server. ";
		
	exec("ls -ld $dir 2>&1", $output); 
	
	$perm = $output[0];
	die("Current owner and permissions:<blockquote><code>$perm</code></blockquote>"
		. "You should probably do:"
		. "<blockquote><b><code>sudo chown www-data $dir"
		. "</b></blockquote></code> from any directory." 
		. "<ul><li>Trying to create file: <code>$file</code></li></ul>"
		. "</body></html>");
}


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

$view_xml = "$cache_dir/$design.xml";
$generator = "config/$design.php";

if (!file_exists($view_xml) || filemtime($generator) > filemtime($view_xml)) {
	if (!$fc = @fopen($view_xml, "w")) {
			complain_cache($cache_dir, $view_xml);
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

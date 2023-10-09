<?php
/* ========================================================================
 * Indigo/Web
 * 
 * File: diagnostics.php - you can customize error reporting here
 * 
 * (c) 2020 Daniel Schlachta
 * ======================================================================== */

ini_set('display_errors',1); 
error_reporting(E_ALL);

function diag($obj, $msg) {
	$name = get_class($obj);
	echo "<h1>$msg</h1>\n";
	echo "<h2>PHP object type: $name</h2>\n";
	echo '<pre>';
	debug_print_backtrace();
	echo '</pre>';
	echo '<br />';
	echo '<pre>';
	unset($obj->parent);
	unset($obj->children);		
	print_r($obj);
	echo '</pre>';
	die();
}

function complain_version() {
	global $idg_min_php_version;
	
	die("<html><body><h1>Indigo requires at least PHP $idg_min_php_version to run.</h1>"
		. "<h2>You are running version " . phpversion() . ". Sorry.</h2>"
		. "</body></html>");
}

function complain_module($name, $file, $repo) {
	$file = getcwd() . '/' . $file;
	
	die("<html><body><h1>Submodule $name is missing</h1>"
		. "Please execute <blockquote><code>" 
		. "git submodule init<br>git submodule update</code></blockquote>"
		. " in the main directory or use the <code>--recurse-submodules</code>"
		. " switch with <code>git clone</code>."
		. "<ul><li>Checking for file: <code>$file</code></li>"
		. "<li>Repository: <a href=\"$repo\"><code>$repo</code></a></li></ul>"
		. "</body></html>");
}

?>

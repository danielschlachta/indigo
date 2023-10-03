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
	echo "<h2>$msg</h2>";
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
?>

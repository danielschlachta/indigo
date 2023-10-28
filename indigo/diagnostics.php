<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

function diag($obj, $msg) {
    if (method_exists($obj, 'clear'))
        $obj->clear();
	
    $name = get_class($obj);
    
    $backtrace = debug_backtrace();
    $function = $backtrace[1]['function'];
	echo "<h1>$name\\$function: $msg</h1>\n";
	
    echo "<pre>\n";
    debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	echo "\n";
	foreach (array_reverse(class_parents($obj)) as $parent)
        echo "$parent->";
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

	die("<html><body><h1>Git Submodule $name is missing</h1>"
		. "Please execute <blockquote><code>"
		. "git submodule init<br>git submodule update</code></blockquote>"
		. " in the main directory or use the <code>--recurse-submodules</code>"
		. " switch with <code>git clone</code>."
		. "<ul><li>Checking for file: <code>$file</code></li>"
		. "<li>Repository: <a href=\"$repo\"><code>$repo</code></a></li></ul>"
		. "</body></html>");
}

?>

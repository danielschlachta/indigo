<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/**
 * Prints as much information as possible and then terminates execution calling die().
 * This is used only if it really does not make any sense to continue.
 * @todo Log this to a file, present some friendly message to the user.
 * @see idg_log
 * @param object $object The calling object
 * @param string $message An error message
 * @param $xml_parser|null An <code>xml</code> parser, to include line and column info
 * if applicable. The actual error is <b>not</b> reproduced, expecting the error message 
 * to be more specific.
 */
function idg_diag(object $object, string $message, $xml_parser = null) {
    $name = get_class($object);
    
    if (method_exists($object, 'clear'))
        $object->clear();
	
    $backtrace = debug_backtrace();
    $function = $backtrace[1]['function'];

    echo "<h1>$name\\$function: $message</h1>\n";
    
    if ($xml_parser) {
        $err_line = xml_get_current_line_number($xml_parser);
        $err_col = xml_get_current_column_number($xml_parser);

        echo "<h2>XML input line $err_line, column $err_col</h2>\n";
    }
	  
    echo "<pre>\n";
    debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	echo "\nobject";
	foreach (array_reverse(class_parents($object)) as $parent)
        echo "->$parent";
    
    if (method_exists($object, 'get_property')) {
        $prop_id = $object->get_property('id');
        $prop_name = $object->get_property('name');
        $prop_str = null;
        
        if ($prop_id) {
            $prop_str = "id='$prop_id'";
            if ($prop_name)
                $prop_str .= ', ';
        }
        
        if ($prop_name)
            $prop_str .= "name='$prop_name'";
        
        if ($prop_str)
            echo "($prop_str)";
    }
    
    echo " ";
	print_r($object);
	echo '</pre>';
	die();
}

/**
 * Todo: make this a more general thingy
 * 
 */
function idg_complain_version() {
	$idg_min_php_version = IDG_MIN_PHP_VERSION;

	die("<html><body><h1>Indigo requires at least PHP $idg_min_php_version to run.</h1>"
		. "<h2>You are running version " . phpversion() . ". Sorry.</h2>"
		. "</body></html>");
}

function idg_complain_module($name, $file, $repo) {
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

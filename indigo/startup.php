<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @todo Create a class path variable for user defined components. */

spl_autoload_register(function ($class_name) {
    $name_arr = explode('_', $class_name);

    if ($name_arr[0] == 'idg') {
        if (count($name_arr) > 1) {
            $filename = __DIR__ . '/library/objects/' . $name_arr[1] . '.php';

            if (file_exists($filename))
                require_once $filename;
        }
    }

    if (strpos($name_arr[0], 'Indigo\\') === 0) {
        $namespace_arr = explode('\\', $name_arr[0]);

        if (count($namespace_arr) > 2) {
            $filename = __DIR__ . '/classes/' . strtolower($namespace_arr[1])
                . '_' . $namespace_arr[2] . '.php';

            if (file_exists($filename))
                require_once $filename;
        }
    }
});

/**
 * Set this to where the web sever can find indigo's components, in case
 * the URL does not reflect the file system layout. Otherwise it is set automatically.
 */
$idg_path = '';

/**
 * The global initializer.
 */
function idg_init(): void {
    global $idg_path;

    $script = explode('/', dirname($_SERVER['SCRIPT_FILENAME']));
    $dir = explode('/', __DIR__);
       
    for ($i = 0; $script[$i] == $dir[$i]; $i++);
    
    for ($j = $i; $j < count($dir); $j++)
        $idg_path = $idg_path . '../';
    
    $idg_path .= $dir[count($dir) - 1];
    
    define('IDG_SHORT_NAME', 'indigo');
    define('IDG_VERSION', '1.4');
    define('IDG_PROGRAM_NAME', IDG_SHORT_NAME . ' v' . IDG_VERSION);

    define('IDG_MIN_PHP_VERSION', '7.4.0');

    if (version_compare(PHP_VERSION, IDG_MIN_PHP_VERSION, '<'))
        idg_complain_version();
    
    if (preg_match('/^PHP.([0-9.]+).*Development Server.*/', @$_SERVER['SERVER_SOFTWARE'],
        $match)) {
        define('IDG_PHP_SERVER_VERSION', $match[1]);
        define('IDG_DEFAULT_USE_PATH_INFO', true); 
    } else {
        define('IDG_PHP_SERVER_VERSION', '');
        define('IDG_DEFAULT_USE_PATH_INFO', false);
    }
    
    define('IDG_URL_DEFAULT_FOLDER_SEPARATOR', '/');
    
    $user_agent = explode('/', @$_SERVER['HTTP_USER_AGENT']);

    if ($user_agent[0] == 'Wget') {
        define('IDG_WGET_VERSION', $user_agent[1]);
        define('IDG_URL_FOLDER_SEPARATOR', '-');
    } else {
        define('IDG_WGET_VERSION', '');
        define('IDG_URL_FOLDER_SEPARATOR', '/');
    }
    
    define('IDG_XML_INDENT', '    ');
    
    $path = explode('/', $_SERVER['DOCUMENT_ROOT']);
    $dir = array_pop($path);

    if (count($path) > 0 && array_pop($path) == 'indigo') {
        $dir = "'indigo/$dir'";
        if (IDG_PHP_SERVER_VERSION)
            die("<html><body><h1>Please do not run php -S"
                . " from a subdirectory ($dir).");
        else
            die("<html><body><h1>Please do not use a subdirectory ($dir) "
                . "as document root.</h1>");
    }

}

require_once 'library/diagnostics.php';
require_once 'library/parameters.php';
require_once 'library/object.php';
require_once 'library/tree.php';
require_once 'library/template.php';
require_once 'library/site.php';
require_once 'library/view.php';

require_once 'modules/module_markdown.php';
require_once 'modules/module_pagemap.php';
require_once 'modules/module_sourcefile.php';

idg_init();

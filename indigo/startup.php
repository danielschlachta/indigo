<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

spl_autoload_register(function ($class_name) {
    $name_arr = explode('_', $class_name);

    if ($name_arr[0] == 'idg') {
        if (count($name_arr) > 1) {
            $filename = __DIR__ . '/objects/' . $name_arr[1] . '.php';

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

require_once 'library/diagnostics.php';
require_once 'library/parameters.php';
require_once 'library/object.php';
require_once 'library/tree.php';
require_once 'library/site.php';
require_once 'library/view.php';

require_once 'modules/mod_md.php';
require_once 'modules/mod_pagemap.php';
require_once 'modules/mod_src.php';

$path = explode('/', $_SERVER['DOCUMENT_ROOT']);
$dir = array_pop($path);

if (count($path) > 0 && array_pop($path) == 'indigo') {
    $dir = "'indigo/$dir'";
    if (strpos($_SERVER['SERVER_SOFTWARE'], 'Development') > 0)
        die("<html><body><h1>Please do not run php -S"
            . " from a subdirectory ($dir).");
    else
        die("<html><body><h1>Please do not use a subdirectory ($dir) "
            . "as document root.</h1>");
}

define('IDG_SHORT_NAME', 'indigo/web');
define('IDG_VERSION', '1.4');
define('IDG_PROGRAM_NAME', IDG_SHORT_NAME . ' version ' . IDG_VERSION);

define('IDG_MIN_PHP_VERSION', '7.4.33');

define('IDG_XML_INDENT', '    ');

if (version_compare(PHP_VERSION, IDG_MIN_PHP_VERSION, '<'))
    idg_complain_version();

$ua = explode('/', @$_SERVER['HTTP_USER_AGENT']);

if ($ua[0] == 'Wget') {
    define('IDG_WGET_VERSION', ua[1]);
    define('IDG_URL_FOLDER_SEPARATOR', '-');
} else {
    define('IDG_WGET_VERSION', '');
    define('IDG_URL_FOLDER_SEPARATOR', '/');
}

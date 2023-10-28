<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

set_include_path(get_include_path() . ':' . __DIR__ . '/lib');
set_include_path(get_include_path() . ':' . __DIR__ . '/modules');
set_include_path(get_include_path() . ':' . __DIR__ . '/classes');

/*
spl_autoload_register(function ($class_name) {
    echo "<!-- $class_name triggered autoload -->\n";
    if (strpos($class_name, 'idg_') === 0) {
        $class_name = str_replace('idg_', '', $class_name);
        $class_name = str_replace('_declaration', '', $class_name);
        try {
            require_once $class_name . '.php';
        } catch (Exception $e) {
            ;
        }
    }
});
*/

require_once 'diagnostics.php';
require_once 'site.php';
require_once 'datasource.php';
require_once 'renderer.php';
require_once 'view.php';
require_once 'templates.php';

/** @todo move this */
require_once 'fragments.php';
require_once 'datasources.php';
require_once 'renderers.php';
require_once 'templates.php';
require_once 'mod_markdown.php';

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
define('IDG_VERSION', '1.2');
define('IDG_PROGRAM_NAME', IDG_SHORT_NAME . ' version ' . IDG_VERSION);

define ('IDG_MIN_PHP_VERSION', '7.4.33');

define('IDG_XML_INDENT', "\t");

if (version_compare(PHP_VERSION, IDG_MIN_PHP_VERSION, '<'))
	complain_version();

$ua = explode('/', @$_SERVER['HTTP_USER_AGENT']);

if ($ua[0] == 'Wget') {
    define('IDG_WGET_VERSION', ua[1]);
    define('IDG_URL_FOLDER_SEPARATOR', '-');
} else {
    define('IDG_WGET_VERSION', '');
    define('IDG_URL_FOLDER_SEPARATOR', '/');
}

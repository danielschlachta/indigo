<?php

/* ========================================================================
 * Indigo/Web
 * 
 * File: startup.php - the entry point
 * 
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

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

if ($idg_path == null)
    die('Before using indigo you must set $idg_path.');


$idg_short_name = 'indigo/web';
$idg_version = '1.2';

$idg_program_name = $idg_short_name . ' version ' . $idg_version;

$idg_min_php_version = '8.0.0';

require_once($idg_path . '/diagnostics.php');

if (version_compare(PHP_VERSION, $idg_min_php_version, '<'))
	complain_version();

require_once($idg_path . '/lib/site.php');
require_once($idg_path . '/lib/view.php');
require_once($idg_path . '/lib/view_html.php');
require_once($idg_path . '/classes/templates.php');
require_once($idg_path . '/classes/html_items.php');
require_once($idg_path . '/classes/renderers.php');
require_once($idg_path . '/classes/datasources.php');

$ua = explode('/', $_SERVER['HTTP_USER_AGENT']);

if ($ua[0] == 'Wget') {
    define('IDG_URL_FOLDER_SEPARATOR', '-');
} else {
    define('IDG_URL_FOLDER_SEPARATOR', '/');
}

?>

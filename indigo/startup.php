<?php

/* ========================================================================
 * Indigo/Web
 * 
 * File: startup.php - the entry point
 * 
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

if ($idg_path == null)
    die('Before using indigo you must set $idg_path.');

$idg_short_name = 'indigo/web';
$idg_version = '1.2';

$idg_program_name = $idg_short_name . ' version ' . $idg_version;

require_once($idg_path . '/diagnostics.php');

require_once($idg_path . '/lib/site.php');
require_once($idg_path . '/lib/view.php');
require_once($idg_path . '/lib/view_html.php');
require_once($idg_path . '/lib/parts.php');
require_once($idg_path . '/lib/views.php');
require_once($idg_path . '/lib/renderers.php');
require_once($idg_path . '/lib/datasources.php');

?>

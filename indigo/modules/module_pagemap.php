<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Module\Pagemap;

$pagemap_main = __DIR__ . '/pagemap/dist/pagemap.min.js';

if (!file_exists($pagemap_main)) {
    idg_complain_module('pagemap', $pagemap_main,
        'https://github.com/lrsjng/pagemap.git');
    exit;
}

function get_css($position_vert, $position_horz,
    $width, $height) {
    return "#map { position: fixed; $position_vert; $position_horz;" 
        . " width: $width; height: $height; z-index: 200; }\n";
}

/**
 * @todo: The path isn't always right!
 * @param type $view
 * @return type
 */

function get_html_body($view) {
    global $idg_path;
    
    return "<canvas id=\"map\"></canvas>\n"
        . "<script src=\"$idg_path/modules/pagemap/dist/pagemap.min.js\"></script>\n"
        . "<script>pagemap(document.querySelector('#map'));"
        . "</script>\n";
}

function add_to_view($view,
    $position_vert = 'top: 0', $position_horz = 'left: 0',
    $width = '160px', $height = '100%') {

    $view->stream_append('css', get_css(
            $position_vert, $position_horz, $width, $height));
    $view->stream_append('html-body', get_html_body($view));
}

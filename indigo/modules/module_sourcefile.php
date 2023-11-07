<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Module\Sourcefile;

$geshi_main = __DIR__ . '/geshi-1.0/src/geshi.php';

if (file_exists($geshi_main)) {
    require_once($geshi_main);
} else {
    idg_complain_module('geshi-1.0', $geshi_main,
        'https://github.com/GeSHi/geshi-1.0.git');
    exit;
}

class datasource extends \idg_datasource_implementation {

    function __construct($parameters = null) {
        parent::__construct($parameters);

        if (!($filename = $this->get_parameter('filename')))
            idg_diag($this, "parameter 'filename' not set");
       
        if (@stat($filename) === false)
            idg_diag($this, "file '$filename' not found");

        $max_size = @$this->parameters['max_size'];
        if (!$max_size || $max_size < 0)
            $max_size = 32 * 1024;

        $max_size = @$this->parameters['max_size'];
        if (!$max_size || $max_size < 0)
            $max_size = 32 * 1024;/** todo make this a parameter */

        if (@!$fp = fopen($filename, 'r'))
            idg_diag($this, "could not open file '$filename'");

        $this->add_token(fread($fp, $max_size));
        fclose($fp);
        
    }
}

/**
 * @todo documentation, parameters
 */

class renderer extends \idg_renderer_implementation {

    function _render(\idg_document $document, \idg_view $view) {
        $datasource = $this->get_datasource();
        
        $language = $datasource->get_parameter('language');
        $bgcolor = $datasource->get_parameter('bgcolor');
        $height = $datasource->get_parameter('height');

        if (!$bgcolor)
            $bgcolor = '#e8e8e8';

        if (!$height)
            $height = '100%';

        $datasource->rewind();
        $text = $datasource->current();
        
        $geshi = new \GeSHi($text, $language);
        $geshi->enable_keyword_links(false);
        $geshi->enable_line_numbers(\GESHI_FANCY_LINE_NUMBERS);
        $geshi->set_line_style("background: $bgcolor;", "background: #f0f0f0;");

        $content = "<div style=\"";

        $content .= "background: $bgcolor; ";
        $content .= "padding-top: 1px; padding-left: 5px; \">\n";

        $content .= $geshi->parse_code();

        $content .= "\n</div>";

        $view->stream_append('html-body', $content);

        $css = "pre.$language { width: 100%; ";

        $css .= "height: $height;";
        $css .= "overflow: auto; }\n";

        $view->stream_append('css', $css);
    }
}


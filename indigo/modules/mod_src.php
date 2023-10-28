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
    complain_module('geshi-1.0', $geshi_main,
        'https://github.com/GeSHi/geshi-1.0.git');
    exit;
}

class datasource extends \idg_datasource_object {

    function __construct($parameters = null) {
        parent::__construct($parameters);

        $filename = $this->get_parameter('filename');

        if (@stat($filename) === false)
            diag($this, get_class($this)
                . ': file "' . $filename . '" not found');

        $max_size = @$this->parameters['max_size'];
        if (!$max_size || $max_size < 0)
            $max_size = 32 * 1024;

        if (@!$fp = fopen($filename, 'r'))
            diag($this, get_class($this)
                . ': could not open text file "' . $filename . '"');

        $tok = fread($fp, $max_size);
        fclose($fp);
        $this->tokens[] = $tok;

        $this->tokens[] = filemtime($filename);

        if (!$filename)
            diag($this, get_class($this)
                . ': mandatory parameter(filename) not set');

        if (@stat($filename) === false)
            diag($this, get_class($this)
                . ': source file "' . $filename . '" not found');

        $max_size = @$this->parameters['max_size'];
        if (!$max_size || $max_size < 0)
            $max_size = 32 * 1024;/** todo make this a parameter */

        if (@!$fp = fopen($filename, 'r'))
            diag($this, get_class($this)
                . ': could not open source file "' . $filename . '"');

        $tok = fread($fp, $max_size);
        fclose($fp);
        $this->tokens[] = $tok;
    }
}

/**
 * @todo documentation, parameters
 */

class renderer extends \idg_renderer {

    function render(idg_document $document, idg_view $view) {
        $this->datasource->rewind();
        $parameters = $this->datasource->get_token();

        $language = 'php';
        //$language = $this->get_parameters('language');
        //$bgcolor = $this->get_parameter('bgcolor');
        //$height = $this->get_parameter('height');

        if (!$bgcolor)
            $bgcolor = '#e8e8e8';

        if (!$height)
            $height = '100%';

        $text = $this->datasource->get_token();

        $geshi = new GeSHi($text, $language);
        $geshi->enable_keyword_links(false);
        $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
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


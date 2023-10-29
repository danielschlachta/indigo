<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://openslot.org/license/mit/
 */

/**
 * Type information for idg_view.
 */
class idg_view_type extends idg_treenode_type {

    function __construct() {
        parent::__construct();

        $this->child_types = ['idg_template'];

        $this->child_types = [
            'idg_container',
            'idg_fragment'
        ];

        $this->register_property('icon');
        $this->register_property('filter');
    }
}

/**
 * Class representing a view.
 */
class idg_view extends idg_treenode {

    private array $streams = [
        'html-head' => '',
        'css' => '',
        'css-print' => '',
        'js' => '',
        'html-body-start' => '',
        'html-body' => ''
    ];
    private string $output = '';
    private array $filters = [];

    function __construct() {
        parent::__construct();
        $this->set_element_name('view');
    }

    /**
     * Adds a filter with a given name or overwrites it if one already exists.
     * @param string $function The name of a global function to call
     */
    function add_filter(string $function): void {
        if (!function_exists($function))
            idg_diag($this, "function '$function' does not exist");
        $this->filters[$function] = true;
    }

    /**
     * Enables/disables a filter function.
     * It is not an error if no such filter exists.
     * @param string $function The filter function
     * @param boolean $enabled Whether the filter is applied
     */
    function enable_filter(string $function, bool $enabled = true): void {
        if (array_key_exists($function, $this->filters))
            $this->filters[$function] = $enabled;
    }

    /**
     * Write some content to a stream.
     * @param string $stream_name The name of the stream
     * @param string $content The content to write to the stream
     */
    function stream_append(string $stream_name, string $content): void {
        if (array_key_exists($stream_name, $this->streams)) {
            if (($stream_name == 'html-body')) {
                foreach ($this->filters as $filter => $is_set) {
                    echo "<!-- filter: $filter: $is_set -->\n";
                    if ($is_set)
                        $content = $filter($content);
                }

                if (($filter = $this->get_property('filter'))) {
                    if (!function_exists($filter))
                        idg_diag($this, "filter function '$filter' does not exist");

                    $content = $filter($content);
                }
            }

            $this->streams[$stream_name] .= $content;
        } else
            idg_diag($this, "stream  '$stream_name' does not exist");
    }

    /**
     * Print everyting.
     */
    function print(): void {
        if ($this->output == '')
            idg_diag($this, "nothing to print, you probably didn't call render()");
        echo $this->output;
    }

    private function _print(string $string): void {
        $this->output .= $string;
    }

    private static function uuid_v4(): string {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private static function milliseconds(): int {
        return hrtime(true) / 1e+6;
    }

    function render(idg_document $document): void {
        if ($this->output != '')
            idg_diag($this, "don't call render more than once, create a new view instead");

        $children = $this->get_children();

        if (!$children)
            die('<code>This page intentionally left blank.</code>');

        $style = $this->get_property('style');
        $icon = $this->get_property('icon');

        $document->uuid = idg_view::uuid_v4();

        $render_start = idg_view::milliseconds();

        foreach ($this->get_children() as $child)
            $child->_render($document, $this);

        $render_time = idg_view::milliseconds() - $render_start;

        $date = new \DateTimeImmutable($document->get_property('last-change'));
        $last_mod = $date->getTimestamp();

        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $last_mod) . " GMT");

        $this->_print('<!-- document UUID=' . $document->uuid
            . ' generated on ' . date('r', time())
            . " by " . IDG_PROGRAM_NAME . ", time: $render_time ms  -->\n"
            . "<!DOCTYPE html>\n");

        $this->_print('<html lang="'
            . $document->get_site()->get_property('content-language')
            . "\">\n"
            . "<head>\n"
            . " <title>" . $document->get_property('title')
            . "</title>\n"
            . " <meta http-equiv=\"Content-Type\" content=\"text/html;"
            . " charset=utf-8\">\n");

        if ($icon)
            $this->_print(" <link rel=\"shortcut icon\" href=\"$icon\" "
                . "type=\"image/x-icon\">\n");

        if ($this->streams['html-head'] != '')
            $this->_print($this->streams['html-head'] . "\n");

        /** @todo: this should be derivable * */
        $css = '';

        if ($style)
            $css .= "  	body { $style }\n";

        $css .= $this->streams['css'];

        if ($this->streams['css-print'] != '') {
            $css .= "	@media print {\n";
            $css .= $this->streams['css-print'];
            $css .= "	}\n";
        }

        if ($css != '')
            $this->_print(" <style>\n$css </style>\n");

        if ($this->streams['js'] != '') {
            $this->_print(" <script>\n");
            $this->_print($this->streams['js']);
            $this->_print(" </script>\n");
        }
        $this->_print("</head>\n<body>\n");
        $this->_print($this->streams['html-body-start']);
        $this->_print($this->streams['html-body']);
        $this->_print("</body>\n</html>");
    }
}

class idg_view_element_type extends idg_treenode_type {

    const CSS_PROPERTIES = [
        'style' => '',
        'style-h1' => 'h1',
        'style-h2' => 'h2',
        'style-h3' => 'h3',
        'style-a' => 'a',
        'style-a-hover' => 'a:hover',
        'style-a-visited' => 'a:visited',
        'style-img' => 'img',
        'style-list-img' => ''
    ];

    function __construct() {
        parent::__construct();

        foreach (idg_view_element_type::CSS_PROPERTIES as $property => $style)
            $this->register_property($property);

        $this->register_property('filter');
        $this->register_property('style-print');
    }
}

abstract class idg_view_element extends idg_treenode {

    function _render(idg_document $document, idg_view $view): void {
        echo "<!-- render default called -->\n";
        $object_name = get_class($this) . '_' . $this->get_property($class);
        $object = new $object_name;
        $object->_render($document, $view);
    }
}

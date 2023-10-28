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

        $this->register_property('class');
        $this->set_property_mandatory('class');
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
            diag($this, "function '$function' does not exist");
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
                        diag($this, "filter function '$filter' does not exist");

                    $content = $filter($content);
                }
            }

            $this->streams[$stream_name] .= $content;
        } else
            diag($this, "stream  '$stream_name' does not exist");
    }

    /**
     * Print everyting.
     */
    function print(): void {
        if ($this->output == '')
            diag($this, "nothing to print, you probably didn't call render()");
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
            diag($this, "don't call render more than once, create a new view instead");

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

        $date = new DateTimeImmutable($document->get_property('last-change'));
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

class idg_container_type extends idg_view_element_type {

    function __construct() {
        parent::__construct();

        $this->child_types = [
            'idg_container',
            'idg_fragment',
            'idg_slot'
        ];

        $this->register_property('filter');
        $this->register_property('anchor');
    }
}

class idg_container extends idg_view_element {

    function __construct() {
        parent::__construct();
        $this->set_element_name('container');
    }

    function _render(idg_document $document, idg_view $view): void {

        $idg_id = $this->get_idg_id();

        $css = '';

        foreach (idg_view_element_type::CSS_PROPERTIES as $property => $style)
            if (($value = $this->get_property($property)))
                $css .= "	div#$idg_id {\n		$value\n	}\n\n";

        $view->stream_append('css', $css);

        if ($style_print = $this->get_property('style-print')) {
            $css_print = "div#$idg_id { $style_print }\n";
            $view->stream_append('css-print', $css_print);
        }

        if ($css != '' || $css_print != '')
            $view->stream_append('html-body', "  <div id=\"$idg_id\">\n");

        $filter = $this->get_property('filter');
        if ($filter)
            $view->add_filter($filter);

        if (($children = $this->get_children()))
            foreach ($children as $child)
                $child->_render($document, $view);

        if ($filter)
            $view->enable_filter($filter, false);

        if ($css != '' || $css_print != '')
            $view->stream_append('html-body', "  </div>\n");
    }
}

class idg_fragment_type extends idg_view_element_type {

    function __construct() {
        parent::__construct();
        $this->child_types = [];

        $this->register_property('class');
        $this->set_property_mandatory('class');
        $this->register_property('source');
    }
}

class idg_fragment extends idg_view_element {

    function __construct() {
        parent::__construct();
        $this->set_element_name('fragment');
    }

    function _render(idg_document $document, idg_view $view): void {
        if (!$class_name = $this->get_property('class'))
            return;

        $class_name = 'idg_fragment_' . $class_name;

        if (!class_exists($class_name))
            diag($this, "class '$class_name' does not exist");

        if (!is_subclass_of($class_name, 'idg_fragment_object'))
            diag($this, "class '$class_name' is not a subclass of idg_fragment_object");

        $datasource = null;
        if (($source_name = $this->get_property('source')))
            $datasource = $document->get_datasource($source_name);

        $object = new $class_name($this, $datasource);
        $object->_render($document, $view);
    }
}

abstract class idg_fragment_object {

    private idg_view_element $parent;
    private ?idg_datasource_object $datasource;

    public function __construct(idg_view_element $parent,
        ?idg_datasource_object $datasource = null) {
        $this->parent = $parent;
        $this->datasource = $datasource;
    }

    function get_parent(): idg_view_element {
        return $this->parent;
    }

    function get_datasource(): ?idg_datasource_object {
        return $this->datasource;
    }
}

class idg_slot_type extends idg_view_element_type {

    function __construct() {
        parent::__construct();

        $this->child_types = [];
        $this->set_property_mandatory('name');
    }
}

class idg_slot extends idg_view_element {

    function __construct(idg_treenode $parent = null) {
        parent::__construct($parent);
        $this->set_element_name('slot');
    }

    function _render(idg_document $document, idg_view $view): void {
        $idg_id = $this->get_idg_id();
        $name = $this->get_property('name');
        $renderers = $document->get_renderers($name);

        if (($filter = $this->get_property('filter')))
            $view->add_filter($filter);

        $has_anchors = false;
        foreach ($renderers as $renderer)
            if (($anchor = $renderer->get_property('anchor')))
                $has_anchors = true;

        if ($has_anchors)
            $view->stream_append('html-body', "<div class=\"$idg_id-anchor\">\n");

        foreach ($renderers as $renderer)
            $renderer->_render($document, $view);

        if ($has_anchors)
            $view->stream_append('html-body', "</div>\n");

        if ($filter)
            $view->enable_filter($filter, false);

        /** @todo implement this properly
          $style = $this->get_property('style');
          $style_link = $this->get_property('style-link');
          $style_link_hover = $this->get_property('style-link-hover');

          $list_style_image = $this->get_property('list-style-image');
          $list_style_position = $this->get_property('list-style-position');

          $css = '';

          if ($style)
          $css .= "	div.$idg_id { $style }\n";

          if ($style_link)
          $css .= "	div.$idg_id a { $style_link }\n";

          if ($style_link_hover)
          $css .= "	div.$idg_id a:hover { $style_link_hover }\n";

          if ($list_style_image || $list_style_position) {
          $css .= "	div.$idg_id ul {\n";

          if ($list_style_image)
          $css .= "		list-style-image: url($list_style_image);\n";

          if ($list_style_position)
          $css .= "	list-style-position: $list_style_position;";

          $css .= "	}\n\n";
          }

          /** @todo css-print?
          if ($anchor_count > 0)
          $css .= "	div.$idg_id-anchor {\n		display: none;\n	}\n\n";

          $view->stream_append('css', $css); */
    }
}

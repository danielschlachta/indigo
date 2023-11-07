<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://openslot.org/license/mit/
 */

/**
 * Type information for idg_view_element.
 */
class idg_view_element_type extends idg_treenode_type {

    function __construct(bool $register_styles = true) {
        parent::__construct();

        if ($register_styles) {
            foreach (idg_view::CSS_PROPERTIES as $property => $style)
                $this->register_property($property);

            $this->register_property('style-list-image');
            $this->register_property('style-print');
        }
    }
}

/**
 * The parent class for idg_view and every sibling thereof.
 */
abstract class idg_view_element extends idg_treenode {

    function _render(idg_document $document, idg_view $view): void {
        
    }
}

/**
 * Type information for idg_view.
 */
class idg_view_type extends idg_view_element_type {

    function __construct() {
        parent::__construct();

        $this->child_types = [
            'idg_container',
            'idg_fragment',
            'idg_filter'
        ];

        $this->register_property('class');
        $this->register_property('icon');
    }
}

/**
 * A view.
 */
class idg_view extends idg_view_element {

    const CSS_PROPERTIES = [
        'style' => '',
        'style-h1' => 'h1',
        'style-h2' => 'h2',
        'style-h3' => 'h3',
        'style-a' => 'a',
        'style-a-hover' => 'a:hover',
        'style-a-visited' => 'a:visited',
        'style-img' => 'img'
    ];

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
    private ?idg_core $core;
    
    function __construct(idg_core $core = null) {
        parent::__construct('view');
        $this->core = $core;
    }
   
    /**
     * @todo Maybe we don't need this?
     * @param idg_view_element $element
     * @param string $property
     * @param string $value
     */
    function inject_css(idg_view_element $element, string $property, string $value) {
        
    }

    /**
     * Emits <code>css</code> style information according to the properties of an object.
     * @param idg_object $object The object
     * @param string $id The id part of the style information
     * @param array $style An array of object property to <code>css</code> mappings
     * @return bool Whether something was emitted
     */
    function render_css(idg_object $object, string $id,
        array $styles = idg_view::CSS_PROPERTIES): bool {

        $retval = false;

        foreach ($styles as $key => $value)
            if (($style = $object->get_property($key))) {
                if ($value != '')
                    $value = ' ' . $value;
                $this->stream_append('css', "$id$value { $style }\n");
                $retval = true;
            }

        if (($list_style_image = $object->get_property('style-list-image'))) {
            $this->stream_append('css',
                "$id ul { list-style-image: url($list_style_image); }\n");
            $retval = true;
        }

        if (($style_print = $this->get_property('style-print'))) {
            $this->stream_append('css-print', "$id { $style }\n");
            $retval = true;
        }

        return $retval;
    }
    
    /**
     * Adds a filter with a given name.
     * It is not an error if the filter already exists. If <code>$fun</code>
     * is not specified, <code>$function</code> is treated as a function name,
     * otherwise <code>$fun</code> will be called. The functions must have
     * the following signature:
     * 
     *      function filter(idg_view $view, string $text): ?string;
     * 
     * If the filter returns <code>null</code> it is ignored.
     * <blockquote>
     * If a namespace is set in the view, the filter will be looked for 
     * in that namespace.
     * </blockquote>
     * @param string $function The name of the filter function
     * @param callable $fun A closure to execute as the filter
     */
    function add_filter(string $function, callable $fun = null): void {
        if (!$fun)
            $fun = @$this->filter_repo[$function];
        
        if (!$fun && !function_exists($function))
            idg_diag($this, "filter function '$function' does not exist");

        $this->filters[$function] = $fun; // null means global function is called
    }

    /**
     * Enables/disables a filter function.
     * It is not an error if no such filter exists.
     * @param string $function The filter function
     * @param boolean $enabled Whether the filter is applied
     */
    function remove_filter(string $function): void {
        unset($this->filters[$function]);
    }

    /**
     * Write some content to a stream.
     * @param string $stream_name The name of the stream
     * @param string $content The content to write to the stream
     */
    function stream_append(string $stream_name, string $content): void {
        if (array_key_exists($stream_name, $this->streams)) {
            if ($stream_name == 'html-body')
                foreach ($this->filters as $filter => $fun) {
                    $tmp = null;

                    if ($fun)
                        $tmp = $fun($this, $content);
                    else
                        $tmp = $filter($this, $content);

                    if ($tmp)
                        $content = $tmp;
                }

            $this->streams[$stream_name] .= $content;
        } else
            idg_diag($this, "stream  '$stream_name' does not exist");
    }
    
    /**
     * Returns a stream the way it is, which depends on when this function is called.
     * This is useful for data sources such as \Indigo\Datasource\extlinks placed
     * in strategic locations throughout the document.
     * @param string $stream_name The name of the stream
     * @return string|null The stream in its current state
     */
    function get_stream(string $stream_name): ?string {
        return @$this->streams[$stream_name];
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

    /**
     * Renders a view with no <code>class</code> property set.
     * @param idg_document $document
     * @param idg_view $view
     */
    function _render(idg_document $document, idg_view $view): void {
        $style = array_replace(idg_view::CSS_PROPERTIES, ['style' => 'body']);
        $this->render_css($this, '', $style);

        foreach ($this->get_children() as $child)
            $child->_render($document, $this);
    }
    
    /**
     * 
     * @param idg_document $document
     * @return void
     */
    function render(idg_document $document): void {
        if ($this->output != '')
            idg_diag($this,
                "don't call render more than once, create a new view instead");

        $children = $this->get_children();

        if (!$children)
            die('<code>This page intentionally left blank.</code>');

        $document->set_property('uuid', idg_view::uuid_v4());
        $render_start = idg_view::milliseconds();

        foreach ($children as $child)
            if ($child->get_element_name() == 'filter') 
                $child->_apply_filter($this);
        
        if ($this->get_property('class'))
            $this->create_instance()->_render($document, $this);
        else
            $this->_render($document, $this);
        
        foreach ($children as $child)
            if ($child->get_element_name() == 'filter')
                $this->remove_filter($child->get_property('name'));

        $render_time = idg_view::milliseconds() - $render_start;

        $date = new \DateTimeImmutable($document->get_property('last-change'));
        $last_mod = $date->getTimestamp();

        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $last_mod) . " GMT");

        $wget = IDG_WGET_VERSION ? " for wget v" . IDG_WGET_VERSION : '';
            
        $this->_print('<!-- document UUID=' . $document->get_property('uuid')
            . ' generated on ' . date('r', time())
            . " by " . IDG_PROGRAM_NAME . "$wget, time: $render_time ms  -->\n"
            . "<!DOCTYPE html>\n");

        $this->_print('<html lang="'
            . $document->get_site()->get_property('content-language')
            . "\">\n"
            . "<head>\n"
            . "<title>" . $document->get_property('title') . "</title>\n"
            . "<meta http-equiv=\"Content-Type\" content=\"text/html;"
            . " charset=\"utf-8\">\n");

        if (($icon = $this->get_property('icon')))
            $this->_print("<link rel=\"shortcut icon\" href=\"$icon\" "
                . "type=\"image/x-icon\">\n");

        if ($this->streams['html-head'] != '')
            $this->_print($this->streams['html-head'] . "\n");

        if ($this->streams['js'] != '') {
            $this->_print("<script>\n");
            $this->_print($this->streams['js']);
            $this->_print("</script>\n");
        }

        if ($this->streams['css'] != '') {
            $this->_print("<style>\n");
            $this->_print($this->streams['css']);
            $this->_print("</style>\n");
        }

        if ($this->streams['css-print'] != '') {
            $this->_print("<style>\n@media print {\n");
            $this->_print($this->streams['css-print']);
            $this->_print("}\n</style>\n");
        }

        $this->_print("</head>\n<body>\n");
        $this->_print($this->streams['html-body-start']);
        $this->_print($this->streams['html-body']);
        $this->_print("</body>\n</html>");
    }
    
    /**
     * Returns the idg_core object the view was initialized with or a default one.
     * @return idg_core The core
     */
    function core(): idg_core {
        if (!$this->core)
            $this->core = new idg_core;
        
        return $this->core;
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
        return (int)(hrtime(true) / 1e+6);
    }
}

/**
 * An object instance of a view.
 * Actual classes need to derive from this one since they do not get type information.
 */
class idg_view_implementation extends idg_object_implementation {

    /**
     * Convenience function: calls the view's get_children() method.
     * @see idg_leafnode\get_chilren()
     */
    protected function get_children(): ?array {
        return $this->get_declaration()->get_children();
    }

    /**
     * Convenience function: calls the view's stream_append() method.
     * @see idg_view\stream_append()
     */
    protected function stream_append(string $stream_name, string $content): void {
        $this->get_declaration()->stream_append($stream_name, $content);
    }
}

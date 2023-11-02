<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://openslot.org/license/mit/
 */

class idg_view_element_type extends idg_treenode_type {

    function __construct() {
        parent::__construct();

        foreach (idg_view::CSS_PROPERTIES as $property => $style)
            $this->register_property($property);

        $this->register_property('style-list-image');
        $this->register_property('style-print');
    }
}

abstract class idg_view_element extends idg_treenode {

    private array $filters = [];
  
    abstract function _render(idg_document $document, idg_view $view): void;
}

/**
 * Type information for idg_view.
 */
class idg_view_type extends idg_view_element_type {

    function __construct() {
        parent::__construct();

        $this->child_types = [
            'idg_container',
            'idg_fragment'
        ];

        $this->register_property('icon');
        $this->register_property('class');
    }
}

/**
 * Class representing a view.
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
    // For load_template():
    private ?string $template_name = null;
    private ?string $namespace = null;
    private ?string $rel_path = null;
    private ?string $rel_url = null;

    function __construct() {
        parent::__construct('view');
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

        if (($list_style_image = $this->get_property('style-list-image'))) {
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
        if (!$fun && !function_exists($function))
            idg_diag($this, "filter function '$function' does not exist");
        
        $this->filters[$function] = $fun;
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

        $document->uuid = idg_view::uuid_v4();
        $render_start = idg_view::milliseconds();

        if ($this->get_property('class'))
            $this->create_instance()->_render($document, $this);
        else
            $this->_render($document, $this);

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
     * Loads a template from a directory.
     * The function looks for a file named <code>load.php</code> in the directory 
     * specified by <code>name</code>, scans it for a  <code>namespace</code> 
     * directive, loads it and then sets the view's class to be the view object declared
     * in the file.
     * @param string $name The name of the template
     * @param string $rel_path Where <code>php</code> can find the directory
     * @param string $rel_url Where the web server can find the files in the directory
     * @return bool True on success
     */
    function load_template(string $name, string $rel_path = null,
        string $rel_url = null): bool {
        $filename = ($rel_path ? $rel_path . '/' . $name : $name) . '/load.php';

        if (!file_exists($filename))
            return false;

        if (!($fp = fopen($filename, "r")))
            return false;

        $string = fread($fp, 2048);
        fclose($fp);

        if (preg_match('/.*namespace[ \t\n]+([^;]+);/', $string, $matches) !== 1)
            return false;

        $this->template_name = $name;
        $this->namespace = $matches[1];
        $this->rel_path = $rel_path;
        $this->rel_url = $rel_url;

        require_once $filename;

        $this->set_property('class', $this->namespace . '\view');
        
        return true;
    }
    
    /**
     * Sets the namespace of the view.
     * This is useful when <code>load_template()</code> has not been executed.
     * @see qualify()
     * @param string $namespace The namespace
     */
    function set_namespace(string $namespace): void {
        $this->namespace = $namespace;
    }

    /**
     * Returns the namespace associated with the template if one is loaded.
     * @return string|null The namespace.
     */
    function get_namespace(): ?string {
        return $this->namespace;
    }

    /**
     * Returns the URL prefix to where sundry files are normally stored for a template.
     * @param string subdir Optional name of a subdirectory for a component
     * @return string The partial URL
     */
    function get_elements(string $subdir = null): string {
        if (!$this->template_name)
            idg_diag($this, "no template loaded");

        $url = $this->rel_url;

        if (!$url)
            $url = $this->rel_path;

        return ($url ? $url . '/' : '') . $this->template_name . '/'
            . ($subdir ? $subdir . '/' : '') . 'elements';
    }

    /**
     * Simply prefixes a string with the view's namespace, if set.
     * @param string $string The string
     * @return string The string with the namespace prepended
     */
    function qualify(string $string): string {
        if ($this->namespace)
            return $this->namespace . '\\' . $string;

        return $string;
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

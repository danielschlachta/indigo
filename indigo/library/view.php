<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://openslot.org/license/mit/
 */

/**
 * Type information for idg_view_element.
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

/**
 * The parent class for idg_view and every sibling thereof.
 */
abstract class idg_view_element extends idg_treenode {

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
            'idg_fragment',
            'idg_filter'
        ];

        $this->register_property('class');
        $this->register_property('icon');
    }
}

/**
 * Specifies how the view should handle the reloading of the emitted page.
 * @see idg_view\set_reload_policy()
 */
class idg_reload_policy {

    /**
     * Don't do anything regarding last change and caching, default.
     */
    const RELOAD_NOHEADER = 0;

    /**
     * Always reload the page, aggressively sets headers and pragmas.
     */
    const RELOAD_ALWAYS = 1;

    /**
     * Emit Last-Change header according to the view's property which can be set after
     * rendering.
     */
    const RELOAD_LASTCHANGE = 2;
    
    /**
     * Like RELOAD_LASTCHANGE but defaults to time() if nothing else is set.
     */
    const RELOAD_LASTCHANGE_DEFAULT_NOW = 3;
}

/**
 * A view.
 */
class idg_view extends idg_view_element {

    /**
     * Style-related properties recognized by idg_view, idg_container, idg_slot 
     * and some idg_fragment descendants based on their implementation.
     */
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

    private int $reload_policy = idg_reload_policy::RELOAD_NOHEADER;
    private int $last_change = 0;
    private array $streams = [
        'html-head' => '',
        'css' => '',
        'css-print' => '',
        'js' => '',
        'html-body-start' => '',
        'html-body' => ''
    ];
    private array $fonts = [];
    private string $output = '';
    private array $filters = [];
    private ?idg_template $template;

    function __construct(idg_template $template = null) {
        parent::__construct('view');
        $this->template = $template;
    }

    /**
     * Sets the reload policy governing how related headers are handled.
     * @see idg_reload_policy
     * @param int $reload_policy The policy as defined in idg_reload_policy
     */
    function set_reload_policy(int $reload_policy): void {
        $this->reload_policy = $reload_policy;
    }

    /**
     * Emits <code>css</code> style information according to the properties of an object.
     * @param idg_object $object The object
     * @param string $id The id part of the style information
     * @param array $style An array of object property to <code>css</code> mappings
     * @return bool Whether something was emitted
     */
    function render_css(idg_object $object, string $id = null,
        array $styles = idg_view::CSS_PROPERTIES): bool {

        $retval = false;

        foreach ($styles as $key => $value)
            if (($style = $object->get_property($key))) {
                if ($key == 'style' && $object instanceof idg_view)
                    $value = 'body';

                if ($value && $id)
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
     * Returns the idg_template object the view was initialized with or a default one.
     * @return idg_template The template
     */
    function template(): idg_template {
        if (!$this->template)
            $this->template = new idg_template;

        return $this->template;
    }

    /**
     * Outputs everything, emits headers if requested.
     */
    function emit(): void {
        if ($this->output == '')
            idg_diag($this, "nothing to do, you probably didn't call render()");
        if ($this->output == '@done@')
            idg_diag($this, "can't emit output more than once");

        switch ($this->reload_policy) {
            case idg_reload_policy::RELOAD_LASTCHANGE_DEFAULT_NOW:
                if (!($this->last_change > 0))
                    $this->last_change = time();
                // fall through
            case idg_reload_policy::RELOAD_LASTCHANGE:
                if ($this->last_change > 0)
                    header("Last-Modified: "
                        . gmdate("D, d M Y H:i:s", $this->last_change) . " GMT");
                break;
            case idg_reload_policy::RELOAD_ALWAYS:
                header("Cache-Control: max-age=3000, no-cache, no-store,"
                    . " must-revalidate");
                header("Pragma: no-cache");
                header("Expires: 0");
                break;
        }

        echo $this->output;
        $this->output = '@done@';
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
        $this->render_css($this);

        foreach ($this->get_children() as $child)
            if (!($child instanceof idg_filter))
                $child->_render($document, $this);
    }

    private function _css_scan_fonts() {
        if (!($css_out = $this->streams['css']))
            return;

        $delim = " \t\n";
        $property = '';
        $value = '';
        $token = strtok($this->streams['css'], $delim);
        $state = 100;

        while ($token !== false) {
            if (($pos = strpos($token, ';')) !== false) {
                $token = substr($token, 0, $pos);
                $state = 300;
            } else if (($pos = strpos($token, '}')) !== false) { // malformed but works
                $token = substr($token, 0, $pos);
                $state = 300;
            }
            switch ($state) {
                case 100:
                    if (($pos = strpos($token, ':')) > 0) {
                        $property = substr($token, 0, $pos);
                        $value = '';
                        $state = 200;
                    }
                    break;
                case 200:
                    $value .= "$token ";
                    break;
                case 300:
                    $value .= $token;
                    if (strtolower($property) == 'font-family') {
                        $match = [];
                        if (preg_match_all("|@([a-z]+)\('([^'\)]*)'\)|", $value, $match))
                            for ($i = 0;
                                $i < count($match[0]);
                                $i++) {
                                $full = $match[0][$i];
                                $selector = $match[1][$i];
                                $font = $match[2][$i];
                                if (!array_key_exists($selector, $this->fonts) ||
                                    !in_array($font, $this->fonts[$selector]))
                                    $this->fonts[$selector][] = $font;
                                $css_out = str_replace($full, "'$font'", $css_out);
                            }
                    }
                    $property = '';
                    $state = 100;
                    break;
            }
            $token = strtok($delim);
        }
        $this->streams['css'] = $css_out;
    }

    /**
     * Renders a document and stores the result.
     * @param idg_document $document The document
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
            if ($child instanceof idg_filter)
                $child->apply_filter($this);

        if ($this->get_property('class'))
            $this->create_instance()->_render($document, $this);
        else
            $this->_render($document, $this);

        foreach ($children as $child)
            if ($child instanceof idg_filter)
                $this->remove_filter($child->get_property('name'));

        $this->_css_scan_fonts();

        $render_time = idg_view::milliseconds() - $render_start;

        if (($lastchg = $document->get_property('last-change'))) {
            $date = new \DateTimeImmutable($lastchg);
            $this->last_change = $date->getTimestamp();
        }

        $wget = IDG_WGET_VERSION ? " for wget v" . IDG_WGET_VERSION : '';

        $this->_print('<!-- document UUID=' . $document->get_property('uuid')
            . ' generated on ' . date('r', time())
            . " by " . IDG_PROGRAM_NAME . "$wget, time: $render_time ms  -->\n"
            . "<!DOCTYPE html>\n");

        if (($lang = $document->get_site()->get_property('content-language')))
            $lang = " lang=\"$lang\"";

        $this->_print("<html$lang>\n"
            . "<head>\n"
            . "<title>" . $document->get_property('title') . "</title>\n"
            . "<meta http-equiv=\"Content-Type\" content=\"text/html\">\n"
            . "<meta charset=\"utf-8\">\n");

        if (($icon = $this->get_property('icon')))
            $this->_print("<link rel=\"shortcut icon\" href=\"$icon\" "
                . "type=\"image/x-icon\">\n");

        if (array_key_exists('google', $this->fonts)) {
            $this->_print(
                "<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\">\n");

            foreach ($this->fonts['google'] as $font) {
                $family = str_replace(' ', '+', $font);
                $this->_print("<link href=\"https://fonts.googleapis.com/css2?"
                    . "family=$family&display=swap\" rel=\"stylesheet\">\n");
            }
        }

        if ($this->streams['html-head'] != '')
            $this->_print($this->streams['html-head']);

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

    private static function uuid_v4(): string {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private static function milliseconds(): int {
        return (int) (hrtime(true) / 1e+6);
    }
}

/**
 * An object instance of a view.
 * Actual classes need to derive from this one since they do not get type information.
 */
abstract
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

    protected function _render(idg_document $document, idg_view $view): void {
        $view->_render($document, $view);
    }
}

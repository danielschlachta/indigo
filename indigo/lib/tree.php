<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: tree.php - defines the tree structure for the view
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

require_once($idg_path . '/lib/object.php');

// contains idg_attribute for now.
require_once($idg_path . '/lib/param_instance.php');

class idg_tree_node_type extends idg_object_type {

    public $child_types = array('idg_tree_node');

    function __construct() {
        parent::__construct();
    }
}

class idg_tree_node extends idg_object {

    static $idg_xml_indent = '  ';
    private $parent;
    private $attributes = [];
    private $current_attribute;
    private $current_option;
    private $parameters = [];
    private $current_parameter;
    protected $children; // accessed in view_html

    /** @todo nicer mech.? */
    protected $idg_xml_translation;
    private $_xml_current_object;

    function __construct(&$parent = null) {
        parent::__construct();
        $this->parent = $parent;
    }

    function get_parent() {
        return $this->parent;
    }

    function create_attribute($name) {
        if (@$this->attributes[$name])
            diag($this, "duplicate attribute: $name");

        return $this->attributes[$name] = new idg_attribute($name, $this);
    }

    function get_attribute($name, $scope = null) {
        $full_name = "$scope::$name";

        if ($scope && array_key_exists($full_name, $this->attributes))
            return $this->attributes[$full_name];

        return @$this->attributes[$name];
    }

    /** Set the parent object manually.
     *
     * This is necessary when programmatically constructing
     * site elements.
     */
    function set_parent($parent) {
        $this->parent = $parent;
    }

    function get_children() {
        return $this->children;
    }

    /**
     * Finds a child whose property \c $key_name is set to \c $key_value.
     *
     * Returns null if nothing was found.
     * This function is recursive.
     */
    function get_child_by_key($key_name, $key_value,
        $child_type = false) {

        if ($this->children)
            foreach ($this->children as $child) {
                if (($child->get_property($key_name) == $key_value) && (!$child_type || (get_class($child) == $child_type)))
                    return $child;

                if ($childchild = $child->get_child_by_key(
                    $key_name, $key_value, $child_type))
                    return $childchild;
            }
    }

    /**
     * Returns an array of all children whose property \c $key_name is
     * set to \c $key_value and have type \c $child_type if specified.
     *
     * Returns null if nothing was found.
     * This function is not recursive!
     */
    function get_children_by_key($key_name, $key_value,
        $child_type = null) {

        $retval = array();
        foreach ($this->children as $child) {
            if (($child->get_property($key_name) == $key_value) && (!$child_type || (get_class($child) == $child_type)))
                $retval[] = $child;
        }

        if (count($retval) == 0)
            return;

        return $retval;
    }

    function add_child(&$child) {
        if (!in_array(get_class($child), $this->type_obj->child_types))
            diag($this, $this->get_idg_id()
                . ': add child: incompatible child type \''
                . get_class($child) . '\'');
        $child->parent = & $this;
        $this->children[] = & $child;
    }

    function get_parameter(string $name) {
        return @$this->parameters[$name];
    }
    
    function set_parameter(string $name, mixed $value) {
        $this->parameters[$name] = $value;
    }
    
   function get_parameters() {
       return $this->parameters;
   }
   
    /**
     * Checks the whole subtree.
     *
     * @todo Currently only checks for the presence of mandatory
     * options. Could use some more thorough probing.
     */
    function check() {
        $dummy = false;
        $this->traverse($dummy, '$this->_check');
    }

    /**
     * Prints an ASCII representation of a subtree.
     *
     * @todo This does not currently work. Add support for attributes.
     */
    function print_debug() {
        $dummy = false;
        $this->traverse($dummy, '$this->_print_debug');
    }

    function traverse(&$param,
        $start_function, $end_function = false, $depth = 0, $path = '') {
        if (strpos($start_function, '$this->') === 0) {
            $do_func = substr($start_function, strlen('$this->'));
            $retval = $this->$do_func($param, $depth, $path);
        } else
            $retval = $start_function($param, $depth, $path);

        if ($this->children) {
            foreach ($this->children as $child) {
                $child->parent = & $this;
                if (!$child->traverse($param,
                        $start_function, $end_function,
                        $depth + 1, $path . $this->get_idg_id() . '/'))
                    return false;
            }
        }

        if ($end_function)
            if (strpos($end_function, '$this->') == 0) {
                $do_func = substr($end_function, strlen('$this->'));
                $this->$do_func($param, $depth, $path);
            } else
                $end_function($param, $depth, $path);

        return $retval;
    }

    function write_xml($file_name) {
        global $idg_program_name;

        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= '<!-- generated on ' . date('r', time())
            . " by $idg_program_name  -->\n";

        if (@!$fp = fopen($file_name, 'w'))
            diag($this, 'xml: could not write ' . $file_name);

        $this->traverse($xml,
            '$this->_xml_write_start', '$this->_xml_write_end');
        fwrite($fp, $xml);
        fclose($fp);
    }

    private function _xml_write_start(&$xml, &$depth, &$path) {
        global $idg_xml_indent;

        for ($indent = '', $i = 0; $i < $depth; $i++)
            $indent .= $idg_xml_indent;

        $tag_type = !$this->children && !$this->text && count($this->attributes) == 0 ? 'single' : 'start';
        $xml_array = $this->get_xml_tag($tag_type);

        foreach ($xml_array as $xml_line) {
            $indent_count = & $xml_line['indent'];
            $text = & $xml_line['line'];

            $tmp_indent = $indent;

            for ($i = 0; $i < $indent_count; $i++)
                $tmp_indent .= $idg_xml_indent;
            $xml .= "$tmp_indent$text\n";
        }

        return true;
    }

    private function _xml_write_end(&$xml, &$depth, &$path) {
        global $idg_xml_indent;

        if (!$this->children && !$this->text && count($this->attributes) == 0)
            return true;

        for ($indent = '', $i = 0; $i < $depth; $i++)
            $indent .= $idg_xml_indent;

        if (count($this->attributes) > 0) {
            foreach ($this->attributes as $attr_name => $attr_obj)
                $xml .= $attr_obj->get_xml("$indent	");
        }

        $xml_array = $this->get_xml_tag('end');
        $xml .= $indent . $xml_array[0]['line'] . "\n";

        return true;
    }

    function read_xml($file_name, $parent = null) {
        $this->_xml_current_object = $parent;

        $xml_version = false;
        $encoding = 'utf-8';

        if (@!$fp = fopen($file_name, 'r'))
            diag($this, 'xml: could not read ' . $file_name);

        $xml = '';
        while ($chunk = fread($fp, 8129))
            $xml .= $chunk;

        if (preg_match('/<\?xml version="(.+)".+encoding="(.+)"\?>/',
                $xml, $match)) {
            $xml_version = $match[1];
            $encoding = $match[2];
        } else if (preg_match('/<\?xml version="(.+)"\?>/',
                $xml, $match)) {
            $xml_version = $match[1];
        }

        if ($xml_version != '1.0')
            diag($this, "read_xml: wrong xml version ($xml_version)");

        $parser = xml_parser_create($encoding);
        xml_set_object($parser, $this);
        xml_set_element_handler($parser,
            "_xml_read_start", "_xml_read_end");
        xml_set_character_data_handler($parser, "_xml_character_data");
        xml_set_default_handler($parser, "_xml_default_handler");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
        //xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, true);

        if (!xml_parse($parser, $xml)) {
            $err_code = xml_get_error_code($parser);
            $err_string = xml_error_string($err_code);
            $err_line = xml_get_current_line_number($parser);
            $err_col = xml_get_current_column_number($parser);

            diag($this, "xml error $err_code: $err_string, "
                . "line $err_line, column $err_col");
        }

        xml_parser_free($parser);
    }

    private function _xml_read_start($parser, $name, $properties) {
        if ($name == 'xi:include') {
            if (!$href = @$properties['href'])
                diag($this,
                    "xml: xi:include: must specify property 'href'");

            $this->read_xml($href, $this->_xml_current_object);
            return;
        }

        if ($name == 'param') {
            if (!$this->_xml_current_object)
                diag($this, 'xml: orphaned param tag');

            $class_name = str_replace('declaration', 'instance',
                get_class($this->_xml_current_object));

            if (!is_subclass_of($class_name, 'idg_param_instance'))
                diag($this, 'xml: parameters are not accepted here');

            if (!($param_name = @$properties['name']))
                diag($this,
                    "xml: parameter '$name' needs a name");

            $this->current_parameter = $param_name;

            return;
        }

        if ($name == 'attribute') {
            if (!$this->_xml_current_object)
                diag($this, 'xml: attribute has no parent');

            if (!($attr_name = @$properties['name']))
                diag($this,
                    "xml: attribute '$name' needs a name");

            $this->current_attribute = $this->_xml_current_object->create_attribute(
                $attr_name);
            return;
        }

        if ($name == 'option') {
            if (!$opt_name = @$properties['name'])
                diag($this, "xml: option has no 'name' property");

            if (!$this->current_attribute) {
                diag($this,
                    "xml: option '$opt_name' outside attribute");
            }

            $this->current_option = $opt_name;
            return;
        }

        if ($this->idg_xml_translation && ($trans = @$this->idg_xml_translation[$name]))
            $name = $trans;
        else
            diag($this, "xml: unknown tag '$name'");

        if (!$this->_xml_current_object) {
            $this->set_properties($properties);
            $this->_xml_current_object = $this;
        } else {
            $new_object = new $name;
            $new_object->set_properties($properties);
            $this->_xml_current_object->add_child($new_object);
            $this->_xml_current_object = $new_object;
        }
    }

    private function _xml_read_end($parser, $name) {
        if ($this->_xml_current_object &&
            $name != 'xi:include' &&
            !in_array($name, ['attribute', 'option', 'param']))
            $this->_xml_current_object = $this->_xml_current_object->get_parent();

        if ($name == 'attribute')
            $this->current_attribute = null;

        if ($name == 'param')
            $this->current_parameter = null;
    }

    private function _xml_character_data($parser, $character_data) {
        if (trim($character_data) == '')
            return;

        if ($this->current_attribute) {
            $this->current_attribute->set_parameter($this->current_option,
                $this->current_attribute->get_parameter(
                    $this->current_option) . $character_data);
            return;
        }

        if ($this->current_parameter) {
            $this->_xml_current_object->set_parameter($this->current_parameter,
                $this->_xml_current_object->get_parameter(
                    $this->current_parameter) . $character_data);
            return;
        }

        if (!$this->_xml_current_object)
            diag($this,
                "xml: spurious character data: '$character_data'");

        $this->_xml_current_object->text .= $character_data;
    }

    private function _xml_default_handler($parser, $data) {
        // <!-- and <? come here to die.
    }

    function add_token(&$tree, &$depth, &$path) {
        return true;
    }

    /** @todo get rid of this? */
    function print_debug_all(&$dummy, &$depth, &$path) {
        global $idg_xml_indent;

        for ($indent = '', $i = 0; $i < $depth; $i++)
            $indent .= $idg_xml_indent;
        echo $this->print_debug($indent);

        return true;
    }
}

/**
 * Base class for idg entities that are attached to a tree node and behave
 * like one.
 * 
 * This class implements the most common tree node functionality by passing
 * the function calls to its parent.
 * 
 * @todo Is there a general mechanism for this?
 */
class idg_tree_node_instance {

    protected $parent;

    function __construct($parent) {
        $this->parent = $parent;
    }

    function clear() {
        $this->parent = null;
    }

    /** Returns the immediate parent, or if $class_name is given,
     * the nearest ancestor of that class, or null.
     */
    function get_parent() {
        return $this->parent;
    }

    function get_idg_id() {
        return $this->parent->get_idg_id();
    }

    function get_child_count() {
        if ($children = $this->parent->get_children())
            return count($children);

        return 0;
    }

    function get_children() {
        return $this->parent->get_children();
    }

    function get_property($name) {
        return $this->parent->get_property($name);
    }

    /** Returns child by number or null, i.e. fails silently. */
    function get_child($index) {
        $children = $this->parent->get_children();

        if ($children && count($children) > $index)
            return $children[$index];
    }

    function get_attribute($name, $scope = null) {
        return $this->parent->get_attribute($name, $scope);
    }

    function get_text() {
        return $this->parent->get_text();
    }
}

?>
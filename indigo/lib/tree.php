<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/**
 * Base class for objects organized in a tree structure. Nodes which can have
 * children are defined in idg_treenode.
 */
abstract class idg_leafnode extends idg_object {

    private ?idg_leafnode $parent = null;
    private ?string $element_name;
    private string $text = '';
    private array $attributes = [];

    /**
     * Set the parent object manually.
     * This is necessary when programmatically constructing
     * site elements.
     * @param idg_treenode $parent The node's new parent
     */
    function set_parent(idg_treenode $parent): void {
        $this->parent = $parent;
    }

    /**
     * Returns the parent of a tree node.
     * @return idg_treenode|null The parent node
     */
    function get_parent(): ?idg_leafnode {
        return $this->parent;
    }

    /**
     * Sets the element name of the node, e.g. <code>datasource</code>.
     * @see https://www.w3schools.com/xml/xml_elements.asp#:~:text=XML%20Naming%20Rules
     * @param string $element_name The name, must be a possible <code>xml</code> element
     */
    protected function set_element_name($element_name): void {
        $this->element_name = $element_name;
    }

    /**
     * Returns the element name of the node.
     * @return string|null The element name
     */
    protected function get_element_name(): ?string {
        return $this->element_name;
    }

    /**
     * Sets a free-format text string (mainly used for <code>xml</code> compatibility).
     * @param string $text The text
     */
    function set_text(string $text): void {
        $this->text = $text;
    }

    /**
     * Gets the text previously set with <code>set_text()</code> or <code>null</code>.
     * @return string|null The text
     */
    function get_text(): ?string {
        return $this->text;
    }

    /** Creates a new idg_attribute with the given name and returns it.
     * <p>An attribute with the given name must not already exist.</p>
     * <p>The scope of the attribute (if any) is specified in the string
     * using the <code>scope::name</code>notation.</p>
     * @param string The name of the attribute
     * @return idg_attribute The new attribute object
     */
    function create_attribute(string $name): idg_attribute {
        if (@$this->attributes[$name])
            diag($this, "duplicate attribute: $name");

        return $this->attributes[$name] = new idg_attribute($name, $this);
    }

    /**
     * Returns the attribute with the given name and possibly scope 
     * or a freshly created one if otherwise <code>null</code> would be returned.
     * Unscoped attributes supersede scoped ones, i.e. if <code>a::b</code>
     * does not exist but <code>b</code> does, <code>get_attribute('a', 'b')</code>
     * returns it. The returned value is meant to be further decorated with
     * a call to its add_options() member.
     * <i>Note: The name can be specified as <code>scope::name</code>.</i>
     * @param string $name The name of the attribute
     * @param string $scope The scope of the attribute
     * @return idg_attribute The corresponding attribute object
     */
    function get_attribute(string $name, string $scope = null): idg_attribute {
        $full_name = "$scope::$name";

        if ($scope && array_key_exists($full_name, $this->attributes))
            return $this->attributes[$full_name];

        $attribute =  @$this->attributes[$name];
        
        if (!$attribute)
            $attribute = new idg_attribute($name, $scope);
        
        return $attribute;
    }

    /**
     * Creates a instance of a subclass of the object with the class name constructed 
     * from the current class and its <code>class</code> property.
     * E.g. an <code>idg_datasource</code> of class <code>text</code> would
     * yield an <code>idg_datasource_text</code> object (if there such a class, or die).
     * @return object The created object
     */
    function create_instance() {
        $object_name = get_class($this)
            . '_' . $this->get_property('class');

        if (!class_exists($object_name))
            diag($this, "object: class does not exist: $object_name");

        return new $object_name($this);
    }

    /**
     * Unlinks the object from the tree, mainly to avoid recursion in diagnostic output. 
     * @todo Make this traverse the subtree to only void the parent backlink.
     */
    function clear(): void {
        unset($this->parent);
        unset($this->children);
        unset($this->current_object);
        unset($this->current_attribute);
    }
}

/**
 * Type information for idg_treenode.
 */
abstract class idg_treenode_type extends idg_type {

    public function __construct() {
        parent::__construct();
        $this->register_property('tag');
    }

    protected array $child_types = ['idg_tree_node'];

    function accepts_child($child_type): bool {
        return in_array(str_replace('Indigo\\', '', $child_type), $this->child_types);
    }
}

/**
 * Base class for all tree nodes which are not leaf nodes.
 */
abstract class idg_treenode extends idg_leafnode {

    /** @todo this is protected because it is accessed in view_html! */
    private ?array $children = null;
    protected array $idg_xml_translation = [];
    private ?object $current_object;
    private ?idg_attribute $current_attribute;
    private ?string $current_option;

    /**
     * The constructor.
     * @param idg_treenode $parent The node's parent
     */
    function __construct(idg_treenode $parent = null) {
        parent::__construct();

        if ($parent && !is_subclass_of($parent, 'idg_treenode'))
            diag($this, "__construct: '" . get_class($parent)
                . "' is not a subclass of idg_treenode");

        $this->parent = $parent;
    }

    /**
     * Unlinks the object from the tree, mainly to avoid recursion in diagnostic output. 
     * @todo Make this traverse the subtree to only void the parent backlink.
     */
    function clear(): void {
        parent::clear();
        unset($this->children);
        unset($this->current_object);
        unset($this->current_attribute);
    }

    /**
     * Returns the children of the node if there are any.
     * @return array|null
     */
    function get_children(): ?array {
        return $this->children;
    }

    /**
     * Recursively looks for a tree node with property <code>$property</code> set to 
     * <code>$value</code>, optionally limiting the search to members of class 
     * <code>$class</code>, and returns the first one found or <code>null</code>.
     * @param string $property The name of the property
     * @param string $value The value of the property
     * @param string $class The name of the class
     * @return idg_treenode|null
     * @see get_children_by_key()
     */
    function get_child_by_key(string $property, string $value, string $class = null)
    : ?idg_treenode {

        if ($this->children)
            foreach ($this->children as $child) {
                if (($child->get_property($property) == $value) &&
                    (!$class || (get_class($child) == $class)))
                    return $child;

                if (method_exists($child, 'get_child_by_key') &&
                    ($childchild = $child->get_child_by_key($key_name, $value,
                    $class_name)))
                    return $childchild;
            }
    }

    /**
     * Returns an array containing all immediate children with property 
     * <code>$property</code> set to <code>$value</code>, optionally limited to members 
     * of class <code>$class</code>, or <code>null</code> if there aren't any.
     * <i>Note: This function is not recursive.</i>
     * @see get_child_by_key()
     * @param string $property The name of the property
     * @param string $value The value of the property
     * @param string $class The name of the class
     * @return array|null
     */
    function get_children_by_key(string $property, string $value, string $class = null)
    : ?array {

        $retval = [];
        foreach ($this->children as $child) {
            if (($child->get_property($property) == $value) &&
                (!$class || (get_class($child) == $class)))
                $retval[] = $child;
        }

        if (count($retval) == 0)
            return null;

        return $retval;
    }

    /**
     * Adds a child to the node.
     * @param idg_leafnode $child The child to be added
     */
    function add_child(idg_leafnode $child) {
        $child_type = get_class($child);

        if (!$this->get_type()->accepts_child($child_type))
            diag($this, "add child: incompatible child type '$child_type'");

        $child->set_parent($this);
        $this->children[] = & $child;
    }

    /**
     * Recursively traverses the subtree and calls <code>_check()</code> 
     * on all children.
     */
    function check(): void {
        $this->traverse($dummy, '$this->_check');
    }

    /**
     * Prints an ASCII representation of a subtree.
     * @todo This does not currently work. Add support for attributes.
     */
    function print_debug(): void {
        $dummy = '';
        $this->traverse($dummy, '$this->_print_debug');
    }

    /**
     * Recursively traverses a subtree calling <code>execute()</code> 
     * on the specified functions.
     * If one of the functions returns a value other than <code>true</code>,
     * the function returns <code>false</code>.
     * @see idg_object\execute()
     * @param $param An arbitrary parameter passed to the functions
     * @param string $start_function Called before traversing a child's subtree
     * @param string $end_function Called after traversing a child's subtree
     * @return bool Whether the traversal was successful
     */
    function traverse(&$param, string $start_function,
        string $end_function = null): bool {

        $retval = $this->execute($start_function, $param) === true;

        if ($this->children)
            foreach ($this->children as $child)
                if (method_exists($child, 'traverse'))
                    $retval = $retval &&
                        $child->traverse($param, $start_function,
                            $end_function) === true;
                else {
                    $retval = $retval &&
                        $child->execute($start_function, $param) === true;

                    if ($end_function)
                        $retval = $retval &&
                            $child->execute($end_function, $param) === true;
                }

        if ($end_function)
            $retval = $retval && !$this->execute($end_function, $param) == true;

        return $retval;
    }

    /**
     * Writes an <code>xml</code> representation of the subtree to a file.
     * @todo Failure to write should probably not be catastrophic and the function
     *       should simply return a string.
     * @param string $file_name The name of the file
     */
    function write_xml(string $file_name): void {

        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= '<!-- generated on ' . date('r', time())
            . " by IDG_PROGRAM_NAME  -->\n";
        $param = ['xml' => $xml, 'deph' => 0, 'path' => ''];

        if (@!$fp = fopen($file_name, 'w'))
            diag($this, 'xml: could not write ' . $file_name);

        $this->traverse($param,
            '$this->_xml_write_start', '$this->_xml_write_end');
        fwrite($fp, $param['xml']);
        fclose($fp);
    }

    private function _xml_write_start(&$param): bool {
        $tag_type = !$this->children && !$this->text && count($this->attributes) == 0 ?
            'single' : 'start';

        $param['xml'] .= $this->get_xml_tag($tag_type);

        return true;
    }

    private function _xml_write_end(&$xml, &$depth, &$path): bool {
        if (!$this->children && !$this->text && count($this->attributes) == 0)
            return true;

        for ($indent = '', $i = 0; $i < $depth; $i++)
            $indent .= IDG_XML_INDENT;

        if (count($this->attributes) > 0) {
            foreach ($this->attributes as $attr_name => $attr_obj)
                $xml .= $attr_obj->get_xml("$indent	");
        }

        $xml_array = $this->get_xml_tag('end');
        $xml .= $indent . $xml_array[0]['line'] . "\n";

        return true;
    }

    /**
     * Reads an <code>xml</code> representation from a file and
     * constructs the corresponding subtree.
     * @param string $file_name The name of the file
     * @param idg_node $parent Needs to be set when called recursively
     */
    function read_xml(string $file_name, idg_treenode $parent = null): void {
        $this->current_object = $parent;

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

    private function _xml_read_start($parser, $name, $properties): void {
        if ($name == 'xi:include') {
            if (!$href = @$properties['href'])
                diag($this,
                    "xml: xi:include: must specify property 'href'");

            $this->read_xml($href, $this->current_object);
            return;
        }

        if ($name == 'parameter') {
            if (!$this->current_object)
                diag($this, 'xml: orphaned param tag');

            $class_name = str_replace('_declaration', '',
                get_class($this->current_object));

            if (!is_subclass_of($class_name, 'idg_parameterized'))
                diag($this, 'xml: parameters are not accepted here');

            if (!($param_name = @$properties['name']))
                diag($this,
                    "xml: parameter '$name' needs a name");

            $this->current_parameter = $param_name;
            return;
        }

        if ($name == 'attribute') {
            if (!$this->current_object)
                diag($this, 'xml: attribute has no parent');

            if (!($attr_name = @$properties['name']))
                diag($this,
                    "xml: attribute '$name' needs a name");

            $this->current_attribute = $this->current_object->create_attribute(
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

        if (!$this->current_object) {
            $this->set_properties($properties);
            $this->current_object = $this;
        } else {
            $new_object = new $name;
            $new_object->set_properties($properties);
            $this->current_object->add_child($new_object);
            $this->current_object = $new_object;
        }
    }

    private function _xml_read_end($parser, $name): void {
        if ($this->current_object &&
            $name != 'xi:include' &&
            !in_array($name, ['attribute', 'option', 'parameter']))
            $this->current_object = $this->current_object->get_parent();

        if ($name == 'attribute')
            $this->current_attribute = null;

        if ($name == 'parameter')
            $this->current_parameter = null;
    }

    private function _xml_character_data($parser, $character_data): void {
        if (trim($character_data) == '')
            return;

        if ($this->current_attribute) {
            $this->current_attribute->set_parameter($this->current_option,
                $this->current_attribute->get_parameter(
                    $this->current_option) . $character_data);
            return;
        }

        if ($this->current_parameter) {
            $this->current_object->set_parameter($this->current_parameter,
                $this->current_object->get_parameter(
                    $this->current_parameter) . $character_data);
            return;
        }

        if (!$this->current_object)
            diag($this,
                "xml: spurious character data: '$character_data'");

        $this->current_object->set_text(
            $this->current_object->get_text() . $character_data);
    }

    private function _xml_default_handler($parser, $data): void {
        // <!-- and <? come here to die.
    }

    /** @todo get rid of this?
    function print_debug_all(&$dummy, &$depth, &$path): void {
        for ($indent = '', $i = 0; $i < $depth; $i++)
            $indent .= IDG_XML_INDENT;
        echo $this->print_debug($indent);

        return true;
    } */
}

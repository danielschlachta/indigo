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

    private string $element_name;
    private ?idg_leafnode $parent = null;
    private array $attributes = [];
    private string $text = '';

    /**
     * Constructs a node object with no children.
     * @param string $element_name The <code>xml</code> compatible name of the thing.
     * @see https://www.w3schools.com/xml/xml_elements.asp#:~:text=XML%20Naming%20Rules
     */
    function __construct(string $element_name) {
        parent::__construct();
        $this->element_name = $element_name;
    }

    /**
     * Set the parent object.
     * This is usually done in idg_treenode\add_child() but becomes
     * necessary when programmatically constructing site elements.
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
     * Returns the element name of the node.
     * @return string|null The element name
     */
    function get_element_name(): ?string {
        return $this->element_name;
    }

    /**
     * Stores free-format text, also referred to as <i>character data</i>
     * because in the <code>xml</code> representation, that's what it is.
     * <blockquote>
     * Note: HTML entities are decoded after reading from <code>xml</code> files.
     * </blockquote>
     * @see get_text()
     * @param string $text The text
     */
    function set_text(string $text): void {
        $this->text = $text;
    }
    
    /**
     * Gets the text previously set with <code>set_text()</code>.
     * <blockquote>
     * Note: HTML entities are encoded before writing to <code>xml</code> files.
     * </blockquote>
     * @see set_text()
     * @return string|null The text
     */
    function get_text(): ?string {
        return $this->text;
    }

    /**
     * Creates a new idg_attribute with the given name and returns it.
     * <p>An attribute with the given name must not already exist.</p>
     * <p>The scope of the attribute (if any) can specified
     * using the <code>scope::name</code>notation.</p>
     * @param string $name The name of the attribute
     * @return idg_attribute The new attribute object
     */
    function create_attribute(string $name): idg_attribute {
        if (@$this->attributes[$name])
            idg_diag($this, "duplicate attribute: $name");

        return $this->attributes[$name] = new idg_attribute($name);
    }

    /**
     * Returns the attribute with the given name if it exists or creates a new one.
     * <blockquote>
     * Note: If <code>$name</code> is <code>a::b</code> and <code>a::b</code>
     * does not exist but <code>b</code> does, <code>b</code> is returned.
     * </blockquote>
     * @param string $name The name of the attribute
     * @return idg_attribute The attribute
     */
    function get_or_create_attribute(string $name): idg_attribute {
        $attribute = @$this->attributes[$name];

        if (!$attribute) {
            $new_attribute = new idg_attribute($name);

            $attribute = @$this->attributes[$new_attribute->get_name_part()];

            if (!$attribute) {
                $this->attributes[$name] = $new_attribute;
                return $new_attribute;
            }
        }

        return $attribute;
    }

    /**
     * Returns the current attributes (including scope) as name/value pairs.
     * @return array|null The attributes
     */
    function get_attributes(): ?array {
        return $this->attributes;
    }

    /**
     * Creates an instance of what is set in the objects <code>class</property>
     * with <code>this</code> as the parent.
     * @return object|null The created object
     */
    function create_instance(): ?object {
        if (!($object_name = $this->get_property('class')))
            idg_diag($this, "internal error: no 'class'");

        if (!class_exists($object_name))
            idg_diag($this, "class '$object_name' does not exist");

        return new $object_name($this);
    }

    /**
     * Unlinks the object from the tree, mainly to avoid recursion in diagnostic output. 
     */
    function clear(): void {
        unset($this->parent);
    }

    /**
     * Returns (part of) an <code>xml</code> element representing the object.
     * The properties of the object are represented as attributes.
     * @param string $tag_type <code>start</code>, <code>end</code>, <code>single</code>
     * @param string $indent Optional prefix for output lines
     * @return string The tag
     */
    protected function get_xml_tag(string $tag_type, int $indentation): string {
        $element = $this->get_element_name();

        for ($i = 0, $indent = ''; $i < $indentation; $i++)
            $indent .= IDG_XML_INDENT;

        $xml = '';

        if ($tag_type == 'end') {
            if (($text = $this->get_text())) {
                $text_lines = explode("\n", htmlentities($text));

                foreach ($text_lines as $line)
                    $xml .= IDG_XML_INDENT . "$indent$line\n";
            }

            return "$xml$indent</$element>\n";
        }

        $xml .= "$indent<$element";

        if (($properties = $this->get_properties())) {
            $xml .= "\n";

            $prop_xml = [];

            foreach ($properties as $name => $value) {
                $value = htmlentities($value);
                $prop_xml[] = "$name=\"$value\"";
            }

            $tagend = $tag_type == 'single' ? ' /' : '';

            if (($prop_count = count($prop_xml)) > 0) {
                for ($i = 0; $i < $prop_count - 1; $i++)
                    $xml .= IDG_XML_INDENT . "$indent$prop_xml[$i]\n";

                $last = $prop_xml[$prop_count - 1];
                $xml .= IDG_XML_INDENT . "$indent$last";
            }

            $xml .= "$tagend>\n";

            if (method_exists($this, 'get_parameters') &&
                ($parameters = $this->get_parameters()))
                foreach ($parameters as $key => $value)
                    $xml .= IDG_XML_INDENT . "$indent<parameter name=\"$key\">"
                        . "$value</parameter>\n";
        }

        return $xml;
    }

    protected function _xml_write_start(&$param, $depth): bool {
        $tag_type = !$this->get_text() && count($this->get_attributes()) == 0 &&
            !(method_exists($this, 'get_parameters') && $this->get_parameters()) ?
            'single' : 'start';

        $param .= $this->get_xml_tag($tag_type, $depth);

        return true;
    }

    protected function _xml_write_end(&$param, $depth): bool {
        $attributes = $this->get_attributes();

        if (!$this->get_text() && count($attributes) == 0 &&
            !(method_exists($this, 'get_parameters') && $this->get_parameters()))
            return true;

        if (count($attributes) > 0)
            foreach ($attributes as $attr_name => $attr_obj)
                $param .= $attr_obj->get_xml($depth + 1);

        $param .= $this->get_xml_tag('end', $depth);

        return true;
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

    protected array $child_types = [];

    function accepts_child($child_type): bool {
        return in_array($child_type, $this->child_types);
    }
}

/**
 * Base class for all tree nodes which are not leaf nodes.
 */
abstract class idg_treenode extends idg_leafnode {

    private ?array $children = null;
    private ?object $current_object;
    private ?idg_attribute $current_attribute = null;
    private ?string $current_parameter = null;
    private ?string $current_option = null;

    /**
     * Unlinks the object from the tree, mainly to avoid recursion in diagnostic output. 
     * @todo Make this traverse the subtree. See also get_xml_sitemap.
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
     * <code>$class</code>, and returns the first one found.
     * @param string $property The name of the property
     * @param string $value The value of the property
     * @param string $class The name of the class
     * @return idg_leafnode|null
     * @see get_children_by_key()
     */
    function get_child_by_key(string $property, string $value,
        string $class_name = null)
    : ?idg_leafnode {

        if ($this->children)
            foreach ($this->children as $child) {
                if (($child->get_property($property) == $value) &&
                    (!$class_name || (get_class($child) == $class_name)))
                    return $child;

                if (method_exists($child, 'get_child_by_key') &&
                    ($childchild = $child->get_child_by_key($property, $value,
                    $class_name)))
                    return $childchild;
            }

        return null;
    }

    /**
     * Returns an array containing all immediate children with property 
     * <code>$property</code> set to <code>$value</code>, optionally limited to members 
     * of class <code>$class</code>
     * <blockquote>
     * Note: This function is not recursive.
     * </blockquote>
     * @see get_child_by_key()
     * @param string $property The name of the property
     * @param string $value The value of the property
     * @param string $class_name The name of the class
     * @return array|null
     */
    function get_children_by_key(string $property, string $value,
        string $class_name = null)
    : ?array {

        $retval = [];
        foreach ($this->children as $child) {
            if ($class_name && get_class($child) != $class_name)
                continue;

            if ($child->get_property($property) == $value)
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
            idg_diag($this, "add child: incompatible child type '$child_type'");

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

    private function _traverse(&$param, int $depth, string $start_function,
        string $end_function = null): bool {

        $retval = true;

        if ($start_function)
            $retval = $this->execute($start_function, $param, $depth) === true;

        if ($this->children)
            foreach ($this->children as $child)
                if (method_exists($child, 'traverse'))
                    $retval = $retval &&
                        $child->_traverse($param, $depth + 1, $start_function,
                            $end_function) === true;
                else {
                    $retval = $retval &&
                        $child->execute($start_function, $param, $depth) === true;

                    if ($end_function)
                        $retval = $retval &&
                            $child->execute($end_function, $param, $depth) === true;
                }

        if ($end_function)
            $retval = $retval && $this->execute($end_function, $param, $depth) === true;

        return $retval;
    }

    /**
     * Recursively traverses a subtree calling <code>execute()</code> 
     * for the specified function(s) on each node.
     * If one of the functions returns a value other than <code>true</code>,
     * the traversal is stopped and the function returns <code>false</code>.
     * @see idg_object\execute()
     * @param $param An arbitrary parameter passed to the functions
     * @param string $start_function Called before traversing a child's subtree
     * @param string $end_function Called after traversing a child's subtree
     * @return bool Whether the traversal was successful
     */
    function traverse(&$param, string $start_function = null,
        string $end_function = null): bool {
        return $this->_traverse($param, 0, $start_function, $end_function);
    }

    /**
     * Writes an <code>xml</code> representation of the subtree to a file.
     * @param string $file_name The name of the file
     */
    function write_xml(string $file_name): void {

        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= '<!-- generated on ' . date('r', time())
            . " by " . IDG_PROGRAM_NAME . " -->\n";

        if (@!$fp = fopen($file_name, 'w'))
            idg_diag($this, "could not write to '$file_name'");

        $this->traverse($xml, '$this->_xml_write_start', '$this->_xml_write_end');
        fwrite($fp, $xml);
        fclose($fp);
    }

    protected function _xml_write_start(&$param, $depth): bool {
        $tag_type = !$this->children && !$this->get_text() &&
            count($this->get_attributes()) == 0 &&
            !(method_exists($this, 'get_parameters') && $this->get_parameters()) ?
            'single' : 'start';

        $param .= $this->get_xml_tag($tag_type, $depth);

        return true;
    }

    protected function _xml_write_end(&$param, $depth): bool {
        $attributes = $this->get_attributes();

        if (!$this->children && !$this->get_text() &&
            count($attributes) == 0 &&
            !(method_exists($this, 'get_parameters') && $this->get_parameters()))
            return true;

        if (count($attributes) > 0)
            foreach ($attributes as $attr_name => $attr_obj)
                $param .= $attr_obj->get_xml($depth + 1);

        $param .= $this->get_xml_tag('end', $depth);

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
            idg_diag($this, "could not read from '$file_name'");

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
            idg_diag($this, "wrong xml version ($xml_version)");

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

            idg_diag($this, "xml error $err_code: $err_string, "
                . "line $err_line, column $err_col");
        }

        xml_parser_free($parser);
    }

    private function _xml_read_start($parser, $name, $properties): void {
        switch ($name) {
            case 'xi:include':
                if (!($href = @$properties['href']))
                    idg_diag($this, "xi:include must specify property 'href'", $parser);

                $this->read_xml($href, $this->current_object);
                break;

            case 'parameter':
                $class_name = str_replace('_declaration', '',
                    get_class($this->current_object));

                if (!$this->current_object)
                    idg_diag($this, "orphaned parameter", $parser);
                
                if (!($param_name = @$properties['name']))
                    idg_diag($this, "anonymous paramenter", $parser);
                
                if (!is_subclass_of($class_name, 'idg_parameterized'))
                    idg_diag($this, 'parameters are not accepted here', $parser);

                $this->current_parameter = $param_name;
                break;

            case 'attribute':
                if (!$this->current_object)
                    idg_diag($this, "orphaned attribute", $parser);

                if (!($attr_name = @$properties['name']))
                    idg_diag($this, "anonymous attribute", $parser);

                $this->current_attribute = $this->current_object->create_attribute(
                    $attr_name);
                break;

            case 'option':
                if (!$this->current_object)
                    idg_diag($this, "orphaned option", $parser);
                
                if (!($opt_name = @$properties['name']))
                    idg_diag($this, "anonymous option", $parser);

                if (!$this->current_attribute) 
                    idg_diag($this, "options are not accepted here", $parser);
                
                $this->current_option = $opt_name;
                break;

            default:
                $class_name = "idg_$name";

                if (!method_exists($class_name, '__construct'))
                    idg_diag($this, "unknown tag '$name'", $parser);

                if (!$this->current_object) {
                    $this->set_properties($properties);
                    $this->current_object = $this;
                } else {
                    $new_object = new $class_name;
                    $new_object->set_properties($properties);
                    $this->current_object->add_child($new_object);
                    $this->current_object = $new_object;
                }
        }
    }

    private function _xml_read_end($parser, $name): void {
        if (!$this->current_object)
            return;
        
         $this->current_object->set_text(html_entity_decode(
             $this->current_object->get_text()));

        switch ($name) {
            case 'parameter':
                $this->current_parameter = null;
                break;
            case 'attribute':
                $this->current_attribute = null;
                break;
            case 'option':
                break;
            default:
                $this->current_object = $this->current_object->get_parent();
        }
    }

    private function _xml_character_data($parser, $character_data): void {
        if (trim($character_data) == '')
            return;

        if ($this->current_attribute) {
            $text = $this->current_attribute->get_parameter(
                    $this->current_option) . $character_data;
            $this->current_attribute->set_parameter($this->current_option, $text);
            return;
        }

        if ($this->current_parameter) {
            $text = $this->current_object->get_parameter(
                    $this->current_parameter) . $character_data;
            $this->current_object->set_parameter($this->current_parameter, $text);
            return;
        }

        if (!$this->current_object)
            idg_diag($this, "spurious character data: ('$character_data')", $parser);
        
        $text = $this->current_object->get_text() . $character_data;
        $this->current_object->set_text($text);
    }

    private function _xml_default_handler($parser, $data): void {
        // <!-- and <? come here to die.
    }
}

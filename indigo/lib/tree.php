<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: tree.php - defines the tree structure for the view
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

require_once($idg_path . '/lib/object.php');

$idg_xml_indent = '  ';

class idg_tree_node_type extends idg_object_type
{
	var $child_types = array('idg_tree_node');
	
	function __construct()
	{
		parent::__construct();
	}
	
	function set_known($name, $inherit = 'no')
	{
		$this->known_properties[$name] = $inherit;
	}
}

class idg_tree_node extends idg_object
{
	var $parent;
	var $children;
	
	var $is_leaf = true;
	var $child_count;
	var $child_counts = array();
	
	var $idg_translation; // array (idg_type => php type) for instance creation
	
	var $_xml_stack;
	
	function __construct()
	{
		parent::__construct();
	}
	
	function get_property($name)
	{
		$node =& $this->parent;
		@$prop = $this->properties[$name];
		
		if (@$this->type_obj->known_properties[$name] == 'yes') {
			while ($node && !$prop) {
				@$prop = $node->properties[$name];
				$node =& $node->parent;
			}
		}
		
		if (!$prop && @($hook = $this->type_obj->prop_hooks[$name])) {
			$prop = $this->_execute($hook);
		}
		
		return $prop;
	}
	
	/*! Assigns the corresponding values to the keys in the array.
	 */
	
	function get_properties(&$prop_array)
	{
		foreach ($prop_array as $name => $value) {
			$node =& $this->parent;
			@$prop = $this->properties[$name];
			
			if (@$this->type_obj->known_properties[$name] == 'yes') {
				while ($node && !$prop) {
					@$prop = $node->properties[$name];
					$node =& $node->parent;
				}
			}
			
			if (!$prop && @($hook = $this->type_obj->prop_hooks[$name])) {
				$prop = $this->_execute($hook);
			}
			
			if ($prop)
				$prop_array[$name] = $prop;
		}
	}
	
	/*!
	 * Finds a child whose property $key_name is set to $key_value.
	 * 
	 * This function is recursive.
	 */
	
	function get_child_by_key($key_name, $key_value, 
		$child_type = false)
	{
		if ($this->is_leaf)
			return false;
		
		foreach ($this->children as $child) {
			if (($child->get_property($key_name) == $key_value) 
				&& (!$child_type || (get_class($child) == $child_type)))
				return $child;
				
			if ($childchild = $child->get_child_by_key(
				$key_name, $key_value, $child_type))
				return $childchild;
		}
		
		return false;
	}
	
	/*!
	 * Returns an array of all children whose property $key_name is set to $key_value.
	 * 
	 * This function is not recursive!
	 */
	
	function get_children_by_key($key_name, $key_value, $child_type = false)
	{
		$retval = array();
		foreach ($this->children as $child) {
			if (($child->get_property($key_name) == $key_value) 
				&& (!$child_type || (get_class($child) == $child_type)))
				$retval[] = $child;
		}
		
		if (count($retval) == 0)
			$retval = false;
		
		return $retval;
	}
	
	function add_child(&$child)
	{
		if (!in_array(get_class($child), $this->type_obj->child_types))
			diag($this, $this->idg_id 
				. ': add child: incompatible child type \'' 
				. get_class($child) . '\'');
		$child->parent =& $this;
		$this->children[] =& $child;
		
		$this->is_leaf = false;
	}
	
	function write_xml($file_name)
	{
		global $idg_program_name;
		
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= '<!-- generated on ' . date('r', time()) 
			. " by $idg_program_name  -->\n";
		
		if (@!$fp = fopen($file_name, 'w'))
			diag($this, 'xml: could not write ' . $file_name);
		
		$this->_get_subtree($xml, 
			'$this->_xml_write_start', '$this->_xml_write_end');
		fwrite($fp, $xml);
		fclose($fp);
	}
	
	function read_xml($file_name)
	{
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
		} else if (preg_match('/<\?xml version="(.+)"\?>/', $xml, $match)) {
			$xml_version = $match[1];
		}
		
		if ($xml_version != '1.0')
			diag($this, "read_xml: wrong xml version ($xml_version)");
		
		$parser = xml_parser_create($encoding);
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, "_xml_read_start", "_xml_read_end");
		xml_set_character_data_handler($parser, "_xml_character_data");
		xml_set_default_handler($parser, "_xml_default_handler");
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
		
		$this->_xml_stack = false;
		
		if (!xml_parse($parser, $xml)) {
			$err_code = xml_get_error_code($parser);
			$err_string = xml_error_string($err_code);
			$err_line = xml_get_current_line_number($parser);
			$err_col = xml_get_current_column_number($parser);
			
			diag($this, "xml error($err_code): $err_string<br />" 
				. "<b>Line:</b> $err_line<br><b>Column: $err_col");
		}
		
		xml_parser_free($parser);
	}

	/*! 
	 * Checks the whole subtree.
	 */
	
	function check_all()
	{
		$dummy = false;
		$this->_get_subtree($dummy, '$this->_check');
	}
	
	/*! 
	 * Prints an ASCII representation of a subtree.
	 */

	function print_debug()
	{
		$dummy = false;
		$this->_get_subtree($dummy, '$this->_print_debug');
	}
	
	function _get_subtree(&$param,
		$start_function, $end_function = false, $depth = 0, $path = '')
	{
		if (strpos($start_function, '$this->') == 0) {
			$do_func = substr($start_function, strlen('$this->'));
			$retval = $this->$do_func($param, $depth, $path);
		} else
			$retval = $start_function($param, $depth, $path);
		
		if ($this->children) {
			foreach ($this->children as $child) {
				$child->parent =& $this;
				if (!$child->_get_subtree($param, 
					$start_function, $end_function, 
					$depth + 1, $path . $this->idg_id . '/'))
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
	
	function _xml_write_start(&$xml, &$depth, &$path)
	{
		global $idg_xml_indent;
		
		for ($indent = '', $i = 0; $i < $depth; $i++)
			$indent .= $idg_xml_indent;
		
		$tag_type = $this->is_leaf && !$this->text ? 'single' : 'start';
		$xml_array = $this->get_xml_tag($tag_type);
		
		foreach ($xml_array as $xml_line) {
			$indent_count =& $xml_line['indent'];
			$text =& $xml_line['line'];
			
			$tmp_indent = $indent;
			
			for ($i = 0; $i < $indent_count; $i++)
				$tmp_indent .= $idg_xml_indent;
			$xml .= "$tmp_indent$text\n";
		}
		
		return true;
	}
	
	function _xml_write_end(&$xml, &$depth, &$path)
	{
		global $idg_xml_indent;
		
		if ($this->is_leaf && !$this->text)
			return true;
		
		for ($indent = '', $i = 0; $i < $depth; $i++)
			$indent .= $idg_xml_indent;
		
		$xml_array = $this->get_xml_tag('end');
		$xml .= $indent . $xml_array[0]['line'] . "\n";
		
		return true;
	}
	
	function _xml_read_start($parser, $name, $properties)
	{
		$class_name = $name;
		
		if ($this->idg_translation 
			&& @($trans = $this->idg_translation[$class_name]))
			$class_name = $trans;
		else
			diag($this, "xml: unknown tag '$class_name'");
		
		if (!$this->_xml_stack) {
			if ($class_name != get_class($this))
				diag($this, get_class($this) 
				. ': read_xml: no parent class of this type');
			$this->set_properties($properties);
			$this->_xml_stack = array();
			$obj =& $this;
		} else {
			if (!class_exists($class_name))
				diag($this, "xml: while trying to instantiate " 
					. "'$class_name'" . ": class does not exist");
			
			$obj = new $class_name;
			
			$obj->set_properties($properties);
			$this->_xml_stack[count($this->_xml_stack) - 1]->add_child($obj);
		}
		$this->_xml_stack[] =& $obj;
	}
	
	function _xml_read_end($parser, $name)
	{
		array_pop($this->_xml_stack);
	}
	
	function _xml_character_data($parser, $character_data)
	{
		if ($data = trim($character_data)) {
			$text =& $this->_xml_stack[count($this->_xml_stack) - 1]->text;
			if ($text)
				$text .= "\n";
			$text .= $data;
		}
	}
	
	function _xml_default_handler($parser, $data)
	{
	}
	
	function _check(&$dummy, &$depth, &$path)
	{
		$this->check();
		return true;
	}
	
	function _print_debug(&$dummy, &$depth, &$path)
	{
		global $idg_xml_indent;
		
		for ($indent = '', $i = 0; $i < $depth; $i++)
			$indent .= $idg_xml_indent;
		echo $this->_obj_print_debug($indent);
		
		return true;
	}
}

?>

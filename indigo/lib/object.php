<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: object.php - defines the basic object types
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

$idg_object_counters = array();
$idg_type_objects = array();

/*!
 * The foundation class for all idg types.
 * 
 */

class idg_object_type
{
	var $known_properties = array();
	var $mandatory_properties = array();
	var $prop_hooks = array(); // hook is executed when property is accessed
	
	function __construct()
	{
		$this->set_known('options');
	}
	
	function set_known($name)
	{
		$this->known_properties[$name] = true;
	}
	
	function set_mandatory($name, $is_mandatory = true)
	{
		if (@!$this->known_properties[$name])
			diag($this, get_class($this) 
			    . ': set_mandatory(' . $name . '): unknown property');
		$this->mandatory_properties[$name] = $is_mandatory;
	}
	
	function add_hook($name, $function_name)
	{
		$this->prop_hooks[$name] = $function_name;
	}
}


/*! 
 * The foundation class for all idg objects
 * 
 */

class idg_object
{
	var $idg_id;
	var $idg_type; // defaults to class type
	
	var $properties = array();
	var $text;
	
	var $type_obj;
	var $object;
	
	function __construct()
	{
		global $idg_object_counters, $idg_type_objects;
		
		$type = get_class($this);
		$type_obj_name = $type . '_type';
		
		if (@!$idg_type_objects[$type]) {
			$this->type_obj = new $type_obj_name();
			$idg_type_objects[$type] =& $this->type_obj;
		} else
			$this->type_obj =& $idg_type_objects[$type];
		
		if (@!$idg_object_counters[$type])
			$id_count = $idg_object_counters[$type] = 1;
		else
			$id_count = ++$idg_object_counters[$type];
		
		$this->idg_id = $type . '-' . $id_count;
	}
	
	function get_id()
	{
		return $this->idg_id;
	}
	
	function get_text()
	{
		return $this->text;
	}
	
	function get_idg_type()
	{
		if ($this->idg_type)
			return $this->idg_type;
		else
			return get_class($this);
	}
	
	function get_property($name)
	{
		@$prop = $this->properties[$name];
		
		if ($prop)
			return $prop;
		
		$hook = $this->type_obj->prop_hooks[$name];
		if (!$prop && $hook) {
			$prop = $this->_execute($hook);
		}
		
		return $prop;
	}
	
	function get_properties(&$prop_array)
	{
		foreach ($prop_array as $name => $value) {
			if (@$this->properties[$name]) {
				$prop_array[$name] = $this->properties[$name];
			}
		}
	}
	
	function get_all_properties()
	{
		$prop_array = array();
		
		foreach ($this->properties as $name => $value) {
			if ($value != '')
				$prop_array[$name] = $value;
		}
		
		return $prop_array;
	}
	
	/*! This allows passing key => value pairs to an actual instance
	 * of the element. 
	 * 
	 * This is useful mostly for html_parts. Note that
	 * there are no getter functions because the values
	 * are mostly accessed from idg_tree_node.
	 * 
	 * */
	
	function set_options($options)
	{
		if (!is_array($options)) 
			diag($this, 'set_options: argument must be an array.');
		
		$this->set_property('options', 
			urlencode(serialize($options)));
	}
	
	/*! Updates an individual key => value pair.
	 * 
	 * This causes the whole of the parameter array to be
	 * re-constructed.
	 * 
	 */ 
	
	function set_option($key, $value) {
		$options = $this->get_options();
		
		if (!$options)
			$options = array();
			
		$options[$key] = $value;
		
		$this->set_options($options);
	}
	
	function get_options() {
		$options = $this->get_property('options');
		
		if ($options)
			return unserialize(urldecode($options));
	}
	
	function get_option($key) {
		if (($options = $this->get_options()) 
		&& array_key_exists($key, $options))
			return $options[$key];
	}
	
	function get_xml_tag($tag_type, $tag_id = '')
	{
		if ($tag_id == '')
			$use_id = $this->get_idg_type();
		else
			$use_id = $tag_id;
		
		if ($tag_type == 'end') {
			$text = '</' . $use_id . '>';
			return array(
				array(
					'indent' => 0,
					'line' => $text
				)
			);
		}
		
		$prop_xml = array();
		
		foreach ($this->properties as $name => $value) {
			$prop_xml[] = "$name=\"$value\"";
		}
		
		$tagend = $tag_type == 'single' ? ' /' : '';
		$xml = array();
		
		if (($prop_count = count($this->properties)) > 0) {			
			$first =& $prop_xml[0];
			$last = '';
			
			if ($prop_count < 2)
				$xml[] = array(
					'indent' => 0,
					'line' => "<$use_id $first$tagend>"
				);
			else {
				$last =& $prop_xml[$prop_count - 1];
				$xml[] = array(
					'indent' => 0,
					'line' => "<$use_id $first"
				);
			
				
				for ($i = 1; $i < count($prop_xml) - 1; $i++)
					$xml[] = array(
						'indent' => 1,
						'line' => &$prop_xml[$i]
					);
				
				if ($tag_type == 'start')
					$xml[] = array(
						'indent' => 1,
						'line' => "$last>"
					);
				else
					$xml[] = array(
						'indent' => 1,
						'line' => "$last />"
					);
			}
		} else
			$xml[] = array(
				'indent' => 0,
				'line' => "<$use_id$tagend>"
			);
		
		if ($this->text) {
			$text_lines = explode("\n", $this->text);
			
			foreach ($text_lines as $line)
				$xml[] = array(
					'indent' => 1,
					'line' => $line
				);
		}
		
		return $xml;
	}
		
	function count()
	{
		return count($this->properties);
	}

	/*!
	 * Performs some (at this moment very rudimentary) consistency
	 * checks.
	 * 
	 * This is called from check_all() in idg_tree_node.
	 */
	
	function check()
	{
		foreach ($this->type_obj->mandatory_properties as $name => $value)
			if ($value && !@$this->properties[$name])
				diag($this, $this->idg_id // idg_id contains class name 
				    . ': mandatory property "' . $name . '" not set');
	}
	
	function get_instance()
	{
		$object_name = get_class($this) . '_' . $this->get_property('class');
		
		if (!class_exists($object_name))
			diag($this, "object: class does not exist: $object_name");
		
		$this->object = new $object_name($this);
		
		return $this->object;
	}
		
	function set_text($text = false)
	{
		$this->text = $text;
	}
	
	function set_property($name, $value)
	{
		$name = strtolower($name);
		if (@$this->type_obj->known_properties[$name])
			$this->properties[$name] = $value;
		else
			diag($this, get_class($this) 
			    . ': set_property: unknown property: ' . $name);
	}
	
	function set_properties($prop_array)
	{
		if ($prop_array) {
			foreach ($prop_array as $name => $value) {
				if (@$this->type_obj->known_properties[$name])
					$this->properties[$name] = $value;
				else
					diag($this, get_class($this) 
					    . ': set_properties: unknown property: ' . $name);
			}
		}
	}

	/*!
	 * Execute a function based on its name.
	 * 
	 * Recognizes the prefix \c $this-> and acts accordingly.
	 */
	
	function _execute($function_name)
	{
		if (strpos($function_name, '$this->') === 0) {
			$do_func = substr($function_name, strlen('$this->'));
			$retval = $this->$do_func();
		} else
			$retval = $function_name();
		
		return $retval;
	}

	function _obj_print_debug($indent = '')
	{
		$lines = array(
			"ID       $this->idg_id"
		);
		$lines[] = 'XML_TYPE ' . $this->get_idg_type();
		$lines[] = '(break)';
		
		$max_header = 0;
		foreach ($lines as $line) {
			if (strlen($line) > $max_header)
				$max_header = strlen($line);
		}
		
		$max_prop = 0;
		foreach ($this->properties as $name => $value) {
			if (strlen($name) > $max_prop)
				$max_prop = strlen($name);
		}
		
		$max_line = 0;
		if ($this->text) {
			$text_lines = explode("\n", $this->text);
			foreach ($text_lines as $line) {
				if ($max_line < strlen($line))
					$max_line = strlen($line);
			}
		}
		
		foreach ($this->properties as $name => $value) {
			for ($j = strlen($name), $space = ''; $j < $max_prop; $j++)
				$space .= ' ';
			$line = "$name$space $value";
			$lines[] = $line;
			if (strlen($line) > $max_line)
				$max_line = strlen($line);
		}
		
		if ($max_header > $max_line)
			$max_line = $max_header;
		
		
		for ($j = 0, $dash = ''; $j < $max_line; $j++)
			$dash .= '-';
		
		$retstr = "$indent+-$dash-+\n";
		
		foreach ($lines as $line) {
			if ($line == '(break)')
				$retstr .= "$indent+-$dash-+\n";
			else {
				for ($j = strlen($line), $space = ''; $j < $max_line; $j++)
					$space .= ' ';
				$retstr .= $indent . '| ' . htmlentities($line) . "$space |\n";
			}
		}
		if (count($this->properties) > 0)
			$retstr .= "$indent+-$dash-+\n";
		
		if ($this->text) {
			foreach ($text_lines as $line) {
				for ($j = strlen($line), $space = ''; $j < $max_line; $j++)
					$space .= ' ';
				$retstr .= "$indent| " . htmlentities($line) . " $space|\n";
			}
			$retstr .= "$indent+-$dash-+\n";
		}
		
		return $retstr;
	}
}

?>

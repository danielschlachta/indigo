<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: site.php - contains the site structure and configuration
 * 
 *  Note that the [class name]_declaration_type 
 *  and [class name]_declaration classes have to be included here to 
 *  avoid circularity in tree.php.
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

require_once($idg_path . '/lib/tree.php');
require_once($idg_path . '/lib/datasource.php');

class idg_datasource_declaration_type extends idg_object_type
{	
	function __construct()
	{
		parent::__construct();
		$this->set_known('name');
		$this->set_known('class');
		$this->set_mandatory('name');
		$this->set_mandatory('class');
	}
}

class idg_datasource_declaration extends idg_tree_node
{	
	var $idg_type = 'datasource';
	
	function __construct()
	{
		parent::__construct();
	}
	
	function get_instance(&$datasource = null)
	{
		if (!$class_name = $this->get_property('class'))
			return false;
			
		$parameters = array();
		if (($text = $this->get_text())) {
			$lines = explode(";", $text);
			foreach ($lines as $line) {
				if ($line) {
					if (!preg_match('/([a-zA-Z][a-zA-Z0-9-]+):[\ ]+(.*)/', 
						$line, $match))
						diag($this, get_class($this->parent) 
							. ': Invalid parameter format (site): ' . $line);
					$parameters[$match[1]] = $match[2];
				}
			}
		}
		$object = new $class_name($parameters);
		$object->parent = $this;
		
		return $object;
	}
	
	function _token(&$tree, &$depth, &$path)
	{
		return true;
	}
}


class idg_renderer_declaration_type extends idg_object_type
{
	
	function __construct()
	{
		parent::__construct();
		$this->set_known('slot');
		$this->set_known('class');
		$this->set_known('source');
		
		$this->set_known('anchor');
		$this->set_known('name');
    	$this->set_known('tag');
		
		$this->set_mandatory('slot');
		$this->set_mandatory('class');
		$this->set_mandatory('source');
	}
}

class idg_renderer_declaration extends idg_tree_node
{
	
	var $idg_type = 'renderer';
	
	function __construct()
	{
		parent::__construct();
	}
	
	function get_instance(&$datasource = null)
	{
		if (!$class_name = $this->get_property('class'))
			return false;
		
		$object = new $class_name($this);
		$object->datasource = $datasource;
		$object->anchor = $this->get_property('anchor');
		
		return $object;
	}
	
	function _token(&$tree, &$depth, &$path)
	{
		if (($anchor = $this->get_property('anchor'))) {
			$prop = array();
			$prop['type'] = 'anchor';
			$prop['anchor'] = $anchor;
			$prop['name'] = $this->get_property('name');
			
			$tree->add_node($depth, $prop, false);
		}
		
		return true;
	}
}

?>

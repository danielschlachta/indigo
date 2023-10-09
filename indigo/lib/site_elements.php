<?php

/* ===================================================================
 * Indigo/Web
 *
 * File: document.php - contains base classes for documents
 *
 * (c) 2023 Daniel Schlachta
 * =========================_======================================== */

require_once($idg_path . '/lib/object.php');

class idg_site_element_type extends idg_tree_node_type
{
	var $child_types = array('idg_folder', 'idg_document');
	
	function __construct()
	{
		parent::__construct();
		$this->set_known('id');
		$this->set_known('name');
		
		$this->set_known('title');
		$this->set_known('content-language', 'yes');
		$this->set_known('title-separator', 'yes');
		$this->set_known('description');
		$this->set_known('navigation-comment');
		$this->set_known('index-document', 'yes');
		$this->set_known('show-name');
		
		$this->set_mandatory('id');
		$this->set_mandatory('name');
	}
}

class idg_site_element extends idg_tree_node
{
	var $site;
	
	function __construct()
	{
		parent::__construct();
	}
	
	function get_site()
	{
		if ($this->site)
			return $this->site;
		
		$site = $this;
		
		while ($site && (get_class($site) != 'idg_site')) {
			$site =& $site->parent;
		}
		
		if (!$site)
			diag($this, 'internal error - no site definition found');
		
		$this->site = $site;
		
		return $site;
	}
}

class idg_folder_type extends idg_site_element_type
{
	function __construct()
	{
		parent::__construct();
		$this->set_mandatory('name', false);
	}
}

class idg_folder extends idg_site_element
{
	function __construct()
	{
		parent::__construct();
		$this->set_idg_type('folder');
	}
	
	function _token(&$tree, &$depth, &$path)
	{
		$prop = $this->get_properties();
		$prop['type'] = 'folder';
		$tree->add_node($depth, $prop, false);
		
		return true;
	}
}

class idg_site_type extends idg_site_element_type
{	
	function __construct()
	{
		parent::__construct();
		$this->child_types[] = 'idg_datasource_declaration';
		$this->set_mandatory('id', false);
	}
}


class idg_document_type extends idg_site_element_type
{
	function __construct()
	{
		parent::__construct();
		$this->set_hook('title', '$this->_get_default_title');
		$this->set_hook('last-change', '$this->_get_last_change');
		$this->child_types[] = 'idg_datasource_declaration';
		$this->child_types[] = 'idg_renderer_declaration';
	}
}

class idg_document extends idg_site_element
{
	var $last_change = false;
	var $params = array();
	
	var $variables;
	
	function __construct()
	{
		parent::__construct();
		$this->set_idg_type('document');
	}
	
	function get_uri()
	{
		$uri = '?display=' . $this->get_path();
		if ($this->variables) {
			foreach ($this->variables as $name => $value) {
				$uri .= "&amp;$name=$value";
			}
		}
		
		return $uri;
	}
	
	function set_variable($name, $value = false)
	{
		$this->variables[$name] = $value;
	}
	
	function get_variable($name)
	{
		if (@($value = $this->variables[$name]))
			return $value;
		else {
			$this->variables[$name] = @$_GET[$name];
			return @$this->variables[$name];
		}
	}
	
	function get_datasource($source_name)
	{
		if ($source_name == '_site') {
			$site = $this->get_site();
			return $site->get_datasource(null);
		} 
		
		/* FIXME: Tell where */
		
		if (!$source_name)
			diag($this,  "idg_renderer_declaration: no datasource");
		
		if (!($datasource_declaration = $this->get_child_by_key('name',
			$source_name, 'idg_datasource_declaration')))
			diag($this, 'idg_renderer_declaration: get_datasource: ' 
				. ' no datasource named ' . $source_name);
		
		return $datasource_declaration->get_instance();
	}
	
	function get_renderers($slot_name)
	{
		$renderers = array();
		
		if (!$renderer_declarations = $this->get_children_by_key('slot',
			$slot_name, 'idg_renderer_declaration'))
			diag($this, get_class($this) 
				. '(' . $this->get_path() 
				. '): unknown renderer (' . $slot_name . ')');
		
		foreach ($renderer_declarations as $renderer_declaration) {
			$source_name = $renderer_declaration->get_property('source');
			$datasource = $this->get_datasource($source_name);;
			$renderers[] = $renderer_declaration->get_instance($datasource);
		}
		
		if (count($renderers) == 0)
			return false;
		
		return $renderers;
	}
	
	function get_path()
	{
		$path = $this->get_property('id');
		$tmp = $this->parent;
		
		while ($tmp && (get_class($tmp) != 'idg_site')) {
			$path = $tmp->get_property('id') . '/' . $path;
			$tmp =& $tmp->parent;
		}
		
		return $path;
	}
	
	function _get_default_title()
	{
		$site = $this->get_site();
		$index = $site->get_document();
		
		if ($this->get_property('show-name') == 'no') {
			$title = '';
		} else {
			$title = $this->get_property('name');
		}
		
		$tmp = $this->parent;
		
		while ($tmp) {
			if (($tmp->get_property('show-name') != 'no') 
				&& (($p_title = $tmp->get_property('name')) != '')) {
				$title = $title . ($title != '' ? 
					$tmp->get_property('title-separator') : '') . $p_title;
			}
			
			$tmp =& $tmp->parent;
		}
		
		return $title;
	}
	
	function set_last_change($time)
	{
		if (!$this->last_change || $this->last_change < $time)
			$this->last_change = $time;
	}
	
	function _get_last_change()
	{
		if (!($last_change = $this->last_change))
			$last_change = time();
		return date("d.m.Y h:i", $last_change);
	}
	
	function _token(&$tree, &$depth, &$path)
	{
		$prop = $this->get_properties();
		$prop['path'] = $this->get_path();
		$prop['uri'] = '?display=' . $prop['path'];
		$prop['type'] = 'document';
		
		$tree->add_node($depth, $prop, false);
		
		return true;
	}
}

?>

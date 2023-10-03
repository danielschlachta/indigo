<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: site.php - contains the site structure and configuration
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

require_once($idg_path . '/lib/tree.php');
require_once($idg_path . '/lib/data.php');

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
		$this->set_known('hidden', 'yes');
		$this->set_known('changefreq', 'yes');
		
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

class idg_document_type extends idg_site_element_type
{
	function __construct()
	{
		parent::__construct();
		$this->add_hook('title', '$this->_get_default_title');
		$this->add_hook('last-change', '$this->_get_last_change');
		$this->child_types[] = 'idg_datasource_declaration';
		$this->child_types[] = 'idg_renderer_declaration';
	}
}

class idg_document extends idg_site_element
{
	var $idg_type = 'document';
	
	var $last_change = false;
	var $params = array();
	
	var $variables;
	
	function __construct()
	{
		parent::__construct();
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
		
		if (!($datasource_declaration = $this->get_child_by_key('name',
			$source_name, 'idg_datasource_declaration')))
			diag($this, 'Document has no datasource named ' . $source_name);
		
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
				$title = $p_title . ($title != '' ? 
					$tmp->get_property('title-separator') : '') . $title;
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
		if ($this->get_property('hidden') == 'yes')
			return true;
		$prop = $this->get_all_properties();
		$prop['path'] = $this->get_path();
		$prop['uri'] = '?display=' . $prop['path'];
		$prop['type'] = 'document';
		
		$tree->add_node($depth, $prop, false);
		
		return true;
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
	var $idg_type = 'folder';
	
	function __construct()
	{
		parent::__construct();
	}
	
	function _token(&$tree, &$depth, &$path)
	{
		if ($this->get_property('hidden') == 'yes')
			return true;
		$prop = $this->get_all_properties();
		$prop['type'] = 'folder';
		$tree->add_node($depth, $prop, false);
		
		return true;
	}
}

class idg_site_type extends idg_folder_type
{	
	function __construct()
	{
		parent::__construct();
		$this->child_types[] = 'idg_datasource_declaration';
		$this->set_mandatory('id', false);
	}
}

class idg_site extends idg_site_element
{
	var $idg_type = 'site';
	var $idg_translation = array(
		'site' => 'idg_site', 
		'folder' => 'idg_folder', 
		'document' => 'idg_document', 
		'datasource' => 'idg_datasource_declaration', 
		'renderer' => 'idg_renderer_declaration'
	);
	
	var $document;
	var $view;
	
	function __construct()
	{
		parent::__construct();
	}
	
	function get_document($document = false)
	{
		$document_name = $document;
		$document_obj = false;
		
		if (!$document_name) {
			if (@$display = $_GET['display']) {
				$document_name = $display;
				
				if (($pos = strpos($document_name, '#')) !== false) {
					$document_name = substr($display, $pos);
				}
			}
		}
		
		if (!$document_name)
			$document_name = $this->get_property('index-document');
		
		if ($document_name) {
			if ($document_name[0] != '/')
				$document_name = '/' . $document_name;
			
			$path = explode('/', $document_name);
			$count = count($path);
			
			for ($i = 1, $tmp =& $this; $i < $count && $tmp; $i++) {
				$tmp = $tmp->get_child_by_key('id', $path[$i]);
			}
			$document_obj = $tmp;
		}
		
		return $document_obj;
	}
	
	function get_datasource($source_name)
	{
		$param = false;
		$tree = new idg_datasource_tree($param);
		
		if ($this->children) {
			foreach ($this->children as $child) {
				if (!$child->_get_subtree($tree, '$this->_token'))
					return false;
			}
		}
		return $tree;
	}
	
	function get_site_url() 
	{
		$parsed_url = parse_url(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" 
				. "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
		$scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
		$host = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
		$port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
		$user = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
		$pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
		$pass = ($user || $pass) ? "$pass@" : ''; 
		$path = (isset($parsed_url['path']) ? $parsed_url['path'] : ''); 
		$query  = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : ''; 
		return "$scheme$user$pass$host$port$path$query$fragment";
    }
	
	function get_sitemap()
	{
	    $this->_sitemap = '<?xml version="1.0" encoding="UTF-8"?>' 
	        . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' 
            . "\n";
        $this->_url = $this->get_site_url();
        $this->_scan_object($this);
        
        return $this->_sitemap . "</urlset>\n";
	}
	
	function _scan_object(&$object, $prefix = '') 
	{
        if ($object->idg_type == 'site')
            foreach ($object->children as $child)
                $this->_scan_object($child, '');


        if ($object->idg_type == 'folder')
            foreach ($object->children as $child)
                $this->_scan_object($child, 
                    $prefix . '/' . $object->properties['id']);
            
        if ($object->idg_type == 'document') {
            $prefix[0] = '=';
            $lastchg = $object->get_property('last-change');
            $lastmod = substr($lastchg, 6, 4) . '-' . substr($lastchg, 3, 2)
              . '-' . substr($lastchg, 0, 2);
            
            if (!(@$changefreq = $object->properties['changefreq']))
                $changefreq = 'daily';
            
            $this->_sitemap .= "  <url>\n     <loc>" . 
                $this->_url . urlencode('?display' . $prefix . '/'  
                    . $object->properties['id'])
                . "</loc>\n     <lastmod>$lastmod</lastmod>\n" 
                . "     <changefreq>$changefreq</changefreq>\n" 
                . "  </url>\n";
    }
}

	
}

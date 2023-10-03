<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: view.php - contains the generic view structure
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

require_once($idg_path . '/lib/tree.php');

class idg_view_type extends idg_tree_node_type
{	
	function __construct()
	{
		parent::__construct();
		$this->set_known('name');
		$this->set_known('tag');
		$this->set_mandatory('name');
	}
}

class idg_view extends idg_tree_node
{
	var $xml_type = 'view';
	
	var $streams;
	var $filters = array();
	
	var $output;
	
	function __construct()
	{
		parent::__construct();
	}
	
	function stream_append($stream_name, $content)
	{
		if (array_key_exists($stream_name, $this->streams)) {
			if (($stream_name == 'html-body')) {
				foreach ($this->filters as $filter => $is_set) {
					if ($is_set)
						$tmp = $filter($content);
					else
						$tmp = false;
					if ($tmp)
						$content = $tmp;
				}
			}
			$this->streams[$stream_name] .= $content;
		} else
			diag($this, 'Unknown stream: ' . $stream_name);
	}
	
	function _set_filter($name)
	{
		$this->filters[$name] = true;
	}
	
	function _unset_filter($name)
	{
		$this->filters[$name] = false;
	}
	
	function printout()
	{
		echo $this->output;
	}
}

class idg_view_node_obj
{	
	var $parent;
	var $text;
	
	function __construct($parent)
	{
		$this->parent = $parent;
		$this->text = $parent->text;
	}
}

?>

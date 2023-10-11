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
		$this->set_known('filter');
	}
}

class idg_view extends idg_tree_node
{
	protected $streams = array();
	protected $filters = array();

	public $output;

	function __construct()
	{
		parent::__construct();
		$this->set_idg_type('view');
	}

	function set_streams($streams) {
		$this->streams = $streams;
	}

	function stream_append($stream_name, $content)	{
		if (array_key_exists($stream_name, $this->streams)) {
			if (($stream_name == 'html-body')) {
				foreach ($this->filters as $filter => $is_set) {
					if ($is_set) {
						if (!function_exists($filter))
							diag($this, "unknown filter: $filter");

						$content = $filter($content);
					}
				}

				if ($filter = $this->get_property('filter')) {
					if (!function_exists($filter))
						diag($this, "unknown filter: $filter");

					$content = $filter($content);
				}
			}

			$this->streams[$stream_name] .= $content;
		} else
			diag($this, 'Unknown stream: ' . $stream_name);
	}

	function printout()
	{
		echo $this->output;
	}

	protected function set_filter($name) {
		$this->filters[$name] = true;
	}

	protected function unset_filter($name)	{
		$this->filters[$name] = false;
	}

}

class idg_view_node_obj
{
	var $parent;

	function __construct($parent)
	{
		$this->parent = $parent;
	}

	function get_idg_id() {
		if ($this->parent)
			return $this->parent->get_idg_id();
		else
			diag('internal error: no parent');
	}
}

class idg_view_node_param_obj extends idg_view_node_obj
{
	var $parameters = array();

	function __construct(&$parent)
	{
		parent::__construct($parent);
		$this->get_parameters();
	}

	function get_parameters()
	{
		if (!($text = $this->parent->get_text()))
			return;

		$lines = explode(";", trim($text));
		foreach ($lines as $line) {
			if ($line != '') {
				if (!preg_match('/([a-zA-Z][a-zA-Z0-9-]+):[\  ]+(.*)/',
					$line, $match))
					diag($this,
					'invalid parameter format: `' . $line . '`');
				$this->parameters[$match[1]] = $match[2];
			}
		}
	}
}

?>

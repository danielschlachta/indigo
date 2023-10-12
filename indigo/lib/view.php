<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

abstract class idg_view_type extends idg_tree_node_type {
	function __construct()	{
		parent::__construct();
		$this->set_known('name');
		$this->set_known('tag');
		$this->set_known('filter');
	}
}

abstract class idg_view extends idg_tree_node {
	protected $streams = array();
	protected $filters = array();
	protected $printout = '';

	function __construct($streams)
	{
		parent::__construct();
		$this->set_idg_type('view');
		$this->streams = $streams;
	}

	function set_filter($name) {
		$this->filters[$name] = true;
	}

	function unset_filter($name)	{
		$this->filters[$name] = false;
	}

	function stream_append($stream_name, $content)	{
		if (array_key_exists($stream_name, $this->streams)) {
			if (($stream_name == 'html-body')) {
				foreach ($this->filters as $filter => $is_set) {
					if ($is_set) {
						$tmp = $filter($content);
					} else
						$tmp = false;

					if ($tmp)
						$content = $tmp;
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

	function print() {
		echo($this->printout);
	}

	function _print($text) {
		$this->printout .= $text;
	}
}

class idg_view_node_param_obj extends idg_tree_node_implementation {
	var $parameters = array();

	function __construct(&$parent)
	{
		parent::__construct($parent);
		$this->get_parameters();
	}

	function get_parameters()
	{
		if (!($text = $this->get_text()))
			return;

		$cn = get_class($this);

		echo "<!-- param obj: $cn: $text -->\n";

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

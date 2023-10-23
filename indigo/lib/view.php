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

	/** @todo HTML specific stream selection! */

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


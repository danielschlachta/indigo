<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** Base class for idg entities with parameters. 
 * 
 * @todo rename options to parameters
 */

abstract class idg_param_instance {
    private $options = [];
    
    function __construct($parameters = null) {
        $this->options = $parameters;
    }
    
    /** Remove pointers etc. to avoid circularity in diagnostic output.
     * 
     * @return void
     */
    function clear() {
        ;
    }
    
    /** @return mixed|null */
    function get_parameter(string $name, mixed $default = null) {
        $opt = @$this->options[$name];
        return $opt ? $opt : $default;
    }
    
    /** @return mixed[] */
    function get_parameters() {
        return $this->options;
    }

    /** @return void */
    function set_parameter($name, $value) {
        $this->options[$name] = $value;
    }

    /** @return void */
    function set_parameters($options = null) {
		if (!$options)
			return;

		foreach ($options as $name => $value)
			$this->set_parameter($name, $value);
	}
}

class idg_attribute extends idg_param_instance {
	private $name;
    private $scope;

    function __construct(string $name) {
        if (($pos = strpos($name, '::')) > 0) {
            $this->scope = substr($name, 0, $pos);
            $this->name = substr($name, $pos + 2);
        } else
            $this->name = $name;
    }

    /** @return string */
	function get_scope() {
		return $this->scope;
	}

    /** Causes the attribute to inherit all instances in and above a given tree node.
     * 
     * @param &$tree_node A tree node.
     * @param $scope The scope for a freshly created attribute, can be used
     * instead of `set_scope()`.
     * 
     * @todo this is ugly
     * 
     * @return void */
	function add_options(idg_tree_node|idg_tree_node_instance &$tree_node, 
        string $scope = null) {
		if (!$tree_node)
			diag($this, 'add_options called with null argument');

		if (method_exists($tree_node, 'get_site')) {
			$site = $tree_node->get_site();
			if (($attr = $site->get_attribute($this->name)))
				$this->set_parameters($attr->get_parameters());
			if (($attr = $site->get_attribute($this->name, $scope)))
				$this->set_parameters($attr->get_parameters());
		}

		$folders = [];
		$folder = $tree_node;

		do {
			if (method_exists($folder, 'get_folder')) {
				$folder = $folder->get_folder();
				$folders[] = $folder;
				$folder = $folder->get_parent();
			} else
				$folder = null;
		} while ($folder);

		while (($folder = array_pop($folders))) {
			if (($attr = $folder->get_attribute($this->name)))
				$this->set_parameters($attr->get_parameters());
			if (($attr = $folder->get_attribute($this->name, $scope)))
				$this->set_parameters($attr->get_parameters());
		}

		if (($attr = $tree_node->get_attribute($this->name)))
			$this->set_parameters($attr->get_parameters());
		if (($attr = $tree_node->get_attribute($this->name, $scope)))
			$this->set_parameters($attr->get_parameters());
	}

    /** @return string */
    function get_xml($indent) {
        $name = $this->name;

        $xml = "$indent<attribute name=\"$name\">\n";

        foreach ($this->options as $name => $value)
            $xml .= "$indent	<option name=\"$name\">$value</option>\n";

        $xml .= "$indent</attribute>\n";

        return $xml;
    }
}

/**
 * The source of all data.
 *
 * This is basically an iterator.
 * 
 * @todo Actually make this an iterator.
 * 
 */
class idg_datasource_instance extends idg_param_instance {

    var $tokens = array();
    var $current_token = 0;

    // public functions

    /**
     * Resets the datasource to return the first token.
     * 
     * @return void
     */
    function rewind() {
        $this->current_token = 0;
    }

    /**
     * Returns the next token.
     * 
     * @return mixed|null The next token in the data stream.
     * 
     * You must use `rewind()` after null has been returned.
     */
    function get_token() {
        if ($this->current_token < 0)
            return;

        if ($this->current_token >= count($this->tokens)) {
            $this->current_token = -1;
            return;
        }

        return $this->tokens[$this->current_token++];
    }
}

<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: datasources.php - basic builtin data sources
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

class idg_token_tree_node extends idg_token
{
	// public functions

	function __construct($depth, $is_leaf)
	{
		parent::__construct($depth);
		$this->is_leaf = $is_leaf;
	}
}

class idg_datasource_tree extends idg_datasource
{
	var $node_count = 0, $leaf_count = 0;

	function add_node($depth, &$properties, $is_leaf = true)
	{
		$tok = new idg_token_tree_node($depth, $is_leaf);
		$tok->properties = $properties;

		if ($is_leaf)
			$this->leaf_count++;
		else
			$this->node_count++;

		$this->tokens[] = $tok;
	}
}

/*!
 * Returns the content of a (normally text) file.
 *
 * Accepts parameters:
 *   + \c filename obviously mandatory
 *   + \c max_size defaults to 32k
 *
 * Returns two tokens:
 * [0] The file content
 * [1] The time/date of last modification as returned by filemtime
 *
 */

class idg_datasource_textfile extends idg_datasource
{
	function __construct(&$parameters)
	{
	    global $idg_max_filesize;

		parent::__construct($parameters);

		$file = @$this->parameters['filename'];

		if (!$file)
			diag($this, get_class($this)
			    . ': mandatory parameter(filename) not set');

		if (@stat($file) === false)
			diag($this, get_class($this)
			    . ': text file "' . $file . '" not found');

		$max_size = @$this->parameters['max_size'];
		if (!$max_size || $max_size < 0)
			$max_size = $idg_max_filesize;

		if (@!$fp = fopen($file, 'r'))
			diag($this, get_class($this)
			    . ': could not open text file "' . $file . '"');

		$tok = fread($fp, $max_size);
		$content = new idg_token($tok);
		fclose($fp);
		$this->tokens[] = $content;

		$mtime = filemtime($file);
		$filetime = new idg_token($mtime);
		$this->tokens[] = $filetime;
	}
}

class idg_datasource_phpscript extends idg_datasource
{
	function __construct(&$parameters)
	{
		parent::__construct($parameters);

		if (@!($script = $this->parameters['script']))
			diag($this, get_class($this)
			    . ': mandatory parameter(script) not found');

		if (@!($class = $this->parameters['class']))
			diag($this, get_class($this)
			    . ': mandatory parameter(class) not found');

		$token = new idg_token($this->parameters);
		$properties = array(
			'script' => $script,
			'class' => $class
		);
		$token->set_properties($properties);
		$this->tokens[] = $token;
	}
}

?>

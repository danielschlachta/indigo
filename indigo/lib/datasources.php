<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: datasources.php - basic builtin data sources
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

/**
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
		fclose($fp);
		$this->tokens[] = $tok;
		$this->tokens[] = filemtime($file);
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

		$this->tokens[] = array(
			'script' => $script,
			'class' => $class
		);
	}
}

?>

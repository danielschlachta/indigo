<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/**
 * Returns the content of a (normally text) file.
 *   @param mixed filename the name of the file, obviously mandatory
 *   @param mixed max_size maximal file size, default is 32KiB
 *
 * @return The datasource produces the following tokens:
 * 
 * Number | Description
 * -------|------------
 * 1      | The file content
 * 2      | The time/date of last modification as returned by filemtime
 *
 */

class idg_datasource_textfile extends idg_datasource_instance
{
    /** sdf
     * 
     * @global type $idg_max_filesize
     * @param string[] $parameters
     * 
     * @return string Description
     */
    
    function __construct($parameters = null)
	{
	    parent::__construct($parameters);

		$filename = $this->get_parameter('filename');

        if (!$filename)
			diag($this, "option filename not specified");

		if (@stat($filename) === false)
			diag($this, "filename not found: '$filename");

        if (is_dir($filename))
            diag($this, "'$filename' is a directory");
        
		$max_size = 0 + @$this->get_parameter('max-size');
        $max_size = $max_size <= 0 ?  32 * 1024 : $max_size;
        
		if (!$max_size || $max_size < 0)
			$max_size = $idg_max_filenamesize;

		if (@!$fp = fopen($filename, 'r'))
			diag($this, get_class($this)
			    . ': could not open text filename "' . $filename . '"');

		$tok = fread($fp, $max_size);
		fclose($fp);
		$this->tokens[] = $tok;
		$this->tokens[] = filemtime($filename);
	}
}

class idg_datasource_phpscript extends idg_datasource_instance
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

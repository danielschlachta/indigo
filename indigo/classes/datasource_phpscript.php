<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Datasource;

class phpscript extends \idg_datasource_implementation
{
	function __construct(&$parameters)
	{
		parent::__construct($parameters);
        
		if (@!($script = $this->parameters['script']))
			idg_diag($this, get_class($this)
			    . ': mandatory parameter(script) not found');

		if (@!($class = $this->parameters['class']))
			idg_diag($this, get_class($this)
			    . ': mandatory parameter(class) not found');

		$this->tokens[] = array(
			'script' => $script,
			'class' => $class
		);
	}
}

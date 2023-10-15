<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: datasource.php - contains base classes for data sources and tokens
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

require_once($idg_path . '/lib/object.php');

$idg_max_filesize = 32 * 1024;

/**
 * The source of all data.
 *
 * This is basically an iterator.
 */

class idg_datasource
{
	var $parameters;
	var $tokens = array();

	var $act_token = 0;

	// public functions

	function __construct(&$parameters)
	{
		$this->parameters = $parameters;
	}

	/**
	 * Resets the datasource to the first token
	 *
	 */

	function rewind()
	{
		$this->act_token = 0;
	}

	/**
	 * Returns the next token or false if there isn't one
	 *
	 */

	function get_token()
	{
		if ($this->act_token < 0)
			return false;

		if ($this->act_token >= count($this->tokens)) {
			$this->act_token = -1;
			return false;
		}

		return $this->tokens[$this->act_token++];
	}

	function get_parameters($name)
	{
		return @$this->parameters[$name];
	}
}


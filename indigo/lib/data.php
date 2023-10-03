<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: data.php - contains base classes for data sources and tokens
 *
 * (c) 2020 Daniel Schlachta
 * ======================================================================== */

require_once($idg_path . '/lib/object.php');

$idg_max_filesize = 32 * 1024;

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
	
	function rewind()
	{
		$this->act_token = 0;
	}
	
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
	
	function get_token_data()
	{
		if (($token = $this->get_token()))
			return $token->data;
		else
			return false;
	}
	
	function get_parameters($name)
	{
		return @$this->parameters[$name];
	}
}

class idg_token
{	
	var $id;
	var $properties;
	var $data;
	
	// public functions 
	
	function __construct(&$data)
	{
		$this->data = $data;
	}
	
	function get_properties()
	{
		return @$this->properties;
	}
	
	function set_properties(&$prop)
	{
		$this->properties = $prop;
	}
	
	function get_property($name)
	{
		return @$this->properties[$name];
	}
}


?>

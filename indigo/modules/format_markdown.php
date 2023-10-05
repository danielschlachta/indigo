<?php

$parsedown_main = $idg_path . '/modules/parsedown/Parsedown.php';

if (file_exists($parsedown_main)) {
	require_once($parsedown_main);
} else {
	include($idg_path . '/modules/parsedown-install.html');
	exit;
}

/*!
 * Support for .md files via parsedown
 *
 * Use datasource_textfile as data source.
 */

class idg_view_html_renderer_markdown extends idg_view_node_obj
{
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}
		
	function render(&$document, &$view)
	{
    	$this->datasource->rewind();     	
    	$content = $this->datasource->get_token()->data;
    	
    	$Parsedown = new Parsedown();

    	$view->stream_append('html-body', $Parsedown->text($content));
		$last_change = $this->datasource->get_token()->data;
		$document->set_last_change($last_change);
	}
}


?>

<?php

$parsedown_main = $idg_path . '/modules/parsedown/Parsedown.php';

if (file_exists($parsedown_main)) {
	require_once($parsedown_main);
} else {
   	complain_module('parsedown', $parsedown_main,
		'https://github.com/erusev/parsedown.git');
	exit;
}


/*!
 * Support for .md files via parsedown
 *
 * Use datasource_textfile as data source.
 */

class idg_view_html_renderer_markdown extends idg_tree_node_implementation
{
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	function render(&$document, &$view)
	{
    	$this->datasource->rewind();
    	$content = $this->datasource->get_token()->get_data();

    	$Parsedown = new Parsedown();

    	$view->stream_append('html-body', $Parsedown->text($content));
		$last_change = $this->datasource->get_token()->get_data();
		$document->set_last_change($last_change);
	}
}


?>

<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: html_items.php - basic builtin items
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

class idg_view_html_item_image extends idg_view_node_param_obj
{

	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	function render(&$document, &$view)
	{
		$idg_id = $this->parent->get_idg_id();

		$style = $this->parent->get_property('style');

		$img_src = $this->parameters['source'];
		$width = $this->parameters['width'];
		$height = $this->parameters['height'];
		$alt_text = @$this->parameters['alt-text'];
		$classname = @$this->parameters['classname'];

		$body = "<img id=\"$idg_id\" src=\"$img_src\" "
			. "width=\"$width\" height=\"$height\" " . "alt=\"$alt_text\">\n";
		$view->stream_append('html-body', $body);

		$css = "	img#$idg_id {\n		border: 0;\n";

		if ($style)
			$css .= "		$style\n";

		$css .= "	}\n\n";

		$view->stream_append('css', $css);
	}
}

class idg_view_html_item_text extends idg_view_node_obj
{
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	function render(&$document, &$view)
	{
		$idg_id = $this->parent->get_idg_id();

		if (!$text = htmlentities($this->parent->get_text()))
			return;

    	$css = '';

    	$style = $this->parent->get_property('style');

		$css .= "	span#$idg_id {\n		$style\n	}\n\n";

	    if ($style_link = $this->parent->get_property('style-link'))
			$css .= "	span#$idg_id a {\n		$style_link\n	}\n\n";

		if ($style_link_hover = $this->parent->get_property('style-link-hover'))
			$css .= "	span#$idg_id a:hover {\n		$style_link_hover\n	}\n\n";

		if ($css != '')
    		$view->stream_append('css', $css);

		$vars = array();
		while (preg_match('/(\{[a-z0-9\-]+\})/', $text, $match)) {
			$name = substr($match[0], 1, strlen($match[0]) - 2);
			$vars[$name] = '';
			$text = preg_replace($match[0], '', $text);
		}

		$document->get_properties($vars);

		if ($css != '') {
			$text = "<span id=\"$idg_id\">"
				. $this->parent->get_text() . "</span>";
		} else
			$text = $this->parent->get_text();

		foreach ($vars as $name => $value) {
			$text = preg_replace("/\{$name\}/", $value, $text);
		}

    	$text = preg_replace("/<\n/", '<', $text);
    	$text = preg_replace("/\n</", '<', $text);
    	$text = preg_replace("/\n>/", '>', $text);
    	$text = preg_replace("/>\n/", '>', $text);

        $text = preg_replace("/&\n/", '&', $text);


		$view->stream_append('html-body', $text);
	}
}

?>

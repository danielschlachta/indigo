<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: view_html.php - contains the structure for html documents
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

/*!
 * Parent class of all views
 *
 * Defines the following properties:
 *
 * * icon
 * * style
 * * style-print
 * * name
 *
 */

require_once($idg_path . '/lib/uuid.php');

class idg_view_html_type extends idg_view_type
{
	var $child_types = array('idg_view_html_part');

	function __construct()
	{
		parent::__construct();
		$this->set_known('icon');
		$this->set_known('style');
		$this->set_known('style-print');
		$this->set_known('name');
	}
}

class idg_view_html extends idg_view
{

	var $idg_translation = array(
		'view' => 'idg_view_html',
		'part' => 'idg_view_html_part',
		'container' => 'idg_view_html_container',
		'renderer' => 'idg_view_html_renderer',
		'item' => 'idg_view_html_item',
		'slot' => 'idg_view_html_slot'
	);

	function __construct()
	{
		parent::__construct();
		$this->set_idg_type('view');

		$this->set_streams(
			array(
				'html-head' => '',
				'html-body-start' => '',
				'html-body' => '',
				'css' => '',
				'css-print' => '',
				'js' => ''
			)
		);
	}

	private function _milliseconds() {
		$mt = explode(' ', microtime());
		return intval($mt[1] * 1E3) + intval(round($mt[0] * 1E3));
	}

	function render(&$document)
	{
		global $idg_program_name;

		$doc_prop = array(
			'name' => false,
			'title-separator' => false,
			'content-language' => false,
			'index-document' => false,
			'title' => false
		);

		if (!$document)
		    die('idg_view_html: render called with null argument.'
		        . ' Forgot to call get_document()?');

		if (!$this->children)
			die('<code>This page intentionally left blank.</code>');

		$document->get_properties($doc_prop);

		$style = $this->get_property('style');
		$icon = $this->get_property('icon');

		$document->uuid = UUID::v4();

		$start = $this->_milliseconds();

		foreach ($this->children as $child) {
			try {
				$child->_render($document, $this);
			}
			catch (Exception $e) {
				var_dump($e->getTraceAsString());
				exit;
			}
		}

		$milli_time = $this->_milliseconds() - $start;

		$output = '<!-- document UUID=' . $document->uuid
			. ' generated on ' . date('r', time())
			. " by $idg_program_name, time: $milli_time ms  -->\n"
			. "<!DOCTYPE html>\n";

		$output .= '<html lang="' . $doc_prop['content-language'] . "\">\n"
			. "<head>\n"
			. " <title>" . $doc_prop['title'] . "</title>\n"
			. " <meta http-equiv=\"Content-Type\" content=\"text/html;"
				. " charset=utf-8\">\n";

		if ($icon)
			$output .= " <link rel=\"shortcut icon\" href=\"$icon\" "
				. "type=\"image/x-icon\">\n";

		if ($this->streams['html-head'] != '')
			$output .= $this->streams['html-head'] . "\n";

		$css = '';

		if ($style)
			$css .= "  	body { $style }\n";

		$css .= $this->streams['css'];

		if ($this->streams['css-print'] != '') {
			$css .= "	@media print {\n";
			$css .= $this->streams['css-print'];
			$css .= "	}\n";
		}

		if ($css != '')
			$output .= " <style>\n$css </style>\n";

		if ($this->streams['js'] != '') {
			$output .= " <script>\n";
			$output .= $this->streams['js'];
			$output .= " </script>\n";
		}
		$output .= "</head>\n<body>\n";

		$output .= $this->streams['html-body-start'];
		$output .= $this->streams['html-body'];
		$output .= "</body>\n</html>";

		$this->output = $output;
	}
}

class idg_view_html_element_type extends idg_view_html_type
{
	function __construct()
	{
		parent::__construct();
		$this->set_known('style');
		$this->set_known('style-link');
		$this->set_known('style-link-hover');
		$this->set_known('style-image');
		$this->set_known('class');
	}
}

class idg_view_html_element extends idg_tree_node
{
	var $object;

	function __construct()
	{
		parent::__construct();
	}

	function _render(&$document, &$view)
	{
		$instance = $this->get_instance($view);
		$instance->render($document, $view);
	}
}

class idg_view_html_part_type extends idg_view_html_element_type
{

	var $child_types = array(
		'idg_view_html_container',
		'idg_view_html_renderer',
		'idg_view_html_item'
	);

	function __construct()
	{
		parent::__construct();
	}
}

class idg_view_html_part extends idg_view_html_element
{
	function __construct()
	{
		parent::__construct();
		$this->set_idg_type('part');
	}
}

class idg_view_html_container_type extends idg_view_html_element_type
{

	var $child_types = array(
		'idg_view_html_container',
		'idg_view_html_item',
		'idg_view_html_renderer',
		'idg_view_html_slot'
	);

	function __construct()
	{
		parent::__construct();
		$this->set_known('style-head');
		$this->set_known('style-subhead');
		$this->set_known('style-link');
		$this->set_known('style-link-hover');
		$this->set_known('style-image');
		$this->set_known('filter');
	}
}

class idg_view_html_container extends idg_view_html_element
{
	function __construct()
	{
		parent::__construct();
		$this->set_idg_type('container');
	}

	function _render(&$document, &$view)
	{
		$body = false;

		$style = $this->get_property('style');

		if ($style) {
			$idg_id = $this->get_idg_id();
			$style_head = $this->get_property('style-head');
			$style_subhead = $this->get_property('style-subhead');
			$style_link = $this->get_property('style-link');
			$style_link_hover = $this->get_property('style-link-hover');
			$style_image = $this->get_property('style-image');

			$body = "<div id=\"$idg_id\">\n";
			$view->stream_append('html-body', $body);

			if ($style)
				$css = "	div#$idg_id {\n		$style\n	}\n\n";
			else
				$css = '';

			if ($style_head)
				$css .= "	div#$idg_id h1 { $style_head }\n";
			if ($style_subhead)
				$css .= "	div#$idg_id h2 { $style_subhead }\n";
			if ($style_link)
				$css .= "	div#$idg_id a { $style_link }\n";
			if ($style_link_hover)
				$css .= "	div#$idg_id a:hover { $style_link_hover }\n";
			if ($style_image)
				$css .= "	div#$idg_id img { $style_image }\n";

			$view->stream_append('css', $css);

			if (($style_print = $this->get_property('style-print'))) {
				$css_print = "div#$idg_id { $style_print }\n";
				$view->stream_append('css-print', $css_print);
			}

			$body = "</div>\n";
		}

		$filter = $this->get_property('filter');
		if ($filter)
			$view->_set_filter($filter);

		if ($this->children) {
			foreach ($this->children as $child) {
				$child->_render($document, $view);
			}
		}

		if ($filter)
			$view->_unset_filter($filter);

		if ($body)
			$view->stream_append('html-body', $body);
	}
}

class idg_view_html_renderer_type extends idg_view_html_element_type
{

	var $child_types = array();

	function __construct()
	{
		parent::__construct();

		$this->set_known('source');
		$this->set_known('tag');
		$this->set_mandatory('source');
	}
}

/*!
 * The parent class of all renderers.
 *
 * Has one single function, _render, which does the basic work
 * required to create an instance of the xml subclassed type.
 *
 */

class idg_view_html_renderer extends idg_view_html_element
{
	var $datasource;

	function __construct()
	{
		parent::__construct();
		$this->set_idg_type('renderer');
	}

	/*!
	 * See class description.
	 *
	 */

	function _render(&$document, &$view)
	{
		$source_name = $this->get_property('source');

		if ($source_name != 'null') {
				$this->datasource =
					$document->get_datasource($source_name);

				if (!$this->datasource)
					diag($this,
						"document: datasource not found: $source_name");
		}

		$instance = $this->get_instance();
		$instance->datasource = $this->datasource;
		$instance->tag = $this->get_property('tag');
		$instance->render($document, $view);
	}
}

class idg_view_html_item_type extends idg_view_html_element_type
{
	var $child_types = array();

	function __construct()
	{
		parent::__construct();
	}

}

class idg_view_html_item extends idg_view_html_element
{
	function __construct()
	{
		parent::__construct();
		$this->set_idg_type('item');
	}
}

class idg_view_html_slot_type extends idg_tree_node_type
{
	var $child_types = array();

	function __construct()
	{
		parent::__construct();
		$this->set_known('name');
		$this->set_known('style');
		$this->set_known('style-link');
		$this->set_known('style-link-hover');
		$this->set_known('list-style-image');
		$this->set_known('list-style-position');
		$this->set_known('filter');
		$this->set_mandatory('name');
	}
}

class idg_view_html_slot extends idg_tree_node
{
	function __construct()
	{
		parent::__construct();
		$this->set_idg_type('slot');
	}

	function _render(&$document, &$view)
	{
		$name = $this->get_property('name');
		$renderers = $document->get_renderers($name);
		$idg_id = $this->get_idg_id();
		$filter = $this->get_property('filter');
		if ($filter)
			$view->_set_filter($filter);

		$anchor_count = 0;

		foreach ($renderers as $renderer) {
			if (!$renderer)
				continue;

			if (($anchor = $renderer->anchor)) {
				$anchor_count++;
				$body = "<div id=\"$anchor\"></div>";
			} else
				$body = '';

			$body .= "<div class=\"$idg_id\">\n";
			$view->stream_append('html-body', $body);
			$renderer->render($document, $view);
			$body = "</div>\n";
			$view->stream_append('html-body', $body);
		}

		if ($filter)
			$view->_unset_filter($filter);

		$style = $this->get_property('style');
		$style_link = $this->get_property('style-link');
		$style_link_hover = $this->get_property('style-link-hover');

		$list_style_image = $this->get_property('list-style-image');
		$list_style_position = $this->get_property('list-style-position');

		$css = '';

		if ($style)
			$css .= "	div.$idg_id { $style }\n";

		if ($style_link)
			$css .= "	div.$idg_id a { $style_link }\n";

		if ($style_link_hover)
			$css .= "	div.$idg_id a:hover { $style_link_hover }\n";

		if ($list_style_image || $list_style_position) {
			$css .= "	div.$idg_id ul {\n";

			if ($list_style_image)
			$css .= "		list-style-image: url($list_style_image);\n";

			if ($list_style_position)
				$css .= "	list-style-position: $list_style_position;";

			$css .= "	}\n\n";
		}

		/*! @todo css-print? */


		if ($anchor_count > 0)
			$css .= "	div.$idg_id-anchor {\n		display: none;\n	}\n\n";

		$view->stream_append('css', $css);
	}
}


?>

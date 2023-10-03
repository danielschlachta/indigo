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
 * * css-static
 * 
 */

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
		$this->set_known('css-static');
	}
}

class idg_view_html extends idg_view
{
	
	var $idg_type = 'view';
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
		$this->streams = array(
			'html-head' => '',
			'html-body-start' => '',
			'html-body' => '',
			'css' => '',
			'css-print' => '',
			'js' => ''
		);
		parent::__construct();
	}

	function _milliseconds() {
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
		    die('Error: idg_view_html->render called with null argument.' 
		        . ' Forgot to call get_document()?');
		
		$document->get_properties($doc_prop);
		
		$style = $this->get_property('style');
		$icon = $this->get_property('icon');
		$css_static = $this->get_property('css-static');
		
		$child_count = count($this->children);

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
		
		$output = '<!-- generated on ' . date('r', time()) 
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
		
		if ($css_static)
			$output .= " <link rel=\"stylesheet\" type=\"text/css\" href=\"$css_static\">";
		
		if ($this->streams['html-head'] != '')
			$output .= $this->streams['html-head'] . "\n";
			
		$output .= " <style>\n";
		
		if ($style)
			$output .= "  	body { $style }\n";
		
		$output .= $this->streams['css'];
		
		if ($this->streams['css-print'] != '') {
			$output .= "	@media print {\n";
			$output .= $this->streams['css-print'];
			$output .= "	}\n";
		}
		
		$output .= " </style>\n";

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
		$this->set_mandatory('class');
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
	
	var $idg_type = 'part';
	
	function __construct()
	{
		parent::__construct();
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
	
	var $idg_type = 'container';
	
	function __construct()
	{
		parent::__construct();
	}
	
	function _render(&$document, &$view)
	{
		$body = false;
		
		$style = $this->get_property('style');
		
		if ($style) {
			$idg_id = $this->idg_id;
			$style_head = $this->get_property('style-head');
			$style_subhead = $this->get_property('style-subhead');
			$style_link = $this->get_property('style-link');
			$style_link_hover = $this->get_property('style-link-hover');
			$style_image = $this->get_property('style-image');
			
			$body = "<div id=\"$idg_id\">\n";
			$view->stream_append('html-body', $body);
			
			if ($style)
				$css = "div#$idg_id { $style }\n";
			else
				$css = '';
			
			if ($style_head)
				$css .= "div#$idg_id h1 { $style_head }\n";
			if ($style_subhead)
				$css .= "div#$idg_id h2 { $style_subhead }\n";
			if ($style_link)
				$css .= "div#$idg_id a { $style_link }\n";
			if ($style_link_hover)
				$css .= "div#$idg_id a:hover { $style_link_hover }\n";
			if ($style_image)
				$css .= "div#$idg_id img { $style_image }\n";
			
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
	var $idg_type = 'renderer';
	var $datasource;
	
	function __construct()
	{
		parent::__construct();
	}
	
	/*!
	 * See class description.
	 * 
	 */
	
	function _render(&$document, &$view)
	{
		$source_name = $this->get_property('source');
		if (!($this->datasource = $document->get_datasource($source_name)))
			diag($this, "Document has no datasource named $source_name");
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
	var $idg_type = 'item';
	
	function __construct()
	{
		parent::__construct();
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
		$this->set_known('list-icon');
		$this->set_known('filter');
		$this->set_mandatory('name');
	}
}

class idg_view_html_slot extends idg_tree_node
{
	var $idg_type = 'slot';
	
	function __construct()
	{
		parent::__construct();
	}
	
	function _render(&$document, &$view)
	{
		$name = $this->get_property('name');
		$renderers = $document->get_renderers($name);
		$idg_id = $this->idg_id;
		$filter = $this->get_property('filter');
		if ($filter)
			$view->_set_filter($filter);
		
		$anchor_count = 0;
		
		foreach ($renderers as $renderer) {
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
		$list_icon = $this->get_property('list-icon');
		
		$css = '';

		if ($style)
			$css .= "div.$idg_id { $style }\n";
			
		if ($style_link)
			$css .= "div.$idg_id a { $style_link }\n";
			
		if ($style_link_hover)
			$css .= "div.$idg_id a:hover { $style_link_hover }\n";
			
		if ($list_icon) {
			$css .= "div.$idg_id ul { list-style-image: url($list_icon);  }\n";
			$css_print = "div.$idg_id ul { list-style: disc outside; }\n";
			$view->stream_append('css-print', $css_print);
		}
		
		if ($anchor_count > 0)
			$css .= "div.$idg_id-anchor { width: 0px; height: 0px; }\n";
		
		$view->stream_append('css', $css);
	}
}

class idg_view_html_param_obj extends idg_view_node_obj
{
	var $parameters = array();
	
	function __construct(&$parent)
	{
		parent::__construct($parent);
		$this->get_parameters();
	}
	
	function get_parameters()
	{
		if (!($text = $this->parent->get_text()))
			return;
		
		$lines = explode(";", $text);
		foreach ($lines as $line) {
			if ($line) {
				if (!preg_match('/([a-zA-Z][a-zA-Z0-9-]+):[\  ]+(.*)/', 
					$line, $match))
					diag($this, get_class($parent) 
					. ': Invalid parameter format (view_html): ' . $line);
				$this->parameters[$match[1]] = $match[2];
			}
		}
	}
}


?>

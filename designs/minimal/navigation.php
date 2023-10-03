<?php

class idg_view_html_renderer_minimal_navigation extends idg_view_node_obj
{
	
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}
	
	function render(&$document, &$view)
	{
		global $font_blocks;
		global $elements;
		
		$idg_id = $this->parent->idg_id;
		
		$doc_path = $document->get_path();
		
		$this->datasource->rewind();
		
		$tag =$view->properties['tag'];
		$title = $document->properties['name'];
		
		$body = '<div id="LogoImg"><img src="elements/minimal/images/logo.png" alt=""></div>' . "\n"
		    . "<div id=\"Logo\">\n" 
		    . '<div id="LogoBar"><div id="LogoText">' . "<i>$tag &ndash; <b>$title</b></i>" . "</div></div>\n"
		    . "<div id=\"LogoNav\">\n<div id=\"LogoBtn\">\n";
		
		while ($node = $this->datasource->get_token()) {
			if ($node->properties['type'] == 'folder')  {
			    while ($node = $this->datasource->get_token()) {
					    if ($node->properties['type'] == 'document') {
						    $name = $node->properties['name'];
						    $uri = $node->properties['uri'];
                			$body .= "<a href=\"$uri\">&middot; $name</a>\n";
                		}
        		}
        	}
		}
		
		$body .= "</div>\n" 
		    . "<div id=\"LogoNavTxt\">&copy; 2023 <a href=\"mailto:daniel@schlachta.info\">Daniel Schlachta</a></div>\n</div>\n</div>\n<div id=\"TextBody\">\n";
				
		$view->stream_append('html-body', $body);
	}
}

?>

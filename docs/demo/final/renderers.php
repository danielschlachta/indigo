<?php

class idg_view_html_renderer_img_scatter extends idg_view_node_obj
{
    
    function __construct(&$parent)
	{
		parent::__construct($parent);
    }
    
    function render(&$document, &$view)
    {
        
        $style = $this->parent->get_property('style');
        $idg_id = $this->parent->idg_id;
        
        $css = "div#$idg_id { position: absolute; top: 300px; left: 300px; " 
            . " font-size: 90%; $style }\n" 
            . " div.picture { position: absolute; }\n";
        
        $view->stream_append('css', $css);
        
        $body = "<div id=\"$idg_id\">\n";
        
        $this->datasource->rewind();
        
        $divs = '';
        
        while ($node = $this->datasource->get_token()) {
            $img = $node->data;
            
            $x = rand(-100, 150);
            $y = rand(-100, 150);
            
            $body .= "<div class=picture style=\"top: ${y}px; left: ${x}px;\">\n";
            $body .= "<img src=$img alt=\"\">\n";
            
            $divs .= "</div>\n";
        }
        
        $body .= $divs . "</div>\n";
        
        $view->stream_append('html-body', $body);
    }
}

class idg_view_html_renderer_img_strip extends idg_view_node_obj
{
    
    function __construct(&$parent)
	{
		parent::__construct($parent);
    }
    
    function render(&$document, &$view)
    {
        
        $style = $this->parent->get_property('style');
        $idg_id = $this->parent->idg_id;
        
        $css = "div#$idg_id { padding-top: 5px; padding-left: 2px; " 
            . " font-size: 90%; $style }\n" 
            . " div.picture { text-align: left; float: right; }\n";
        
        $view->stream_append('css', $css);
        
        $body = "<div id=\"$idg_id\">\n";
        
        $this->datasource->rewind(); // not necessary here, do it on principle
        
        while ($node = $this->datasource->get_token()) {
            $img = $node->data;
            
            $body .= "<div class=picture>\n";
            $body .= "<img src=$img alt=\"\">\n</div>\n";
        }
        
        $body .= "</div>\n";
        
        $view->stream_append('html-body', $body);
    }
}

?> 

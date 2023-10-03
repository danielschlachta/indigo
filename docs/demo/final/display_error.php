<?php

class idg_view_html_renderer_errormsg 
    extends idg_view_html_renderer_phpscript_obj
{    
    function __construct(&$parent)
    {
        parent::__construct($parent, $this->variables);
    }
    
    function render(&$document, &$view)
    {
        $display = $_GET['display'];
        
        $body = "The page you requested ($display)" 
            . " was not found on this server.";
        
        $view->stream_append('html-body', $body);
    }
}

?>

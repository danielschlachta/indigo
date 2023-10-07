 <?php

/*!
 * The navigation for the fancy design.
 * 
 */
 
class idg_view_html_renderer_fancy_navigation extends idg_view_node_obj
{
    
    function __construct(&$parent)
	{
		parent::__construct($parent);
    }
    
    function render(&$document, &$view)
    {
		global $nav_font;
		global $nav_bg_color;
		
        $top = true;
        
        $css =  "	#navigation {\n" 
			. "		position: fixed;\n"
			. "		border-radius: 1em;\n";
			
		if ($top) {
			$css .=   "		top: 0; left: 0;\n"
					. "		height: 4em; width: 90%;\n"			  		
					. "		margin: -1.1em 0 0 2%;\n"
					. "		padding: 1.9em 4% 0 1em;\n";
		} else {
			$css .=   "		bottom: 0; left: 0;\n"
					. "		height: 2.8em; width: 90%;\n"			  		
					. "		margin: 0 0 -0.8em 2%;\n"
					. "		padding: 0.8em 4% 0 1em;\n";
		}
		
		$css .= 
			  " 		background: $nav_bg_color;\n"
			. "	}\n\n"
			. "	.navlink, .navlink-selected {\n"
			. "		color: black;\n"
			. "		font-family: '$nav_font';\n" 
			. "		padding: 0 1em 0 1em;\n"
			. "		text-decoration: none;\n"
			// . "		font-weight: normal;\n"
        	. "	}\n\n"
        	. "	.navlink:hover {\n"
        	. " 		text-decoration: underline;\n"
        	. "	}\n\n"
			. "	.navlink-selected {\n"
			. "		font-weight: bold;\n"
        	. "	}\n\n";        	
        
        $view->stream_append('css', $css);
        
       	$body = "	<nav id=\"navigation\">\n";
		       
        $this->datasource->rewind();
        
        $document_path = $document->get_path();
        
        while ($node = $this->datasource->get_token()) {
            $path =& $node->properties['path'];
            
            $class = 'navlink';
            
            if ($path) {
				if ($path == $document_path)
					$class = 'navlink-selected';

                $name =& $node->properties['name'];
               
                $body .= "		<a class=\"$class\" href=\"?display=$path\"><span>$name</span></a>\n";
            }
        } 
        
        $body .= "	</nav>\n";
        
        $view->stream_append('html-body', $body); 
    }
}

?>

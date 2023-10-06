<?php

$geshi_main = $idg_path . '/modules/geshi-1.0/src/geshi.php';

if (file_exists($geshi_main)) {
	require_once($geshi_main);
} else {
	complain_module('geshi-1.0', $geshi_main, 
		'https://github.com/GeSHi/geshi-1.0.git');
	exit;
}

class idg_datasource_sourcefile extends idg_datasource
{
	
	function __construct(&$parameters)
	{
        global $idg_max_filesize;
	
		parent::__construct($parameters);
	
		$tok = new idg_token($parameters);
		$this->tokens[] = $tok;
	
	    $file = @$this->parameters['filename'];
		
		if (!$file)
			diag($this, get_class($this) 
			    . ': mandatory parameter(filename) not set');
			
		if (@stat($file) === false)
			diag($this, get_class($this) 
			    . ': text file "' . $file . '" not found');
		
		$max_size = @$this->parameters['max_size'];
		if (!$max_size || $max_size < 0)
			$max_size = $idg_max_filesize;
		
		if (@!$fp = fopen($file, 'r'))
			diag($this, get_class($this) 
			    . ': could not open text file "' . $file . '"');
		
		$tok = fread($fp, $max_size);
		$content = new idg_token($tok);
		fclose($fp);
		$this->tokens[] = $content;
		
		$mtime = filemtime($file);
		$filetime = new idg_token($mtime);
		$this->tokens[] = $filetime;$file = @$this->parameters['filename'];
		
		if (!$file)
			diag($this, get_class($this) 
			    . ': mandatory parameter(filename) not set');
			
		if (@stat($file) === false)
			diag($this, get_class($this) 
			    . ': source file "' . $file . '" not found');
		
		$max_size = @$this->parameters['max_size'];
		if (!$max_size || $max_size < 0)
			$max_size = $idg_max_filesize;
		
		if (@!$fp = fopen($file, 'r'))
			diag($this, get_class($this) 
			    . ': could not open source file "' . $file . '"');
		
		$tok = fread($fp, $max_size);
		$content = new idg_token($tok);
		fclose($fp);
		$this->tokens[] = $content;
	}
}

class idg_view_html_renderer_sourcefile extends idg_view_node_obj
{
	function __construct(&$parent)
	{
		parent::__construct($parent);
	}
		
	function render(&$document, &$view)
	{
    	$this->datasource->rewind();     	
		$token = $this->datasource->get_token();
		$parameters = $token->data;
	
    	
        $language = $parameters['language'];
        @$bgcolor = $parameters['bgcolor'];
        @$height = $parameters['height'];

		if (!$bgcolor)
            $bgcolor = '#e8e8e8';

		if (!$height)
    	    $height = '100%';
                
        $text = $this->datasource->get_token()->data;

		$geshi = new GeSHi($text, $language);
		$geshi->enable_keyword_links(false);
        $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
        $geshi->set_line_style("background: $bgcolor;", "background: #f0f0f0;");
        
		$content = "<div style=\"";
		
    	$content .= "background: $bgcolor; ";	
		$content .= "padding-top: 1px; padding-left: 5px; \">\n";
		
		$content .= $geshi->parse_code();
		
		$content .= "\n</div>";
		
		$view->stream_append('html-body', $content);
		
		$css = "pre.$language { width: 100%; ";

    	
    	$css .= "height: $height;";  	
    	$css .= "overflow: auto; }\n";
    	
		$view->stream_append('css', $css);
		
	}	
}


?>

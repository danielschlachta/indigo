 <?php

class idg_view_html_renderer_fancy_navigation
	extends idg_treenode {

    function __construct(&$parent)
	{
		parent::__construct($parent);
    }

    function render(&$document, &$view)
    {
		if (!$this->datasource)
			return;

		$css = "	body {\n"
			. "		margin-top: 2em;\n"
			. "		font-size: 110%;\n"
			. "	}\n\n"
			. "	nav {\n"
			. "		position: fixed;\n"
			. "		border-radius: 1em;\n"
			. "		top: 0; left: 0;\n"
			. "		height: 4em; width: 95%;\n"
			. "		margin: -1.1em 0 0 2%;\n"
			. "		padding: 1.9em 4% 0 1em;\n"
			. "	}\n\n"
			. "	.navlink, .navlink-selected {\n"
			. "		color: black;\n"
			. "		padding: 0 1em 0 1em;\n"
			. "		text-decoration: none;\n"
        	. "	}\n\n"
        	. "	.navlink:hover {\n"
        	. " 		text-decoration: underline;\n"
        	. "	}\n\n"
			. "	.navlink-selected {\n"
			. "		font-weight: bold;\n"
        	. "	}\n\n";

        $view->stream_append('css', $css);

       	$body = '';

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

        $view->stream_append('html-body', $body);
    }
}

?>

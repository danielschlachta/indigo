<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Design\Fancy;

class fancy_header extends \idg_fragment_implementation {

    function _render(&$document, &$view)
	{
		$image = '../docs/views/fancy/caption.png';
        $idg_id = $this->get_idg_id();

		$css = "	#caption {\n"
			. "		margin: 0; padding: 0.1em;\n"
			. "	}\n\n";

			if (@$image)
			$css .= "	#$idg_id {\n"
				. "		background-image: url($image);\n"
				. "		background-repeat: no-repeat;\n"
				. "		background-position: right center;\n"
				.  "	}\n\n";

		$view->stream_append('css', $css);

		$caption = $this->get_property('tag');
		$view->stream_append('html-body', "<div id=\"$idg_id\">"
			. "<h1 id=\"caption\">foobar</h1></div>");
	}
}

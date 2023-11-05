<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Design\Fancy;

class fancy_footer extends \idg_fragment_implementation {
	
	function _render(&$document, &$view)
	{
		$image = '../docs/views/fancy/postmark.png';

		$css = "	footer {\n"
            . "		margin: -1em 0 -1em 10px;\n";
		
        if ($image) {
			$css .= "		background-image: url($image);"
				. "		background-position: left center;\n"
				. "		background-repeat: no-repeat;\n"
				. "		text-align: right;\n";
		} else
			$css .= "		padding-left: 1em;\n";

		$css .= "	}\n\n";

		$view->stream_append('css', $css);

		$tag = $this->get_property('tag');

		if ($tag)
			$view->stream_append('html-body', $tag);
	}
}

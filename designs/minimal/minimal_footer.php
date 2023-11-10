<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Minimal */

namespace Indigo\Design\Minimal;

class footer extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view) {
		$view->stream_append('html-body', "<div id=\"footer\"><a href=\"#top\">" 
			. "&middot; back to top</a></div>\n</div>\n");
	}
}

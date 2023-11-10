<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Fancy */

namespace Indigo\Design\Fancy;

class footer extends \idg_fragment_implementation {
	
	function _render(&$document, &$view)
	{
        /** 
         * @todo Make this settable 
         */
		$image = '../docs/views/fancy/postmark.png';

		$css = "footer { height: 4em; padding: 1.5em; ";
		
        if ($image) {
			$css .= "background-image: url($image); "
                . "background-position: left center; "
				. "background-repeat: no-repeat; "
				. "text-align: right; ";
		} 
        
		$css .= "}\n";

		$view->stream_append('css', $css);
        
        $view->render_css($this->get_declaration(), "footer");

		$text = $this->get_declaration()->get_text();

		if ($text)
			$view->stream_append('html-body', $text);
	}
}

<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Fragment;

class image extends \idg_fragment_object
{
	function _render(idg_document $document, idg_view $view)
	{
		if (!$image = $this->get_attribute('image'))
			return;

		if (!$img_src = $image->get_parameter('src'))
			return;

		$idg_id = $this->get_idg_id();
		$style = $this->get_property('style');

		$width = $image->get_parameter('width');
		$width = $width ? "		width: $width;\n" : "";

		$height = $image->get_parameter('height');
		$height = $height ? "		height: $height;\n" : "";

		$alt_text = $image->get_parameter('alt');
		$alt_text = $alt_text ? " alt=\"$alt_text\"" : "";

		$body = "<img id=\"$idg_id\" src=\"$img_src\" "
			. "width=\"$width\" height=\"$height\"$alt_text>\n";
		$view->stream_append('html-body', $body);

		$css = "	img#$idg_id {\n		border: 0;\n";

		if ($style)
			$css .= "		$style\n";

		$css .= "	}\n\n";

		$view->stream_append('css', $css);
	}
}


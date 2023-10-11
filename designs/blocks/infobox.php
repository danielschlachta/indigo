<?php

function blocks_filter_infobox(&$text)
{
	global $elements;

	$output = '';

	$ext_image = "<img src=\"$elements/infobox/link_ext.png\" width=\"11\""
		. ' height="10" style="margin-left: -11px;" alt="">';

	$image = "<img src=\"$elements/infobox/link.png\" width=\"11\""
		. ' height="10" style="margin-left: -11px;" alt="">';

    $style = 'style="margin-left: 12px; font-weight: bold;"';
	if (preg_match('/(<a[^hH]+href=["\']([^"]+)["\'][^>]*)>/', $text, $match)) {
		$output = preg_replace('/(<a[^hH]+href=["\']http[^>]*)>/', "\\1 $style>$ext_image", $text);
		$output = preg_replace('/(<a[^hH]+href=["\'][^h][^t][^t][^p][^>]*)>/', "\\1 $style>$image", $output);
	} else
		$output = false;


	return $output;
}


class idg_datasource_blocks_infobox extends idg_datasource
{

	function __construct(&$parameters)
	{
		parent::__construct($parameters);
		$tok = new idg_token($parameters);
		$this->tokens[] = $tok;
	}
}

class idg_view_html_renderer_blocks_infobox extends idg_view_node_obj
{
	var $text;

	var $is_item = false;
	var $last_name = '';
	var $chardata = array();
	var $itemcnt = 0;
	var $max_items = 0;

	function __construct(&$parent)
	{
		parent::__construct($parent);
	}

	function render(&$document, &$view)
	{
		global $font_blocks;
		global $elements;

		$view->set_filter('blocks_filter_infobox');

		$idg_id = $this->get_idg_id();
		//$this->datasource->rewind();
		//$token = $this->datasource->get_token();
		//$parameters = $token->get_data();

		$caption = @$parameters['caption'];
		if (@$parameters['file']) {
			$file = $document->get_path() . '/' . $parameters['file'];
			if (@!$fp = fopen($file, 'r'))
				die(get_class($this) . ': file not found: ' . $file);
			$this->text = fread($fp, 10000);
			fclose($fp);
		} else if (@$parameters['text'])
			$this->text = @$parameters['text'];
		else if (@$parameters['rss']) {
			$file = $parameters['rss'];
			$this->max_items = $parameters['maxitems'];

			if (@!$fp = fopen($file, 'rb')) {
				$view->_unset_filter('blocks_filter_infobox');
				return;
			}
			$rss = stream_get_contents($fp);
			fclose($fp);

			$more_link = @$parameters['rss-extlink'];
			$more_text = @$parameters['rss-exttext'];

			if (!$this->_parse_rss($rss, $more_link, $more_text))
				return;
		} else if (@$parameters['image']) {
			$image = $document->get_path()
				. '/images/' . $parameters['image'];
			$width = $parameters['width'];
			$height = $parameters['height'];
		} else if (@$parameters['links']) {
			$links = $parameters['links'];
			if (@$fc = file_get_contents($links)) {
				$this->text = "<ol>\n";

				preg_match_all('|(<a[^hH]+href=["\']http[^>]*)>.*</a>|', $fc, $out, PREG_PATTERN_ORDER);

				$arr = $out[0];

				foreach($arr as &$entry) {
					$this->text .= "<li>$entry</li>\n";
				}

				$this->text .= "\n</ol>\n";
			} else {
				$this->text = "File not found: $links";
			}
		} else {
			$view->unset_filter('blocks_filter_infobox');
			return;
		}

		$body = "<div id=\"$idg_id-box\">\n"
			. "<div id=\"$idg_id-top\"></div>\n"
			. "<div id=\"$idg_id-body\">\n"
			. "<div id=\"$idg_id-content\">\n";

		if ($caption)
			$body .= "<img src=\"$elements/infobox/info.png\""
				. " id=\"$idg_id-info\""
				. " width=\"14\" height=\"14\" alt=\"\">\n"
				. "<div style=\"font-weight: bold; "
				. "padding-top: 2px; margin-bottom: 10px\">$caption"
				. "</div>\n$this->text\n";
		else if (@$image) {
			$body .= "<img src=\"$image\" id=\"$idg_id-image\""
				. " width=\"$width\" height=\"$height\" alt=\"\">\n";
			$height -= 5;
		}

		$body .= "</div>\n</div>\n</div>\n";

		$fg_col = '#42262c';
		$bg_col = '#9d6575';
		$bo_col = '#4d2535';
		$co_col = '#ae6575';
		$ch_col = '#d496ab';

		$css = "div#$idg_id-box { position: fixed;"
			. " right: 30px; width: 248px; bottom: 0px; font-size: 63%;"
			. " $font_blocks color: $fg_col; z-index: 205; }\n"
			. "div#$idg_id-top { width: 250px; height: 16px;"
			. " background: url($elements/infobox/box_top.png)"
			. " no-repeat; background-position: bottom left; }\n"
			. "div#$idg_id-body { width: 100%; background: $bg_col;"
			. " border: 1px solid $bo_col; border-width: 0px 1px 0px 1px; }\n";

		if (@$image) {
			$css .= "div#$idg_id-content { height: ${height}px; width: 228px;"
				. " background: $co_col; margin-left: 10px; padding: 0;"
				. " text-align: right; }\n"
				. "img#$idg_id-image { position: relative;"
				. " top: -5px; left: 0px; }\n";
		} else
			$css .= "div#$idg_id-content { width: 218px;"
				. " background: $co_col url($elements/infobox/arms.png)"
				. " no-repeat;"
				. " background-position: top right; margin-left: 10px;"
				. " padding: 0px 5px 10px 5px; }\n";

		$css .= "img#$idg_id-info { float: left; margin-right: 6px; }\n"
			. "div#$idg_id-content h2 { font-size: 100%; font-weight: bold;"
			. " padding: 0 0 0.7em 0; margin: 0; }\n"
			. "div#$idg_id-content p { margin: 0 0 0.5em 0; }\n"
			. "div#$idg_id-content img { border: 0; }\n"
			. "div#$idg_id-content a { border: 0; text-decoration: none;"
			. " color: $fg_col; }\n"
			. "div#$idg_id-content a:hover { background: $ch_col;"
			. " opacity:0.5; }\n"
			. "div#$idg_id-content ul { margin: 0 0 -5px 0; padding: 0;"
			. " list-style: none; }\n"
			. "div#$idg_id-content ol { margin: 0 0 -5px 5px; padding: 0; }\n"
			. "div#$idg_id-content li { margin-left: 12px;"
			. " padding-bottom: 0.3em; }\n";

		$css_ie = "div#$idg_id-box { position: absolute;"
			. " right: 46px; width: 250px;  }\n"
			. "div#$idg_id-content { width: 228px; }\n";

		$css_print = "div#$idg_id-box { display: none }\n";

		$view->stream_append('html-body', $body);
		$view->stream_append('css', $css);
		$view->stream_append('css-print', $css_print);

		$view->_unset_filter('blocks_filter_infobox');
	}

	function _parse_rss($text, $more_link, $more_text = false)
	{
		$xml_version = '1.0';
		$encoding = 'iso-8859-1';

		$this->itemcnt = 0;

		$parser = xml_parser_create($encoding);
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, "_xml_read_start", "_xml_read_end");
		xml_set_character_data_handler($parser, "_xml_character_data");
		xml_set_default_handler($parser, "_xml_default_handler");
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);

		$this->text = "<ul>\n";

		if (!xml_parse($parser, $text)) {
			return false;
		}

		if ($more_link && $more_text)
			$this->text .= "<li><a href=\"$more_link\"><i>"
				. "$more_text</i></a></li>\n";
		$this->text .= "</ul>\n";

		xml_parser_free($parser);
		return true;
	}

	function _xml_read_start($parser, $name, $properties)
	{
		if ($name == 'item') {
			$this->is_item = true;
		} else if ($this->is_item) {
			$this->last_name = $name;
		}
	}

	function _xml_read_end($parser, $name)
	{
		if ($name == 'item') {
			$this->is_item = false;
			if ($this->itemcnt++ < $this->max_items &&
				isset($this->chardata['pubDate']))
			{
				$date = $this->chardata['pubDate'];
				$title = $this->chardata['title'];
				$link = @$this->chardata['link'];
				$description = @$this->chardata['description'];

				if ($link)
					$this->text .= "<li>$date<br><a href=\"$link\">$title</a></li>\n";
				else if ($this->max_items > 1)
					$this->text .= "<li>$title<br><strong>$description</strong></li>\n";
				else
					$this->text .= "<li style=\"font-size: 150%;\">&ldquo;$description&rdquo;</li>\n";
			}
		}
	}

	function _xml_character_data($parser, $character_data)
	{
		if ($this->is_item)
			if (($data = trim($character_data)) != '') {
				$this->chardata[$this->last_name] = trim($character_data);
			}
	}

	function _xml_default_handler($parser, $data)
	{
	}

}


?>

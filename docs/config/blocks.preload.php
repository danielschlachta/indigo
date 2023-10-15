<?php

function blocks_filter_linkimg(&$text) {
    $image = '<img src="elements/blocks/link.png" '
        . 'width="11" height="10" alt="">';
    $ext_image = '<img src="elements/blocks/link_ext.png" '
        . 'width="11" height="10" alt="">';

    if (preg_match('/(<a[^hH]+href=["\']([^"]+)["\'][^>]*)>/', $text, $match)) {
        $output = preg_replace('/(<a[^hH]+href=["\']http[^>]*)>/', "\\1>$ext_image", 
            $text);
        $output = preg_replace('/(<a[^hH]+href=["\'][^h][^t][^t][^p][^>]*)>/', 
            "\\1>$image", $output);
        $output = preg_replace('/(<a[^hH]+href=["\']#[^>]*)>/', "\\1>$image", 
            $output);
    } else
        $output = false;

    return $output;
}

function blocks_filter_blockquote(&$text) {
    $output = str_replace('<blockquote>',
        '<div style="background: url(elements/blocks/bg_div.png); '
        . 'width: 100%; height: 100%; border: solid black; border-width: 1px;">'
        . '<blockquote style="margin-left: 1em;">', $text);

    $output = str_replace('</blockquote>', '</blockquote>'
        . '<img src="elements/blocks/lightbulb.png" alt="" style="float: right; '
        . 'position: relative; bottom: 40px; right: 2px;"></div>', $output);

    return $output;
}

class idg_view_html_item_searchbox extends idg_tree_node_instance {

    function __construct(&$parent) {
        parent::__construct($parent);
    }

    function render(&$document, &$view) {
        global $font_blocks;
        global $elements;

        $idg_id = $this->get_idg_id();

        $body = "<div id=\"$idg_id\">\n<form onsubmit=\"append();\" " 
            . "action=\"https://google.com/search\">\n"
            . "<div id=\"$idg_id-caption\">Search</div>\n"
            . "<div><input type=\"text\" name=\"q\" id=\"querytext\">"
            . "</div>\n</form>\n</div>\n";

        $css = "div#$idg_id { position: fixed;"
            . " bottom: 60px; left: 30px; $font_blocks z-index: 210; }\n"
            . "div#$idg_id form { margin: 0 0 10px 0; padding: 0; }\n"
            . "div#$idg_id input { width: 82px; font-size: 80%;"
            . " font-family: verdana,sans-serif; color: #bbb;"
            . " background: transparent; border: solid #9095c6;"
            . " border-width: 1px 0 1px 1px; margin: 0; padding: 0; }\n"
            . "div#$idg_id-caption { color: #aaa;"
            . " background: url($elements/searchbox/magnifier.png)"
            . " no-repeat; background-position: center left;"
            . " padding-left: 15px; font-size: 80%; }\n";

        $css_ie = "div#$idg_id { position: absolute; }\n"
            . "div#$idg_id input { width: 80px; }\n";

        $css_print = "div#$idg_id { display: none }\n";

        $js = "function append() { "
            . "document.getElementById(\"querytext\").value += "
            . "\" site:danielschlachta.github.io\"; }\n";

        $view->stream_append('css', $css);
        $view->stream_append('css-print', $css_print);
        $view->stream_append('html-body', $body);
        $view->stream_append('js', $js);
    }
}

class idg_view_html_item_toolbox extends idg_tree_node_instance {

    function __construct(&$parent) {
        parent::__construct($parent);
    }

    private function qr_link(&$document) {
        $link = urlencode($document->get_site()->get_site_url());

        return "http://chart.googleapis.com/chart?chs=300x300&cht=qr&amp;" 
            . "chl=$link&amp;choe=UTF-8";
    }

    function render(&$document, &$view) {
        global $font_blocks;
        global $elements;

        $idg_id = $this->get_idg_id();

        $body = "<div id=\"$idg_id\">\n<div id=\"$idg_id-body\">\n"
            . "<ul>\n"
            . " <li><a href=\"mailto:Daniel Schlachta <daniel@schlachta.info>\">" 
            . "Contact</a></li>\n"
            . " <li><a href=\"javascript:void(0);\" ."
            . " onClick=\"document.getElementById('framedimage').src='"
            . $this->qr_link($document) . "'\">QR Code</a></li>\n"
            . "</ul>\n</div>\n</div>\n";

        $bo_col = '#9095c6';
        $fg_col = '#23314f';
        $bg_col = '#6b6bb2';
        $li_col = '#aaa';

        $css = "div#$idg_id { position: fixed;"
            . " $font_blocks bottom: 13px; left: 30px;"
            . " z-index: 210; }\n"
            . "div#$idg_id-body { border: solid $bo_col;"
            . " border-width: 1px 1px 1px 0px; padding: 5px 0 5px 2px;"
            . " font-size: 90%; width: 80px; }\n"
            . "div#$idg_id-body ul { margin: 0 0 0 -2px; padding: 0;"
            . " list-style: none; font-size: 80%; }\n"
            . "div#$idg_id-body li { padding: 0; margin: 0; }\n"
            . "div#$idg_id-body a { padding: 1px 0 1px 15px;"
            . " display: block; width: 67px; height: 100%; color: $li_col;"
            . " text-decoration: none;"
            . " background: url($elements/toolbox/link.png)"
            . " no-repeat; background-position: left center; }\n"
            . "div#$idg_id-body a:hover { color: $fg_col; background: $bg_col"
            . " url($elements/toolbox/link.png) no-repeat; "
            . "background-position: left center; }\n";

        $css_ie = "div#$idg_id { position: absolute; }\n"
            . "div#$idg_id-body a { width: 80px }\n";

        $css_print = "div#$idg_id { display: none }\n";

        $view->stream_append('css', $css);
        $view->stream_append('css-print', $css_print);
        $view->stream_append('html-body', $body);
    }
}

?>

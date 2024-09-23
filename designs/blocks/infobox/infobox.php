<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Blocks */

namespace Indigo\Design\Blocks;

/**
 * Decorates links with icons, used by infobox.
 */
function infobox_filter_links(\idg_view $view, string $text): string {
    $output = $text;
    $elements = $view->template()->get_uri('infobox/elements');

    $ext_image = "<img src=\"$elements/link_ext.png\" width=\"11\""
        . ' height="10" style="margin-left: -11px;" alt="">';

    $image = "<img src=\"$elements/link.png\" width=\"11\""
        . ' height="10" style="margin-left: -11px;" alt="">';

    $style = 'style="margin-left: 12px; font-weight: bold;"';

    if (preg_match('/(<a[^hH]+href=["\']([^"]+)["\'][^>]*)>/', $text, $match)) {
        $output = preg_replace(
            '/(<a[^hH]+href=["\']http[^>]*)>/',
            "\\1 $style>$ext_image", $text);
        $output = preg_replace(
            '/(<a[^hH]+href=["\'][^h][^t][^t][^p][^>]*)>/', "\\1 $style>$image",
            $output);
    }

    return $output;
}

/**
 * A box that displays various types of information gleaned from one of the
 * following data sources: rss.
 */
class infobox extends \idg_fragment_implementation {

    // for render_css()
    private string $text;
    private bool $is_item = false;
    private string $last_name = '';
    private array $chardata = [];
    private int $itemcnt = 0;
    private int $max_items = 0;

    private function _render_text(\idg_datasource_implementation $datasource): string {
        return "<ul><li>\n" . $datasource->current() . "</li></ul>\n";
    }

    private function _render_textfile(\idg_datasource_implementation $datasource): 
        string {
        return '<div style="margin-left: 10px;">' . $datasource->current() . '</div>';
    }
    
    private function _render_rss(\idg_datasource_implementation $datasource): string {
        $retval = "<ul>\n";

        foreach ($datasource as $key => $value) {
            $link = @$value['link'];
            $title = @$value['title'];
            $description = @$value['description'];
            $date = @$value['pubDate'];

            if ($link)
                $retval .= "<li>$date<br><a href=\"$link\">$title</a></li>\n";
            else if ($datasource->get_parameter('max-items') == 1)
                $retval .= "<li>$title<br><strong>$description</strong></li>\n";
            else
                $retval .= "<li>For $date: &ldquo;"
                    . "$description&rdquo;</li>\n";
        }

        return "$retval</ul>";
    }

    private function _render_scanlinks(\idg_datasource_implementation $datasource)
    : string {
        $retval = "<ul>\n";

        foreach ($datasource as $key => $value) {
            $text = $value['text'];
            $link = $value['link'];
            $retval .= "<li><a href=\"$link\"><i>$text</i></a></li>\n";
        }

        return "$retval</ul>\n";
    }

    function _render(\idg_document $document, \idg_view $view): void {
        $elements = $view->template()->get_uri('infobox/elements');

        if (!$source = $this->get_property('source'))
            idg_diag($this, "property 'source' not set");
        
        ($datasource = $this->get_datasource($source))->rewind();
           
        $idg_id = $this->get_idg_id();

        $attr = $this->fetch_attribute('blocks::infobox', $document);
        $bg_url = $attr->get_parameter('bg-url');
        $caption = $attr->get_parameter('caption');

        $view->add_filter($view->template()->qualify('infobox_filter_links'));

        switch (($class_name = get_class($datasource))) {
            case 'Indigo\Datasource\text':
                $content = $this->_render_text($datasource);
                break;
            case 'Indigo\Datasource\textfile':
                $content = $this->_render_textfile($datasource);
                break;
            case 'Indigo\Datasource\rss':
                $content = $this->_render_rss($datasource);
                break;
            case 'Indigo\Datasource\scanlinks':
                $content = $this->_render_scanlinks($datasource);
                break;
            default:
                idg_diag($this, "incompatible datasource type '$class_name'");
        }

        $body = "<div id=\"$idg_id-box\">\n"
            . "<div id=\"$idg_id-top\"></div>\n"
            . "<div id=\"$idg_id-body\">\n"
            . "<div id=\"$idg_id-content\">\n";

        $body .= "<img src=\"$elements/info.png\""
            . " id=\"$idg_id-info\""
            . " width=\"14\" height=\"14\" alt=\"\">\n"
            . "<div id=\"$idg_id-caption\">$caption</div>\n"
            . "$content</div>\n</div>\n</div>\n";

        $fg_col = '#42262c';
        $bg_col = '#9d6575';
        $bo_col = '#4d2535';
        $co_col = '#ae6575';
        $ch_col = '#d496ab';

        $css = "div#$idg_id-box { position: fixed;"
            . " right: 30px; width: 248px; bottom: 0px; font-size: 63%;"
            . " color: $fg_col; }\n"
            . "div#$idg_id-top { width: 250px; height: 16px;"
            . " background: url($elements/box_top.png)"
            . " no-repeat; background-position: bottom left; }\n"
            . "div#$idg_id-body { width: 100%; background: $bg_col;"
            . " border: 1px solid $bo_col; border-width: 0px 1px 0px 1px; }\n"
            . "div#$idg_id-caption { font-weight: bold; margin-bottom: 8px; }\n"
            . "div#$idg_id-content { width: 218px; background: $co_col ";

        /* if (@$image) {
          $css .= "div#$idg_id-content { height: ${height}px; width: 228px;"
          . " background: $co_col; margin-left: 10px; padding: 0;"
          . " text-align: right; }\n"
          . "img#$idg_id-image { position: relative;"
          . " top: -5px; left: 0px; }\n";
          } else */

        if ($bg_url)
            $css .= " url($bg_url) no-repeat; "
                . " background-position: top right; ";
        else
            $css .= "; ";

        $css .= "margin-left: 10px; padding: 5px 5px 5px 0; font-size: 120%; }"
            . "img#$idg_id-info { float: left; margin: 0 6px 0 6px; }\n"
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
            . "div#$idg_id-content li { margin-left: 12px; padding-bottom: 0.3em; }\n";

        $css_print = "div#$idg_id-box { display: none }\n";

        $view->stream_append('html-body', $body);
        $view->stream_append('css', $css);
        $view->stream_append('css-print', $css_print);

        $view->render_css($this->get_declaration(), "div#$idg_id-content");

        $view->remove_filter($view->template()->qualify('infobox_filter_links'));
    }
}

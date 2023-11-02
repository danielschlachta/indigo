<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Design\Blocks;

function filter_links(\idg_view $view, string $text): string {
    $output = $text;
    $elements = $view->get_elements('infobox');

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
 * Attribute: infobox
 * Options:
 *   bg_url: the url() part of the background div
 *   caption: the caption for the box
 */
class infobox extends \idg_fragment_implementation {

    var $text;
    var $is_item = false;
    var $last_name = '';
    var $chardata = array();
    var $itemcnt = 0;
    var $max_items = 0;

    function _render(\idg_document $document, \idg_view $view): void {
        $elements = $view->get_elements('infobox');

        if (!$datasource = $this->get_datasource())
            idg_diag($this, "datasource not found");

        $idg_id = $this->get_idg_id();

        $attr = $this->fetch_attribute('blocks::infobox', $document);
        $bg_url = $attr->get_parameter('bg-url');
        $caption = $attr->get_parameter('caption');

        $view->add_filter($view->qualify('filter_links'));

        $text = "<ul>\n";

        foreach ($datasource as $key => $value) {
            $link = @$value['link'];
            $title = @$value['title'];
            $description = @$value['description'];
            $date = @$value['pubDate'];

            if ($link)
                $text .= "<li>$date<br><a href=\"$link\">$title</a></li>\n";
            else if ($datasource->get_parameter('max-items') == 1)
                $text .= "<li>$title<br><strong>$description</strong></li>\n";
            else
                $text .= "<li>For $date: &ldquo;"
                    . "$description&rdquo;</li>\n";
        }

        $text .= "</ul>\n";

        /*
          if (($file = @$parameters['file'])) {
          if (@!$fp = fopen($file, 'r'))
          die(get_class($this) . "file not found: '$file'");
          $this->text = fread($fp, 10000);
          fclose($fp);
          } else if (@$parameters['text'])
          $this->text = @$parameters['text'];
          else if (@$parameters['rss']) {
          } else if (@$parameters['image']) {
          $image = $document->get_path()
          . '/images/' . $parameters['image'];
          $width = $parameters['width'];
          $height = $parameters['height'];
          } else if (@$parameters['links']) {
          $links = $parameters['links'];
          if (@$fc = file_get_contents($links)) {
          $this->text = "<ol>\n";

          preg_match_all('|(<a[^hH]+href=["\']http[^>]*)>.*</a>|', $fc, $out,
          PREG_PATTERN_ORDER);

          $arr = $out[0];

          foreach ($arr as &$entry) {
          $this->text .= "<li>$entry</li>\n";
          }

          $this->text .= "\n</ol>\n";
          } else {
          $this->text = "File not found: $links";
          }
          } else {
          $view->enable_filter('blocks_filter_infobox', false);
          return;
          } */

        $body = "<div id=\"$idg_id-box\">\n"
            . "<div id=\"$idg_id-top\"></div>\n"
            . "<div id=\"$idg_id-body\">\n"
            . "<div id=\"$idg_id-content\">\n";

        $body .= "<img src=\"$elements/info.png\""
            . " id=\"$idg_id-info\""
            . " width=\"14\" height=\"14\" alt=\"\">\n"
            . "<div id=\"$idg_id-caption\">$caption</div>\n" 
            . "$text\n</div>\n</div>\n</div>\n";

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

        $view->remove_filter($view->qualify('filter_links'));
    }
}

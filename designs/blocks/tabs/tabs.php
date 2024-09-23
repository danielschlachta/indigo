<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** @package Blocks */

namespace Indigo\Design\Blocks;

class tabs extends \idg_fragment_implementation {

    function _render(\idg_document $document, \idg_view $view): void {
        $elements = $view->template()->get_uri('tabs/elements');
        
        if (!$source_name = $this->get_property('source'))
            return;
        $datasource = $document->get_datasource($source_name);
        $idg_id = $this->get_idg_id();
        
        $document_path = $document->get_path();
        $tag = $this->get_property('tag');
        $has_current = false;
        $in_folder = false;
        $list = '';
        
        foreach ($datasource as $count => $token) {
            $type = $token['type'];
            $name = @$token['name'];
            
            $path = @$token['path'];
            
            if ($path && (!$tag || $tag == @$token['parent-folder-id'])) {              
                $uri = $view->template()->get_full_uri($path);
                
                if ($path == $document_path) {
                    $list .= "<li id=\"current\">"
                        . "<div>$name</div></li>\n";
                    $has_current = true;
                } else
                    $list .=  "<li><a href=\"$uri\">$name</a></li>\n";
            }
        }
        
        if ($list == '')
            return;
        
        $body = "<div id=\"$idg_id-tabbox\">\n" 
            . "<div id=\"$idg_id-tabs\">\n<ul>\n$list</ul>\n</div>\n" 
            . "</div>\n"
            . "<div id=\"$idg_id-corner\">\n<div id=\"$idg_id-top\">\n</div>\n</div>\n";

        if ($has_current)
            $bottom = '-1px';
        else
            $bottom = '0';

        $li_col = '#23314f';
        $fg_col = '#284a2f';
        $bg_col = '#b4d2b0';
        $to_col = '#1e3723';
        $tx_col = '#555';
        $style = $this->get_property('style');
        
        $css = "div#$idg_id-tabbox { position: relative;"
            . " top: 0; left: 30px; height: 130px; $style }\n"
            . "div#$idg_id-tabs { position: absolute;"
            . " left: 0; bottom: $bottom; width: 100%;"
            . " font-size: 60%; line-height:normal; }\n"
            . "div#$idg_id-tabs ul { margin: 0; padding: 10px 10px 0 10px;"
            . " list-style: none; }\n"
            . "div#$idg_id-tabs li { float: left;"
            . " background: url($elements/left.png) no-repeat"
            . " left top; padding-left: 9px; }\n"
            . "div#$idg_id-tabs a { float: left; display: block;"
            . " background: url($elements/right.png) no-repeat"
            . " right top; padding: 5px 15px 4px 6px;"
            . " text-decoration:none; color: $tx_col; }\n"
            . "div#$idg_id-tabs a:hover { color: $li_col;"
            . " background:url($elements/right_ro.png) no-repeat"
            . " right top; }\n"
            . "div#$idg_id-tabs #current {"
            . " background-image:url($elements/left_on.png); }\n"
            . "div#$idg_id-tabs #current div { color: $fg_col;"
            . " background: url($elements/right_on.png)"
            . " no-repeat right top; padding: 6px 15px 4px 6px; }\n"
            . "div#$idg_id-corner { height: 30px;"
            . " background: url($elements/top.png) no-repeat;"
            . " background-position: top right; }\n"
            . "div#$idg_id-top { padding: 0; margin-right: 20px; height: 30px;"
            . " background: $bg_col; border: solid $to_col;"
            . " border-width: 1px 0px 0px 1px; }\n";

        $css_print = "div#$idg_id-tabbox { display: none }\n"
            . "div#$idg_id-corner { display: none }\n";

        $view->stream_append('html-body', $body);
        $view->stream_append('css', $css);
        $view->stream_append('css-print', $css_print);
    }
}

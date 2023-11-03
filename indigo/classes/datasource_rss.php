<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Datasource;

/**
 * Retrieves an <code>rss</code> feed and returns the entries as tokens.
 *   @param url The URL of the feed
 *   @param max-items The maximum number of entries to retrieve, defaults to 0 (all)
 *
 * @return The datasource produces tokens of the following form:
 * 
 * Key    | Value
 * -------|------------
 * 1      | URL
 * 2      | bla
 *
 */
class rss extends \idg_datasource_implementation {

    private int $num_items = 0;
    private ?array $token = null;
    private string $name;

    function __construct(\idg_leafnode $parent) {
        parent::__construct($parent);
       
        $url = $this->get_parameter('url');

        if (!$url)
            idg_diag($this, "no 'url' parameter given");

        $max_items = $this->get_parameter('max-items');
        $max_items = $max_items ? $max_items : 1000;
        
        $reverse = $this->get_parameter('reverse') === 'yes';
       
        if (@!$fp = fopen($url, 'rb'))
            idg_diag($this, "could not retrieve '$url'");

        $parser = xml_parser_create('utf-8');
        xml_set_object($parser, $this);
        xml_set_element_handler($parser, "_xml_read_start", "_xml_read_end");
        xml_set_character_data_handler($parser, "_xml_character_data");
        xml_set_default_handler($parser, "_xml_default_handler");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);

        do {
            $rss = stream_get_contents($fp, 1024);
        } while ($rss && xml_parse($parser, $rss) && 
            ($this->num_items <= $max_items || $reverse));

        //idg_diag($this, "rss parsing failed for '$file'", E_USER_WARNING);

        fclose($fp);
        xml_parser_free($parser);
   
        if ($reverse) 
            $this->reverse();
         
        $this->truncate($max_items);
    }

    function _xml_read_start($parser, $name, $properties) {
        if ($name == 'item')
            $this->token = [];
        $this->name = $name;
    }

    function _xml_read_end($parser, $name) {
        if ($name == 'item') {
            $this->add_token($this->token);
            $this->token = null;
            $this->num_items++;
        }
    }

    private function _xml_character_data($parser, $character_data) {
        if ($this->token !== null && $data = trim($character_data) != '') 
            $this->token[$this->name] = trim($character_data);
    }

    private function _xml_default_handler($parser, $data) {
        
    }
}

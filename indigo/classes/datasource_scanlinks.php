<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Datasource;

/**
 * Scans the <code>html-body</code> stream for links and returns them as tokens.
 * @todo docu
 */
class scanlinks extends \idg_datasource_implementation {

    function __construct(\idg_datasource $parent) {
        parent::__construct($parent);
        
        $tokens = [];
        
        if (!($view_name = $this->get_parameter('view')))
            idg_diag($this, "required parameter 'view' missing");

        global $$view_name;
        
        if (!is_object($$view_name)) 
            idg_diag($this, "variable '$view_name' is not an object");
        
        if (!$$view_name instanceof \idg_view)
            idg_diag($this, "variable '$view_name' is not an idg_view");
        
        $text = $$view_name->get_stream('html-body');
        
        preg_match_all('|<a[^hH]+href=["\'](http[^>]*)["\']>.*</a>|', $text, $match); 
              
        for ($i = 0; $i < count($match[0]); $i++) {
            $token['text'] = preg_replace('|<[^>]*>|', '', $match[0][$i]);
            $token['link'] = $match[1][$i];
            $this->add_token($token);
        }
    }
}

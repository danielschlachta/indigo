<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Datasource;

/**
 * Executes a <code>php</code> function and returns the resulting array as tokens.
 * @param script (optional: a <code>php</code> script to execute before the function call
 * @param function The name of the function to call
 */
class php extends \idg_datasource_implementation {

    function __construct(\idg_datasource $parent) {
        parent::__construct($parent);
        
        if (!($function = $this->get_parameter('function')))
            idg_diag($this, "required parameter 'function' missing");

        if (($load = $this->get_parameter('load')))
            require_once $load;

        $this->tokens = $function($parent);
    }
}

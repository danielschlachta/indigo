<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Datasource;

/**
 * Simply quotes its text (set with <code>set_text</code> or per 
 * <code>xml</code> chacter data, respectively).
 * @return The datasource produces the following tokens:
 * 
 * Number | Description
 * -------|------------
 * 1      | The text
 */
class text extends \idg_datasource_implementation {

    function __construct(\idg_datasource $parent) {
        $this->add_token($parent->get_text());
    }
}

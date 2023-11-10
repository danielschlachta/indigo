<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/**
 * Type information for idg_folder.
 * Adds a <code>show-name</code> property for constructing document titles.
 * @see idg_document\get_default_title()
 */
class idg_folder_type extends idg_site_element_type {

    function __construct() {
        parent::__construct();
        $this->set_property_mandatory('name');
        
        $this->register_property('show-name'); 
    }
}

/**
 * A folder. Can contain documents and folders.
 */
class idg_folder extends idg_site_element {

    function __construct() {
        parent::__construct('folder');
    }
}


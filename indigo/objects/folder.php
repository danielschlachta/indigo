<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/**
 * Type information for idg_folder.
 */
class idg_folder_type extends idg_site_element_type {

    function __construct() {
        parent::__construct();
        $this->set_property_mandatory('name');
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


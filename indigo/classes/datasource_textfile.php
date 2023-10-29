<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Datasource;

/**
 * Returns the content of a (normally text) file.
 *   @param filename The name of the file
 *   @param max-size The maximum number of bytes read, defaults to 32KiB
 *
 * @return The datasource produces the following tokens:
 * 
 * Number | Description
 * -------|------------
 * 1      | The file content
 * 2      | The time/date of last modification as returned by filemtime
 *
 */
class textfile extends \idg_datasource_implementation {

    function __construct(idg_datasource $parent) {
        parent::__construct($parent);

        $filename = $this->get_parameter('filename');

        if (!$filename) {
            $name = $this->parent->get_property('name');
            idg_diag($this, "no 'filename' parameter given");
        }

        if (@stat($filename) === false)
            idg_diag($this, "file '$filename' not found");

        if (is_dir($filename))
            idg_diag($this, "'$filename' is a directory");

        $max_size = 0 + @$this->get_parameter('max-size');
        $max_size = $max_size <= 0 ? 32 * 1024 : $max_size;

        if (@!$fp = fopen($filename, 'r'))
            idg_diag($this, "could not open file '$filename'");

        $this->tokens[] = fread($fp, $max_size);
        fclose($fp);
        
        $this->tokens[] = filemtime($filename);
    }
}

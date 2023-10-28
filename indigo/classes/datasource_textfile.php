<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Datasource;

/**
 * Returns the content of a (normally text) file.
 *   @param mixed filename the name of the file, obviously mandatory
 *   @param mixed max_size maximal file size, default is 32KiB
 *
 * @return The datasource produces the following tokens:
 * 
 * Number | Description
 * -------|------------
 * 1      | The file content
 * 2      | The time/date of last modification as returned by filemtime
 *
 */
class textfile extends \idg_datasource_object {

    /** sdf
     * 
     * @global type $idg_max_filesize
     * @param string[] $parameters
     * 
     * @return string Description
     */
    function __construct(idg_datasource $parent) {
        parent::__construct($parent);

        $filename = $this->get_parameter('filename');

        if (!$filename) {
            $name = $this->parent->get_property('name');
            diag($this, "datasource_textfile($name): option filename not specified");
        }

        if (@stat($filename) === false)
            diag($this, "filename not found: '$filename");

        if (is_dir($filename))
            diag($this, "'$filename' is a directory");

        $max_size = 0 + @$this->get_parameter('max-size');
        $max_size = $max_size <= 0 ? 32 * 1024 : $max_size;

        if (!$max_size || $max_size < 0)
            $max_size = $idg_max_filenamesize;

        if (@!$fp = fopen($filename, 'r'))
            diag($this, get_class($this)
                . ': could not open text filename "' . $filename . '"');

        $tok = fread($fp, $max_size);
        fclose($fp);
        $this->tokens[] = $tok;
        $this->tokens[] = filemtime($filename);
    }
}

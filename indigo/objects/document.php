<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/**
 * Type information for idg_document.
 */
class idg_document_type extends idg_site_element_type {

    function __construct() {
        parent::__construct();

        $this->child_types[] = 'idg_datasource';
        $this->child_types[] = 'idg_renderer';
        $this->child_types[] = 'idg_attribute';

        $this->set_property_mandatory('name');
        $this->set_property_hook('title', '$this->get_default_title');
        $this->set_property_hook('last-change', '$this->get_last_change');
    }
}

/**
 * A document.
 */
class idg_document extends idg_site_element {

    private string $last_change = '';
    
    function __construct() {
        parent::__construct('document');
    }

    /**
     * Returns the relative URL that produces the document.
     * @return string The relative URL, which is also the fragment part
     */
    function get_rel_url(): string {
        return '?display=' . $this->get_path();
    }

    /**
     * Returns the containing folder for the document.
     * <blockquote>
     * Note: This can indeed return <code>null</code> since a document
     * can be a direct sibling of an idg_site object.
     * </blockquote>
     * @return idg_folder|null The folder object
     */
    function get_folder(): ?idg_folder {
        $folder = $this;

        while ($folder && (get_class($folder) != 'idg_folder'))
            $folder = $folder->get_parent();

        return $folder;
    }

    /**
     * Returns a datasource object for the given source name.
     * @param string $name The name of the datasource
     * @return idg_datasource The datasource
     */
    function get_datasource(string $name): idg_datasource_implementation {
        if ($name == '_site') 
            return $this->get_site()->get_datasource();

        if (!($datasource = $this->get_child_by_key('name',
            $name, 'idg_datasource')))
            idg_diag($this, "datasource '$name' not found");

        return $datasource->create_instance();
    }

    /**
     * Returns an array containing instances of all renderers for a given slot.
     * @param type $slot_name The name of the slot
     * @return array|null An array of idg_datasource_implementation
     */
    function get_renderers($slot_name): ?array {
        $renderers = [];

        if (!($children = $this->get_children_by_key('slot', $slot_name, 
            'idg_renderer')))
            return null;

        foreach ($children as $renderer) {
            $source_name = $renderer->get_property('source');
            $datasource = $this->get_datasource($source_name);
            $renderers[] = $renderer->create_instance($datasource);
        }
        
        return $renderers;
    } 

    /**
     * Returns a string representing the document and its location in the folder 
     * structure.
     * The path is constructed using the <code>id</code> property.
     * @return string The path
     */
    function get_path(): string {
        $path = $this->get_property('id');
        $tmp = $this->get_parent();

        while ($tmp && (get_class($tmp) != 'idg_site')) {
            $path = $tmp->get_property('id') . IDG_URL_FOLDER_SEPARATOR . $path;
            $tmp = $tmp->get_parent();
        }

        return $path;
    }

    /**
     * Returns a title for the document. 
     * Used as a hook in case the <code>title</code> property is not set.
     * @return string The title
     */
    function get_default_title(): string {
        $site = $this->get_site();
        $index = $site->get_document();

        $tmp = $this;
        $path = '';
        $reverse = $site->get_property('title-reverse-order') != 'no';
        $separator = $site->get_property('title-separator');

        if (!$separator)
            $separator = ' - ';

        while ($tmp) {
            if (($tmp->get_property('show-name') != 'no') && 
                (($p_title = $tmp->get_property('name')) != '')) {
                $path = ($reverse ? $path : $p_title)
                    . ($path != '' ? $separator : '')
                    . ($reverse ? $p_title : $path);
            }

            $tmp = $tmp->get_parent();
        }

        return $path;
    }

    /**
     * Sets the document's last modification time as a timestamp.
     * @param int $time The time
     */
    function set_last_change(int $time) {
        if (!$this->last_change || $this->last_change < $time)
            $this->last_change = $time;
    }

    /**
     * Returns the time of the document's last change, or the result of 
     * <code>time()</code> if none was set.
     * @return string Human readable form of the timestamp
     */
    function get_last_change(): string {
        if (!($last_change = $this->last_change))
            $last_change = time();
        return date("d.m.Y h:i", $last_change);
    }
    
    /**
     * Loads up a token for the site's built-in datasource.
     * @param array $token The token 
     */
    protected function _get_token(array &$token) {
        parent::_get_token($token);
        
        $token['path'] = $this->get_path();
        $token['url'] = $this->get_rel_url();
        $token['parent-folder-id'] = $this->get_parent()->get_property('id');
    }
}

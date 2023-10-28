<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

include_once 'tree.php';

/**
 * Type class for `idg_site_element`
 */
abstract class idg_site_element_type extends idg_treenode_type {

    function __construct() {
        parent::__construct();

        $this->child_types = ['idg_folder', 'idg_document'];

        $this->register_property('id');
        $this->set_property_mandatory('id');

        $this->register_property('name');
        $this->register_property('title');
        $this->register_property('content-language');
        $this->register_property('title-separator');
        $this->register_property('title-reverse-order');
        $this->register_property('description');
        $this->register_property('navigation-comment');
        $this->register_property('index-document');
        $this->register_property('show-name');
    }
}

/**
 * Base class for elements of a site definition. 
 */
abstract class idg_site_element extends idg_treenode {

    function __construct() {
        parent::__construct();
    }

    function get_site() {
        $site = $this;

        while ($site && (get_class($site) != 'idg_site'))
            $site = $site->get_parent();

        if (!$site)
            diag($this, 'internal error - no site definition found');

        return $site;
    }

    /**
     * Loads up a token for the site's built-in datasource.
     * @param array $token The token 
     */   
    protected function _get_sitemap_token(array &$token) {
        $token['type'] = $this->get_element_name();
        $token['name'] = $this->get_property('name');
        $token['id'] = $this->get_property('id');
    }
    
    /**
     * Produces a token meant for the site's built-in datasource.
     * @param idg_datasource $datasource An arbitrary datasource
     */
    protected function _get_sitemap(idg_datasource_object $datasource): bool {
        $token = [];
        $this->_get_sitemap_token($token);
        $datasource->add_token($token);
        
        return true;
    }
}

/** 
 * Type information of idg_site.
 */
class idg_site_type extends idg_site_element_type {

    function __construct() {
        parent::__construct();

        $this->child_types[] = 'idg_datasource';

        $this->set_property_mandatory('id', false);
    }
}

/**
 * The object that defines a whole site, i.e. the root of the folder structure.
 */
class idg_site extends idg_site_element {

    private $document;
    private $view;

    function __construct() {
        parent::__construct();

        /** @todo this has become rather redundant */
        
        $this->idg_xml_translation = [
            'site' => 'idg_site',
            'folder' => 'idg_folder',
            'document' => 'idg_document',
            'datasource' => 'idg_datasource',
            'renderer' => 'idg_renderer'
        ];

        $this->set_element_name('site');
    }

    /**
     * Returns an instance of the document with the given name, or the index document
     * if the parameter is left out.
     * The index document is defined in the site's <code>index-document</code>
     * property. If no document could be found, <code>null</code> is returned.
     * @param string $document_name The name of the document
     * @return idg_document|null The object instance
     */
    function get_document(?string $document_name = null): ?idg_document {
        $document_obj = null;
        $last_type = '';

        if (!$document_name) {
            if (@$display = $_GET['display']) {
                $document_name = $display;

                if (($pos = strpos($document_name, '#')) != false) {
                    $document_name = substr($display, $pos);
                }
            }
        }

        /** @todo wget */
        
        if (!$document_name)
            $document_name = $this->get_property('index-document');

        if ($document_name) {
            if ($document_name[0] != '/')
                $document_name = '/' . $document_name;

            $path = explode(IDG_URL_FOLDER_SEPARATOR, $document_name);
            $count = count($path);

            for ($i = 1, $tmp = $this;
                $i < $count && $tmp;
                $i++) {
                $tmp = $tmp->get_child_by_key('id', $path[$i]);
            }

            $document_obj = $tmp;
        }

        return $document_obj;
    }

    /**
     * Returns an instance of a datasource containing the folder structure.
     * The datasource tokens are arrays with key/value pairs.
     * See the individual classes' _get_token() functions for the actual content.
     * @return idg_datasource_object The datasource
     */
    function get_datasource(): idg_datasource_object {
        $datasource = new idg_datasource_object($this);

        if (!$this->traverse($datasource, '$this->_get_sitemap'))
            diag($this, 'internal error: traverse(_get_sitemap) failed');
            
        return $datasource;
    }

    function get_absolute_url($include_fragment = true) {
        $parsed_url = parse_url(isset($_SERVER['HTTPS']) && 
            $_SERVER['HTTPS'] === 'on' ? "https" : "http"
            . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

        $scheme = isset($parsed_url['scheme']) ?
            $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ?
            $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ?
            ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ?
            $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ?
            ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = (isset($parsed_url['path']) ?
            $parsed_url['path'] : '');
        $query = isset($parsed_url['query']) ?
            '?' . $parsed_url['query'] : '';

        if ($include_fragment)
            $fragment = isset($parsed_url['fragment']) ?
                '#' . $parsed_url['fragment'] : '';
        else
            $fragment = '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * Returns an <code>xml</code> representation of the sitemap.
     * @see https://www.sitemaps.org/protocol.html
     * @return string The sitemap
     */
    function get_sitemap(): string {
        $this->_sitemap = '<?xml version="1.0" encoding="UTF-8"?>'
            . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . "\n";
        $this->_url = $this->get_absolute_url();
        $this->_scan_object($this);

        return $this->_sitemap . "</urlset>\n";
    }

    /** @todo currently no way to set changefreq, should we make use of traverse? */
    private function _scan_object(&$object, $prefix = '') {
        if ($object->get_type_name() == 'site')
            foreach ($object->children as $child)
                $this->_scan_object($child, '');

        if ($object->get_type_name() == 'folder')
            foreach ($object->children as $child)
                $this->_scan_object($child,
                    $prefix . IDG_URL_FOLDER_SEPARATOR . $object->get_property('id'));

        if ($object->get_type_name() == 'document') {
            $prefix[0] = '=';
            $lastchg = $object->get_property('last-change');
            $lastmod = substr($lastchg, 6, 4) . '-' . substr($lastchg, 3, 2)
                . '-' . substr($lastchg, 0, 2);

            if (!(@$changefreq = $object->get_property('changefreq')))
                $changefreq = 'daily';

            $this->_sitemap .= "  <url>\n     <loc>" .
                $this->_url . urlencode('?display' . $prefix . IDG_URL_FOLDER_SEPARATOR
                    . $object->get_property('id'))
                . "</loc>\n     <lastmod>$lastmod</lastmod>\n"
                . "     <changefreq>$changefreq</changefreq>\n"
                . "  </url>\n";
        }
    }
}

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
        parent::__construct();
        $this->set_element_name('folder');
    }
}

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

    var $last_change = false;
    var $params = [];
    var $variables;

    function __construct() {
        parent::__construct();
        $this->set_element_name('document');
    }

    /**
     * Returns the parameter part of the url that produces the document.
     * @return string A partial url, starting with <code>?display=</code>
     */
    function get_url(): string {
        $url = '?display=' . $this->get_path();

        if ($this->variables) 
            foreach ($this->variables as $name => $value)
                $url .= "&amp;$name=$value";

        return $url;
    }

    /**
     * Returns the containing folder for the document.
     * <i>Note: This can indeed return <code>null</code> since a document
     * can be a direct sibling of an idg_site object.</i>
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
    function get_datasource(string $name): idg_datasource_object {
        if ($name == '_site') 
            return $this->get_site()->get_datasource();

        if (!($datasource = $this->get_child_by_key('name',
            $name, 'idg_datasource')))
            diag($this, "datasource '$name' not found");

        return $datasource->create_instance();
    }

    /**
     * Returns an array containing instances of all renderers for a given slot.
     * @param type $slot_name The name of the slot
     * @return array|null An array or idg_datasource_object or null if none found
     */
    function get_renderers($slot_name): ?array {
        $renderers = [];

        if (!($renderers = $this->get_children_by_key('slot', $slot_name, 
            'idg_renderer')))
            return null;

        foreach ($renderess as $renderer) {
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
        echo "<!-- entering get_default_title -->\n";
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

        echo "<!-- leaving get_default_title -->\n";
        
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
    protected function _get_sitemap_token(array &$token) {
        parent::_get_sitemap_token($token);
        
        $token['path'] = $this->get_path();
        $token['url'] = $this->get_url();
        $token['parent-folder-id'] = $this->get_parent()->get_property('id');
    }
}

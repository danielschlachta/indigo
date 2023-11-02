<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** 
 * Type information for idg_site.
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
        parent::__construct('site');
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
     * @return idg_datasource_implementation The datasource
     */
    function get_datasource(): idg_datasource_implementation {
        $datasource = new idg_datasource_implementation($this);

        if (!$this->traverse($datasource, '$this->_add_token'))
            idg_diag($this, 'internal error: traverse failed');
            
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
    
    function get_sitemap(): string {
        $this->_sitemap = '<?xml version="1.0" encoding="UTF-8"?>'
            . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . "\n";
        $this->_scan_object($this);

        return $this->_sitemap . "</urlset>\n";
    }

    private function _scan_object(&$object, $prefix = '') {
        if ($object->get_type_name() == 'site')
            foreach ($object->get_children() as $child)
                $this->_scan_object($child, '');

        if ($object->get_type_name() == 'folder')
            foreach ($object->get_children() as $child)
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
                $this->get_absolute_url() 
                . urlencode('?display' . $prefix . IDG_URL_FOLDER_SEPARATOR
                . $object->get_property('id'))
                . "</loc>\n     <lastmod>$lastmod</lastmod>\n"
                . "     <changefreq>$changefreq</changefreq>\n"
                . "  </url>\n";
        }
    } */
}

/**
 * Type information for idg_site_element.
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

    function __construct(string $element_name) {
        parent::__construct($element_name);
    }

    function get_site() {
        $site = $this;

        while ($site && (get_class($site) != 'idg_site'))
            $site = $site->get_parent();

        if (!$site)
            idg_diag($this, 'internal error - no site definition found');

        return $site;
    }

    /**
     * Loads up a token for the site's built-in datasource.
     * @param array $token The token 
     */   
    protected function _get_token(array &$token) {
        $token['type'] = $this->get_element_name();
        $token['name'] = $this->get_property('name');
        $token['id'] = $this->get_property('id');
    }
    
    /**
     * Produces a token meant for the site's built-in datasource.
     * @param idg_datasource $datasource An arbitrary datasource
     */
    protected function _add_token(idg_datasource_implementation $datasource): bool {
        $token = [];
        $this->_get_token($token);
        $datasource->add_token($token);
        
        return true;
    }
}

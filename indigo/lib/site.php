<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

require_once($idg_path . '/lib/tree.php');
require_once($idg_path . '/lib/declaration.php');

/** Type class for `idg_site_element` */

class idg_site_element_type extends idg_tree_node_type {

    var $child_types = array('idg_folder', 'idg_document');

    function __construct() {
        parent::__construct();
        $this->set_known('id');
        $this->set_mandatory('id');

        $this->set_known('name');
        $this->set_known('title');
        $this->set_known('content-language');
        $this->set_known('title-separator');
        $this->set_known('title-reverse-order');
        $this->set_known('description');
        $this->set_known('navigation-comment');
        $this->set_known('index-document');
        $this->set_known('show-name');
    }
}

/** Base class for elements of a site definition. */

class idg_site_element extends idg_tree_node {

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
}

class idg_site_type extends idg_site_element_type {

    function __construct() {
        parent::__construct();
        $this->child_types[] = 'idg_datasource_declaration';
        $this->set_mandatory('id', false);
    }
}

class idg_site extends idg_site_element {

    protected $idg_xml_translation = array(
        'site' => 'idg_site',
        'folder' => 'idg_folder',
        'document' => 'idg_document',
        'datasource' => 'idg_datasource_declaration',
        'renderer' => 'idg_renderer_declaration'
    );
    private $document;
    private $view;

    function __construct() {
        parent::__construct();
        $this->set_idg_type('site');
    }

    function get_document($document = false) {
        $document_name = $document;
        $document_obj = false;
        $last_type = '';

        if (!$document_name) {
            if (@$display = $_GET['display']) {
                $document_name = $display;

                if (($pos = strpos($document_name, '#')) != false) {
                    $document_name = substr($display, $pos);
                }
            }
        }

        if (!$document_name)
            $document_name = $this->get_property('index-document');

        if ($document_name) {
            if ($document_name[0] != '/')
                $document_name = '/' . $document_name;

            $path = explode(IDG_URL_FOLDER_SEPARATOR, $document_name);
            $count = count($path);

            for ($i = 1, $tmp = $this; $i < $count && $tmp; $i++) {
                $tmp = $tmp->get_child_by_key('id', $path[$i]);
            }
        
            $document_obj = $tmp;
        }

        return $document_obj;
    }

    function get_datasource(array &$parameters = null) {
        $datasource = new idg_datasource_instance;
        $datasource->set_parameters($this->get_parameters());

        if ($this->children) {
            foreach ($this->children as $child) {
                if (!$child->traverse($datasource, '$this->add_token'))
                    diag($this, 'traverse failed for $this->add_token');
            }
        }

        return $datasource;
    }

    function get_site_url($include_fragment = true) {
        $parsed_url = parse_url(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http"
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

    function get_sitemap() {
        $this->_sitemap = '<?xml version="1.0" encoding="UTF-8"?>'
            . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . "\n";
        $this->_url = $this->get_site_url();
        $this->_scan_object($this);

        return $this->_sitemap . "</urlset>\n";
    }

    /** @todo currently no way to set changefreq */
    private function _scan_object(&$object, $prefix = '') {
        if ($object->get_idg_type() == 'site')
            foreach ($object->children as $child)
                $this->_scan_object($child, '');


        if ($object->get_idg_type() == 'folder')
            foreach ($object->children as $child)
                $this->_scan_object($child,
                    $prefix . IDG_URL_FOLDER_SEPARATOR . $object->get_property('id'));

        if ($object->get_idg_type() == 'document') {
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

class idg_folder_type extends idg_site_element_type {

    function __construct() {
        parent::__construct();
        $this->set_mandatory('name');
    }
}

class idg_folder extends idg_site_element {

    function __construct() {
        parent::__construct();
        $this->set_idg_type('folder');
    }

    function add_token(&$tree, &$depth, &$path) {
        $prop = $this->get_properties();
        $prop['type'] = 'folder';
        $prop['depth'] = $depth;
        $tree->tokens[] = $prop;

        return true;
    }
}

class idg_document_type extends idg_site_element_type {

    function __construct() {
        parent::__construct();
        $this->set_mandatory('name');
        $this->set_hook('title', '$this->get_default_title');
        $this->set_hook('last-change', '$this->get_last_change');
        $this->child_types[] = 'idg_datasource_declaration';
        $this->child_types[] = 'idg_renderer_declaration';
        $this->child_types[] = 'idg_attribute';
    }
}

class idg_document extends idg_site_element {

    var $last_change = false;
    var $params = array();
    var $variables;

    function __construct() {
        parent::__construct();
        $this->set_idg_type('document');
    }

    function get_url() {
        $url = '?display=' . $this->get_path();
        if ($this->variables) {
            foreach ($this->variables as $name => $value) {
                $url .= "&amp;$name=$value";
            }
        }

        return $url;
    }

    function get_folder() {
        $folder = $this;

        while ($folder && (get_class($folder) != 'idg_folder'))
            $folder = $folder->get_parent();

        return $folder;
    }

    function set_variable($name, $value = false) {
        $this->variables[$name] = $value;
    }

    function get_variable($name) {
        if (@($value = $this->variables[$name]))
            return $value;
        else {
            $this->variables[$name] = @$_GET[$name];
            return @$this->variables[$name];
        }
    }

    function get_datasource($source_name) {
        if ($source_name == '_site') {
            return $this->get_site()->get_datasource($this->get_parameters);
        }

        /** @todo Tell where */
        if (!$source_name)
            diag($this, "idg_renderer_declaration: no datasource");

        if (!($datasource_declaration = $this->get_child_by_key('name',
            $source_name, 'idg_datasource_declaration')))
            diag($this, 'idg_renderer_declaration: get_datasource: '
                . ' no datasource named ' . $source_name);

        return $datasource_declaration->create_instance();
    }

    function get_renderers($slot_name) {
        $renderers = array();

        if (!$renderer_declarations = $this->get_children_by_key('slot',
            $slot_name, 'idg_renderer_declaration'))
            diag($this, get_class($this)
                . '(' . $this->get_path()
                . '): unknown renderer (' . $slot_name . ')');

        foreach ($renderer_declarations as $renderer_declaration) {
            $source_name = $renderer_declaration->get_property('source');
            $datasource = $this->get_datasource($source_name);
            ;
            $renderers[] = $renderer_declaration->create_instance($datasource);
        }

        if (count($renderers) == 0)
            return false;

        return $renderers;
    }

    function get_path() {
        $path = $this->get_property('id');
        $tmp = $this->get_parent();

        while ($tmp && (get_class($tmp) != 'idg_site')) {
            $path = $tmp->get_property('id') . IDG_URL_FOLDER_SEPARATOR . $path;
            $tmp = $tmp->get_parent();
        }

        return $path;
    }

    function get_default_title() {
        $site = $this->get_site();
        $index = $site->get_document();

        $tmp = $this;
        $path = '';
        $reverse = $site->get_property('title-reverse-order') != 'no';
        $separator = $site->get_property('title-separator');

        if (!$separator)
            $separator = ' - ';

        while ($tmp) {
            if (($tmp->get_property('show-name') != 'no') && (($p_title = $tmp->get_property('name')) != '')) {
                $path = ($reverse ? $path : $p_title)
                    . ($path != '' ? $separator : '')
                    . ($reverse ? $p_title : $path);
            }

            $tmp = $tmp->get_parent();
        }

        return $path;
    }

    function set_last_change($time) {
        if (!$this->last_change || $this->last_change < $time)
            $this->last_change = $time;
    }

    function get_last_change() {
        if (!($last_change = $this->last_change))
            $last_change = time();
        return date("d.m.Y h:i", $last_change);
    }

    function add_token(&$tree, &$depth, &$path) {
        $prop = $this->get_properties();
        $prop['path'] = $this->get_path();
        $prop['url'] = '?display=' . $prop['path'];
        $prop['type'] = 'document';
        $prop['depth'] = $depth;

        if (($parent = $this->get_parent()) && get_class($parent) == 'idg_folder') {
            $prop['parent-folder-id'] = $parent->get_property('id');
            $prop['parent-folder-name'] = $parent->get_property('name');
        }

        $tree->tokens[] = $prop;

        return true;
    }
}

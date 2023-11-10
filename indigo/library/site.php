<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/**
 * Type information for idg_site.
 * Adds <code>title</code>, <code>content-language</code>, <code>title-separator</code>,
 * <code>title-reverse-order</code> properties.
 * @see idg_document\get_default_title()
 * @todo Scrap this comment or make it a table ...
 */
class idg_site_type extends idg_site_element_type {

    function __construct() {
        parent::__construct();

        $this->child_types[] = 'idg_datasource';

        $this->set_property_mandatory('id', false);
        
        $this->register_property('title');
        $this->register_property('content-language');
        $this->register_property('title-separator');
        $this->register_property('title-reverse-order');
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
            $document_name = $this->get_property('index-document');

            if (IDG_WGET_VERSION)
                $document_name = str_replace(IDG_URL_DEFAULT_FOLDER_SEPARATOR,
                    IDG_URL_FOLDER_SEPARATOR, $document_name);
        }

        if (!$document_name)
            return null;

        if ($document_name[0] != IDG_URL_FOLDER_SEPARATOR)
            $document_name = IDG_URL_FOLDER_SEPARATOR . $document_name;

        $path = explode(IDG_URL_FOLDER_SEPARATOR, $document_name);
        $count = count($path);

        for ($i = 1, $document = $this;
            $i < $count && $document;
            $i++) {
            $document = $document->get_child_by_key('id', $path[$i]);
        }

        return $document;
    }

    /**
     * Returns an instance of a datasource containing the folder structure.
     * The datasource tokens are arrays with key/value pairs.
     * See the individual classes' _get_token() functions for the actual content.
     * @todo This (the tokens) does not show up in the documentation!
     * @return idg_datasource_implementation The datasource
     */
    function get_datasource(): idg_datasource_implementation {
        $datasource = new idg_datasource_implementation($this);

        if (!$this->traverse($datasource, '$this->_add_token'))
            idg_diag($this, 'internal error: traverse failed');

        return $datasource;
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

        $this->register_property('description');
        $this->register_property('navigation-comment');
        $this->register_property('index-document');
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

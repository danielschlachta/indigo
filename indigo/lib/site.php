<?php

/* ========================================================================
 * Indigo/Web
 *
 * File: site.php - contains the site structure and configuration
 *
 * (c) 2023 Daniel Schlachta
 * ======================================================================== */

require_once($idg_path . '/lib/tree.php');
require_once($idg_path . '/lib/declarations.php');
require_once($idg_path . '/lib/site_elements.php');

class idg_site extends idg_site_element
{
	var $idg_translation = array(
		'site' => 'idg_site',
		'folder' => 'idg_folder',
		'document' => 'idg_document',
		'datasource' => 'idg_datasource_declaration',
		'renderer' => 'idg_renderer_declaration'
	);

	var $document;
	var $view;

	function __construct()
	{
		parent::__construct();
		$this->set_idg_type('site');
	}

	function get_document($document = false)
	{
		$document_name = $document;
		$document_obj = false;

		if (!$document_name) {
			if (@$display = $_GET['display']) {
				$document_name = $display;

				if (($pos = strpos($document_name, '#')) !== false) {
					$document_name = substr($display, $pos);
				}
			}
		}

		if (!$document_name)
			$document_name = $this->get_property('index-document');

		if ($document_name) {
			if ($document_name[0] != '/')
				$document_name = '/' . $document_name;

			$path = explode('/', $document_name);
			$count = count($path);

			for ($i = 1, $tmp =& $this; $i < $count && $tmp; $i++) {
				$tmp = $tmp->get_child_by_key('id', $path[$i]);
			}
			$document_obj = $tmp;
		}

		return $document_obj;
	}

	/*!
	 *  @todo This is rather mysterious! Seems to be a stub ... */

	function get_datasource($source_name)
	{
		$param = false;
		$tree = new idg_datasource_tree($param);

		if ($this->children) {
			foreach ($this->children as $child) {
				if (!$child->traverse($tree, '$this->_token'))
					return false;
			}
		}
		return $tree;
	}

	function get_site_url()
	{
		$parsed_url = parse_url(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http"
				. "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
		$scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
		$host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
		$port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
		$user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
		$pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
		$pass = ($user || $pass) ? "$pass@" : '';
		$path = (isset($parsed_url['path']) ? $parsed_url['path'] : '');
		$query  = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
		return "$scheme$user$pass$host$port$path$query$fragment";
    }

	function get_sitemap()
	{
	    $this->_sitemap = '<?xml version="1.0" encoding="UTF-8"?>'
	        . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . "\n";
        $this->_url = $this->get_site_url();
        $this->_scan_object($this);

        return $this->_sitemap . "</urlset>\n";
	}

	function _scan_object(&$object, $prefix = '')
	{
        if ($object->get_idg_type() == 'site')
            foreach ($object->children as $child)
                $this->_scan_object($child, '');


        if ($object->get_idg_type() == 'folder')
            foreach ($object->children as $child)
                $this->_scan_object($child,
                    $prefix . '/' . $object->properties['id']);

        if ($object->get_idg_type() == 'document') {
            $prefix[0] = '=';
            $lastchg = $object->get_property('last-change');
            $lastmod = substr($lastchg, 6, 4) . '-' . substr($lastchg, 3, 2)
              . '-' . substr($lastchg, 0, 2);

            if (!(@$changefreq = $object->properties['changefreq']))
                $changefreq = 'daily';

            $this->_sitemap .= "  <url>\n     <loc>" .
                $this->_url . urlencode('?display' . $prefix . '/'
                    . $object->properties['id'])
                . "</loc>\n     <lastmod>$lastmod</lastmod>\n"
                . "     <changefreq>$changefreq</changefreq>\n"
                . "  </url>\n";
    }
}


}

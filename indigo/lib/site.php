<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

require_once($idg_path . '/lib/tree.php');
require_once($idg_path . '/lib/declaration.php');
require_once($idg_path . '/lib/site_element.php');

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

	function get_datasource() {
		$dummy = false;
		$tree = new idg_datasource_tree($dummy);

		if ($this->children) {
			foreach ($this->children as $child) {
				if (!$child->traverse($tree, '$this->add_token'))
					diag($this, 'traverse failed for $this->add_token');
			}
		}

		return $tree;
	}

	function get_site_url($include_fragment = true)	{
		$parsed_url = parse_url(isset($_SERVER['HTTPS'])
				&& $_SERVER['HTTPS'] === 'on' ? "https" : "http"
				. "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

		$scheme = isset($parsed_url['scheme']) ?
			$parsed_url['scheme'] . '://' : '';
		$host = isset($parsed_url['host']) ?
			$parsed_url['host'] : '';
		$port =	isset($parsed_url['port']) ?
			':' . $parsed_url['port'] : '';
		$user =	isset($parsed_url['user']) ?
			$parsed_url['user'] : '';
		$pass =	isset($parsed_url['pass']) ?
			':' . $parsed_url['pass']  : '';
		$pass =	($user || $pass) ? "$pass@" : '';
		$path =	(isset($parsed_url['path'])	?
			$parsed_url['path'] : '');
		$query  = isset($parsed_url['query']) ?
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

	/*! @todo currently no way to set changefreq */

	private function _scan_object(&$object, $prefix = '') {
        if ($object->get_idg_type() == 'site')
            foreach ($object->children as $child)
                $this->_scan_object($child, '');


        if ($object->get_idg_type() == 'folder')
            foreach ($object->children as $child)
                $this->_scan_object($child,
                    $prefix . '/' . $object->get_property('id'));

        if ($object->get_idg_type() == 'document') {
            $prefix[0] = '=';
            $lastchg = $object->get_property('last-change');
            $lastmod = substr($lastchg, 6, 4) . '-' . substr($lastchg, 3, 2)
              . '-' . substr($lastchg, 0, 2);

            if (!(@$changefreq = $object->get_property('changefreq')))
                $changefreq = 'daily';

            $this->_sitemap .= "  <url>\n     <loc>" .
                $this->_url . urlencode('?display' . $prefix . '/'
                    . $object->get_property('id'))
                . "</loc>\n     <lastmod>$lastmod</lastmod>\n"
                . "     <changefreq>$changefreq</changefreq>\n"
                . "  </url>\n";
		}
	}
}

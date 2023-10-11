<?php

/* ===================================================================
 * Indigo/Web
 *
 * File: document.php - contains base classes for documents
 *
 * (c) 2023 Daniel Schlachta
 * =========================_======================================== */

class idg_site_element_type extends idg_tree_node_type
{
	var $child_types = array('idg_folder', 'idg_document');

	function __construct()
	{
		parent::__construct();
		$this->set_known('id');
		$this->set_known('name');

		$this->set_known('title');
		$this->set_known('content-language', 'yes');
		$this->set_known('title-separator', 'yes');
		$this->set_known('description');
		$this->set_known('navigation-comment');
		$this->set_known('index-document', 'yes');
		$this->set_known('show-name');

		$this->set_mandatory('id');
		$this->set_mandatory('name');
	}
}

class idg_site_element extends idg_tree_node {
	private $site;

	function __construct()
	{
		parent::__construct();
	}

	function get_site()
	{
		if ($this->site)
			return $this->site;

		$site = $this;

		while ($site && (get_class($site) != 'idg_site')) {
			$site = $site->get_parent();
		}

		if (!$site)
			diag($this, 'internal error - no site definition found');

		$this->site = $site;

		return $site;
	}
}

?>

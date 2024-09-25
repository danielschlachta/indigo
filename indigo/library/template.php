<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://openslot.org/license/mit/
 */

/**
 * A central repository for (volatile) information.
 * <blockquote>
 * Note: While this class does <em>not</em> exactly complete the controller part of the
 * [MVC pattern](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller) -
 * because it is stateless and provides no functionality of its own -
 * it could easily be extended for this purpose. Things like cookies, session management,
 * authentication and the like should indeed be handled by subclassing it.
 * </blockquote>
 */
class idg_template {

    private ?string $name;
    private ?string $path;
    private ?string $uri;
    private ?string $namespace = null;

    /**
     * Constructs an idg_template object and possibly loads a site template.
     * Site templates contain exactly one <code>php</code> script (and what is
     * included from there, of course) defining a namespace that is
     * subsequently used throughout the idg_template object.<br>
     * Other than that, no assumptions are made as to their purpose or structure.
     * <blockquote>
     * Notes:
     *  - The namespace can also be set programmatically. This is useful for generating
     * files, e.g. from the command line.
     *  - The site designs that come with indigo are implemented as templates. Therefore,
     * a site that uses such a design can have no template of its own.
     * </blockquote>
     * @see set_namespace(), get_namespace(), get_path(), get_uri(), qualify()
     * @param string $name The entry point without the <code>.php</code> suffix
     * @param string $path The path to where <code>php</code> can find the file
     * @param string $uri The path part of the <code>URL</code> if it differs
     */
    public function __construct(string $name = null,  string $path = null,
        string $uri = null) {
        
        $this->name = $name;
        $this->path = $path;
        $this->uri = $uri;

        if ($name)
            $this->load($name);
    }

    /**
     * Returns the path to the template as specified in the constructor.
     * @see __construct()
     * @return string|null The path
     */
    function get_path(): ?string {
        return $this->path ? $this->path : $this->name;
    }

    /**
     * Returns the <code>URL</code> path to the template, as specified in the 
     * constructor, or optionally a subfolder thereof.
     * Defaults to the value of <code>$template_path</code> if <code>$template_uri</code>
     * was not specified. If nothing at all is set, the empty string is returned.
     * @see __construct()
     * @param string $subfolder The name of a subfolder
     * @return string The path to the directory
     */
    function get_uri(string $subfolder = null): string {
        $uri = $this->uri ? $this->uri : $this->get_path();
    
        if ($subfolder && $uri && $uri[strlen($uri) - 1] != '/')
            $uri .= '/';

        return "$uri$subfolder";
    }

    private function load(string $template_name): void {

        $filename = $this->get_path() . "/$template_name.php";

        if (!file_exists($filename))
            idg_diag($this, "file '$filename' does not exist");

        if (!($fp = fopen($filename, "r")))
            idg_diag($this, "unable to open file '$filename' for reading");

        $string = fread($fp, 4096); // Hope it's enough, some people write novels ...
        fclose($fp);

        require_once $filename;

        if (preg_match('/.*namespace[ \t\n]+([^;]+);/', $string, $matches) !== 1)
            idg_log('template/load: no namespace found in template');
        else
            $this->namespace = trim($matches[1]);

        $this->name = $template_name;
    }

    /**
     * Sets the namespace to the given value.
     * @param string $namespace The namespace
     */    
    function set_namespace(string $namespace = null): void {
        $this->namespace = $namespace;
    }

    /**
     * Gets the current namespace.
     * @return string|null The namespace
     */
    function get_namespace(): ?string {
        return $this->namespace;
    }

    /**
     * Returns the given string prepended with the current namespace if applicable.
     * @param string $string The qualified string
     */
    function qualify(string $string): string {
        if ($this->namespace)
            return $this->namespace . '\\' . $string;

        return $string;
    }
    
    /* TODO: Move URL handling out of here! */
    
    /**
     * Gets the complete <code>URI</code> for a given document (or the current one if 
     * <code>$document</code> is not specified), variables and all.
     * @param string $document The path to a document, as used by idg_site
     * @return string The <code>URI</code>
     * @see idg_site\get_datasource()
     */
    function get_full_uri(string $document = null): string {
        return "?display=$document";
    }

    /**
     * Returns the document and path specified in the <code>URL</code>
     * or <code>null</code>.
     * @return string|null The name and path of the document
     */
    function get_request_document(): ?string {
        return @$_GET['display'];
    }
    
    private function get_absolute_url($include_fragment = true) {
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
}

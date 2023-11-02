<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

namespace Indigo\Renderer;

class phpscript extends \idg_renderer_implementation {

    function _render(idg_documen $document, idg_view $view): void {
        $this->datasource->rewind();
        $token = $this->datasource->get_token();
        $class = $token['class'];
        $file = $token['script'];
        /*
          if (@stat($file) === false) {
          $body = get_class($this)
          . '(' . $this->parent->idg_id . '): script file('
          . $file . ') could not be found';
          $view->stream_append('html-body', $body);
          } else {
          if (!$document->last_change) {
          $document->set_last_change(filemtime($file));
          }
          require_once($file);
          $data = false;
          $obj = new $class($data);
          $obj->set_parent($this->parent);
          $obj->idg_id = $this->get_idg_id();
          $obj->set_properties($token);
          $obj->render($document, $view);
          } */
    }
}

class idg_renderer_phpscript_obj {

    var $idg_id;
    var $parent;
    var $datasource;
    var $variables = array();
    var $is_invalid = array();
    var $is_valid = true;

    function __construct($parent, $variables = false) {
        foreach ($variables as $name => $value) {
            $value = @$_POST[$name];
            $this->set_variable($name, $value);
            $this->is_invalid[$name] = false;
        }
    }

    function render(&$document, &$view) {
        
    }

    function set_variable($name, $value = false) {
        if (!$value) {
            $this->is_invalid[$name] = true;
            return;
        }

        $this->variables[$name] = $value;
        $str = "\$this->$name=\$this->variables['$name'];";
        eval($str);
    }

    function get_variable($name) {
        if (@($value = $this->variables[$name]))
            return $value;

        return false;
    }

    function is_valid($name = false) {
        if (!$name)
            return $this->is_valid;
        return @!$this->is_invalid[$name];
    }

    function set_invalid($name) {
        $this->is_invalid[$name] = true;
        $this->is_valid = false;
    }

    function set_invalid_if_empty($name) {
        if (!@$this->variables[$name]) {
            $this->set_variable($name);
            $this->is_valid = false;
        }
    }
}

<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

class idg_object_type {

    protected $properties = [];
    protected $hooks = [];

    function __construct() {
        $this->set_known('options');
    }

    function get_known($name) {
        return array_key_exists($name, $this->properties);
    }

    function set_known($name) {
        if (!array_key_exists($name, $this->properties))
            $this->properties[$name] = false;
    }

    function get_properties() {
        return $this->properties;
    }

    function set_mandatory($name, $is_mandatory = true) {
        if (!array_key_exists($name, $this->properties))
            diag($this, "set_mandatory: unknown property '$name'");

        $this->properties[$name] = $is_mandatory;
    }

    function is_mandatory($name) {
        return array_key_exists($name, $this->properties) && $this->properties[$name];
    }

    function get_hook($name) {
        return @$this->hooks[$name];
    }

    function set_hook($name, $function_name) {
        $this->hooks[$name] = $function_name;
    }
}

class idg_object {

    private $idg_id;
    private $idg_type;
    protected $type_obj;
    private $properties = [];
    private $hooks = [];
    protected $text;
    private static $idg_object_counters = array();
    private static $idg_type_objects = array();

    /**
     * Constructs an idg object and assigns the corresponding idg type.
     *
     * The idg_object_type is created if necessary, otherwise
     * taken from a list.
     *
     * $idg_id is assigned a unique id based on the (php) object type
     * and the number of objects constructed so far.
     *
     */
    function __construct() {
        $type = get_class($this);
        $type_obj_name = $type . '_type';

        if (!class_exists($type_obj_name))
            diag($this,
                "constructing $type: no corresponding idg_object_type");

        if (@!idg_object::$idg_type_objects[$type]) {
            $this->type_obj = new $type_obj_name();
            idg_object::$idg_type_objects[$type] = & $this->type_obj;
        } else
            $this->type_obj = & idg_object::$idg_type_objects[$type];

        if (@!idg_object::$idg_object_counters[$type])
            $id_count = idg_object::$idg_object_counters[$type] = 1;
        else
            $id_count = ++idg_object::$idg_object_counters[$type];

        $this->idg_id = $type . '-' . $id_count;
    }

    function clear() {
        unset($this->parent);
        unset($this->children);
    }

    function get_idg_id() {
        return $this->idg_id;
    }

    function get_text() {
        return $this->text;
    }

    function set_text($text) {
        $this->text = $text;
    }

    function get_idg_type() {
        return $this->idg_type;
    }

    protected function set_idg_type($idg_type) {
        $this->idg_type = $idg_type;
    }

    function get_property($name, $execute_hooks = true) {
        if ($prop = @$this->properties[$name])
            return $prop;

        if ($hook = $this->get_hook($name))
            return $this->execute($hook);

        if ($hook = $this->type_obj->get_hook($name))
            return $this->execute($hook);
    }

    function get_properties() {
        return $this->properties;
    }

    function get_property_values(&$prop_array,
        $execute_hooks = false) {
        foreach ($prop_array as $name => $value) {
            if ($value = $this->get_property($name, $execute_hooks)) {
                $prop_array[$name] = $value;
            } else if ($execute_hooks)
                $prop_array[$name] = $this->execute($hook);
            else
                $prop_array[$name] = null;
        }
    }

    function set_property($name, $value) {
        if (!$this->type_obj)
            diag($this, 'set_property: idg object has no type');

        if (!$this->type_obj->get_known($name))
            diag($this, 'set_property: unknown property: ' . $name);

        $this->properties[$name] = $value;
    }

    function set_properties($prop_array) {
        if (!$this->type_obj)
            diag($this, 'set_properties: idg object has no type');

        foreach ($prop_array as $name => $value) {
            if ($this->type_obj->get_known($name))
                $this->properties[$name] = $value;
            else
                diag($this, "set_properties: unknown property '$name'");
        }
    }

    function get_hook($name) {
        return @$this->hooks[$name];
    }

    function set_hook($name, $function_name) {
        $this->hooks[$name] = $function_name;
    }

    /**   Executes members of this object and global functions. */
    function execute($function_name) {
        if (strpos($function_name, '$this->') === 0) {
            $do_func = substr($function_name, strlen('$this->'));
            $retval = $this->$do_func();
        } else
            $retval = $function_name();

        return $retval;
    }

    function get_instance() {
        $object_name = get_class($this)
            . '_' . $this->get_property('class');

        if (!class_exists($object_name))
            diag($this, "object: class does not exist: $object_name");

        return new $object_name($this);
    }

    /**
     * Performs consistency checks, dies when an error is encountered.
     *
     * This currently only checks whether all mandatory properties have
     * values.
     */
    protected function _check() {
        $properties = $this->type_obj->get_properties();

        foreach ($properties as $name => $value) {
            if (!$this->type_obj->is_mandatory($name))
                continue;

            if (!@$this->get_property($name))
                diag($this, "check: missing property '$name '");
        }
    }

    protected function _print_debug($indent = '') {
        $lines = array(
            "ID       $this->idg_id"
        );
        $lines[] = 'XML_TYPE ' . $this->get_idg_type();
        $lines[] = '(break)';

        $max_header = 0;
        foreach ($lines as $line) {
            if (strlen($line) > $max_header)
                $max_header = strlen($line);
        }

        $max_prop = 0;
        foreach ($this->properties as $name => $value) {
            if (strlen($name) > $max_prop)
                $max_prop = strlen($name);
        }

        $max_line = 0;
        if ($this->text) {
            $text_lines = explode("\n", $this->text);
            foreach ($text_lines as $line) {
                if ($max_line < strlen($line))
                    $max_line = strlen($line);
            }
        }

        foreach ($this->properties as $name => $value) {
            for ($j = strlen($name), $space = ''; $j < $max_prop; $j++)
                $space .= ' ';
            $line = "$name$space $value";
            $lines[] = $line;
            if (strlen($line) > $max_line)
                $max_line = strlen($line);
        }

        if ($max_header > $max_line)
            $max_line = $max_header;


        for ($j = 0, $dash = ''; $j < $max_line; $j++)
            $dash .= '-';

        $retstr = "$indent+-$dash-+\n";

        foreach ($lines as $line) {
            if ($line == '(break)')
                $retstr .= "$indent+-$dash-+\n";
            else {
                $space = '';
                for ($j = strlen($line); $j < $max_line; $j++)
                    $space .= ' ';
                $retstr .= $indent . '| ' . htmlentities($line)
                    . "$space |\n";
            }
        }
        if (count($this->properties) > 0)
            $retstr .= "$indent+-$dash-+\n";

        if ($this->text) {
            foreach ($text_lines as $line) {
                $space = '';
                for ($j = strlen($line); $j < $max_line; $j++)
                    $space .= ' ';
                $retstr .= "$indent| " . htmlentities($line)
                    . " $space|\n";
            }
            $retstr .= "$indent+-$dash-+\n";
        }

        return $retstr;
    }

    protected function get_xml_tag($tag_type, $tag_id = '') {
        if ($tag_id == '')
            $use_id = $this->get_idg_type();
        else
            $use_id = $tag_id;

        if ($tag_type == 'end') {
            $text = '</' . $use_id . '>';
            return array(
                array(
                    'indent' => 0,
                    'line' => $text
                )
            );
        }

        $prop_xml = array();

        foreach ($this->properties as $name => $value) {
            $prop_xml[] = "$name=\"$value\"";
        }

        $tagend = $tag_type == 'single' ? ' /' : '';
        $xml = array();

        if (($prop_count = count($this->properties)) > 0) {
            $first = & $prop_xml[0];
            $last = '';

            if ($prop_count < 2)
                $xml[] = array(
                    'indent' => 0,
                    'line' => "<$use_id $first$tagend>"
                );
            else {
                $last = & $prop_xml[$prop_count - 1];
                $xml[] = array(
                    'indent' => 0,
                    'line' => "<$use_id $first"
                );

                for ($i = 1; $i < count($prop_xml) - 1; $i++)
                    $xml[] = array(
                        'indent' => 1,
                        'line' => &$prop_xml[$i]
                    );

                if ($tag_type == 'start')
                    $xml[] = array(
                        'indent' => 1,
                        'line' => "$last>"
                    );
                else
                    $xml[] = array(
                        'indent' => 1,
                        'line' => "$last />"
                    );
            }
        } else
            $xml[] = array(
                'indent' => 0,
                'line' => "<$use_id$tagend>"
            );

        if ($this->text) {
            $text_lines = explode("\n", htmlentities($this->text));

            foreach ($text_lines as $line)
                $xml[] = array(
                    'indent' => 1,
                    'line' => $line
                );
        }

        return $xml;
    }
}

<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/** 
 * Base class for idg entities with parameters. 
 */
class idg_attribute implements idg_parameterized {

    use idg_parameters;

    private string $name;
    
    function __construct(string $name) {
        $this->name = $name;
    }

    /**
     * Returns the name of the attribute with the scope stripped.
     * @return string The name
     */
    function get_name_part(): string {
        if (($pos = strpos($this->name, '::')) > 0) 
            return substr($this->name, $pos + 2);
        
        return $this->name;
    }
    
    /**
     * Returns the scope of an attribute, i.e. the part before <code>::</code>.
     * @return string|null The scope
     */
    function get_scope_part(): ?string {
        if (($pos = strpos($this->name, '::')) > 0) 
            return substr($this->name, 0, $pos);           
        
        return null;
    }
    
    /**
     * Adds the options for the given attribute and scope, possibly inheriting them
     * from parent objects if there are any.
     * @param idg_object $object
     * @param string $scope
     */
    function add_options($object): void {
        do {
            if (method_exists($object, 'get_attribute') &&
                (($attribute = $object->get_attribute($this->name, $this->scope))) &&
                (($parameters = $attribute->get_parameters())))
                
                foreach ($parameters as $option => $value)
                    if (!$this->get_parameter($option))
                        $this->set_parameter($option, $value);

            if (method_exists($object, 'get_parent'))
                $object = $object->get_parent();
            else
                $object = null;
        } while ($object);
    }

    /**
     * Returns an <code>xml</code> representation of the attribute and its options.
     * @param string $indent What to prefix the returned lines with
     * @return string The <code>xml</code> fragment
     */
    function get_xml(int $indentation): string {
        $xml = '';
        $indent = '';
        
        for ($i = 0; $i < $indentation; $i++)
            $indent .= IDG_XML_INDENT;
        
        $xml = "$indent<attribute name=\"$this->name\">\n";

        foreach ($this->parameters as $name => $value) {
            $value = htmlentities($value);
            $xml .= $indent . IDG_XML_INDENT 
                . "<option name=\"$name\">$value</option>\n";
        }

        $xml .= "$indent</attribute>\n";

        return $xml;
    }
}

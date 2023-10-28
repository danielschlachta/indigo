<?php

/*
 *  Copyright (c) 2023 Daniel Schlachta <daniel.schlachta@gmail.com>
 *  License: MIT License, see https://opensource.org/license/mit/
 */

/**
 * Interface for parameterized objects, i.e. entities that have a declaration
 * and an implementation part.
 */
interface idg_parameterized {

    /**
     * Sets a parameter by name.
     * @param string $name The name of the parameter
     * @param string $value The value of the parameter
     */
    function set_parameter(string $name, string $value): void;

    /**
     * Sets the parameters all at once to the contents of the given array.
     * @param array|null $parameters The parameters as key/value pairs
     */
    function set_parameters(?array $parameters = null): void;

    /**
     * Get a parameter optionally providing a default value if not set.
     * @param string $name The name of the parameter
     * @param string|null $default The default value of the parameter
     * @return string|null The value of the parameter
     */
    function get_parameter(string $name, ?string $default = null): ?string;

    /**
     * Gets the parameters all at once in an array using key/value pairs.
     * @return array|null The perameters
     */
    function get_parameters(): ?array;
}

trait idg_parameters {

    private $parameters;

    function set_parameter(string $name, string $value): void {
        if (!$this->parameters)
            $this->parameters = [];
        $this->parameters[$name] = $value;
    }

    function set_parameters(?array $parameters = null): void {
        $this->parameters = $parameters;
    }

    function get_parameter(string $name, ?string $default = null): ?string {
        if (($value = @$this->parameters[$name]))
            return $value;

        return $default;
    }

    function get_parameters(): ?array {
        return $this->parameters;
    }
}

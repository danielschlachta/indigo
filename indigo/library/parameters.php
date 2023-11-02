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
     * Gets a parameter optionally providing a default value.
     * @param string $name The name of the parameter
     * @param string|null $default The default value of the parameter
     * @return string|null The value of the parameter
     */
    function get_parameter(string $name, ?string $default = null): ?string;

    /**
     * Gets the parameters all at once in an array using key/value pairs.
     * @return array|null The parameters
     */
    function get_parameters(): ?array;
}

trait idg_parameters {

    private ?array $parameters = null;

    function set_parameter(string $name, string $value): void {
        $this->parameters[$name] = $value;
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

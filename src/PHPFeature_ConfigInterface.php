<?php
/**
 * PHPFeature_ConfigInterface Interface.
 *
 * @package   brightnucleus/phpfeature
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

/**
 * Interface PHPFeature_ConfigInterface.
 *
 * Configuration Interface to use. The library includes a generic implementation of this interface named
 * `PHPFeature_Config`.
 *
 * @since  0.1.0
 *
 * @author Alain Schlesser <alain.schlesser@gmail.com>
 */
interface PHPFeature_ConfigInterface
{

    /**
     * Creates a copy of the ArrayObject.
     *
     * Returns a copy of the array. When the ArrayObject refers to an object an
     * array of the public properties of that object will be returned.
     * This is implemented by \ArrayObject.
     *
     * @since 0.1.0
     *
     * @return array Copy of the array.
     */
    public function getArrayCopy();

    /**
     * Check whether the Config has a specific key.
     *
     * @since 0.1.0
     *
     * @param string $key The key to check the existence for.
     *
     * @return bool Whether the specified key exists.
     */
    public function hasKey($key);

    /**
     * Get the value of a specific key.
     *
     * @since 0.1.0
     *
     * @param string $key The key to get the value for.
     *
     * @return mixed Value of the requested key.
     */
    public function getKey($key);

    /**
     * Get the an array with all the keys
     *
     * @since 0.1.0
     *
     * @return array Array of config keys.
     */
    public function getKeys();
}

<?php
/**
 * ConfigInterface Interface.
 *
 * @package   phpfeature
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

/**
 * Interface ConfigInterface
 *
 * @since  0.1.0
 *
 * @author Alain Schlesser <alain.schlesser@gmail.com>
 */
interface ConfigInterface
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
     * @return bool
     */
    public function hasKey($key);

    /**
     * Get the value of a specific key.
     *
     * @since 0.1.0
     *
     * @param string $key The key to get the value for.
     *
     * @return mixed
     */
    public function getKey($key);

    /**
     * Get the an array with all the keys
     *
     * @since 0.1.0
     *
     * @return mixed
     */
    public function getKeys();
}

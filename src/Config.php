<?php
/**
 * Config Class.
 *
 * @package   brightnucleus/phpfeature
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

/**
 * Config loader used to load config PHP files as objects.
 *
 * @since  0.1.0
 *
 * @author Alain Schlesser <alain.schlesser@gmail.com>
 */
class Config extends ArrayObject implements ConfigInterface
{

    /**
     * Instantiate the Config object.
     *
     * @since 0.1.0
     *
     * @param  array $config Array with settings.
     */
    public function __construct(array $config)
    {
        // Make sure the config entries can be accessed as properties.
        parent::__construct($config, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Magic method that enables the use of normal array_* functions on the
     * Config object.
     *
     * @since  0.1.0
     *
     * @param  string $method    The method that was called on this object.
     * @param  mixed  $arguments The arguments that were used for the method
     *                           call.
     *
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($method, $arguments)
    {
        if ( ! is_callable($method) || 0 !== strpos($method, 'array')) {
            throw new BadMethodCallException(__CLASS__ . '->' . $method);
        }

        return call_user_func_array($method, array_merge(array($this->getArrayCopy()), $arguments));
    }

    /**
     * Check whether the Config has a specific key.
     *
     * @since 0.1.0
     *
     * @param string $key The key to check the existence for.
     *
     * @return bool
     */
    public function hasKey($key)
    {
        return array_key_exists($key, (array)$this);
    }

    /**
     * Get the value of a specific key.
     *
     * @since 0.1.0
     *
     * @param string $key The key to get the value for.
     *
     * @return mixed
     */
    public function getKey($key)
    {
        return $this[$key];
    }

    /**
     * Get the an array with all the keys
     *
     * @since 0.1.0
     *
     * @return mixed
     */
    public function getKeys()
    {
        return array_keys((array)$this);
    }
}

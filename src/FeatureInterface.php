<?php
/**
 * FeatureInterface Interface
 *
 * @package   phpfeature
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

/**
 * Interface FeatureInterface
 *
 * @since  0.1.0
 *
 * @author Alain Schlesser <alain.schlesser@gmail.com>
 */
interface FeatureInterface
{

    /**
     * Check whether a feature or a collection of features is supported.
     *
     * Accepts either a string or an array of strings. Returns true if all the
     * passed-in features are supported, or false if at least one of them is
     * not.
     *
     * @since 0.1.0
     *
     * @param string|array $features What features to check the support of.
     * @return bool
     */
    public function isSupported($features);

    /**
     * Get the minimum required version that supports all of the requested
     * features.
     *
     * Accepts either a string or an array of strings. Returns a
     * SemanticVersion object for the version number that is known to support
     * all the passed-in features, or false if at least one of
     * them is not supported by any known version.
     *
     * @since 0.2.0
     *
     * @param string|array $features What features to check the support of.
     * @return SemanticVersion|bool
     */
    public function getMinimumRequired($features);
}

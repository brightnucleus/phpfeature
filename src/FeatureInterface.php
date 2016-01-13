<?php
/**
 * FeatureInterface Interface
 *
 * @package   phpfeature
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
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
interface FeatureInterface {

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
	public function is_supported( $features );
}
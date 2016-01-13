<?php
/**
 * PHPFeature Class
 *
 * @package   phpfeature
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

/**
 * Class PHPFeature
 *
 * @since  0.1.0
 *
 * @author Alain Schlesser <alain.schlesser@gmail.com>
 */
class PHPFeature implements FeatureInterface {

	/**
	 * RegEx pattern that matches the comparison string.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	const COMPARISON_PATTERN = '/^(?:(<=|lt|<|le|>=|gt|>|ge|=|==|eq|!=|<>|ne))([0-9].*)$/';

	/**
	 * Reference to the Configuration object.
	 *
	 * @since 0.1.0
	 *
	 * @var ConfigInterface
	 */
	protected $config;

	/**
	 * Reference to the Version object.
	 *
	 * @since 0.1.0
	 *
	 * @var SemanticVersion
	 */
	protected $version;

	/**
	 * Instantiate a PHPFeature object.
	 *
	 * @since 0.1.0
	 *
	 * @param SemanticVersion|string|int|null $php_version Version of PHP to
	 *                                                     check the features
	 *                                                     for.
	 * @param ConfigInterface|null            $config      Configuration that
	 *                                                     contains the known
	 *                                                     features.
	 * @throws RuntimeException If the PHP version could not be validated.
	 */
	public function __construct( $php_version = null, ConfigInterface $config = null ) {

		// TODO: Better way to bootstrap this while still allowing DI?
		if ( ! $config ) {
			$config = new Config( include( __DIR__ . '/../config/known_features.php' ) );
		}

		$this->config = $config;

		if ( null === $php_version ) {
			$php_version = phpversion();
		}

		if ( is_integer( $php_version ) ) {
			$php_version = (string) $php_version;
		}

		if ( is_string( $php_version ) ) {
			$php_version = new SemanticVersion( $php_version, true );
		}

		$this->version = $php_version;
	}

	/**
	 * Check whether a feature or a collection of features is supported.
	 *
	 * Accepts either a string or an array of strings. Returns true if all the
	 * passed-in features are supported, or false if at least one of them is
	 * not.
	 *
	 * @since 0.1.0
	 *
	 * @param string|array $features    What features to check the support of.
	 * @return bool
	 * @throws InvalidArgumentException If the wrong type of argument is passed
	 *                                  in.
	 * @throws RuntimeException         If a requirement could not be parsed.
	 */
	public function is_supported( $features ) {

		if ( is_string( $features ) ) {
			$features = array( $features );
		}

		if ( ! is_array( $features ) ) {
			throw new InvalidArgumentException( sprintf(
				'Wrong type of argument passed in to is_supported(): "%1$s".',
				gettype( $features )
			) );
		}

		$is_supported = true;

		while ( $is_supported && count( $features ) > 0 ) {
			$feature = array_pop( $features );
			$is_supported &= (bool) $this->check_support( $feature );
		}

		return (bool) $is_supported;
	}

	/**
	 * Check whether a single feature is supported.
	 *
	 * @since 0.1.0
	 *
	 * @param string $feature The feature to check.
	 * @return bool
	 * @throws RuntimeException If the requirement could not be parsed.
	 */
	protected function check_support( $feature ) {

		if ( ! $this->config->has_key( $feature ) ) {
			return false;
		}

		$requirements = $this->config->get_key( $feature );

		if ( ! is_array( $requirements ) ) {
			$requirements = array( $requirements );
		}

		$is_supported = true;

		while ( $is_supported && count( $requirements ) > 0 ) {
			$requirement = array_pop( $requirements );
			$is_supported &= (bool) $this->check_requirement( $requirement );
		}

		return (bool) $is_supported;
	}

	/**
	 * Check whether a single requirement is met.
	 *
	 * @since 0.1.0
	 *
	 * @param string $requirement A requirement that is composed of an operator
	 *                            and a version milestone.
	 *
	 * @return bool
	 * @throws RuntimeException If the requirement could not be parsed.
	 */
	protected function check_requirement( $requirement ) {

		$requirement = trim( $requirement );
		$pattern     = self::COMPARISON_PATTERN;

		$arguments = array();
		$result    = preg_match( $pattern, $requirement, $arguments );

		if ( ! $result || ! isset( $arguments[1] ) || ! isset( $arguments[2] ) ) {
			throw new RuntimeException( sprintf(
				'Could not parse the requirement "%1$s".',
				(string) $requirement
			) );
		}

		$operator  = isset( $arguments[1] ) ? (string) $arguments[1] : '>=';
		$milestone = isset( $arguments[2] ) ? (string) $arguments[2] : '0.0.0';

		return (bool) version_compare( $this->version->get_version(), $milestone, $operator );
	}
}
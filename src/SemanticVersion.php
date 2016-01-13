<?php
/**
 * SemanticVersion Class
 *
 * @package   phpfeature
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

/**
 * Class SemanticVersion
 *
 * @since  0.1.0
 *
 * @author Alain Schlesser <alain.schlesser@gmail.com>
 */
class SemanticVersion {

	/**
	 * RegEx pattern that matches the different version components.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	const VERSION_PATTERN = '/^(\d+)(?:\.(\d+))?(?:\.(\d+))?(?:-([0-9A-Za-z-]*))?(?:\+([0-9A-Za-z-]*))?$/';

	/**
	 * Version that is used.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Different components of the version that is used.
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	protected $components;

	/**
	 * Instantiate a Version object.
	 *
	 * @since 0.1.0
	 *
	 * @param string|null $version Optional. The version to use. Defaults to
	 *                             the current PHP interpreter's version.
	 * @param bool        $partial Optional. Whether to accept a partial
	 *                             version number. If true, the missing
	 *                             components will default to `0` instead of
	 *                             throwing an exception.
	 * @throws RuntimeException When the version fails to validate.
	 */
	public function __construct( $version = null, $partial = false ) {

		if ( ! $version ) {
			$version = '0.0.0';
		}

		$version = $this->validate( $version, $partial );

		$this->version = $version;
	}

	/**
	 * Validate the version and assert it is in SemVer format.
	 *
	 * @since 0.1.0
	 *
	 * @param string $version The version to validate.
	 * @param bool   $partial Optional. Whether to accept a partial version
	 *                        number. If true, the missing components will
	 *                        default to `0` instead of throwing an exception.
	 * @return string
	 * @throws RuntimeException When the version fails to validate.
	 */
	protected function validate( $version, $partial = false ) {

		$version = trim( $version );
		$pattern = self::VERSION_PATTERN;

		$components = array();
		$result     = preg_match( $pattern, $version, $components );

		if ( ! $result ) {
			throw new RuntimeException( sprintf(
				'Failed to validate version "%1$s".',
				(string) $version
			) );
		}

		if ( ! $partial && ( ! isset( $components[2] ) || ! isset( $components[3] ) ) ) {
			throw new RuntimeException( sprintf(
				'Could not accept partial version "%1$s", requested full versions only.',
				(string) $version
			) );
		}

		$this->set_component( 'major', isset( $components[1] ) ? (int) $components[1] : 0 );
		$this->set_component( 'minor', isset( $components[2] ) ? (int) $components[2] : 0 );
		$this->set_component( 'patch', isset( $components[3] ) ? (int) $components[3] : 0 );
		$this->set_component( 'pre-release', isset( $components[4] ) ? (string) $components[4] : '' );
		$this->set_component( 'build', isset( $components[5] ) ? (string) $components[5] : '' );

		$version = $this->get_version_from_components();

		return $version;
	}

	/**
	 * Get the version that is used.
	 *
	 * @since 0.1.0
	 *
	 * @return string The version that is used. '0.0.0' if not defined.
	 */
	public function get_version() {
		return (string) isset( $this->version ) ? $this->version : '0.0.0';
	}

	/**
	 * Build and return a versin from the separated components.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	protected function get_version_from_components() {

		$pre_release = $this->get_pre_release() ? '-' . $this->get_pre_release() : '';
		$build       = $this->get_build() ? '+' . $this->get_build() : '';

		$version = sprintf(
			'%1$s.%2$s.%3$s%4$s%5$s',
			$this->get_major(),
			$this->get_minor(),
			$this->get_patch(),
			$pre_release,
			$build
		);

		return $version;
	}

	/**
	 * Get the major version number.
	 *
	 * @since 0.1.0
	 *
	 * @return int The major version that is used. 0 if not defined.
	 */
	public function get_major() {
		return (int) $this->get_component( 'major' ) ?: 0;
	}

	/**
	 * Get the minor version number.
	 *
	 * @since 0.1.0
	 *
	 * @return int The minor version that is used. 0 if not defined.
	 */
	public function get_minor() {
		return (int) $this->get_component( 'minor' ) ?: 0;
	}

	/**
	 * Get the patch version number.
	 *
	 * @since 0.1.0
	 *
	 * @return int The patch version that is used. 0 if not defined.
	 */
	public function get_patch() {
		return (int) $this->get_component( 'patch' ) ?: 0;
	}

	/**
	 * Get the pre-release label.
	 *
	 * @since 0.1.0
	 *
	 * @return int The patch version that is used. '' if not defined.
	 */
	public function get_pre_release() {
		return (string) $this->get_component( 'pre-release' ) ?: '';
	}

	/**
	 * Get the build metadata.
	 *
	 * @since 0.1.0
	 *
	 * @return int The build metadata for the version that is used. '' if not
	 *             defined.
	 */
	public function get_build() {
		return (string) $this->get_component( 'build' ) ?: '';
	}

	/**
	 * Get a component of the version.
	 *
	 * @since 0.1.0
	 *
	 * @param string $level What level of component to get. Possible values:
	 *                      'major', 'minor', 'patch'
	 * @return int The requested version component. null if not defined.
	 */
	protected function get_component( $level ) {
		return array_key_exists( $level, $this->components )
			? $this->components[ $level ]
			: null;
	}

	/**
	 * Set a component of the version.
	 *
	 * @since 0.1.0
	 *
	 * @param string $level   What level of component to set. Possible values:
	 *                        'major', 'minor', 'patch'
	 * @param int    $version What version to set that component to.
	 */
	protected function set_component( $level, $version ) {
		$this->components[ $level ] = $version;
	}
}
<?php
/**
 * SemanticVersionTest Class
 *
 * @package   phpfeature
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

/**
 * Class SemanticVersionTest
 *
 * @since  0.1.0
 *
 * @author Alain Schlesser <alain.schlesser@gmail.com>
 */
class SemanticVersionTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test fetching the single components for a version.
	 *
	 * @dataProvider components_data_provider
	 *
	 * @since        0.1.0
	 *
	 * @param string $version    Version to test.
	 * @param bool   $partial    Whether to accept partial versions.
	 * @param int    $major      Expected major version component.
	 * @param int    $minor      Expected minor version component.
	 * @param int    $patch      Expected path version component.
	 * @param string $prerelease Expected pre-release component.
	 * @param string $build      Expected build metadata component.
	 */
	public function test_partial_components( $version, $partial, $major, $minor, $patch, $prerelease, $build ) {
		$semver = new SemanticVersion( $version, $partial );
		$this->assertEquals( $major, $semver->get_major() );
		$this->assertEquals( $minor, $semver->get_minor() );
		$this->assertEquals( $patch, $semver->get_patch() );
		$this->assertEquals( $prerelease, $semver->get_pre_release() );
		$this->assertEquals( $build, $semver->get_build() );
	}

	/**
	 * Provide testable data to the test_partial_components() method.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function components_data_provider() {
		return array(
			// string $version, bool $partial, int $major, int $minor, int $patch, string $prerelease, string $build
			array( '1.2.3', true, 1, 2, 3, '', '' ),
			array( '1.2.3', false, 1, 2, 3, '', '' ),
			array( '1.2.3-alpha', true, 1, 2, 3, 'alpha', '' ),
			array( '1.2.3-alpha', false, 1, 2, 3, 'alpha', '' ),
			array( '1.2.3-alpha+20160101', true, 1, 2, 3, 'alpha', '20160101' ),
			array( '1.2.3-alpha+20160101', false, 1, 2, 3, 'alpha', '20160101' ),
			array( '1.2.3+20160101', true, 1, 2, 3, '', '20160101' ),
			array( '1.2.3+20160101', false, 1, 2, 3, '', '20160101' ),
			array( '1.2', true, 1, 2, 0, '', '' ),
			array( '1.2-alpha', true, 1, 2, 0, 'alpha', '' ),
			array( '1.2-alpha', false, 1, 2, 0, 'alpha', '' ),
			array( '1.2-alpha+20160101', true, 1, 2, 0, 'alpha', '20160101' ),
			array( '1.2-alpha+20160101', false, 1, 2, 0, 'alpha', '20160101' ),
			array( '1.2+20160101', true, 1, 2, 0, '', '20160101' ),
			array( '1.2+20160101', false, 1, 2, 0, '', '20160101' ),
			array( '1', true, 1, 0, 0, '', '' ),
			array( '1-alpha', true, 1, 0, 0, 'alpha', '' ),
			array( '1-alpha', false, 1, 0, 0, 'alpha', '' ),
			array( '1-alpha+20160101', true, 1, 0, 0, 'alpha', '20160101' ),
			array( '1-alpha+20160101', false, 1, 0, 0, 'alpha', '20160101' ),
			array( '1+20160101', true, 1, 0, 0, '', '20160101' ),
			array( '1+20160101', false, 1, 0, 0, '', '20160101' ),
			array( '11.22.33', true, 11, 22, 33, '', '' ),
			array( '11.22.33', false, 11, 22, 33, '', '' ),
			array( '111.222.333', true, 111, 222, 333, '', '' ),
			array( '111.222.333', false, 111, 222, 333, '', '' ),
			array( '0.0.1', true, 0, 0, 1, '', '' ),
			array( '0.0.1', false, 0, 0, 1, '', '' ),
			array( null, true, 0, 0, 0, '', '' ),
			array( null, false, 0, 0, 0, '', '' ),
		);
	}

	/**
	 * Test generating exceptions when working with invalid versions.
	 *
	 * @dataProvider exceptions_data_provider
	 *
	 * @since        0.1.0
	 *
	 * @param string $version Version to test.
	 * @param bool   $partial Whether to accept partial versions.
	 */
	public function test_exceptions( $version, $partial ) {
		$this->setExpectedException( 'RuntimeException' );
		$semver = new SemanticVersion( $version, $partial );
	}

	/**
	 * Provide testable data to the test_exceptions() method.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function exceptions_data_provider() {
		return array(
			// string $version, bool $partial
			array( 25, false ),
			array( 'nonsense', true ),
			array( 'nonsense', false ),
			array( '1.2.3.4', true ),
			array( '1.2.3.4', false ),
			array( '.1.2.3', true ),
			array( '.1.2.3', false ),
			array( '-alpha', true ),
			array( '-alpha', false ),
			array( '+20160101', true ),
			array( '+20160101', false ),
			array( '1.2', false ),
			array( '1', false ),
		);
	}
}

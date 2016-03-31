<?php
/**
 * SemanticVersionTest Class
 *
 * @package   brightnucleus/phpfeature
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

use PHPFeature_SemanticVersion as SemanticVersion;

/**
 * Class SemanticVersionTest
 *
 * @since  0.1.0
 *
 * @author Alain Schlesser <alain.schlesser@gmail.com>
 */
class SemanticVersionTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test fetching the single components for a version.
     *
     * @dataProvider componentsDataProvider
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
    public function testPartialComponents($version, $partial, $major, $minor, $patch, $prerelease, $build)
    {
        $semver = new SemanticVersion($version, $partial);
        $this->assertEquals($major, $semver->getMajor());
        $this->assertEquals($minor, $semver->getMinor());
        $this->assertEquals($patch, $semver->getPatch());
        $this->assertEquals($prerelease, $semver->getPreRelease());
        $this->assertEquals($build, $semver->getBuild());
    }

    /**
     * Provide testable data to the testPartialComponents() method.
     *
     * @since 0.1.0
     *
     * @return array
     */
    public function componentsDataProvider()
    {
        return array(
            // string $version, bool $partial, int $major, int $minor, int $patch, string $prerelease, string $build
            array('1.2.3', true, 1, 2, 3, '', ''),
            array('1.2.3', false, 1, 2, 3, '', ''),
            array('1.2.3-alpha', true, 1, 2, 3, 'alpha', ''),
            array('1.2.3-alpha', false, 1, 2, 3, 'alpha', ''),
            array('1.2.3-alpha+20160101', true, 1, 2, 3, 'alpha', '20160101'),
            array('1.2.3-alpha+20160101', false, 1, 2, 3, 'alpha', '20160101'),
            array('1.2.3+20160101', true, 1, 2, 3, '', '20160101'),
            array('1.2.3+20160101', false, 1, 2, 3, '', '20160101'),
            array('1.2', true, 1, 2, 0, '', ''),
            array('1.2-alpha', true, 1, 2, 0, 'alpha', ''),
            array('1.2-alpha', false, 1, 2, 0, 'alpha', ''),
            array('1.2-alpha+20160101', true, 1, 2, 0, 'alpha', '20160101'),
            array('1.2-alpha+20160101', false, 1, 2, 0, 'alpha', '20160101'),
            array('1.2+20160101', true, 1, 2, 0, '', '20160101'),
            array('1.2+20160101', false, 1, 2, 0, '', '20160101'),
            array('1', true, 1, 0, 0, '', ''),
            array('1-alpha', true, 1, 0, 0, 'alpha', ''),
            array('1-alpha', false, 1, 0, 0, 'alpha', ''),
            array('1-alpha+20160101', true, 1, 0, 0, 'alpha', '20160101'),
            array('1-alpha+20160101', false, 1, 0, 0, 'alpha', '20160101'),
            array('1+20160101', true, 1, 0, 0, '', '20160101'),
            array('1+20160101', false, 1, 0, 0, '', '20160101'),
            array('11.22.33', true, 11, 22, 33, '', ''),
            array('11.22.33', false, 11, 22, 33, '', ''),
            array('111.222.333', true, 111, 222, 333, '', ''),
            array('111.222.333', false, 111, 222, 333, '', ''),
            array('0.0.1', true, 0, 0, 1, '', ''),
            array('0.0.1', false, 0, 0, 1, '', ''),
            array(null, true, 0, 0, 0, '', ''),
            array(null, false, 0, 0, 0, '', ''),
        );
    }

    /**
     * Test generating exceptions when working with invalid versions.
     *
     * @dataProvider exceptionsDataProvider
     *
     * @since        0.1.0
     *
     * @param string $version Version to test.
     * @param bool   $partial Whether to accept partial versions.
     */
    public function testExceptions($version, $partial)
    {
        $this->setExpectedException('RuntimeException');
        new SemanticVersion($version, $partial);
    }

    /**
     * Provide testable data to the testExceptions() method.
     *
     * @since 0.1.0
     *
     * @return array
     */
    public function exceptionsDataProvider()
    {
        return array(
            // string $version, bool $partial
            array(25, false),
            array('nonsense', true),
            array('nonsense', false),
            array('1.2.3.4', true),
            array('1.2.3.4', false),
            array('.1.2.3', true),
            array('.1.2.3', false),
            array('-alpha', true),
            array('-alpha', false),
            array('+20160101', true),
            array('+20160101', false),
            array('1.2', false),
            array('1', false),
        );
    }
}

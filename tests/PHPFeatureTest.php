<?php
/**
 * PHPFeatureTest Class
 *
 * @package   phpfeature
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

/**
 * Class PHPFeatureTest
 *
 * @since  0.1.0
 *
 * @author Alain Schlesser <alain.schlesser@gmail.com>
 */
class PHPFeatureTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test supported features for specific PHP versions.
     *
     * @dataProvider featureSupportDataProvider
     *
     * @since        0.1.0
     *
     * @param string $features One or more features to test.
     * @param string $version  Version to test.
     * @param bool   $result   Expected result.
     */
    public function testFeatureSupport($features, $version, $result)
    {
        $php = new PHPFeature($version);
        $this->assertEquals($result, (bool)$php->isSupported($features));
    }

    /**
     * Provide testable data to the testFeatureSupport() method.
     *
     * @since 0.1.0
     *
     * @return array
     */
    public function featureSupportDataProvider()
    {
        return array(
            // string $features, string $version, bool $result
            array('namespaces', '5', false),
            array('namespaces', '5.0.0', false),
            array('namespaces', '5.1.0', false),
            array('namespaces', '5.2', false),
            array('namespaces', '5.2.0', false),
            array('namespaces', '5.2.9', false),
            array('namespaces', '5.2.9-beta', false),
            array('namespaces', '5.2.9+20160101', false),
            array('namespaces', '5.2.9-beta+20160101', false),
            array('namespaces', '5.3.0-beta', false),
            array('namespaces', '5.3.0-beta+20160101', false),
            array('namespaces', '5.3.0+20160101', true),
            array('namespaces', '5.3', true),
            array('namespaces', '5.3.0', true),
            array('namespaces', '5.3.1', true),
            array('namespaces', '5.4.0', true),
            array('namespaces', '5.5.0', true),
            array('namespaces', '5.6.0', true),
            array('nonsense', '5.6.0', false),
            array('traits', '5.3.0', false),
            array('traits', '5.4.0', true),
            array(array('namespaces', 'traits'), '5.3.0', false),
            array(array('namespaces', 'traits'), '5.4.0', true),
        );
    }

    /**
     * Test minimum required for specific PHP versions.
     *
     * @dataProvider minimumRequiredDataProvider
     *
     * @since        0.1.0
     *
     * @param string          $features One or more features to test.
     * @param string          $version  Version to test.
     * @param SemanticVersion $result   Expected result.
     */
    public function testMinimumRequired($features, $version, $result)
    {
        $php = new PHPFeature($version);
        $this->assertEquals($result, $php->getMinimumRequired($features));
    }

    /**
     * Provide testable data to the testMinimumRequired() method.
     *
     * @since 0.1.0
     *
     * @return array
     */
    public function minimumRequiredDataProvider()
    {
        return array(
            // string $features, string $version, SemanticVersion $result
            array('namespaces', '5', new SemanticVersion('5.3.0')),
            array('namespaces', '5.0.0', new SemanticVersion('5.3.0')),
            array('namespaces', '5.1.0', new SemanticVersion('5.3.0')),
            array('namespaces', '5.2', new SemanticVersion('5.3.0')),
            array('namespaces', '5.2.0', new SemanticVersion('5.3.0')),
            array('namespaces', '5.2.9', new SemanticVersion('5.3.0')),
            array('namespaces', '5.2.9-beta', new SemanticVersion('5.3.0')),
            array('namespaces', '5.2.9+20160101', new SemanticVersion('5.3.0')),
            array('namespaces', '5.2.9-beta+20160101', new SemanticVersion('5.3.0')),
            array('namespaces', '5.3.0-beta', new SemanticVersion('5.3.0')),
            array('namespaces', '5.3.0-beta+20160101', new SemanticVersion('5.3.0')),
            array('namespaces', '5.3.0+20160101', new SemanticVersion('5.3.0')),
            array('namespaces', '5.3', new SemanticVersion('5.3.0')),
            array('namespaces', '5.3.0', new SemanticVersion('5.3.0')),
            array('namespaces', '5.3.1', new SemanticVersion('5.3.0')),
            array('namespaces', '5.4.0', new SemanticVersion('5.3.0')),
            array('namespaces', '5.5.0', new SemanticVersion('5.3.0')),
            array('namespaces', '5.6.0', new SemanticVersion('5.3.0')),
            array('nonsense', '5.6.0', false),
            array('traits', '5.3.0', new SemanticVersion('5.4.0')),
            array('traits', '5.4.0', new SemanticVersion('5.4.0')),
            array(array('namespaces', 'traits'), '5.3.0', new SemanticVersion('5.4.0')),
            array(array('namespaces', 'traits'), '5.4.0', new SemanticVersion('5.4.0')),
        );
    }
}

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
class PHPFeature implements FeatureInterface
{

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
     * @param SemanticVersion|string|int|null $phpVersion  Version of PHP to
     *                                                     check the features
     *                                                     for.
     * @param ConfigInterface|null            $config      Configuration that
     *                                                     contains the known
     *                                                     features.
     * @throws RuntimeException If the PHP version could not be validated.
     */
    public function __construct($phpVersion = null, ConfigInterface $config = null)
    {

        // TODO: Better way to bootstrap this while still allowing DI?
        if ( ! $config) {
            $config = new Config(include(__DIR__ . '/../config/known_features.php'));
        }

        $this->config = $config;

        if (null === $phpVersion) {
            $phpVersion = phpversion();
        }

        if (is_integer($phpVersion)) {
            $phpVersion = (string)$phpVersion;
        }

        if (is_string($phpVersion)) {
            $phpVersion = new SemanticVersion($phpVersion, true);
        }

        $this->version = $phpVersion;
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
    public function isSupported($features)
    {

        if (is_string($features)) {
            $features = array($features);
        }

        if ( ! is_array($features)) {
            throw new InvalidArgumentException(sprintf(
                'Wrong type of argument passed in to is_supported(): "%1$s".',
                gettype($features)
            ));
        }

        $isSupported = true;

        while ($isSupported && count($features) > 0) {
            $feature = array_pop($features);
            $isSupported &= (bool)$this->checkSupport($feature);
        }

        return (bool)$isSupported;
    }

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
     * @param string|array $features    What features to check the support of.
     * @return SemanticVersion|bool
     * @throws InvalidArgumentException If the wrong type of argument is passed
     *                                  in.
     * @throws RuntimeException         If a requirement could not be parsed.
     */
    public function getMinimumRequired($features)
    {

        if (is_string($features)) {
            $features = array($features);
        }

        if ( ! is_array($features)) {
            throw new InvalidArgumentException(sprintf(
                'Wrong type of argument passed in to get_minimum_required(): "%1$s".',
                gettype($features)
            ));
        }

        $minimumRequired = '0.0.0';
        $isSupported     = true;

        while (count($features) > 0) {
            $feature = array_pop($features);
            $isSupported &= (bool)$this->checkSupport($feature, $minimumRequired);
        }

        return $minimumRequired !== '0.0.0' ? new SemanticVersion($minimumRequired, true) : false;
    }

    /**
     * Check whether a single feature is supported.
     *
     * @since 0.1.0
     *
     * @param string $feature         The feature to check.
     * @param string $minimumRequired Optional. Minimum required version that
     *                                supports all features.
     * @return bool
     * @throws RuntimeException If the requirement could not be parsed.
     */
    protected function checkSupport($feature, &$minimumRequired = null)
    {

        if ( ! $this->config->hasKey($feature)) {
            return false;
        }

        $requirements = $this->config->getKey($feature);

        if ( ! is_array($requirements)) {
            $requirements = array($requirements);
        }

        $isSupported = true;

        while (($isSupported || $minimumRequired) && count($requirements) > 0) {
            $requirement = array_pop($requirements);
            $isSupported &= (bool)$this->checkRequirement($requirement, $minimumRequired);
        }

        return (bool)$isSupported;
    }

    /**
     * Check whether a single requirement is met.
     *
     * @since 0.1.0
     *
     * @param string      $requirement     A requirement that is composed of an
     *                                     operator and a version milestone.
     * @param string|null $minimumRequired Optional. Minimum required version that
     *                                     supports all features.
     * @return bool
     * @throws RuntimeException If the requirement could not be parsed.
     */
    protected function checkRequirement($requirement, &$minimumRequired = null)
    {

        $requirement = trim($requirement);
        $pattern     = self::COMPARISON_PATTERN;

        $arguments = array();
        $result    = preg_match($pattern, $requirement, $arguments);

        if ( ! $result || ! isset($arguments[1]) || ! isset($arguments[2])) {
            throw new RuntimeException(sprintf(
                'Could not parse the requirement "%1$s".',
                (string)$requirement
            ));
        }

        $operator  = isset($arguments[1]) ? (string)$arguments[1] : '>=';
        $milestone = isset($arguments[2]) ? (string)$arguments[2] : '0.0.0';

        $isSupported = (bool)version_compare($this->version->getVersion(), $milestone, $operator);

        if (null !== $minimumRequired) {
            $requiredVersion = $this->getRequiredVersion($milestone, $operator);
            if (version_compare($requiredVersion, $minimumRequired, '>')) {
                $minimumRequired = $requiredVersion;
            }
        }

        return $isSupported;
    }

    /**
     * Get the required version for a single requirement.
     *
     * @since 0.2.0
     *
     * @param string $milestone A version milestone that is used to define the
     *                          requirement.
     * @param string $operator  An operator that gets applied to the milestone.
     *                          Possible values: '<=', 'lt', '<', 'le', '>=',
     *                          'gt', '>', 'ge', '=', '==', 'eq', '!=', '<>',
     *                          'ne'
     * @return string
     */
    protected function getRequiredVersion($milestone, $operator)
    {

        // TODO: Algorithm is still missing, the `$operator` is simply ignored
        // and the pure `$milestone` is returned.
        return $milestone;
    }
}

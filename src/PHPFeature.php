<?php
/**
 * PHPFeature Class.
 *
 * @package   brightnucleus/phpfeature
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @license   MIT
 * @link      http://www.brightnucleus.com/
 * @copyright 2016 Alain Schlesser, Bright Nucleus
 */

use BrightNucleus_Config as Config;
use BrightNucleus_ConfigInterface as ConfigInterface;
use PHPFeature_SemanticVersion as SemanticVersion;

/**
 * Class PHPFeature.
 *
 * The main PHP Feature implementation of the `FeatureInterface`.
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
     * Reference to the PHP releases.
     *
     * @since 0.2.4
     *
     * @var PHPReleases
     */
    protected $releases;

    /**
     * Instantiate a PHPFeature object.
     *
     * @since 0.1.0
     *
     * @param SemanticVersion|string|int|null $phpVersion Version of PHP to check the features for.
     * @param ConfigInterface|null            $config     Configuration that contains the known features.
     *
     * @throws RuntimeException If the PHP version could not be validated.
     */
    public function __construct($phpVersion = null, ConfigInterface $config = null)
    {

        // TODO: Better way to bootstrap this while still allowing DI?
        if ( ! $config) {
            $config = new Config(include(dirname(__FILE__) . '/../config/known_features.php'));
        }

        $this->config = $config;

        if (null === $phpVersion) {
            $phpVersion = phpversion();
        }

        if (is_int($phpVersion)) {
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
     * Accepts either a string or an array of strings. Returns true if all the passed-in features are supported, or
     * false if at least one of them is not.
     *
     * @since 0.1.0
     *
     * @param string|array $features What features to check the support of.
     *
     * @return bool Whether the set of features as a whole is supported.
     * @throws InvalidArgumentException If the wrong type of argument is passed in.
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
     * Get the minimum required version that supports all of the requested features.
     *
     * Accepts either a string or an array of strings. Returns a SemanticVersion object for the version number that is
     * known to support all the passed-in features, or false if at least one of them is not supported by any known
     * version.
     *
     * @since 0.2.0
     *
     * @param string|array $features What features to check the support of.
     *
     * @return SemanticVersion|false SemanticVersion object for the version number that is known to support all the
     *                               passed-in features, false if none.
     * @throws InvalidArgumentException If the wrong type of argument is passed in.
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
     * @param string      $feature         The feature to check.
     * @param string|null $minimumRequired Optional. Minimum required version that supports all features.
     *
     * @return bool Whether the requested feature is supported.
     * @throws RuntimeException If the requirement could not be parsed.
     */
    protected function checkSupport($feature, &$minimumRequired = null)
    {

        if ( ! $this->config->hasKey($feature)) {
            return false;
        }

        $requirements = (array)$this->config->getKey($feature);

        $isSupported = true;

        while (($isSupported || null !== $minimumRequired) && count($requirements) > 0) {
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
     * @param string      $requirement     A requirement that is composed of an operator and a version milestone.
     * @param string|null $minimumRequired Optional. Minimum required version that supports all features.
     *
     * @return bool Whether the requirement is met.
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
     * @todo  The entire algorithm is only an approximation. A 5.2 SemVer library is needed.
     *
     * @since 0.2.0
     *
     * @param string $milestone A version milestone that is used to define the requirement.
     * @param string $operator  An operator that gets applied to the milestone.
     *                          Possible values: '<=', 'lt', '<', 'le', '>=', 'gt', '>', 'ge', '=', '==', 'eq', '!=',
     *                          '<>', 'ne'
     *
     * @return string Version string that meets a single requirement.
     * @throws RuntimeException If the requirement could not be satisfied.
     * @throws RuntimeException If the NotEqual is used.
     */
    protected function getRequiredVersion($milestone, $operator)
    {
        if (null === $this->releases) {
            $this->releases = new PHPReleases();
        }

        switch ($operator) {
            case '>':
            case 'gt':
                return $this->getGreaterThanVersion($milestone);
            case '<':
            case 'lt':
                return $this->getLesserThanVersion($milestone);
            case '>=':
            case 'ge':
                return $this->getGreaterEqualVersion($milestone);
            case '<=':
            case 'le':
                return $this->getLesserEqualVersion($milestone);
            case '!=':
            case '<>':
            case 'ne':
                throw new RuntimeException('NotEqual operator is not implemented.');
        }

        return $milestone;
    }

    /**
     * Get a version greater than the milestone.
     *
     * @since 0.2.4
     *
     * @param string $milestone A version milestone that is used to define the requirement.
     *
     * @return string Version number that meets the requirement.
     * @throws RuntimeException If the requirement could not be satisfied.
     */
    protected function getGreaterThanVersion($milestone)
    {
        $data = $this->releases->getAll();
        foreach ($data as $version => $date) {
            if (version_compare($version, $milestone, '>')) {
                return $version;
            }
        }

        throw new RuntimeException('Could not satisfy version requirements.');
    }

    /**
     * Get a version lesser than the milestone.
     *
     * @since 0.2.4
     *
     * @param string $milestone A version milestone that is used to define the requirement.
     *
     * @return string Version number that meets the requirement.
     * @throws RuntimeException If the requirement could not be satisfied.
     */
    protected function getLesserThanVersion($milestone)
    {
        if (version_compare($this->version->getVersion(), $milestone, '<')) {
            return $this->version->getVersion();
        }
        $data = array_reverse($this->releases->getAll());
        foreach ($data as $version => $date) {
            if (version_compare($version, $milestone, '<')) {
                return $version;
            }
        }

        throw new RuntimeException('Could not satisfy version requirements.');
    }

    /**
     * Get a version greater or equal than the milestone.
     *
     * @since 0.2.4
     *
     * @param string $milestone A version milestone that is used to define the requirement.
     *
     * @return string Version number that meets the requirement.
     */
    protected function getGreaterEqualVersion($milestone)
    {
        if ($this->releases->exists($milestone)) {
            return $milestone;
        }

        $data = $this->releases->getAll();
        foreach ($data as $version => $date) {
            if (version_compare($version, $milestone, '>=')) {
                return $version;
            }
        }

        throw new RuntimeException('Could not satisfy version requirements.');
    }

    /**
     * Get a version lesser or equal than the milestone.
     *
     * @since 0.2.4
     *
     * @param string $milestone A version milestone that is used to define the requirement.
     *
     * @return string Version number that meets the requirement.
     */
    protected function getLesserEqualVersion($milestone)
    {
        if (version_compare($this->version->getVersion(), $milestone, '<=')) {
            return $this->version->getVersion();
        }

        $data = array_reverse($this->releases->getAll());
        foreach ($data as $version => $date) {
            if (version_compare($version, $milestone, '<=')) {
                return $version;
            }
        }

        throw new RuntimeException('Could not satisfy version requirements.');
    }
}

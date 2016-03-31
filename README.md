# PHPFeature

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/brightnucleus/phpfeature/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/brightnucleus/phpfeature/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/brightnucleus/phpfeature/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/brightnucleus/phpfeature/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/brightnucleus/phpfeature/badges/build.png?b=master)](https://scrutinizer-ci.com/g/brightnucleus/phpfeature/build-status/master)
[![Codacy Badge](https://api.codacy.com/project/badge/grade/2fbc26a45380443c804ef8de5ba07f33)](https://www.codacy.com/app/BrightNucleus/phpfeature)
[![Code Climate](https://codeclimate.com/github/brightnucleus/phpfeature/badges/gpa.svg)](https://codeclimate.com/github/brightnucleus/phpfeature)

[![Latest Stable Version](https://poser.pugx.org/brightnucleus/phpfeature/v/stable)](https://packagist.org/packages/brightnucleus/phpfeature)
[![Total Downloads](https://poser.pugx.org/brightnucleus/phpfeature/downloads)](https://packagist.org/packages/brightnucleus/phpfeature)
[![Latest Unstable Version](https://poser.pugx.org/brightnucleus/phpfeature/v/unstable)](https://packagist.org/packages/brightnucleus/phpfeature)
[![License](https://poser.pugx.org/brightnucleus/phpfeature/license)](https://packagist.org/packages/brightnucleus/phpfeature)

PHPFeature is a first draft of a PHP Feature Detection Library similar to what Modernizr does for the browser features. So, instead of checking for a specific PHP version number (which forces you to know which and compare which features were introduced by which versions), you can simply tell the library what features you need, and it will tell you with a simple boolean value whether these are supported or not.

You can read more background information here: http://www.alainschlesser.com/php-feature/

## Basic Usage

To include the library in your project, you can use Composer:

```BASH
composer require brightnucleus/phpfeature
```

Alternatively, you can copy the classes inside of your application and make sure that your application can find them.

Usage is pretty simple, it only comes with two methods so far.

```PHP
public function is_supported( $features );
```

This returns a boolean value telling you whether all of the features that you passed in are supported by the current PHP version.

```PHP
public function get_minimum_required( $features );
```

This returns a SemanticVersion object that contains the minimum PHP version that supports all of the features that you passed in.

Here’s an example of how to use it to stop execution and inform the user:

```PHP
// Array of strings to define what features you need.
$features = array( 'namespaces', 'traits' );

// Instantiate the PHPFeature library.
// When you don't provide a version number as the first argument,
// the version of the currently used PHP interpreter is fetched.
$php = new PHPFeature();

// Check whether all of the features are supported. If not...
if ( ! $php->is_supported( $features ) ) {

    // ... throw exception and let user know the minimum needed.
    throw new RuntimeException( sprintf(
        'Your PHP interpreter does not support some features needed to run this application. Please upgrade to version %1$s or newer.',
        $php->get_minimum_required( $features )
    ) );
}
```

## Known Issues

* The library currently uses PHP's `version_compare()` to make the actual comparison. This should probably be done by a real SemVer comparison algorithm.

* The list of features is not yet exhaustive. There should also be some guidelines to know how these are named.

* The algorithm behind `get_minimum_required()` is still missing, it only fetches bare version. For a requirement like '>5.4.2', this will return incorrect values.

* The required PHP version to use the library is currently at v5.3.2, because of Composer. This should be lowered to 5.2 at least, so that WordPress projects can reliably use the library.

## License

This code is released under the MIT license. For the full copyright and license information, please view the LICENSE file distributed with this source code.

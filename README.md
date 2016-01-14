# PHPFeature

PHPFeature is a first draft of a PHP Feature Detection Library similar to what Modernizr does for the browser features. So, instead of checking for a specific PHP version number (which forces you to know which and compare which features were introduced by which versions), you can simply tell the library what features you need, and it will tell you with a simple boolean value whether these are supported or not.

## Basic Usage

```PHP
$features = array( 'namespaces', 'traits' );

$php = new PHPFeature();

if ( ! $php->is_supported( $features ) ) {
	throw new RuntimeException( sprintf(
		'Your PHP interpreter does not support some features needed to run this application. Please upgrade to version %1$s or newer.',
		$php->get_minimum_required( $features )
	) );
}
```

## Known Issues

* The library currently uses PHP's `version_compare()` to make the actual comparison. This should probably be done by a real SemVer comparison algorithm.

* The list of features is not yet exhaustive. There should also be some guidelines to know how these are named.

* The library should provide a function that returns the minimum version that supports all the requested features.

* The algorithm behind `get_minimum_required()` is still missing, it only fetches bare version. For a requirement like '>5.4.2', this will return incorrect values.

* The required PHP version to use the library is currently at v5.3.2, because of Composer. This should be lowered to 5.2 at least, so that WordPress projects can reliably use the library.
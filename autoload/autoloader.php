<?php
/**
 * NU Standard Autoloader Boilerplate.
 * Generally no need to modify anything here aside from the $class_name comparison.
 *
 * @package NUEDU_Trustpilot\Autoload
 */

/**
 * Dynamically loads the class attempting to be instantiated elsewhere in the
 * plugin by looking at the $class_name parameter being passed as an argument.
 *
 * The argument should be in the form: NUEDU_Trustpilot\Namespace. The
 * function will then break the fully-qualified class name into its pieces and
 * will then build a file to the path based on the namespace.
 *
 * The namespaces in this plugin map to the paths in the directory structure.
 *
 * @param string $class_name The fully-qualified name of the class to load.
 */
spl_autoload_register( function( $class_name ) {
	// If the specified $class_name does not include our namespace, GTFO.
	if ( false === strpos( $class_name, 'NUEDU_Trustpilot' ) ) {
		return;
	}

	// Split the class name into an array to read the namespace and class.
	$file_parts = explode( '\\', $class_name );

	// Do a reverse loop through $file_parts to build the path to the file.
	$namespace = '';
	for ( $i = count( $file_parts ) - 1; $i > 0; $i-- ) {
		// Read the current component of the file part.
		$current = str_replace( '_', '-', strtolower( $file_parts[ $i ] ) );

		// If we're at the first entry, then we're at the filename.
		if ( count( $file_parts ) - 1 === $i ) {
			$file_name = "class-$current.php";
		} else {
			$namespace = '/' . $current . $namespace;
		}
	}

	// Now build a path to the file using mapping to the file location.
	$filepath  = trailingslashit( dirname( dirname( __FILE__ ) ) . $namespace );
	$filepath .= $file_name;

	// If the file exists in the specified path, then include it.
	if ( file_exists( $filepath ) ) {
		include_once $filepath; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
	} elseif ( function_exists( 'write_to_log' ) ) {
		write_to_log( "The file attempting to be loaded at $filepath does not exist." );
	}
} );

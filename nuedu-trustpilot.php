<?php
/**
 * Plugin Name: NU.edu Trustpilot Integration
 * Description: Manages NU's various integrations with the Trustpilot platform & related functionality.
 * Version: 1.0.0
 * Author: Dan Scott
 *
 * Key features/functionality:
 *   - Custom REST endpoint to allow custom email encryption links
 *   - @TODO [Manage Trustpilot reviews widget visiblity across various pages] - migrate to this plugin & expand functionality
 *
 *
 * Test API Path:
 * .../xyz/xyz/?firstname=john&lastname=doe&clientnumber=12345
 * (Note: On live site, we will set up a vanity URL to redirect to the above path so that we're not linking directly to the Wordpress /wp-json/ folder path)
 *
 * @package national-university
 */

namespace NUEDU_Trustpilot;

if ( ! defined( 'WPINC' ) ) {
	die( 'Nope' );
}

define( 'NUEDU_TRUSTPILOT_FUNC_PATH', plugin_dir_path( __FILE__ ) );
define( 'NUEDU_TRUSTPILOT_FUNC_URL', plugin_dir_url( __FILE__ ) );

register_activation_hook( __FILE__, function() {

	/**
	 * No required functionality on plugin activation,
	 * but leaving this here for now as a placeholder, just in case we need it later:
	 * require_once NUEDU_TRUSTPILOT_FUNC_PATH . 'inc/class-activation.php';
	 * new Inc\Activation();
	 */

} );

use NUEDU_Trustpilot\Autoload\Init;

// NU's standard plugin file autoloader/initializer.
add_action( 'plugins_loaded', function() {
	require_once NUEDU_TRUSTPILOT_FUNC_PATH . 'autoload/autoloader.php'; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
	new Init();
} );

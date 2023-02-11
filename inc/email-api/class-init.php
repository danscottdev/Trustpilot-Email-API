<?php
/**
 * Initialize our API used for Trustpilot email links.
 *
 * @category   Class
 * @package    nuedu-trustpilot
 * @subpackage email-api
 */

namespace NUEDU_Trustpilot\Inc\Email_API;

/** Init */
class Init {
	/**
	 * Instance of this class
	 *
	 * @var boolean
	 */
	public static $instance = false;

	/** Construct */
	public function __construct() {
		new Admin();
		new REST_API();
	}

	/** Singleton */
	public static function singleton() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

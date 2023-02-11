<?php
/**
 * Trustpilot Email - REST API
 *
 * NU sends out emails to current students, prompting them to leave a review on Trustpilot.
 * Email includes personalized link. Something like https://nu.edu/vanity-url?params
 * This link will redirect to the endpoint below, which will process the ?params and generate an encrypted Trustpilot URL
 * User will then be redirected to that URL.
 *
 * A SUCCESSFUL sequence will result in the user clicking on the link that's emailed to them (nu.edu/vanity-url?params)
 * and then being redirected to a Trustpilot "submit a review" page, with encrypted url params (trustpilot.com/evaluate-bgl/www.nu.edu?p=[ENCRYPTED_PARAMS_HERE])
 * This will allow them to submit a review for NU without having to supply their personal info/signup for a trustpilot account. Or something like that.
 */

namespace NUEDU_Trustpilot\Inc\Email_API;

/** REST API class */
class REST_API {

	/**
	 * Encryption Key (PROVIDED BY TRUSTPILOT REP)
	 * Stored via wp-admin menu (Trustpilot -> Settings)
	 *
	 * @var string
	 */
	private $encrypt_key;

	/**
	 * Auth Key (PROVIDED BY TRUSTPILOT REP)
	 * Stored via wp-admin menu (Trustpilot -> Settings)
	 *
	 * @var string
	 */
	private $auth_key;

	/**
	 * API Base URL (PROVIDED BY TRUSTPILOT REP)
	 * Stored via wp-admin menu (Trustpilot -> Settings)
	 *
	 * @var string
	 */
	private $base_url;

	/**
	 * URL Parameters
	 * Will be grabbed from the current URL string
	 *
	 * @var array
	 */
	private $url_params;

	/**
	 * Trustpilot API Payload
	 * Data that will be sent to Trustpilot's API endpoint
	 *
	 * @var array
	 */
	private $payload;

	/** Construct */
	public function __construct() {

		if ( ! get_option( 'nuedu_trustpilot_email_api_is_enabled' ) ) {
			// If we don't have this enabled, we can just stop now.
			return;
		}

		add_action( 'init', [ $this, 'trustpilot_fetch_settings' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_api_route' ] );
	}

	/**
	 * Trustpilot Fetch Settings
	 *
	 * Retrieves mission-critical info for trustpilot API from the WordPress Settings API
	 * These values are stored via custom settings page created in class-admin.php
	 *
	 * @return void
	 */
	public function trustpilot_fetch_settings() {
		$this->encrypt_key = get_option( 'nuedu_trustpilot_email_api_encryption_key' );
		$this->auth_key    = get_option( 'nuedu_trustpilot_email_api_auth_key' );
		$this->base_url    = get_option( 'nuedu_trustpilot_email_api_base_url' );

		if ( ! isset( $_GET['firstname'] ) || ! isset( $_GET['lastname'] ) || ! isset( $_GET['clientnumber'] ) || ! isset( $_GET['email'] ) ) { //phpcs:ignore
			// If any of our URL parameters are somehow not set, abort.
			return;
		} else {

			$this->url_params = [
				'firstname'    => esc_html( $_GET['firstname'] ),
				'lastname'     => esc_html( $_GET['lastname'] ),
				'email'        => esc_html( $_GET['email'] ),
				'clientnumber' => esc_html( $_GET['clientnumber'] ),
			];

			$this->payload = [
				'email' => $this->url_params['email'],
				'name'  => ( $this->url_params['firstname'] . ' ' . $this->url_params['lastname'] ),
				'ref'   => $this->url_params['clientnumber'],
			];

		}

	}

	/**
	 * Register REST API Route
	 *
	 * Production URL: //omit for portfolio
	 * Local Dev URL: //omit for portfolio
	 *
	 * Important! All parameters must be supplied, otherwise Trustpilot's encryption will not work.
	 */
	public function register_rest_api_route() {

		register_rest_route(
			'xyz',
			'/xyz',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,              // "GET" method.
					'callback'            => [ $this, 'do_trustpilot_email_api' ],
					'args'                => [],
					'permission_callback' => '__return_true',
				],
			]
		);
	}

	/**
	 * Do Trustpilot... stuff
	 *
	 * Process the URL params passed in as args (via script provided from Trustpilot),
	 * Generate a new Trustpilot URL,
	 * Then redirect user to that.
	 *
	 * Reference: vendor folder. Example code snippets provided by Trustpilot Rep.
	 *
	 * @param WP_REST_Request $request WP_REST_Request object containing request data.
	 * @return WP_REST_Response WP_REST_Response object containing response data.
	 */
	public function do_trustpilot_email_api( $request = null ) {

		try {

			// Script provided by Trustpilot Rep.
			require_once 'vendor/authenticatedencryption.php';

			$encrypt_key   = base64_decode( $this->encrypt_key );
			$auth_key      = base64_decode( $this->auth_key );
			$final_payload = json_encode( $this->payload );

			$trustpilot = new \Trustpilot();

			$encrypted_data = $trustpilot->{'encryptPayload'}( $final_payload, $encrypt_key, $auth_key );

			$trustpilot_invitation_link = $this->base_url . $encrypted_data;

			// redirect user to encrypted link.
			header( 'Location:' . $trustpilot_invitation_link );

		} catch ( \Exception $e ) {

			// Log error if something effs up.
			return new \WP_REST_Response( $e->getMessage(), 400 );
		}
	}

}

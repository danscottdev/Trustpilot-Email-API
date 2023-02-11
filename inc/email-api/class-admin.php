<?php
/**
 * Create WP-ADMIN page to allow us to configure Trustpilot email functionality.
 * 
 * Leaving this in the email-api/ folder for ease of development now. 
 * But as we migrate other Trustpilot functionality to this plugin, we should bump this file higher up in the file structure 
 *   so that it applies to all aspects of our Trustpilot integration, not just the email API.
 * 
 * Current configuration fields:
 * - Enable Trustpilot? (Y/N)
 * - Trustpilot API Encryption Key
 * - Trustpilot API Auth Key
 * - Trustpilot API Base URL
 */

namespace NUEDU_Trustpilot\Inc\Email_API;

/** Add Admin Settings Page */
class Admin {

	/** 
	 * Page slug used for this menu page. Breaking it out here because it makes the menu IDs/slugs easier to keep straight.
	 * 
	 * @var string 
	 */
	protected $page_slug = 'nuedu-trustpilot';

	/** Construct */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_admin_trustpilot_menu' ] );
		add_action( 'admin_init', [ $this, 'add_trustpilot_settings' ] );
	}

	/** Add custom menu page to wp-admin */
	public function add_admin_trustpilot_menu() {
		add_menu_page( 
			'Trustpilot Settings',                         // Page Title.
			'Global Trustpilot',                           // Menu Title.
			'manage_options',                              // user capability.
			$this->page_slug . '-page',                    // Menu Slug.
			[ $this, 'render_trustpilot_settings_page' ],  // Callback fn.
			'dashicons-megaphone',                         // Menu icon.
			99                                             // Menu position.
		);
	}

	/** Render admin page */
	public function render_trustpilot_settings_page() {

		// Make sure only 'admin' user roles can access.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// WP Native functionality to add 'admin messages' to top of page, for success/error feedback.
		if ( isset( $_GET['settings-updated'] ) ) { //phpcs:ignore
			add_settings_error( 'trustpilot_messages', 'trustpilot_message', __( 'Settings Saved', 'trustpilot' ), 'updated' );
		}

		settings_errors( 'trustpilot_messages' );
		?>

		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form action="options.php" method="post">

				<?php
					settings_fields( $this->page_slug . '-option-group' );
					do_settings_sections( $this->page_slug . '-option-group' );
					submit_button( 'Save Trustpilot Settings' );
				?>

			</form>
		</div>

		<?php
	}


	/**
	 * Register settings via WP Settings API
	 * 
	 * Settings needed for Email API:
	 * - Encryption Key
	 * - Auth Key
	 * - Base URL
	 */
	public function add_trustpilot_settings() {

		register_setting(
			$this->page_slug . '-option-group',
			'nuedu_trustpilot_email_api_is_enabled'
		);

		register_setting( 
			$this->page_slug . '-option-group',
			'nuedu_trustpilot_email_api_encryption_key'
		);

		register_setting( 
			$this->page_slug . '-option-group',
			'nuedu_trustpilot_email_api_auth_key'
		);

		register_setting( 
			$this->page_slug . '-option-group',
			'nuedu_trustpilot_email_api_base_url'
		);

		add_settings_section(
			$this->page_slug . '-section',
			'Trustpilot Settings Section',
			null,
			$this->page_slug . '-option-group'
		);

		add_settings_field(
			'nuedu_trustpilot_email_api_is_enabled',
			'Enable Trustpilot Email API?',
			[ $this, 'nuedu_trustpilot_email_api_is_enabled' ],
			$this->page_slug . '-option-group',
			$this->page_slug . '-section',
		);

		add_settings_field(
			'nuedu_trustpilot_email_api_encryption_key',
			'Trustpilot API Encryption Key',
			[ $this, 'nuedu_trustpilot_email_api_encryption_key_cb' ],
			$this->page_slug . '-option-group',
			$this->page_slug . '-section',
		);

		add_settings_field(
			'nuedu_trustpilot_email_api_auth_key',
			'Trustpilot API Auth Key',
			[ $this, 'nuedu_trustpilot_email_api_auth_key_cb' ],
			$this->page_slug . '-option-group',
			$this->page_slug . '-section',
		);

		add_settings_field(
			'nuedu_trustpilot_email_api_base_url',
			'Trustpilot API Base URL',
			[ $this, 'nuedu_trustpilot_email_api_base_url_cb' ],
			$this->page_slug . '-option-group',
			$this->page_slug . '-section',
		);
	}

	/** Enable/Disable Global Trustpilot */
	public function nuedu_trustpilot_email_api_is_enabled() {
		?>
			<input type="checkbox" name="nuedu_trustpilot_email_api_is_enabled" value="1" <?php checked( 1, get_option( 'nuedu_trustpilot_email_api_is_enabled' ), true ); ?> />
		<?php
	}

	/** Render input field for Encryption Key */
	public function nuedu_trustpilot_email_api_encryption_key_cb() {
		?>
			<input name="nuedu_trustpilot_email_api_encryption_key" value="<?php echo esc_attr( get_option( 'nuedu_trustpilot_email_api_encryption_key' ) ); ?>" /><br/>
			<em>From Trustpilot platform.</em>
		<?php
	}

	/** Render input field for Auth Key */
	public function nuedu_trustpilot_email_api_auth_key_cb() {
		?>
			<input name="nuedu_trustpilot_email_api_auth_key" value="<?php echo esc_attr( get_option( 'nuedu_trustpilot_email_api_auth_key' ) ); ?>" /><br/>
			<em>From Trustpilot platform.</em>
		<?php
	}

	/** Render input field for Base URL */
	public function nuedu_trustpilot_email_api_base_url_cb() {
		?>
			<input name="nuedu_trustpilot_email_api_base_url" value="<?php echo esc_attr( get_option( 'nuedu_trustpilot_email_api_base_url' ) ); ?>" /><br/>
			<em>From Trustpilot platform.</em>
		<?php
	}

}

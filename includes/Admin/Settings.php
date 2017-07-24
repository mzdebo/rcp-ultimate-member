<?php

namespace RCP_UM\Admin;

use RCP_UM\Init as RCP_UM;

class Settings {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of \RCP_UM\Settings
	 *
	 * @return Settings
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof Settings ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add Hooks and Actions
	 */
	protected function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ), 50 );
		add_action( 'admin_menu', array( $this, 'admin_menu'        ), 50 );
		add_action( 'admin_notices', array( $this, 'license_admin_notice' ) );
	}

	/**
	 * Register the settings
	 *
	 * @access      public
	 * @since       1.0.0
	 */
	public function register_settings() {
		register_setting( 'rcpum_settings_group', 'rcpum_license_key', array( $this, 'sanitize_license' ) );
	}

	/**
	 * Add the menu item
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function admin_menu() {
		add_submenu_page( 'rcp-members', __( 'Ultimate Member Settings', 'rcp-ultimate-member' ), __( 'Ultimate Member', 'rcp-ultimate-member' ), 'manage_options', rcp_ultimate_member()->get_id(), array( $this, 'settings_page' ) );
	}

	public function settings_page() {
		$license  = get_option( 'rcpum_license_key', '' );
		$status   = get_option( 'rcpum_license_status', '' );

		if ( isset( $_REQUEST['updated'] ) && $_REQUEST['updated'] !== false ) : ?>
			<div class="updated fade"><p><strong><?php _e( 'Options saved', 'rcp-ultimate-member' ); ?></strong></p></div>
		<?php endif; ?>

		<div class="rcpbp-wrap">

			<h2 class="rcpbp-settings-title"><?php echo esc_html( get_admin_page_title() ); ?></h2><hr>

			<form method="post" action="options.php" class="rcp_options_form">
				<?php settings_fields( 'rcpum_settings_group' ); ?>

				<table class="form-table">
					<tr>
						<th>
							<label for="rcpum_license_key"><?php _e( 'License Key', 'rcp-ultimate-member' ); ?></label>
						</th>
						<td>
							<p><input class="regular-text" type="text" id="rcpum_license_key" name="rcpum_license_key" value="<?php echo esc_attr( $license ); ?>" />
							<?php if( $status == 'valid' ) : ?>
								<?php wp_nonce_field( 'rcpum_deactivate_license', 'rcpum_deactivate_license' ); ?>
								<?php submit_button( 'Deactivate License', 'secondary', 'rcpum_license_deactivate', false ); ?>
								<span style="color:green">&nbsp;&nbsp;<?php _e( 'active', 'rcp-ultimate-member' ); ?></span>
							<?php else : ?>
								<?php submit_button( 'Activate License', 'secondary', 'rcpum_license_activate', false ); ?>
							<?php endif; ?></p>

							<p class="description"><?php printf( __( 'Enter your Restrict Content Pro - Ultimate Member license key. This is required for automatic updates and <a href="%s">support</a>.', 'rcp-ultimate-member' ), rcp_ultimate_member()->get_support_url() ); ?></p>
						</td>
					</tr>

				</table>

				<?php settings_fields( 'rcpum_settings_group' ); ?>
				<?php wp_nonce_field( 'rcpum_nonce', 'rcpum_nonce' ); ?>
				<?php submit_button( 'Save Options' ); ?>

			</form>
		</div>

	<?php
	}


	public function sanitize_license( $new ) {
		$old = get_option( 'rcpum_license_key' );
		if ( $old && $old != $new ) {
			delete_option( 'rcpum_license_key' ); // new license has been entered, so must reactivate
		}

		return $new;
	}

	/**
	 * This is a means of catching errors from the activation method above and displaying it to the customer
	 */
	public function license_admin_notice() {
		if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

			switch ( $_GET['sl_activation'] ) {

				case 'false':
					$message = urldecode( $_GET['message'] );
					?>
					<div class="error">
						<p><?php echo $message; ?></p>
					</div>
					<?php
					break;

				case 'true':
				default:
					// Developers can put a custom success message here for when activation is successful if they way.
					break;

			}
		}
	}


}
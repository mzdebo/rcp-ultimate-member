<?php

namespace RCP_UM\Admin;

class License {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of \RCP_UM\License
	 *
	 * @return License
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof License ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	protected function __construct() {
		add_action( 'admin_init', array( $this, 'check_license'      ) );
		add_action( 'admin_init', array( $this, 'activate_license'   ) );
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );
		add_action( 'admin_init', array( $this, 'plugin_updater'     ) );
	}

	/**
	 * Handle License activation
	 */
	public function activate_license() {

		// listen for our activate button to be clicked
		if ( ! isset( $_POST['rcpum_license_activate'], $_POST['rcpum_nonce'] ) ) {
			return;
		}

		// run a quick security check
		if ( ! check_admin_referer( 'rcpum_nonce', 'rcpum_nonce' ) ) {
			return;
		} // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'rcpum_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( RCP_ULTIMATE_MEMBER_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( RCP_ULTIMATE_MEMBER_STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			return;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "valid" or "invalid"
		update_option( 'rcpum_license_status', $license_data->license );
		delete_transient( 'rcpum_license_check' );

	}

	/**
	 * Handle License deactivation
	 */
	public function deactivate_license() {

		// listen for our activate button to be clicked
		if ( ! isset( $_POST['rcpum_license_deactivate'], $_POST['rcpum_nonce'] ) ) {
			return;
		}

		// run a quick security check
		if ( ! check_admin_referer( 'rcpum_nonce', 'rcpum_nonce' ) ) {
			return;
		}

		// retrieve the license from the database
		$license = trim( get_option( 'rcpum_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( RCP_ULTIMATE_MEMBER_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( RCP_ULTIMATE_MEMBER_STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			return;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( $license_data->license == 'deactivated' ) {
			delete_option( 'rcpum_license_status' );
			delete_transient( 'rcpum_license_check' );
		}

	}

	/**
	 * Check license
	 *
	 * @since       1.0.0
	 */
	public function check_license() {

		// Don't fire when saving settings
		if ( ! empty( $_POST['rcpum_nonce'] ) ) {
			return;
		}

		$license = get_option( 'rcpum_license_key' );
		$status  = get_transient( 'rcpum_license_check' );

		if ( $status === false && $license ) {

			$api_params = array(
				'edd_action' => 'check_license',
				'license'    => trim( $license ),
				'item_name'  => urlencode( RCP_ULTIMATE_MEMBER_ITEM_NAME ),
				'url'        => home_url()
			);

			$response = wp_remote_post( RCP_ULTIMATE_MEMBER_STORE_URL, array( 'timeout' => 35, 'sslverify' => false, 'body' => $api_params ) );

			if ( is_wp_error( $response ) ) {
				return;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			$status = $license_data->license;

			update_option( 'rcpum_license_status', $status );

			set_transient( 'rcpum_license_check', $license_data->license, DAY_IN_SECONDS );

			if ( $status !== 'valid' ) {
				delete_option( 'rcpum_license_status' );
			}
		}

	}

	/**
	 * Plugin Updater
	 */
	public function plugin_updater() {
		// load our custom updater
		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			include( RCP_ULTIMATE_MEMBER_PLUGIN_DIR . '/includes/updater.php' );
		}

		// retrieve our license key from the DB
		$license_key = trim( get_option( 'rcpum_license_key' ) );

		// setup the updater
		new \EDD_SL_Plugin_Updater( RCP_ULTIMATE_MEMBER_STORE_URL, RCP_ULTIMATE_MEMBER_PLUGIN_FILE, array(
				'version'   => RCP_ULTIMATE_MEMBER_PLUGIN_VERSION,    // current version number
				'license'   => $license_key,     // license key (used get_option above to retrieve from DB)
				'item_name' => urlencode( RCP_ULTIMATE_MEMBER_ITEM_NAME ), // the name of our product in EDD
				'author'    => 'Tanner Moushey'  // author of this plugin
			)
		);

	}
}

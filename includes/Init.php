<?php

namespace RCP_UM;

class Init {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of Init
	 *
	 * @return Init
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof Init ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add Hooks and Actions
	 */
	protected function __construct() {
		add_action( 'plugins_loaded', array( $this, 'maybe_setup' ), - 9999 );
	}

	/**
	 * Setup the plugin
	 */
	public function maybe_setup() {
		if ( ! $this->check_required_plugins() ) {
			return;
		}

		$this->includes();
		$this->actions();
	}

	/**
	 * Includes
	 */
	protected function includes() {
		Admin\Init::get_instance();

		if ( ultimatemember_version < 2 ) {
			UltimateMember::get_instance();
		} else {
			UltimateMember2::get_instance();
		}
	}

	/**
	 * Actions and Filters
	 */
	protected function actions() {}

	/** Actions **************************************/

	/**
	 * Required Plugins notice
	 */
	public function required_plugins() {
		printf( '<div class="error"><p>%s</p></div>', __( 'Restrict Content Pro and Ultimate Member are required for the Restrict Content Pro - Ultimate Member add-on to function.', 'rcp-ultimate-member' ) );
	}

	/** Helper Methods **************************************/

	/**
	 * Make sure RCP is active
	 * @return bool
	 */
	protected function check_required_plugins() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( is_plugin_active( 'restrict-content-pro/restrict-content-pro.php' ) && defined( 'ultimatemember_version' ) ) {
			return true;
		}

		add_action( 'admin_notices', array( $this, 'required_plugins' ) );

		return false;
	}

	/**
	 * Gets the plugin support URL
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_support_url() {
		return 'https://skillfulplugins.com/support';
	}

	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.0.0
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'Restrict Content Pro - Ultimate Member', 'rcp-ultimate-member' );
	}

	public function get_id() {
		return 'rcp-ultimate-member';
	}

}

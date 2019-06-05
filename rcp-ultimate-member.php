<?php
/**
 * Plugin Name: Restrict Content Pro - Ultimate Member
 * Plugin URL: https://skillfulplugins.com/plugins/rcp-ultimate-member
 * Description: Integrate RCP subscriptions with Ultimate Member
 * Version: 1.1.2
 * Author: Skillful Plugins
 * Author URI: https://skillfulplugins.com
 * Text Domain: rcp-ultimate-member
 * Domain Path: languages
 */

if ( !defined( 'RCP_ULTIMATE_MEMBER_PLUGIN_DIR' ) ) {
	define( 'RCP_ULTIMATE_MEMBER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'RCP_ULTIMATE_MEMBER_PLUGIN_URL' ) ) {
	define( 'RCP_ULTIMATE_MEMBER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'RCP_ULTIMATE_MEMBER_PLUGIN_FILE' ) ) {
	define( 'RCP_ULTIMATE_MEMBER_PLUGIN_FILE', __FILE__ );
}
if ( !defined( 'RCP_ULTIMATE_MEMBER_PLUGIN_VERSION' ) ) {
	define( 'RCP_ULTIMATE_MEMBER_PLUGIN_VERSION', '1.1.2' );
}

// EDD Licensing constants
define( 'RCP_ULTIMATE_MEMBER_STORE_URL', 'https://skillfulplugins.com' );
define( 'RCP_ULTIMATE_MEMBER_ITEM_NAME', 'Restrict Content Pro - Ultimate Member' );

require_once( RCP_ULTIMATE_MEMBER_PLUGIN_DIR . 'vendor/autoload.php' );

/**
 * @var RCP_UM\Init
 */
global $rcp_ultimate_member;

if( version_compare( PHP_VERSION, '5.3', '<' ) ) {

} else {
	$rcp_ultimate_member = rcp_ultimate_member();
}

/**
 * Load plugin text domain for translations.
 *
 * @return void
 */
function rcp_ultimate_member_load_textdomain() {

	// Set filter for plugin's languages directory
	$rcp_lang_dir = dirname( plugin_basename( RCP_ULTIMATE_MEMBER_PLUGIN_FILE ) ) . '/languages/';
	$rcp_lang_dir = apply_filters( 'rcp_ultimate_member_languages_directory', $rcp_lang_dir );


	// Traditional WordPress plugin locale filter

	$get_locale = get_locale();

	if ( function_exists( 'rcp_compare_wp_version' ) && rcp_compare_wp_version( 4.7 ) ) {
		$get_locale = get_user_locale();
	}

	/**
	 * Defines the plugin language locale used in RCP.
	 *
	 * @var string $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
	 *                  otherwise uses `get_locale()`.
	 */
	$locale        = apply_filters( 'plugin_locale',  $get_locale, 'rcp-ultimate-member' );
	$mofile        = sprintf( '%1$s-%2$s.mo', 'rcp-ultimate-member', $locale );

	// Setup paths to current locale file
	$mofile_local  = $rcp_lang_dir . $mofile;
	$mofile_global = WP_LANG_DIR . '/rcp-ultimate-member/' . $mofile;

	if ( file_exists( $mofile_global ) ) {
		// Look in global /wp-content/languages/rcp folder
		load_textdomain( 'rcp-ultimate-member', $mofile_global );
	} elseif ( file_exists( $mofile_local ) ) {
		// Look in local /wp-content/plugins/easy-digital-downloads/languages/ folder
		load_textdomain( 'rcp-ultimate-member', $mofile_local );
	} else {
		// Load the default language files
		load_plugin_textdomain( 'rcp-ultimate-member', false, $rcp_lang_dir );
	}

}
add_action( 'init', 'rcp_ultimate_member_load_textdomain' );

/**
 * @return \RCP_UM\Init
 */
function rcp_ultimate_member() {
	return RCP_UM\Init::get_instance();
}

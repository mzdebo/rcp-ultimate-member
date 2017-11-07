<?php

namespace RCP_UM;

use RCP_Member;

class UltimateMember {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * @var string
	 */
	public static $_expired_status = 'expired';

	/**
	 * Only make one instance of UltimateMember
	 *
	 * @return UltimateMember
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof UltimateMember ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add Hooks and Actions
	 */
	protected function __construct() {
		add_action( 'rcp_set_status',          array( $this, 'set_member_status' ), 10, 3 );
		add_action( 'um_after_header_meta',    array( $this, 'suspended_profile' ), 8 );
		add_action( 'um_account_tab__subscription', array( $this, 'account_page' ) );

		add_filter( 'um_account_page_default_tabs_hook', array( $this, 'account_menu' ), 11 );
	}

	/**
	 * Update Ultimate Member status when RCP status changes
	 * 
	 * @param $new_status
	 * @param $user_id
	 * @param $old_status
	 *
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function set_member_status( $new_status, $user_id, $old_status ) {
		global $ultimatemember;
		$ultimatemember->user->set( $user_id );

		$um_status = um_user( 'account_status' );

		$member = new RCP_Member( $user_id );

		$is_active = ( $member->is_active() || $new_status == 'free' );

		// everything is as it should be
		if ( $is_active && 'approved' == $um_status ) {
			return;
		}

		// if the user no longer has an active membership, unapprove
		if ( ! $is_active && 'approved' == $um_status ) {
			$ultimatemember->user->set_status( self::$_expired_status );
			return;
		}

		if ( $is_active && 'approved' != $um_status ) {
			// set to 'awaiting_admin_review' so that we send the Approved email not the Welcome email
			$ultimatemember->user->set_status( 'awaiting_admin_review' );
			$ultimatemember->user->approve();
		}

	}

	/**
	 * Customize suspended profile
	 *
	 * @param $args
	 */
	public function suspended_profile( $args ) {

		if ( ! $user_id = um_profile_id() ) {
			return;
		}

		if ( self::$_expired_status != um_user( 'account_status' ) ) {
			return;
		}

		if ( ! um_is_on_edit_profile() ) {
			global $wp_filter;
			unset( $wp_filter[ 'um_profile_content_main' ] );
			unset( $wp_filter[ 'um_profile_content_main_default' ] );
		}

		$message = __( 'This account has expired.', 'rcp-ultimate-member' );

		if ( um_is_myprofile() ) {
			$message .= sprintf( __( ' Update your <a href="%s">payment information</a>a to re-activate your account.', 'rcp-ultimate-member' ), um_get_core_page('account' ) . 'subscription' );
		}

		echo apply_filters( 'rcpum_expired_member_message', sprintf( '<div class="um-field-error">%s</div>', $message ), $args );
	}

	/**
	 * Update Ultimate Member tabs
	 *
	 * @param $tabs
	 *
	 * @since  1.0.0
	 *
	 * @return mixed
	 * @author Tanner Moushey
	 */
	public function account_menu( $tabs ) {

		$tabs[100]['subscription'] = array(
			'icon'   => 'um-icon-card',
			'title'  => __( 'Subscription', 'rcp-ultimate-member' ),
			'custom' => true,
		);

		return $tabs;
	}

	/**
	 * Create Ultimate Member account page for Restrict Content Pro
	 *
	 * @param $info
	 *
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function account_page( $info ) { ?>

		<div class="um-account-heading uimob340-hide uimob500-hide"><i class="<?php echo $info['icon']; ?>"></i><?php echo $info['title']; ?></div>
		<hr />

		<?php
		echo do_shortcode( '[subscription_details]' );
	}

}
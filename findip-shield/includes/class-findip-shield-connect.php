<?php
/**
 * "Connect to FindIP Shield": links this WordPress site to a Shield site without copying a key.
 *
 * @package FindIP_Shield
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin sends the owner to the FindIP Shield dashboard with a one-time state; the dashboard
 * sends them back here with the site's public key. Only the public key travels (it is in every
 * page the SDK runs on), and it is saved only when the state matches the one issued to this admin.
 */
final class FindIP_Shield_Connect {
	const START_ACTION  = 'findip_shield_connect';
	const RETURN_ACTION = 'findip_shield_connect_return';
	const STATE_TTL     = 1800;

	/**
	 * Register admin-post handlers.
	 */
	public function __construct() {
		add_action( 'admin_post_' . self::START_ACTION, array( $this, 'start' ) );
		add_action( 'admin_post_' . self::RETURN_ACTION, array( $this, 'finish' ) );
	}

	/**
	 * Where the Connect button sends the owner. Filterable for local development only.
	 *
	 * @return string
	 */
	public static function connect_base_url() {
		return (string) apply_filters( 'findip_shield_connect_url', 'https://www.findip.net/shield/connect/wordpress' );
	}

	/**
	 * The Connect button's address: a nonce-protected admin-post request that starts the flow.
	 *
	 * @return string
	 */
	public static function start_url() {
		return wp_nonce_url( admin_url( 'admin-post.php?action=' . self::START_ACTION ), self::START_ACTION );
	}

	/**
	 * Transient key holding the hash of the state issued to this admin.
	 *
	 * @return string
	 */
	private static function state_key() {
		return 'findip_shield_connect_' . get_current_user_id();
	}

	/**
	 * Issue a state and send the owner to the Shield dashboard.
	 */
	public function start() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to connect FindIP Shield.', 'findip-shield' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( self::START_ACTION );

		$state = wp_generate_password( 40, false, false );
		set_transient( self::state_key(), hash( 'sha256', $state ), self::STATE_TTL );

		$url = add_query_arg(
			array(
				'site'         => rawurlencode( home_url( '/' ) ),
				'return'       => rawurlencode( admin_url( 'admin-post.php?action=' . self::RETURN_ACTION ) ),
				'state'        => $state,
				'v'            => rawurlencode( FINDIP_SHIELD_VERSION ),
				'utm_source'   => 'wordpress',
				'utm_medium'   => 'plugin',
				'utm_campaign' => 'shield_connect',
			),
			self::connect_base_url()
		);

		// An external host by design: wp_safe_redirect() would refuse it.
		wp_redirect( $url ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		exit;
	}

	/**
	 * Save the key the dashboard sent back, when the state is the one issued to this admin.
	 */
	public function finish() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to connect FindIP Shield.', 'findip-shield' ), '', array( 'response' => 403 ) );
		}

		// The state below replaces a nonce: this request comes back from findip.net, so it cannot carry one.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$state    = isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : '';
		$site_key = isset( $_GET['findip_site_key'] ) ? sanitize_text_field( wp_unslash( $_GET['findip_site_key'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		$expected = get_transient( self::state_key() );
		delete_transient( self::state_key() );

		$result = 'connected';
		if ( ! is_string( $expected ) || '' === $state || ! hash_equals( $expected, hash( 'sha256', $state ) ) ) {
			$result = 'expired';
		} elseif ( ! preg_match( '/^pub_[a-f0-9]+$/', $site_key ) ) {
			$result = 'invalid_key';
		} else {
			$settings             = FindIP_Shield::settings();
			$settings['site_key'] = $site_key;
			update_option( FindIP_Shield::OPTION_NAME, $settings );
		}

		wp_safe_redirect( add_query_arg( 'findip_connect', $result, admin_url( 'options-general.php?page=findip-shield' ) ) );
		exit;
	}
}

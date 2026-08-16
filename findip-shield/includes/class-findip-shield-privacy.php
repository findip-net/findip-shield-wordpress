<?php
/**
 * WordPress privacy-policy integration.
 *
 * @package FindIP_Shield
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds suggested FindIP disclosure text to the privacy-policy editor.
 */
final class FindIP_Shield_Privacy {
	/**
	 * Register privacy hooks.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'add_privacy_policy_content' ) );
	}

	/**
	 * Suggest disclosure text in the WordPress privacy-policy editor.
	 */
	public function add_privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		$content = '<p>' .
			esc_html__( 'This site uses FindIP Shield, an external visitor-risk service operated by FindIP. Shield receives connection information such as the visitor IP address and limited website-event metadata, then returns network and reputation signals including VPN, proxy, Tor, hosting, and malicious-IP indicators. The integration does not send form values, passwords, payment details, or message contents to FindIP.', 'findip-shield' ) .
			'</p><p>' .
			wp_kses_post(
				sprintf(
					/* translators: 1: data documentation URL, 2: privacy policy URL. */
					__( 'Learn exactly what is processed in the <a href="%1$s">FindIP Shield data-collection documentation</a> and review the <a href="%2$s">FindIP privacy policy</a>.', 'findip-shield' ),
					esc_url( 'https://www.findip.net/docs/shield/data-collection' ),
					esc_url( 'https://www.findip.net/Docs/privacy-policy' )
				)
			) .
			'</p>';

		wp_add_privacy_policy_content( 'FindIP Shield', wp_kses_post( wpautop( $content, false ) ) );
	}
}

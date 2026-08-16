<?php
/**
 * Uninstall FindIP Shield.
 *
 * @package FindIP_Shield
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'findip_shield_settings' );
delete_site_option( 'findip_shield_settings' );

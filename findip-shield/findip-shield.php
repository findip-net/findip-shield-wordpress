<?php
/**
 * Plugin Name:       FindIP Shield: Block VPN, Proxy & Fake Signups
 * Plugin URI:        https://www.findip.net/shield/overview
 * Description:       Stop fake signups, logins and orders from VPNs, proxies, Tor and bots, and see every risky visitor with the reason. Free, no code.
 * Version:           0.4.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            FindIP
 * Author URI:        https://www.findip.net/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       findip-shield
 * WC requires at least: 8.2
 * WC tested up to:   11.0
 *
 * @package FindIP_Shield
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FINDIP_SHIELD_VERSION', '0.4.0' );
define( 'FINDIP_SHIELD_FILE', __FILE__ );
define( 'FINDIP_SHIELD_DIR', plugin_dir_path( __FILE__ ) );
define( 'FINDIP_SHIELD_URL', plugin_dir_url( __FILE__ ) );

require_once FINDIP_SHIELD_DIR . 'includes/class-findip-shield.php';
require_once FINDIP_SHIELD_DIR . 'includes/class-findip-shield-admin.php';
require_once FINDIP_SHIELD_DIR . 'includes/class-findip-shield-connect.php';
require_once FINDIP_SHIELD_DIR . 'includes/class-findip-shield-privacy.php';
require_once FINDIP_SHIELD_DIR . 'includes/class-findip-shield-woocommerce.php';

FindIP_Shield::instance();

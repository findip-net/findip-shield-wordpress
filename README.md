# FindIP Shield for WordPress and WooCommerce

Official WordPress and WooCommerce integration for [FindIP Shield](https://www.findip.net/shield/overview), maintained by FindIP at [info@findip.net](mailto:info@findip.net).

The installable plugin is in [`findip-shield/`](findip-shield/).

## See Shield in action

[![A sample sign-up arrives through a VPN. FindIP Shield shows the reasons behind its risk score, follows the session across networks, and records what the page did](.github/media/shield-signup-insight-teaser.gif)](https://www.findip.net/assets/videos/shield-signup-insight.mp4)

▶ **[Watch with sound (0:30)](https://www.findip.net/assets/videos/shield-signup-insight.mp4)** · [Try the interactive demo](https://www.findip.net/shield/demo?utm_source=github&utm_medium=readme&utm_campaign=shield_clips&utm_content=findip-shield-wordpress) · [Explore FindIP Shield](https://www.findip.net/shield/overview?utm_source=github&utm_medium=readme&utm_campaign=shield_clips&utm_content=findip-shield-wordpress)

This is the Shield dashboard that the plugin reports to, shown with sample data: each visit with its risk score, the reasons behind it, and a recommendation. The plugin reports signals. Form protection is switched on from the dashboard.

## Release status

FindIP Shield is available from the [WordPress.org Plugin Directory](https://wordpress.org/plugins/findip-shield/). Version `0.2.0` pins SDK 1.11.0. It uses strict privacy defaults, never reads form values, and enforces nothing until form protection is switched on for the site in the Shield dashboard. WooCommerce-specific flows remain a separate compatibility test requirement.

## Local test

1. Copy `findip-shield/` to `wp-content/plugins/findip-shield/`.
2. Activate **FindIP Shield: VPN, Proxy & Bot Traffic Detection**.
3. Configure it under **Settings → FindIP Shield**.
4. Exercise a page view, login form, and—if available—a WooCommerce checkout.
5. Verify events in the Shield dashboard and inspect browser requests for unexpected fields.

## Support

- Product support: [info@findip.net](mailto:info@findip.net)
- Security reports: [security@findip.net](mailto:security@findip.net)
- Documentation: <https://www.findip.net/docs/shield>

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).

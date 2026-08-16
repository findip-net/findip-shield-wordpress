# FindIP Shield for WordPress and WooCommerce

Official WordPress and WooCommerce integration for [FindIP Shield](https://www.findip.net/shield/overview), maintained by FindIP at [info@findip.net](mailto:info@findip.net).

The installable plugin is in [`findip-shield/`](findip-shield/).

## Development status

Version `0.1.0` is an MVP intended for local and staging validation before submission to the WordPress.org Plugin Directory. It uses strict privacy defaults, never reads form values, and does not automatically enforce risk decisions.

## Local test

1. Copy `findip-shield/` to `wp-content/plugins/findip-shield/`.
2. Activate **FindIP Shield – Visitor Risk Intelligence**.
3. Configure it under **Settings → FindIP Shield**.
4. Exercise a page view, login form, and—if available—a WooCommerce checkout.
5. Verify events in the Shield dashboard and inspect browser requests for unexpected fields.

## Support

- Product support: [info@findip.net](mailto:info@findip.net)
- Security reports: [security@findip.net](mailto:security@findip.net)
- Documentation: <https://www.findip.net/docs/shield>

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).

# Publishing checklist

All official publication is performed on behalf of FindIP.

## GitHub

- Repository owner: `findip-net`
- Repository name: `findip-shield-wordpress`
- Publishing account: `findip-bot`
- Commit identity: `FindIP <info@findip.net>`
- Do not push or create releases using a personal GitHub account.

Verify before pushing:

```bash
git config user.name
git config user.email
```

## WordPress.org

- Proposed plugin slug: `findip-shield`
- Approved owner username: `findipshield`
- Registration and review email: `info@findip.net`
- Support contact: `info@findip.net`
- Security contact: `security@findip.net`

WordPress.org accounts must be operated by one human and must not be shared. The branded owner account should use the official company email; additional humans should receive their own individual committer or support accounts.

Before submission:

1. Confirm `info@findip.net` receives mail from `plugins@wordpress.org`.
2. Run CI and the official WordPress Plugin Check tool.
3. Test a clean install with current WordPress and WooCommerce versions.
4. Inspect browser requests in strict, balanced, advanced, and no-consent modes.
5. Confirm the external-service, privacy, and terms links in `readme.txt` are public.
6. Build the ZIP from the `findip-shield/` directory only.

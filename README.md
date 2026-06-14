# plugin-template

GitHub **template repo** for a WPPoland storefront FREE plugin. A thin adapter over
`wppoland/storefront-kit`, pre-wired to the reusable CI/release workflows. Spin up a new plugin in
minutes instead of rebuilding CI each time.

## Create a new plugin

> 🔔 **You must create a new repository for each plugin.** FREE → a **public** repo
> `wppoland/<slug>`. PRO → a separate **private** repo `wppoland/<slug>-pro`.

1. **"Use this template" → create `wppoland/<slug>`** (public).
2. **Run the scaffold script** — replaces all tokens and renames `giftcards.php → <slug>.php`
   (cross-platform; review the diff before committing):
   ```bash
   python3 scripts/init.py restock Restock "Restock" "Back-in-stock notifications for WooCommerce"
   #                        ^slug   ^Namespace ^Name    ^short description
   rm scripts/init.py
   ```
   Tokens it replaces (case-sensitive):

   | Token | Replace with | Example |
   |---|---|---|
   | `giftcards` | lowercase slug = text-domain = i18n domain | `restock` |
   | `GiftCards` | PSR-4 PHP namespace | `Restock` |
   | `GIFTCARDS` (in `define()`) | uppercased namespace | `RESTOCK` |
   | `giftcards_` | option/meta prefix (slug, dashes→underscores) | `restock_` |
   | `Gift Cards – Store Credit for WooCommerce` / `Sell gift cards that email a redeemable code to the recipient and apply as a discount at checkout.` / `Sell gift cards that email a redeemable code to the recipient and apply as a discount at checkout.` | name + descriptions | … |
3. `composer install` — resolves `wppoland/storefront-kit ^1.0` from VCS (no symlink). Implement
   your adapter in `src/`, wire it in `config/services.php` + `config/hooks.php`.
   *(For local atomic kit+adapter dev, see the kit README's path-override note.)*
4. Add repo secrets: **`WPORG_SVN_USERNAME`**, **`WPORG_SVN_PASSWORD`**.
5. Drop wp.org assets in `.wordpress-org/`; fill in `readme.txt`.
6. Add a `PluginEntry` to `plogins` `packages/registry/src/plugins.config.ts` + a docs folder.
7. **Release:** bump the header `Version:` + `readme.txt` Stable tag, tag `vX.Y.Z`, push →
   `_release-free.yml` runs CI, vendors the kit, and auto-deploys to wp.org SVN.

## What's wired

- **Bootstrap** (`giftcards.php`): PHP/WC guards, HPOS + cart-blocks compat, `plugins_loaded`
  boot, `do_action('giftcards/booted')` (the hook a PRO companion extends).
- **Autoload** (`autoload.php`): Composer vendor autoloader + PSR-4 fallback (incl. the kit).
- **DI**: `src/Plugin.php` singleton + `src/Container.php`; services in `config/services.php`,
  boot order in `config/hooks.php`, defaults in `config/defaults.php`; `src/Migrator.php`.
- **CI/Release**: `.github/workflows/{ci,release}.yml` call `wppoland/workflows@v1`.
- **Quality**: `phpcs.xml.dist` (WPCS), `phpstan.neon.dist` (level 6 + WC stubs), `.distignore`
  (ships `vendor/` so the kit travels), `.wp-env.json`.

## PRO companion (`<slug>-pro`, private)

Create a separate private repo. It hooks `add_action('<slug>/booted', …)`, bundles the Freemius
SDK, and releases via `wppoland/workflows/.github/workflows/_release-pro.yml@v1`.

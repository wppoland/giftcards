<?php
/**
 * Service wiring. Returns a closure that registers every service in the
 * container. Keep services thin; product logic lives in storefront-kit engines
 * instantiated here with this plugin's text-domain / option storage / asset URLs.
 *
 * @package GiftCards
 */

declare(strict_types=1);

use GiftCards\Admin\ProductFields;
use GiftCards\Admin\Settings;
use GiftCards\Container;
use GiftCards\Migrator;
use GiftCards\Service\GiftCardService;

defined('ABSPATH') || exit;

return static function (Container $c): void {
    $c->singleton(Migrator::class, static fn (): Migrator => new Migrator());

    // Thin adapter over the storefront-kit GiftCardEngine.
    $c->singleton(GiftCardService::class, static fn (): GiftCardService => new GiftCardService());

    // Admin (only needed in wp-admin context).
    if (is_admin()) {
        $c->singleton(Settings::class, static fn (): Settings => new Settings());
        $c->singleton(ProductFields::class, static fn (): ProductFields => new ProductFields());
    }
};

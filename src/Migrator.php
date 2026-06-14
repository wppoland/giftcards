<?php

declare(strict_types=1);

namespace GiftCards;

defined('ABSPATH') || exit;

/**
 * Idempotent schema/version migrations, run on every boot. Compares a stored
 * option against VERSION and applies forward steps as needed.
 */
final class Migrator
{
    private const OPTION = 'giftcards_db_version';

    private const SETTINGS = 'giftcards_settings';

    public function maybeMigrate(): void
    {
        $current = (string) get_option(self::OPTION, '0');

        if (version_compare($current, VERSION, '>=')) {
            return;
        }

        $this->createGiftCardsTable();
        $this->seedDefaultSettings();

        update_option(self::OPTION, VERSION, false);
    }

    /**
     * Create the gift-cards table: one row per issued card holding the unique
     * code, remaining balance, recipient email and source order id.
     */
    private function createGiftCardsTable(): void
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table   = $wpdb->prefix . 'giftcards';
        $collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            code varchar(64) NOT NULL,
            balance decimal(19,4) NOT NULL DEFAULT 0,
            recipient_email varchar(191) NOT NULL DEFAULT '',
            order_id bigint(20) unsigned NOT NULL DEFAULT 0,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY code (code),
            KEY order_id (order_id)
        ) {$collate};";

        dbDelta($sql);
    }

    /**
     * Seed the default settings once, without clobbering an existing config.
     */
    private function seedDefaultSettings(): void
    {
        if (get_option(self::SETTINGS, null) !== null) {
            return;
        }

        /** @var array<string, mixed> $defaults */
        $defaults = require GIFTCARDS_DIR . 'config/defaults.php';

        add_option(self::SETTINGS, $defaults, '', false);
    }
}

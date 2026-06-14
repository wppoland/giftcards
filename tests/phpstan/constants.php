<?php
/**
 * Constants needed by PHPStan to analyse the plugin without bootstrapping WordPress.
 *
 * @package GiftCards
 */

declare(strict_types=1);

namespace {
    if (! defined('ABSPATH')) {
        define('ABSPATH', '/tmp/wordpress/');
    }
    if (! defined('GIFTCARDS_DIR')) {
        define('GIFTCARDS_DIR', '/tmp/giftcards/');
    }
    if (! defined('GIFTCARDS_URL')) {
        define('GIFTCARDS_URL', 'https://example.test/wp-content/plugins/giftcards/');
    }
}

namespace GiftCards {
    if (! defined('GiftCards\\VERSION')) {
        define('GiftCards\\VERSION', '0.1.0');
    }
    if (! defined('GiftCards\\PLUGIN_FILE')) {
        define('GiftCards\\PLUGIN_FILE', '/tmp/giftcards/giftcards.php');
    }
}

<?php
/**
 * Default settings, merged under the option key `giftcards_settings`.
 *
 * The feature ships enabled. The merchant tunes the optional code prefix and the
 * recipient email templates. All gift-card logic lives in the storefront-kit
 * GiftCardEngine; these values are passed through to it as the resolved
 * settings / labels.
 *
 * @package GiftCards
 *
 * @return array<string, mixed>
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

return [
    'enabled' => true,

    // Optional prefix prepended to every generated code (e.g. "GIFT-").
    'code_prefix' => '',

    // Recipient email templates. {code} and {amount} are interpolated.
    'email_subject' => 'You have received a {amount} gift card',
    'email_body'    => "You have received a gift card worth {amount}.\n\nUse this code at checkout: {code}",
];

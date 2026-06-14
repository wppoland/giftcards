<?php

declare(strict_types=1);

namespace GiftCards\Service;

use GiftCards\Contract\HasHooks;
use GiftCards\Repository\GiftCardTableRepository;
use WPPoland\StorefrontKit\GiftCard\GiftCardEngine;

defined('ABSPATH') || exit;

/**
 * Thin adapter over the storefront-kit {@see GiftCardEngine}.
 *
 * Injects this plugin's text-domain ('giftcards'), option storage, session key,
 * checkout-field markup and labels into the namespace-neutral engine. All gift
 * card orchestration (code generation on order completion, recipient email,
 * redeem-code field, applying the balance as a negative cart fee, decrementing
 * the balance) lives in the kit. This class supplies the three closures the
 * engine needs:
 *
 *  - `isGiftCard`  — true when a product is flagged via `_giftcards_is_gift_card`.
 *  - `resolveCard` — `[amount, recipient_email]` for a purchased gift-card line:
 *                    amount is the per-unit line total, recipient is the line's
 *                    custom recipient (cart meta) or the order billing email.
 *  - `renderField` — echoes the packaged checkout redeem-code template.
 *
 * Storage is delegated to {@see GiftCardTableRepository}.
 */
final class GiftCardService implements HasHooks
{
    private const OPTION = 'giftcards_settings';

    private const SESSION_KEY = 'giftcards_redeem_code';

    private ?GiftCardEngine $engine = null;

    public function __construct()
    {
        // The engine ships with storefront-kit >= 1.5.0. When present, wire it
        // with this plugin's text-domain / option storage / asset paths.
        // Otherwise leave the service inert (see registerHooks()).
        if (! class_exists(GiftCardEngine::class)) {
            return;
        }

        $this->engine = new GiftCardEngine(
            repository: new GiftCardTableRepository(),
            sessionKey: self::SESSION_KEY,
            fieldName: 'giftcards_redeem_code',
            nonceAction: 'giftcards_redeem',
            fieldTemplate: 'checkout-redeem-field',
            labels: [
                'fee_label'     => __('Gift card ({code})', 'giftcards'),
                'email_subject' => __('You have received a {amount} gift card', 'giftcards'),
                'email_body'    => __("You have received a gift card worth {amount}.\n\nUse this code at checkout: {code}", 'giftcards'),
                'invalid_code'  => __('That gift card code is not valid.', 'giftcards'),
                'applied'       => __('Gift card applied.', 'giftcards'),
            ],
            isEnabled: fn (): bool => $this->isEnabled(),
            settings: fn (): array => $this->settings(),
            isGiftCard: static fn (\WC_Product $product): bool => 'yes' === $product->get_meta('_giftcards_is_gift_card'),
            resolveCard: fn (\WC_Order_Item_Product $item): array => $this->resolveCard($item),
            renderField: function (string $template, array $context): void {
                $this->renderField($template, $context);
            },
        );
    }

    public function registerHooks(): void
    {
        if (! $this->engine instanceof GiftCardEngine) {
            // storefront-kit < 1.5.0 has no GiftCardEngine. Bump the
            // `wppoland/storefront-kit` constraint (composer update) to enable
            // gift cards. No hooks are registered until the engine is present.
            return;
        }

        $this->engine->registerHooks();
    }

    /**
     * Resolve `[amount, recipient_email]` for a purchased gift-card line.
     *
     * Amount is the per-unit line total (so partial/quantity buys are honoured).
     * Recipient is the per-line recipient captured at add-to-cart time, falling
     * back to the order's billing email so a card is always deliverable.
     *
     * @return array{0: float, 1: string}
     */
    private function resolveCard(\WC_Order_Item_Product $item): array
    {
        $quantity = max(1, (int) $item->get_quantity());
        $amount   = (float) $item->get_total() / $quantity;

        $recipient = (string) $item->get_meta('_giftcards_recipient_email');

        if (! is_email($recipient)) {
            $order     = $item->get_order();
            $recipient = $order instanceof \WC_Order ? (string) $order->get_billing_email() : '';
        }

        return [$amount, $recipient];
    }

    /**
     * Echo the packaged checkout redeem-code field template.
     *
     * @param array<string, mixed> $context
     */
    private function renderField(string $template, array $context): void
    {
        $file = GIFTCARDS_DIR . 'templates/' . $template . '.php';

        if (! is_readable($file)) {
            return;
        }

        $giftcards_field_name   = isset($context['field_name']) ? (string) $context['field_name'] : '';
        $giftcards_nonce_field  = isset($context['nonce_field']) ? (string) $context['nonce_field'] : '';
        $giftcards_applied_code = isset($context['applied_code']) ? (string) $context['applied_code'] : '';
        $giftcards_settings     = isset($context['settings']) && is_array($context['settings']) ? $context['settings'] : [];

        require $file;
    }

    private function isEnabled(): bool
    {
        return (bool) ($this->settings()['enabled'] ?? false);
    }

    /**
     * Stored settings merged over packaged defaults.
     *
     * @return array<string, mixed>
     */
    private function settings(): array
    {
        $stored = get_option(self::OPTION, []);

        if (! is_array($stored)) {
            $stored = [];
        }

        /** @var array<string, mixed> $defaults */
        $defaults = require GIFTCARDS_DIR . 'config/defaults.php';

        return array_merge($defaults, $stored);
    }
}

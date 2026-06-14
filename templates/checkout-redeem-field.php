<?php
/**
 * Checkout redeem-code field.
 *
 * Echoed by the storefront-kit GiftCardEngine via the host `renderField`
 * closure ({@see \GiftCards\Service\GiftCardService::renderField()}) on
 * `woocommerce_review_order_before_payment`.
 *
 * @package GiftCards
 *
 * @var string $giftcards_field_name   Input name the engine reads on update.
 * @var string $giftcards_nonce_field  Nonce value for the redeem action.
 * @var string $giftcards_applied_code Currently applied code, if any.
 * @var array<string, mixed> $giftcards_settings Resolved settings.
 *
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template-scope variables supplied by the host renderField closure.
 */

defined('ABSPATH') || exit;
?>
<div class="giftcards-redeem">
    <p class="form-row giftcards-redeem__row">
        <label for="<?php echo esc_attr($giftcards_field_name); ?>">
            <?php esc_html_e('Gift card code', 'giftcards'); ?>
        </label>
        <input
            type="text"
            id="<?php echo esc_attr($giftcards_field_name); ?>"
            name="<?php echo esc_attr($giftcards_field_name); ?>"
            value="<?php echo esc_attr($giftcards_applied_code); ?>"
            class="input-text"
            autocomplete="off"
        />
        <input type="hidden" name="<?php echo esc_attr($giftcards_field_name); ?>_nonce" value="<?php echo esc_attr($giftcards_nonce_field); ?>" />
    </p>
</div>

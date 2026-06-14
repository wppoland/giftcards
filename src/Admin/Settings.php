<?php

declare(strict_types=1);

namespace GiftCards\Admin;

use GiftCards\Contract\HasHooks;

defined('ABSPATH') || exit;

/**
 * Admin settings page registered as a WooCommerce submenu ("Gift Cards").
 *
 * Stores settings in the `giftcards_settings` option (array): the master toggle,
 * an optional code prefix, and the recipient email subject/body templates passed
 * through to the storefront-kit engine. All output is escaped; all input is
 * sanitised on save. The save capability is aligned to `manage_woocommerce` so
 * shop managers can save.
 */
final class Settings implements HasHooks
{
    private const OPTION = 'giftcards_settings';

    private const PAGE = 'giftcards-settings';

    public function registerHooks(): void
    {
        add_action('admin_menu', [$this, 'addMenuPage']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    public function addMenuPage(): void
    {
        add_submenu_page(
            'woocommerce',
            __('Gift Cards', 'giftcards'),
            __('Gift Cards', 'giftcards'),
            'manage_woocommerce',
            self::PAGE,
            [$this, 'renderPage'],
        );
    }

    public function registerSettings(): void
    {
        register_setting(
            self::PAGE,
            self::OPTION,
            [
                'type'              => 'array',
                'sanitize_callback' => [$this, 'sanitize'],
            ],
        );

        // The menu uses manage_woocommerce; align the options.php save
        // capability so shop managers (not just admins) can save.
        add_filter(
            'option_page_capability_' . self::PAGE,
            static fn (): string => 'manage_woocommerce',
        );
    }

    public function renderPage(): void
    {
        if (! current_user_can('manage_woocommerce')) {
            return;
        }

        $settings = $this->settings();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields(self::PAGE); ?>

                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><?php esc_html_e('Enable gift cards', 'giftcards'); ?></th>
                            <td>
                                <label for="giftcards_enabled">
                                    <input
                                        type="checkbox"
                                        id="giftcards_enabled"
                                        name="<?php echo esc_attr(self::OPTION); ?>[enabled]"
                                        value="1"
                                        <?php checked((bool) ($settings['enabled'] ?? false), true); ?>
                                    />
                                    <?php esc_html_e('Generate and email a code when a gift-card product is purchased, and accept codes at checkout.', 'giftcards'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="giftcards_code_prefix"><?php esc_html_e('Code prefix', 'giftcards'); ?></label>
                            </th>
                            <td>
                                <input
                                    type="text"
                                    id="giftcards_code_prefix"
                                    name="<?php echo esc_attr(self::OPTION); ?>[code_prefix]"
                                    value="<?php echo esc_attr((string) ($settings['code_prefix'] ?? '')); ?>"
                                    class="regular-text"
                                />
                                <p class="description"><?php esc_html_e('Optional. Prepended to every generated code (e.g. "GIFT-").', 'giftcards'); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h2><?php esc_html_e('Recipient email', 'giftcards'); ?></h2>
                <p class="description">
                    <?php esc_html_e('Use {code} for the gift-card code and {amount} for the value.', 'giftcards'); ?>
                </p>
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="giftcards_email_subject"><?php esc_html_e('Subject', 'giftcards'); ?></label>
                            </th>
                            <td>
                                <input
                                    type="text"
                                    id="giftcards_email_subject"
                                    name="<?php echo esc_attr(self::OPTION); ?>[email_subject]"
                                    value="<?php echo esc_attr((string) ($settings['email_subject'] ?? '')); ?>"
                                    class="large-text"
                                />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="giftcards_email_body"><?php esc_html_e('Body', 'giftcards'); ?></label>
                            </th>
                            <td>
                                <textarea
                                    id="giftcards_email_body"
                                    name="<?php echo esc_attr(self::OPTION); ?>[email_body]"
                                    rows="5"
                                    class="large-text"
                                ><?php echo esc_textarea((string) ($settings['email_body'] ?? '')); ?></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="description">
                    <?php esc_html_e('Flag any product as a gift card from the product editor (General tab → Gift card).', 'giftcards'); ?>
                </p>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Sanitises the submitted settings before save, preserving defaults for any
     * field not on the form.
     *
     * @param mixed $raw
     * @return array<string, mixed>
     */
    public function sanitize(mixed $raw): array
    {
        if (! is_array($raw)) {
            $raw = [];
        }

        $defaults = $this->settings();

        $prefix  = isset($raw['code_prefix']) ? sanitize_text_field((string) $raw['code_prefix']) : '';
        $subject = isset($raw['email_subject']) ? sanitize_text_field((string) $raw['email_subject']) : '';
        $body    = isset($raw['email_body']) ? sanitize_textarea_field((string) $raw['email_body']) : '';

        return array_merge($defaults, [
            'enabled'       => ! empty($raw['enabled']),
            'code_prefix'   => $prefix,
            'email_subject' => $subject !== '' ? $subject : (string) ($defaults['email_subject'] ?? ''),
            'email_body'    => $body !== '' ? $body : (string) ($defaults['email_body'] ?? ''),
        ]);
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

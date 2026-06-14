<?php

declare(strict_types=1);

namespace GiftCards\Repository;

use WPPoland\StorefrontKit\GiftCard\GiftCardRepository;

defined('ABSPATH') || exit;

/**
 * Custom-table storage for the storefront-kit {@see GiftCardRepository}.
 *
 * A gift-card record is `{code, balance, recipient_email, order_id}`, persisted
 * in `{$wpdb->prefix}giftcards`. The table is created by the
 * {@see \GiftCards\Migrator}. Storage lives here (not in the kit) so the library
 * hard-codes no table name and no `$wpdb` access — the same delegation the kit
 * uses for the waitlist. The `$wpdb->prefix`-derived table name cannot be passed
 * as a placeholder, so the direct-query / unescaped-DB-parameter sniffs are
 * disabled with justification, mirroring restock's WaitlistRepository.
 *
 * phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Custom plugin table; name derived from $wpdb->prefix and cannot be parameterised; queries are prepared.
 */
final class GiftCardTableRepository implements GiftCardRepository
{
    public function table(): string
    {
        global $wpdb;

        return $wpdb->prefix . 'giftcards';
    }

    public function issue(string $code, float $balance, string $recipientEmail, int $orderId): int
    {
        global $wpdb;

        $wpdb->insert(
            $this->table(),
            [
                'code'            => $code,
                'balance'         => $balance,
                'recipient_email' => $recipientEmail,
                'order_id'        => $orderId,
                'created_at'      => current_time('mysql', true),
            ],
            ['%s', '%f', '%s', '%d', '%s'],
        );

        return (int) $wpdb->insert_id;
    }

    /**
     * @return object{id:int,code:string,balance:float,recipient_email:string,order_id:int}|null
     */
    public function findByCode(string $code): ?object
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT id, code, balance, recipient_email, order_id FROM %i WHERE code = %s LIMIT 1',
                $this->table(),
                $code,
            ),
        );

        if (! is_object($row)) {
            return null;
        }

        return (object) [
            'id'              => (int) $row->id,
            'code'            => (string) $row->code,
            'balance'         => (float) $row->balance,
            'recipient_email' => (string) $row->recipient_email,
            'order_id'        => (int) $row->order_id,
        ];
    }

    public function updateBalance(int $id, float $balance): void
    {
        global $wpdb;

        $wpdb->update(
            $this->table(),
            ['balance' => $balance],
            ['id' => $id],
            ['%f'],
            ['%d'],
        );
    }
}

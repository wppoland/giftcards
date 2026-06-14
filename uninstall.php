<?php

/**
 * Gift Cards uninstall routine.
 *
 * Drops the plugin table and removes plugin options when the user deletes the
 * plugin from the WordPress admin.
 *
 * @package GiftCards
 */

defined('WP_UNINSTALL_PLUGIN') || exit;

global $wpdb;

// Drop the gift-cards table.
$giftcards_table = $wpdb->prefix . 'giftcards';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name from $wpdb->prefix, cannot be parameterised.
$wpdb->query( "DROP TABLE IF EXISTS {$giftcards_table}" );

// Remove options.
delete_option( 'giftcards_settings' );
delete_option( 'giftcards_db_version' );

<?php
/**
 * Uninstall cleanup for lessthanweb Order-to-Board Sync.
 *
 * @package lessthanweb_Order_To_Board_Sync
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'lessthanweb_order_to_board_sync_settings' );
delete_option( 'lessthanweb_order_to_board_sync_setup_complete' );
delete_option( 'lessthanweb_order_to_board_sync_oauth_client_secret' );
delete_option( 'lessthanweb_order_to_board_sync_trello_oauth_tokens' );
delete_option( 'lessthanweb_order_to_board_sync' );
delete_transient( 'lessthanweb_order_to_board_sync_boards' );

global $wpdb;

$lessthanweb_order_to_board_sync_card_claim_option_prefix = $wpdb->esc_like( 'lessthanweb_order_to_board_sync_card_claim_' ) . '%';

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Bulk delete of dynamically-named options on uninstall; no caching needed.
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		$lessthanweb_order_to_board_sync_card_claim_option_prefix
	)
);

<?php
namespace lessthanweb\Order_To_Board_Sync\Admin;

/**
 * Handles admin settings form submission and processing.
 *
 * @author		lessthanweb
 * @package 	lessthanweb_Order_To_Board_Sync
 * @since		1.0.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use lessthanweb\Order_To_Board_Sync\Core\Settings;
use lessthanweb\Order_To_Board_Sync\Core\Hooks;
use lessthanweb\Order_To_Board_Sync\Admin\Notices;

/**
 * Settings_Handler Class
 */
class Settings_Handler {
	/**
	 * Constructor
	 *
	 * @param	Settings    $settings
	 * @param	Notices $notices
	 * @return	void
	 * @since	1.0.0
	 *
	 */
	public function __construct( private Settings $settings, private Notices $notices ) {
		// Register a plugin-specific route for this settings form.
		add_action( 'admin_post_' . Hooks::ACTION_SAVE_SETTINGS, [ $this, 'handle_save' ] ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Hook name is a prefixed class constant.
	}

	/**
	 * Handle settings form submission.
	 *
	 * Verifies nonce, extracts data from $_POST, validates and saves settings,
	 * displays appropriate admin notice, and redirects back to settings page.
	 *
	 * @param	void
	 * @return	void
	 * @since	1.0.0
	 *
	 */
	public function handle_save(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to save these settings.', 'lessthanweb-order-to-board-sync' ) );
		}

		// Verify nonce
		if ( ! isset( $_POST['lessthanweb_order_to_board_sync_settings'] ) || ! is_string( $_POST['lessthanweb_order_to_board_sync_settings'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lessthanweb_order_to_board_sync_settings'] ) ), 'lessthanweb_order_to_board_sync_save_settings' ) ) {
			wp_die( esc_html__( 'Sorry, your nonce did not verify.', 'lessthanweb-order-to-board-sync' ) );
		}

		// Accept only a URL string and sanitize it before the redirect.
		$url_redirect_to = '';

		if ( isset( $_POST['lessthanweb_order_to_board_sync_current_page'] ) && is_string( $_POST['lessthanweb_order_to_board_sync_current_page'] ) ) {
			$url_redirect_to = esc_url_raw( wp_unslash( $_POST['lessthanweb_order_to_board_sync_current_page'] ) );
		}

		// Unslash only the expected settings array; field-specific sanitization runs before extension filters.
		$data = [];

		if ( isset( $_POST['lessthanweb_order_to_board_sync'] ) && is_array( $_POST['lessthanweb_order_to_board_sync'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Settings::save() applies field-specific sanitization before persistence.
			$data = wp_unslash( $_POST['lessthanweb_order_to_board_sync'] );
		}

		// Save settings and get result.
        // Array on success and WP_Error on issue..
		$result = $this->settings->save( $data );

		// Check if there was an error
		if ( is_wp_error( $result ) ) {
			$this->notices->error( $result->get_error_message() );
		} else {
			$this->notices->success( __( 'Settings saved successfully!', 'lessthanweb-order-to-board-sync' ) );
		}

		// Redirect back to settings page
		if ( ! empty( $url_redirect_to ) ) {
			wp_safe_redirect( $url_redirect_to );
		} else {
			wp_safe_redirect( admin_url( 'admin.php?page=' . LESSTHANWEB_ORDER_TO_BOARD_SYNC_PAGE_NAME ) );
		}
		
        die();
	}
}

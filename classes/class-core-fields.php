<?php
namespace lessthanweb\Order_To_Board_Sync\Core;

use DateTime;

/**
 * Contains field definitions
 *
 * @author		lessthanweb
 * @package 	lessthanweb_Order_To_Board_Sync
 * @since		1.0.0
 *
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fields Class
 */
class Fields {
    /**
	 * Due date options
	 *
	 * @var	array
	 */
    private array $due_date_options;

    public function __construct() {
        $this->due_date_options = [
            [
                'value' => 'hour',
                'text' => __( 'Hour(s)', 'lessthanweb-order-to-board-sync' ),
            ],
            [
                'value' => 'day',
                'text' => __( 'Day(s)', 'lessthanweb-order-to-board-sync' ),
            ],
            [
                'value' => 'week',
                'text' => __( 'Week(s)', 'lessthanweb-order-to-board-sync' ),
            ],
            [
                'value' => 'month',
                'text' => __( 'Month(s)', 'lessthanweb-order-to-board-sync' ),
            ]
        ];
    }

    /**
	 * Define all form fields.
	 *
	 * @param	void
	 * @return	array
     * @since	1.0.0
	 *
	 */
    public function get_fields(): array {
        return [
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Hook name is a prefixed class constant.
            'cards' => apply_filters( Hooks::FILTER_FIELDS_CARDS, [
                'enabled' => [
                    'type' => 'switch',
                    'html_id' => 'enabled',
                    'name' => 'enabled',
                    'label' => __( 'Enable', 'lessthanweb-order-to-board-sync' ),
                    'sanitize_callback' => '',
                    'validate_callback' => [ $this, 'validate_switch' ]
                ],
				'board_id' => [
                    'type' => 'select',
                    'html_id' => 'board-id',
                    'name' => 'board_id',
                    'label' => __( 'Board', 'lessthanweb-order-to-board-sync' ),
					'options' => [],
					'classes' => 'lessthanweb-order-to-board-sync-boards',
                    'loader' => true,
                    'sanitize_callback' => [ $this, 'trello_sanitize_board_list_id' ],
                    'validate_callback' => ''
                ],
				'list_id' => [
                    'type' => 'select',
                    'html_id' => 'list-id',
                    'name' => 'list_id',
                    'label' => __( 'List', 'lessthanweb-order-to-board-sync' ),
					'options' => [],
                    'loader' => true,
					'classes' => 'lessthanweb-order-to-board-sync-lists',
                    'sanitize_callback' => [ $this, 'trello_sanitize_board_list_id' ],
                    'validate_callback' => ''
                ],
                'card_name' => [
                    'type' => 'text',
                    'html_id' => 'card-name',
                    'name' => 'card_name',
                    'label' => __( 'Name', 'lessthanweb-order-to-board-sync' ),
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => ''
                ],
                'card_color' => [
                    'type' => 'color',
                    'html_id' => 'card-color',
                    'name' => 'card_color',
                    'label' => __( 'Color', 'lessthanweb-order-to-board-sync' ),
                    'classes' => 'lessthanweb-order-to-board-sync-color-field',
                    'selected' => 'pink',
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => [ $this, 'validate_card_color' ]
                ],
                'card_description' => [
                    'type' => 'textarea',
                    'html_id' => 'card-description',
                    'name' => 'card_description',
                    'label' => __( 'Description', 'lessthanweb-order-to-board-sync' ),
                    'sanitize_callback' => 'sanitize_textarea_field',
                    'validate_callback' => ''
                ],
                'card_due_date' => [
                    'type' => 'number_with_select',
                    'html_id' => 'card-due-date',
                    'name' => 'card_due_date',
                    'label' => __( 'Due Date', 'lessthanweb-order-to-board-sync' ),
                    'options' => $this->due_date_options,
                    'classes' => 'lessthanweb-order-to-board-sync-die-date-field',
                    'sanitize_callback' => 'sanitize_text_field' ,
                    'validate_callback' => [ $this, 'validate_due_date' ]
                ],
                'card_position' => [
                    'type' => 'select',
                    'html_id' => 'card-position',
                    'name' => 'card_position',
                    'label' => __( 'Position', 'lessthanweb-order-to-board-sync' ),
					'options' => [
                        [
                            'value' => 'top',
                            'text' => __( 'Top', 'lessthanweb-order-to-board-sync' ),
                        ],
                        [
                            'value' => 'bottom',
                            'text' => __( 'Bottom', 'lessthanweb-order-to-board-sync' ),
                        ]
                    ],
					'classes' => 'lessthanweb-order-to-board-sync-position',
                    'sanitize_callback' => '',
                    'validate_callback' => [ $this, 'validate_card_position' ]
                ],
                'include_order_url' => [
                    'type' => 'switch',
                    'html_id' => 'include-order-url',
                    'name' => 'include_order_url',
                    'label' => __( 'Include Order URL', 'lessthanweb-order-to-board-sync' ),
                    'note' => __( 'Enabling this will add URL to the order in the admin area to the card.', 'lessthanweb-order-to-board-sync' ),
                    'sanitize_callback' => '',
                    'validate_callback' => [ $this, 'validate_switch' ]
                ],
                'billing_address' => [
                    'type' => 'switch',
                    'html_id' => 'billing-address',
                    'name' => 'billing_address',
                    'checked' => true,
                    'label' => __( 'Include Billing Address', 'lessthanweb-order-to-board-sync' ),
                    'sanitize_callback' => '',
                    'validate_callback' => [ $this, 'validate_switch' ]
                ],
                'shipping_address' => [
                    'type' => 'switch',
                    'html_id' => 'shipping-address',
                    'name' => 'shipping_address',
                    'label' => __( 'Include Shipping Address', 'lessthanweb-order-to-board-sync' ),
                    'note' => __( 'Shipping address will be included only if it is different then billing address.', 'lessthanweb-order-to-board-sync' ),
                    'sanitize_callback' => '',
                    'validate_callback' => [ $this, 'validate_switch' ]
                ],
                'shipping_method' => [
                    'type' => 'switch',
                    'html_id' => 'shipping-method',
                    'name' => 'shipping_method',
                    'label' => __( 'Include Shipping Method', 'lessthanweb-order-to-board-sync' ),
                    'sanitize_callback' => '',
                    'validate_callback' => [ $this, 'validate_switch' ]
                ],
                'ordered_products' => [
                    'type' => 'switch',
                    'html_id' => 'ordered-products',
                    'name' => 'ordered_products',
                    'checked' => true,
                    'label' => __( 'Include Product List', 'lessthanweb-order-to-board-sync' ),
                    'note' => __( 'Adds the ordered products, quantities, and line totals to the Trello card.', 'lessthanweb-order-to-board-sync' ),
                    'sanitize_callback' => '',
                    'validate_callback' => [ $this, 'validate_switch' ]
                ],
                'total_costs' => [
                    'type' => 'switch',
                    'html_id' => 'totalcosts',
                    'name' => 'total_costs',
                    'label' => __( 'Include Total Costs', 'lessthanweb-order-to-board-sync' ),
                    'note' => __( 'Includes total costs with taxes and shipping.', 'lessthanweb-order-to-board-sync' ),
                    'sanitize_callback' => '',
                    'validate_callback' => [ $this, 'validate_switch' ]
                ],
                'bottom_save_separator' => [
                    'type' => 'separator',
                    'html_id' => 'bottom-save-separator',
                ],
                'btn_save' => [
                    'type' => 'btn_save',
                    'html_id' => 'btn-save',
                    'name' => 'btn_save',
                    'value' => __( 'Save', 'lessthanweb-order-to-board-sync' ),
                ],
            ] ),
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Hook name is a prefixed class constant.
            'settings' => apply_filters( Hooks::FILTER_FIELDS_SETTINGS, [
                'trello_api_instructions' => [
                    'section' => 'trello_api',
                    'type' => 'paragraph',
                    'html_id' => 'trello-api-instructions',
                    'text' => sprintf(
                        __( 'Create a Trello OAuth 2.0 client, then enter its client ID and secret below. Setup steps are available in the %s tab.', 'lessthanweb-order-to-board-sync' ),
                        '<a href="#help">' . esc_html__( 'Help', 'lessthanweb-order-to-board-sync' ) . '</a>'
                    ),
                ],
                'oauth_client_id' => [
                    'section' => 'trello_api',
                    'type' => 'text',
                    'html_id' => 'oauth-client-id',
                    'name' => 'oauth_client_id',
                    'label' => __( 'OAuth client ID', 'lessthanweb-order-to-board-sync' ),
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'oauth_client_secret' => [
                    'section' => 'trello_api',
                    'type' => 'password',
                    'html_id' => 'oauth-client-secret',
                    'name' => 'oauth_client_secret',
                    'label' => __( 'OAuth client secret', 'lessthanweb-order-to-board-sync' ),
                    'note' => __( 'Leave blank to keep the saved secret. The secret is never displayed after saving.', 'lessthanweb-order-to-board-sync' ),
                    'sensitive' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'oauth_controls' => [
                    'section' => 'trello_api',
                    'type' => 'oauth_controls',
                    'html_id' => 'trello-oauth-controls',
                ],
                'btn_save' => [
                    'section' => 'trello_api',
                    'type' => 'btn_save',
                    'html_id' => 'btn-save',
                    'name' => 'btn_save',
                    'value' => __( 'Save', 'lessthanweb-order-to-board-sync' ),
                ],
            ] ),
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Hook name is a prefixed class constant.
            'status' => apply_filters( Hooks::FILTER_FIELDS_STATUS, [
                //  Fields shown under System Info tab
                'system_info' => [
                    'section' => 'system_info',
                    'type' => 'system_info',
                    'html_id' => 'system-info',
                ],

                //  This is for the Logs tab
                'log' => [
                    'section' => 'logs',
                    'type' => 'log',
                    'html_id' => 'log',
                ],
            ] ),
            'rows' => [
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Hook name is a prefixed class constant.
                'cards' => apply_filters( Hooks::FILTER_FIELDS_CARDS_ROWS, [
                    [
                        'fields' => [ 'enabled' ]
                    ],
                    [
                        'fields' => [ 'board_id', 'list_id' ]
                    ],
                    [
                        'fields' => [ 'card_name', 'card_color' ]
                    ],
                    [
                        'fields' => [ 'card_name_instructions' ]
                    ],
                    [
                        'fields' => [ 'card_description' ]
                    ],
                    [
                        'fields' => [ 'card_position', 'card_due_date' ]
                    ],
                    [
                        'fields' => [ 'include_order_url' ]
                    ],
                    [
                        'fields' => [ 'billing_address' ]
                    ],
                    [
                        'fields' => [ 'shipping_address' ]
                    ],
                    [
                        'fields' => [ 'shipping_method' ]
                    ],
                    [
                        'fields' => [ 'ordered_products' ]
                    ],
                    [
                        'fields' => [ 'total_costs' ]
                    ],
                    [
                        'fields' => [ 'bottom_save_separator' ]
                    ],
                    [
                        'fields' => [ 'btn_save' ]
                    ],
                ] ),
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Hook name is a prefixed class constant.
                'settings' => apply_filters( Hooks::FILTER_FIELDS_SETTINGS_ROWS, [
                    [
                        'fields' => [ 'trello_api_instructions' ]
                    ],
                    [
                        'fields' => [ 'oauth_client_id' ]
                    ],
                    [
                        'fields' => [ 'oauth_client_secret' ]
                    ],
                    [
                        'fields' => [ 'oauth_controls' ]
                    ],
                    [
                        'fields' => [ 'btn_save' ]
                    ],
                ] ),
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Hook name is a prefixed class constant.
                'status' => apply_filters( Hooks::FILTER_FIELDS_STATUS_ROWS, [
                    [
                        'fields' => [ 'system_info' ]
                    ],
                    [
                        'fields' => [ 'log' ]
                    ],
                ] )
            ]
        ];
    }
    
    /**
	 * Get a single field configuration.
	 *
	 * @param	string  $field_name
	 * @return	array|null
	 * @since	1.0.0
	 *
	 */
    public function get_field( string $field_name ): ?array {
        $fields = self::get_fields();

        return $fields[ $field_name ] ?? null;
    }

    /**
	 * Sanitize settings before passing them to an extension filter.
	 *
	 * @param	array	$data	Submitted settings.
	 * @return	array
	 * @since	1.0.0
	 *
	 */
	public function sanitize_for_extension_filter( array $data ): array {
		// Sanitize with each field's configured callback so descriptions and IDs keep their intended formats.
		$fields_config = $this->get_fields();

		foreach ( $data as $section_key => $section ) {
			if ( ! is_array( $section ) ) {
				$data[ $section_key ] = $this->sanitize_unconfigured_value( $section );

				continue;
			}

			foreach ( $section as $context_key => $context ) {
				if ( ! is_array( $context ) ) {
					$data[ $section_key ][ $context_key ] = $this->sanitize_unconfigured_value( $context );

					continue;
				}

				foreach ( $context as $field_key => $field_value ) {
					if ( $section_key === 'cards' && $field_key === 'board_lists' ) {
						$data[ $section_key ][ $context_key ][ $field_key ] = $this->sanitize_board_list_destinations( $field_value, $fields_config[ $section_key ] ?? [] );

						continue;
					}

					$field_config = $fields_config[ $section_key ][ $field_key ] ?? null;

					$data[ $section_key ][ $context_key ][ $field_key ] = is_array( $field_config ) ? $this->sanitize_configured_value( $field_config, $field_value ) : $this->sanitize_unconfigured_value( $field_value );
				}
			}
		}

		return $data;
	}

	/**
	 * Validate submitted setting sections, contexts, fields, and destination row keys.
	 *
	 * @param	array	$data	Submitted settings.
	 * @return	true|\WP_Error
	 * @since	1.2.0
	 *
	 */
	public function validate_settings_structure( array $data ): true|\WP_Error {
		$fields_config = $this->get_fields();

		foreach ( $data as $section_key => $section ) {
			// Reject sections that are not registered by the plugin or its extensions.
			if ( ! is_string( $section_key ) || ! isset( $fields_config[ $section_key ] ) || ! is_array( $fields_config[ $section_key ] ) || ! is_array( $section ) ) {
				return new \WP_Error( 'invalid_settings_structure', __( 'The submitted settings contain an invalid section.', 'lessthanweb-order-to-board-sync' ) );
			}

			$allowed_contexts = $this->get_allowed_setting_contexts( $section_key );

			foreach ( $section as $context_key => $context ) {
				// Context names must be registered tabs or valid WooCommerce order statuses.
				if ( ! is_string( $context_key ) || sanitize_key( $context_key ) !== $context_key || ! isset( $allowed_contexts[ $context_key ] ) || ! is_array( $context ) ) {
					return new \WP_Error( 'invalid_settings_structure', __( 'The submitted settings contain an invalid context.', 'lessthanweb-order-to-board-sync' ) );
				}

				foreach ( $context as $field_key => $field_value ) {
					if ( $section_key === 'cards' && $field_key === 'board_lists' ) {
						$structure_error = $this->validate_destination_rows_structure( $field_value, $fields_config[ $section_key ] );

						if ( is_wp_error( $structure_error ) ) {
							return $structure_error;
						}

						continue;
					}

					// Only registered fields may be submitted in their configured tab.
					if ( ! is_string( $field_key ) || ! isset( $fields_config[ $section_key ][ $field_key ] ) || ! is_array( $fields_config[ $section_key ][ $field_key ] ) ) {
						return new \WP_Error( 'invalid_settings_structure', __( 'The submitted settings contain an unregistered field.', 'lessthanweb-order-to-board-sync' ) );
					}

					$field_context = $fields_config[ $section_key ][ $field_key ]['section'] ?? null;

					if ( $section_key !== 'cards' && $field_context !== null && $field_context !== $context_key ) {
						return new \WP_Error( 'invalid_settings_structure', __( 'The submitted settings contain a field in the wrong context.', 'lessthanweb-order-to-board-sync' ) );
					}
				}
			}
		}

		return true;
	}

	/**
	 * Get valid context keys for one settings section.
	 *
	 * @param	string	$section_key	Settings section.
	 * @return	array
	 * @since	1.2.0
	 *
	 */
	private function get_allowed_setting_contexts( string $section_key ): array {
		$contexts = [];

		if ( $section_key === 'cards' ) {
			// Match the statuses shown by the card settings UI, including extension-added statuses.
			$order_statuses = ( new \lessthanweb\Order_To_Board_Sync\WC\Order_Status() )->get_list_of_order_statuses();

			foreach ( array_keys( $order_statuses ) as $status_key ) {
				if ( is_string( $status_key ) && sanitize_key( $status_key ) === $status_key ) {
					$contexts[ $status_key ] = true;
				}
			}

			return $contexts;
		}

		// Use the same tab filters as the admin page instead of trusting submitted context names.
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Hook name is a prefixed class constant.
		$tabs = $section_key === 'settings' ? apply_filters( Hooks::FILTER_SETTINGS_TABS, [ 'trello_api' => '' ] ) : apply_filters( Hooks::FILTER_STATUS_TABS, [ 'system_info' => '', 'logs' => '' ] );

		if ( ! is_array( $tabs ) ) {
			return $contexts;
		}

		foreach ( array_keys( $tabs ) as $context_key ) {
			if ( is_string( $context_key ) && sanitize_key( $context_key ) === $context_key ) {
				$contexts[ $context_key ] = true;
			}
		}

		return $contexts;
	}

	/**
	 * Validate repeatable destination row keys and registered row field keys.
	 *
	 * @param	mixed	$destinations	Submitted destination rows.
	 * @param	array	$fields_config	Registered card fields.
	 * @return	true|\WP_Error
	 * @since	1.2.0
	 *
	 */
	private function validate_destination_rows_structure( mixed $destinations, array $fields_config ): true|\WP_Error {
		if ( ! is_array( $destinations ) ) {
			return new \WP_Error( 'invalid_settings_structure', __( 'The submitted board/list destinations are invalid.', 'lessthanweb-order-to-board-sync' ) );
		}

		foreach ( $destinations as $row_key => $row ) {
			// Form-generated repeatable row keys are non-negative numeric indexes.
			if ( ( ! is_int( $row_key ) && ( ! is_string( $row_key ) || ! ctype_digit( $row_key ) ) ) || ( is_int( $row_key ) && $row_key < 0 ) || ! is_array( $row ) ) {
				return new \WP_Error( 'invalid_settings_structure', __( 'The submitted board/list destinations contain an invalid row.', 'lessthanweb-order-to-board-sync' ) );
			}

			foreach ( $row as $field_key => $field_value ) {
				// Destination extensions must register row fields in the Cards field configuration.
				if ( ! is_string( $field_key ) || ! isset( $fields_config[ $field_key ] ) ) {
					return new \WP_Error( 'invalid_settings_structure', __( 'The submitted board/list destinations contain an unregistered field.', 'lessthanweb-order-to-board-sync' ) );
				}
			}
		}

		return true;
	}

	/**
	 * Sanitize one configured field value without running its validator.
	 *
	 * @param	array	$field_config	Field configuration.
	 * @param	mixed	$field_value	Field value.
	 * @return	mixed
	 * @since	1.0.0
	 *
	 */
	private function sanitize_configured_value( array $field_config, mixed $field_value ): mixed {
		// Avoid invoking scalar field sanitizers with attacker-controlled nested values.
		if ( is_array( $field_value ) ) {
			return $this->sanitize_unconfigured_value( $field_value );
		}

		if ( $field_value === null ) {
			return null;
		}

		if ( ! is_scalar( $field_value ) ) {
			return '';
		}

		$value = (string) $field_value;

		if ( ! empty( $field_config['sanitize_callback'] ) ) {
			return call_user_func( $field_config['sanitize_callback'], $value );
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Recursively sanitize values without a field-specific configuration.
	 *
	 * @param	mixed	$value	Input value.
	 * @return	mixed
	 * @since	1.0.0
	 *
	 */
	private function sanitize_unconfigured_value( mixed $value ): mixed {
		// Preserve extension payload structure while sanitizing every submitted string leaf.
		if ( is_string( $value ) ) {
			return sanitize_textarea_field( $value );
		}

		if ( is_array( $value ) ) {
			foreach ( $value as $key => $nested_value ) {
				$value[ $key ] = $this->sanitize_unconfigured_value( $nested_value );
			}

			return $value;
		}

		return is_scalar( $value ) || $value === null ? $value : '';
	}

	/**
	 * Sanitize board/list IDs and any extension data attached to their rows.
	 *
	 * @param	mixed	$destinations	Board/List rows.
	 * @param	array	$fields_config	Field configuration.
	 * @return	mixed
	 * @since	1.0.0
	 *
	 */
	private function sanitize_board_list_destinations( mixed $destinations, array $fields_config ): mixed {
		// Preserve row structure while sanitizing both IDs and extension-provided row values.
		if ( ! is_array( $destinations ) ) {
			return $this->sanitize_unconfigured_value( $destinations );
		}

		foreach ( $destinations as $row_key => $row ) {
			if ( ! is_array( $row ) ) {
				$destinations[ $row_key ] = $this->sanitize_unconfigured_value( $row );

				continue;
			}

			foreach ( $row as $field_key => $field_value ) {
				$field_config = $fields_config[ $field_key ] ?? null;
				$destinations[ $row_key ][ $field_key ] = is_array( $field_config ) ? $this->sanitize_configured_value( $field_config, $field_value ) : $this->sanitize_unconfigured_value( $field_value );
			}
		}

		return $destinations;
	}

	/**
     * Validate and sanitize form data based on field configuration.
     *
     * @param	array   $data Form data to validate and sanitize
     * @return	array|\WP_Error  Validated and sanitized data or WP_Error on validation failure
     * @since	1.0.0
     *
     */
    public function validate_and_sanitize( array $data ): array|\WP_Error {
		// Get config for all fields
        $fields_config = $this->get_fields();

        $validated = [];

		// Loop over submitted data
		foreach ( $data as $section_key => $section ) {
			if ( ! is_array( $section ) || ! isset( $fields_config[ $section_key ] ) ) {
				continue;
			}

			foreach ( $section as $context_key => $context ) {
				if ( ! is_array( $context ) ) {
					continue;
				}

				foreach ( $context as $field_key => $field_value ) {
					if ( $section_key === 'cards' && $field_key === 'board_lists' ) {
						if ( ! is_array( $field_value ) ) {
							continue;
						}

						foreach ( $field_value as $row_key => $row ) {
							if ( ! is_array( $row ) ) {
								continue;
							}

							foreach ( [ 'board_id', 'list_id' ] as $destination_field_key ) {
								if ( ! array_key_exists( $destination_field_key, $row ) || ! isset( $fields_config[ $section_key ][ $destination_field_key ] ) ) {
									continue;
								}

								$field_result = $this->sanitize_and_validate_field( $fields_config[ $section_key ][ $destination_field_key ], $row[ $destination_field_key ] );

								if ( is_wp_error( $field_result ) ) {
									return $field_result;
								}

								$validated[ $section_key ][ $context_key ][ $field_key ][ $row_key ][ $destination_field_key ] = $field_result;
							}
						}

						// Let extensions safely retain validated data stored alongside each Board/List destination.
                        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Hook name is a prefixed class constant.
						$validated_destinations = apply_filters( Hooks::FILTER_BOARD_LIST_DESTINATIONS,
							$validated[ $section_key ][ $context_key ][ $field_key ] ?? [],
							$field_value,
							$section_key,
							$context_key
						);

						if ( ! is_array( $validated_destinations ) ) {
							return new \WP_Error( 'invalid_destinations', __( 'Board/List destination filters must return an array.', 'lessthanweb-order-to-board-sync' ) );
						}

						$validated[ $section_key ][ $context_key ][ $field_key ] = $validated_destinations;

						continue;
					}

					if ( ! isset( $fields_config[ $section_key ][ $field_key ] ) ) {
						continue;
					}

					$field_result = $this->sanitize_and_validate_field( $fields_config[ $section_key ][ $field_key ], $field_value );

					if ( is_wp_error( $field_result ) ) {
						return $field_result;
					}

					$validated[ $section_key ][ $context_key ][ $field_key ] = $field_result;
				}
			}
		}

		return $validated;
    }

    /**
     * Sanitize and validate one configured field value.
     *
     * @param	array	$field_config	Field configuration.
     * @param	mixed	$field_value	Field value.
     * @return	mixed	Sanitized value or validation error.
     * @since	1.0.0
     *
     */
    private function sanitize_and_validate_field( array $field_config, mixed $field_value ): mixed {
		// Reject malformed scalar fields before callbacks can receive unexpected arrays or objects.
		if ( $field_value !== null && ! is_string( $field_value ) ) {
			return new \WP_Error( 'invalid_field_value', __( 'A submitted setting has an invalid value.', 'lessthanweb-order-to-board-sync' ) );
		}

		$value = $field_value;

		if ( ! empty( $field_config['sanitize_callback'] ) ) {
			$value = call_user_func( $field_config['sanitize_callback'], $field_value );
		}

		if ( ! empty( $field_config['validate_callback'] ) ) {
			$result = call_user_func( $field_config['validate_callback'], $field_value );

			if ( $result !== true ) {
				return new \WP_Error( 'validation', $result );
			}
		}

		return $value;
    }

    /**
     * Removes everything from string apart from a-z and 0-9.
     *
     * @param	string|null   $text Form data to validate and sanitize
     * @return	string
     * @since	1.0.0
     *
     */
    private function trello_sanitize_board_list_id( ?string $text ): string {
        if ( $text === null ) {
            return '';
        }

        // Convert to lowercase and remove everything except a-z and 0-9
        $sanitized = strtolower( $text );
        $sanitized = preg_replace( '/[^a-z0-9]/', '', $sanitized );

        return $sanitized;
    }

    /**
     * Validate switch element.
     * 
     * Switch should have two states only:
     * On: 'on'
     * Off: ''
     * 
     * Anything else then that we remove and set default which should be turned off state or ''.
     *
     * @param	string|null   $value
     * @return	boolean|string
     * @since	1.0.0
     *
     */
    public function validate_switch( ?string $value ): bool|string {
        if ( $value === null || $value === '' || $value == 'on' ) {
            return true;
        } else {
            return __( 'One of the card switch elements has an invalid value.', 'lessthanweb-order-to-board-sync' );
        }
    }

    /**
     * Validate color element.
     * 
     * Trello allows only specific colors to be set.
     * 
     * If input is anything other then set colors or empty, we 
     * 
     * Anything else then that we remove and set default which should be turned off state or ''.
     *
     * @param	string|null   $value
     * @return	boolean|string
     * @since	1.0.0
     *
     */
    private function validate_card_color( ?string $value ): bool|string {
        $colors = Helper::trello_colors();

        if ( $value === null || $value === '' || in_array( $value, $colors ) ) {
            return true;
        } else {
            return __( 'Selected card color is not valid.', 'lessthanweb-order-to-board-sync' );
        }
    }

    /**
     * Validate color element.
     * 
     * Trello allows only specific colors to be set.
     * 
     * If input is anything other then set colors or empty, we 
     * 
     * Anything else then that we remove and set default which should be turned off state or ''.
     *
     * @param	string|null   $value
     * @return	boolean|string
     * @since	1.0.0
     *
     */
    private function validate_card_position( ?string $value ): bool|string {
        if ( $value === null || $value === '' || $value === 'top' || $value === 'bottom' ) {
            return true;
        } else {
            return __( 'Selected card position is not valid.', 'lessthanweb-order-to-board-sync' );
        }
    }

    /**
     * Sanitize number field
     *
     * @param	string|null   $number
     * @return	string
     * @since	1.0.0
     *
     */
    private function sanitize_number_field( ?string $number ): string {
        return filter_var( $number, FILTER_SANITIZE_NUMBER_INT );
    }

    /**
     * Validate number field
     *
     * @param	string|null   $number
     * @return	boolean|string
     * @since	1.0.0
     *
     */
    private function validate_number_field( ?string $number ): bool|string {
        if ( ! filter_var( $number, FILTER_VALIDATE_INT ) ) {
            return __( 'Provided number is not a valid integer.', 'lessthanweb-order-to-board-sync' );
        } else {
            return true;
        }
    }

    /**
     * Validate due date field.
     * 
     * Due date is a hidden field which is built from number field and select box.
     * 
     * It should be in format like:
     * - 1 hour
     * - 1 day
     * - 1 week
     * - 1 month
     * 
     * This is then used to generate a "real" date :)
     * 
     * Default being: 0 hour
     * We accept empty string or null as well.
     *
     * @param	string|null   $date
     * @return	boolean|string
     * @since	1.0.0
     *
     */
    private function validate_due_date( ?string $date ): bool|string {
        if ( $date === null || empty( $date ) ) {
            return true;
        }

        //  Check if supplied date string is in valid format
        if ( Helper::validate_date( $date ) === false ) {
            return __( 'Invalid due date set.', 'lessthanweb-order-to-board-sync' );
        }
        
        return true;
    }
}
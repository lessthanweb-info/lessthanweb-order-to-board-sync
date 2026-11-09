jQuery( document ).ready( function( $ ) {
    const lessthanweb_order_to_board_sync_top_tab_links = $( '.lessthanweb-order-to-board-sync-top-tabs .top-tab' );
    const lessthanweb_order_to_board_sync_top_tab_contents = $('.lessthanweb-order-to-board-sync-top-content');
    let lessthanweb_order_to_board_sync_current_top_tab_id = 'cards';

    $( '.lessthanweb-order-to-board-sync-top-tabs' ).on( 'click', '.top-tab', function ( e ) {
        e.preventDefault();

        const tab_id = $( this ).data( 'top-id' );

        history.replaceState( null, null, '#' + tab_id );

        lessthanweb_order_to_board_sync_current_top_tab_id = tab_id;

        lessthanweb_order_to_board_sync_set_active_tab();
    } );

    $( '.lessthanweb-order-to-board-sync-container' ).on( 'click', '.tab-link', function ( e ) {
        e.preventDefault();

        const tab_id = $( this ).data( 'tab-id' );

        $( '.lessthanweb-order-to-board-sync-top-content-' + lessthanweb_order_to_board_sync_current_top_tab_id + ' .lessthanweb-order-to-board-sync-container .tab-link' ).removeClass( 'active' );
        $( '.lessthanweb-order-to-board-sync-top-content-' + lessthanweb_order_to_board_sync_current_top_tab_id + ' .lessthanweb-order-to-board-sync-container .tab-content' ).removeClass( 'active' );

        $( this ).addClass( 'active' );

        $( this ).parents( '.lessthanweb-order-to-board-sync-container' ).children( '.lessthanweb-order-to-board-sync-content' ).children( '#' + tab_id ).addClass( 'active' );
    } );

    //  Set active tab based on hash from URL if there is any
    let lessthanweb_order_to_board_sync_set_active_tab = function() {
        let hash = window.location.hash.substring( 1 );

        if ( hash ) {
            //  Hide all tabs
            lessthanweb_order_to_board_sync_top_tab_links.removeClass( 'active' );
            lessthanweb_order_to_board_sync_top_tab_contents.removeClass( 'active' );

            //  Find and activate the tab with matching data-top-id
            const active_tab = $( '.lessthanweb-order-to-board-sync-top-tabs' ).children( '.top-tab[data-top-id="' + hash + '"]' );

            if ( active_tab.length ) {
                active_tab.addClass( 'active' );

                lessthanweb_order_to_board_sync_current_top_tab_id = hash;

                // Update the forms URL as well
                $( '#lessthanweb-order-to-board-sync-current-page' ).val( $( '#lessthanweb-order-to-board-sync-page-url' ).val() + '#' + hash );

                $( '.lessthanweb-order-to-board-sync-main-content' ).children( '.lessthanweb-order-to-board-sync-top-content-' + hash ).addClass( 'active' );
            }
        }
    }

    lessthanweb_order_to_board_sync_set_active_tab();

    //  Clicking the back/forward buttons in browser changes the tab as well
    $( window ).on( 'hashchange', lessthanweb_order_to_board_sync_set_active_tab );

    //  Delete log handler
    $( '.lessthanweb-logs-container' ).on( 'click', '.lessthanweb-wc-log-delete', function( e ) {
        e.preventDefault();

        const log_id = $( this ).data( 'log-id' );
        const log_element = $( '#lessthanweb-order-to-board-sync-' + log_id );

        $.ajax( {
            type: 'post',
            url: lessthanweb_order_to_board_sync.ajax_url,
            data: {
                'action': 'lessthanweb_trello_delete_log',
                'nonce': lessthanweb_order_to_board_sync.nonce,
                'log_id': log_id
            },
            dataType: 'json',
            success: function( response ) {
                if ( response.success ) {
                    //  Remove the log element from the list
                    log_element.fadeOut( 200, function() {
                        $( this ).remove();

                        //  Check if there are any logs left
                        if ( $( '.lessthanweb-logs-container .lessthanweb-wc-log' ).length === 0 ) {
                            //  If no logs remain, show "no logs" message
                            $( '#lessthanweb-logs' ).empty().append(
                                $( '<h2>' ).text( lessthanweb_order_to_board_sync.texts.logs ),
                                $( '<p>' ).text( lessthanweb_order_to_board_sync.texts.no_logs_available )
                            );
                        }
                    } );
                } else {
                    console.error( lessthanweb_order_to_board_sync.texts.error, response.data.message );
                }
            },
            error: function() {
                console.error( 'AJAX error when deleting log' );
            }
        } );
    } );

    //  Delete all logs handler
    $( '.lessthanweb-order-to-board-sync-content' ).on( 'click', '#lessthanweb-order-to-board-sync-delete-all-logs', function( e ) {
        e.preventDefault();

        //  Ask for confirmation as this is a destructive, irreversible action
        if ( ! window.confirm( lessthanweb_order_to_board_sync.texts.confirm_delete_all_logs ) ) {
            return;
        }

        $.ajax( {
            type: 'post',
            url: lessthanweb_order_to_board_sync.ajax_url,
            data: {
                'action': 'lessthanweb_trello_delete_all_logs',
                'nonce': lessthanweb_order_to_board_sync.nonce
            },
            dataType: 'json',
            success: function( response ) {
                if ( response.success ) {
                    //  Show "no logs" message and remove the logs list + delete-all button
                    $( '#lessthanweb-logs' ).empty().append(
                        $( '<h2>' ).text( lessthanweb_order_to_board_sync.texts.logs ),
                        $( '<p>' ).text( lessthanweb_order_to_board_sync.texts.no_logs_available )
                    );
                } else {
                    console.error( lessthanweb_order_to_board_sync.texts.error, response.data.message );
                }
            },
            error: function() {
                console.error( 'AJAX error when deleting all logs' );
            }
        } );
    } );

    //  Color picker field
    $( '.lessthanweb-order-to-board-sync .lessthanweb-order-to-board-sync-color-field' ).on( 'click', 'li', function( e ) {
        e.preventDefault();

        let selected_color = $( this ).data( 'color' );

        //  Remove selected
        $( this ).parent().children( 'li' ).removeClass( 'selected' );
        //  Add selected class to the current item
        $( this ).addClass( 'selected' );

        //  Add currently selected item to the input field
        $( this ).closest( 'details' ).children( 'input[type=hidden]' ).val( selected_color );

        //  Change summary
        $( this ).closest( '.lessthanweb-order-to-board-sync-color-field' ).children( 'summary' ).text( $( this ).text() );

        //  Close dropdown
        $( this ).closest( '.lessthanweb-order-to-board-sync-color-field' ).attr( 'open', false );
    } );

    //  Due date field
    $( '.lessthanweb-order-to-board-sync .lessthanweb-order-to-board-sync-due-date' ).on( 'change', 'input[type="number"], select', function( e ) {
        e.preventDefault();

        let hidden_field = $( this ).parent().children( 'input[type=hidden]' );
        let input_field = $( this ).parent().children( 'input[type=number]' );
        let select_field = $( this ).parent().children( 'select' );
        let due_date = input_field.val() + ' ' + select_field.val();

        //  Save the result to hidden field
        hidden_field.val( due_date );
    } );

    //  Copy the status report using the clipboard API, with a legacy fallback
    $( '#wp-status-copy-btn' ).on( 'click', function() {
        const textarea = document.getElementById( 'wp-status-plain-text' );
        const feedback = document.getElementById( 'wp-status-copy-feedback' );

        if ( ! textarea ) {
            return;
        }

        const text = textarea.value;
        const show_feedback = function( message ) {
            if ( feedback ) {
                feedback.textContent = message;
            }

            setTimeout( function() {
                if ( feedback ) {
                    feedback.textContent = '';
                }
            }, 2500 );
        };
        const fallback_copy = function() {
            textarea.hidden = false;
            textarea.select();

            try {
                document.execCommand( 'copy' );
                show_feedback( 'Copied!' );
            } catch ( error ) {
                show_feedback( 'Copy failed – please select the text manually.' );
            }

            textarea.hidden = true;
        };

        if ( navigator.clipboard && navigator.clipboard.writeText ) {
            navigator.clipboard.writeText( text ).then( function() {
                show_feedback( 'Copied!' );
            } ).catch( function() {
                fallback_copy();
            } );
        } else {
            fallback_copy();
        }
    } );
} );
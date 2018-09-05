jQuery( function( $ ) {
    'use strict';

    var $section_header = $( '#import h2' );
    var $import_btn = $( '#wp-review-import' );
    var default_error = { message: wprImportVars.server_error };
    var source, nonce;

    $import_btn.on( 'click', function( e ) {
        var $plugin = $( '#wp-review-import-source' ).find('option:selected' );
        var confirm_message = wprImportVars.confirm.replace( '%s', $plugin.text() );

        e.preventDefault();

        // If there's no plugin selected or the user bailed out.
        if ( ! $plugin.length ||
             ! $plugin.val() ||
             ! confirm( confirm_message ) ) {
            return;
        }

        nonce = $( '#wp-review-import-nonce' ).val();
        source = $plugin.val();

        $import_btn.prop( 'disabled', true );
        import_ratings();
    } );

    function import_ratings( offset ) {
        var request = wp.ajax.post( 'wp_review_import_rating', {
            source: source,
            offset: offset || 0,
            _ajax_nonce: nonce
        });

        request
            .done( on_import_success )
            .fail( on_import_error );
    }

    function on_import_success( res ) {
        add_import_notice( res.message, res.is_error ? '' : 'notice-info' );

        if ( res.is_done ) {
            $import_btn.prop( 'disabled', false );
        } else {
            import_ratings( res.offset );
        }
    }

    function on_import_error( errors ) {
        errors = $.isArray( errors ) ? errors : [ default_error ];

        $.each( errors, function() {
            add_import_notice( this.message );
        } );
        $import_btn.prop( 'disabled', false );
    }

    function add_import_notice( message, type ) {
        type = type || 'notice-error'

        $( '.wrap .notice' ).remove();
        $section_header.after('<div class="notice ' + type + '"><p>' + message + '</p></div>' );
    }

    $( '#wp-review-import-options-btn' ).on( 'click', function( ev ) {
        ev.preventDefault();
        var $btn, code, check;
        $btn = $( this );
        code = $( '#wp-review-import-options-code' ).val().trim();
        if ( ! code ) {
            return;
        }
        check = confirm( wprImportVars.confirmOptionsImport );
        if ( ! check ) {
            return;
        }
        $btn.prop( 'disabled', true );
        var request = wp.ajax.post( 'wp_review_import_options', {
            code: code,
            _ajax_nonce: wprImportVars.importOptionsNonce
        });
        request.done( function( response ) {
            window.location.href = window.location.href;
        });
        request.fail( function( response ) {
            console.error( response );
            $btn.prop( 'disabled', false );
        });
    });
} );

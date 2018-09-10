jQuery( function( $ ) {
    'use strict';

    var $section_header = $( '#import h2' );
    var default_error = { message: wprImportVars.server_error };
    var source, nonce;

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

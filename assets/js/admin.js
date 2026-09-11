( function ( $ ) {
	'use strict';

	$( function () {
		var $typeSelect = $( '#wfb_type' );

		function toggleTypeSettings() {
			var type = $typeSelect.val();

			$( '.wfb-type-settings' ).hide();
			$( '.wfb-type-settings[data-type="' + type + '"]' ).show();
		}

		$typeSelect.on( 'change', toggleTypeSettings );
		toggleTypeSettings();

		$( '.wfb-add-option' ).on( 'click', function () {
			var $table = $( this ).closest( 'p' ).prev( '.wfb-options-repeater' );
			var $row = $table.find( '.wfb-options-repeater-row' ).first().clone();

			$row.find( 'input' ).val( '' );
			$table.find( 'tbody' ).append( $row );
		} );

		$( document ).on( 'click', '.wfb-remove-option', function () {
			var $table = $( this ).closest( '.wfb-options-repeater' );

			if ( $table.find( '.wfb-options-repeater-row' ).length > 1 ) {
				$( this ).closest( '.wfb-options-repeater-row' ).remove();
			}
		} );

		$( 'input[name="wfb_scope"]' ).on( 'change', function () {
			$( '.wfb-specific-products' ).toggle( 'specific' === $( this ).val() );
		} );
	} );
} )( jQuery );

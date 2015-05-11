/**
 *  WP Rollback Scripts
 *
 *  @description:
 *  @copyright: http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

var wpr_vars;

jQuery.noConflict();
(function ( $ ) {

	//On DOM Ready
	$( function () {

		var form = $( '.rollback-form' );
		var form_labels = $( 'label', form.get( 0 ) );
		var form_submit_btn = $( '.magnific-popup' );

		//On Element Click
		form_labels.on( 'click', function () {

			//add a selected class
			form_labels.removeClass( 'wpr-selected' );
			form_submit_btn.removeClass( 'wpr-rollback-disabled' );
			$( this ).addClass( 'wpr-selected' );

			//ensure the radio button always gets clicked
			$( this ).find( 'input' ).prop( 'checked', true );

		} );

		//Modal
		form_submit_btn.on( 'click', function () {

			var rollback_form_vals = form.serializeArray();

			var rollback_version = form.find( 'input[name="plugin_version"]:checked' ).val();
			if ( !rollback_version ) {
				rollback_version = form.find( 'input[name="theme_version"]:checked' ).val();
			}
			var installed_version = form.find( 'input[name="installed_version"]' ).val();
			var new_version = form.find( 'input[name="new_version"]' ).val();
			var rollback_name = form.find( 'input[name="rollback_name"]' ).val();

			//Ensure a version is selected
			if ( !rollback_version ) {

				alert( wpr_vars.version_missing );
				$.magnificPopup.close(); //just for good measure

			} else {

				//Passed
				$.magnificPopup.open( {
					items         : {
						src : $( '#wpr-modal-confirm' ), // can be a HTML string, jQuery object, or CSS selector
						type: 'inline'
					},
					closeBtnInside: false,
					callbacks     : {
						open: function () {

							$( 'span.wpr-plugin-name' ).text( rollback_name );
							$( 'span.wpr-installed-version' ).text( installed_version );
							$( 'span.wpr-new-version' ).text( rollback_version );

						}
					}
				} );


			}

		} );

		//Modal Close
		$( '.wpr-close' ).on( 'click', function ( e ) {
			e.preventDefault();
			$.magnificPopup.close();
		} );
		//Modal Confirm (GO! GO! GO!)
		$( '.wpr-go' ).on( 'click', function ( e ) {
			//submit form
			form.submit();
		} );

	} );


})( jQuery );
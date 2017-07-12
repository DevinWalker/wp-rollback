/**
 *  WP Rollback Scripts
 *
 *  @description:
 *  @copyright: http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

var wpr_vars;

jQuery.noConflict();
(function( $ ) {

	// On DOM Ready
	$( function() {

		var form = $( '.rollback-form' );
		var form_labels = $( 'label', form.get( 0 ) );
		var form_submit_btn = $( '.magnific-popup' );

		/**
		 * On version click
		 */
		form_labels.on( 'click', function() {

			// add a selected class
			form_labels.removeClass( 'wpr-selected' );
			form_submit_btn.removeClass( 'wpr-rollback-disabled' );
			$( this ).addClass( 'wpr-selected' );

			// ensure the radio button always gets clicked
			$( this ).find( 'input' ).prop( 'checked', true );

		} );

		/**
		 * On view changelog clicked.
		 */
		$( '.wpr-changelog-link' ).on( 'click', function( e ) {

			e.preventDefault();

			var changelog_container = $( '.wpr-changelog' );
			var changelog_placement = $( this ).parent( 'li' );
			var version = $( this ).data( 'version' );

			// Ensure all change log links are visible.
			$('.wpr-changelog-link').removeClass('wpr-hidden-changelog')

			// If changelog was already fetched, use that data.
			if ( changelog_container.html().length ) {
				wpr_append_changelog_entry( changelog_placement, version );
				return false;
			}

			// Get changelog via AJAX.
			$.post( ajaxurl, {
					'action': 'wpr_check_changelog',
					'slug': $( 'input[name="plugin_slug"]' ).val()
				}, function( response ) {
					// Add changelog to DOM.
					$( changelog_container ).append( $.parseHTML( response ) );

					// Show changelog entry.
					wpr_append_changelog_entry( changelog_placement, version );

				}
			);

		} );

		/**
		 * Show changelog entry.
		 *
		 * @param placement
		 * @param version
		 */
		function wpr_append_changelog_entry( placement, version ) {

			var changelog = $( '.wpr-changelog' );
			var changelog_headings = $( changelog ).find( 'h4' );

			// Remove old entry.
			$( '.wpr-changelog-entry' ).remove();

			// Hide this change log link.
			$(placement).find('.wpr-changelog-link').addClass('wpr-hidden-changelog');

			// Append a new one.
			$( placement ).after( '<div class="wpr-changelog-entry"></div>' );

			// Loop through changelog headings to get changelog entry.
			$( changelog_headings ).each( function( index, value ) {

				var raw_val = $( value ).text();

				// Match the changelog version heading using regex from: https://github.com/sindresorhus/semver-regex/blob/master/index.js
				// var regex_symver = /\bv?(?:0|[1-9]\d*)\.(?:0|[1-9]\d*)\.(?:0|[1-9]\d*)(?:-[\da-z\-]+(?:\.[\da-z\-]+)*)?(?:\+[\da-z\-]+(?:\.[\da-z\-]+)*)?\b/;

				// from: https://stackoverflow.com/a/27540795/684352
				var regex_symver = /(?:(\d+)\.)?(?:(\d+)\.)?(?:(\d+)\.\d+)?(?:(\d+)\.\d+)/;
				var found_version_num = raw_val.match( regex_symver );
				var found_version_num = $( found_version_num ).get( 0 );

				// Match version number.
				if ( found_version_num == version ) {

					// Assemble entry.
					var changelog_heading = $( value ).clone();
					var changelog_entry = $( value ).nextUntil( 'h4' ).clone();

					// Append changelog entry.
					$( '.wpr-changelog-entry' ).append( changelog_heading ).append( changelog_entry );

				}

			} );

			// If no changelog found, show message.
			if ( ! $( '.wpr-changelog-entry' ).html().length ) {
				$( '.wpr-changelog-entry' ).append( '<p class="wpr-no-changelog-message">' + wpr_vars.text_no_changelog_found + '</p>' );
			}

		}

		/**
		 * Modal rollback.
		 */
		form_submit_btn.on( 'click', function() {

			var rollback_form_vals = form.serializeArray();

			var rollback_version = form.find( 'input[name="plugin_version"]:checked' ).val();
			if ( ! rollback_version ) {
				rollback_version = form.find( 'input[name="theme_version"]:checked' ).val();
			}
			var installed_version = form.find( 'input[name="installed_version"]' ).val();
			var new_version = form.find( 'input[name="new_version"]' ).val();
			var rollback_name = form.find( 'input[name="rollback_name"]' ).val();

			// Ensure a version is selected
			if ( ! rollback_version ) {

				alert( wpr_vars.version_missing );
				$.magnificPopup.close(); // just for good measure

			} else {

				// Passed
				$.magnificPopup.open( {
					items: {
						src: $( '#wpr-modal-confirm' ), // can be a HTML string, jQuery object, or CSS selector
						type: 'inline'
					},
					closeBtnInside: false,
					callbacks: {
						open: function() {

							$( 'span.wpr-plugin-name' ).text( rollback_name );
							$( 'span.wpr-installed-version' ).text( installed_version );
							$( 'span.wpr-new-version' ).text( rollback_version );

						}
					}
				} );

			}

		} );

		// Modal Close
		$( '.wpr-close' ).on( 'click', function( e ) {
			e.preventDefault();
			$.magnificPopup.close();
		} );
		// Modal Confirm (GO! GO! GO!)
		$( '.wpr-go' ).on( 'click', function( e ) {
			// submit form
			form.submit();
		} );

	} );

})( jQuery );

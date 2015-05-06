/**
 *  WP Rollback Scripts
 *
 *  @description:
 *  @copyright: http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
jQuery.noConflict();
(function ( $ ) {

	//On DOM Ready
	$( function () {

		var form = $( '.rollback-form' );
		var form_labels = $( '> label', form.get( 0 ) );

		form_labels.on( 'click', function () {

			form_labels.removeClass( 'wpr-selected' );

			$( this ).addClass( 'wpr-selected' );

		} );

	} );


})( jQuery );
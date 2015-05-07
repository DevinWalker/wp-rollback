/**
 *  Theme Specific WP Rollback
 *
 *  @description: Adds a rollback option to themes
 *  @copyright: http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */



jQuery.noConflict();
(function ( $ ) {

	//On DOM Ready
	$( function () {
		var themes;
		themes = wp.themes = wp.themes || {};
		themes.data = _wpThemeSettings;

		//Use backbone
		//var themes;
		//themes.view.Theme = themes.view.Theme.extend( {
		//
		//	render: function () {
		//		console.log( "2 " );
		//		this.on( "theme:expand", function ( data ) {
		//			console.log( "member submit event " );
		//
		//		} );
		//	}
		//
		//} );
		//var rollback = Backbone.View.extend( {
		//
		//	initialize: function () {
		//		console.log( 'here' );
		//	}
		//	//var view = new themes.view.Themes();
		//	//view.on( 'theme:expand', function ( theView ) {
		//	//	console.log( 'here' );
		//	//	console.log( theView );
		//	//} );
		//
		//} );
		//var rollback = _.extend({}, Backbone.Events);
		console.log( themes );
		//console.log( rollback );
		$( themes.template ).on( 'click', function ( e ) {

			console.log(e);
			$( '.inactive-theme' ).append( 'Hello' );

		} );


	} );


})( jQuery );
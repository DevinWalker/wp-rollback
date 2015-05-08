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

		var rollback = wp.Backbone.View.extend( {

			events     : {
				'click': 'addRollback'
			},
			addRollback: function ( e ) {
				alert( 'here' );
			}
			//var view = new themes.view.Themes();
			//view.on( 'theme:expand', function ( theView ) {
			//	console.log( 'here' );
			//	console.log( theView );
			//} );

		} );

		var themes = new themes.Model;

		//var rollback = _.extend({}, Backbone.Events);
		console.log( themes );

		//var HeaderView = themes.view.Details.extend({
		//  initialize: function() {
		//	  themes.view.Details.prototype.initialize.call(this);
		//
		//	  alert('heyo');
		//
		//  }
		//});


		//console.log( rollback );
		//$( themes ).on( 'click', function ( e ) {
		//	console.log( themes.template );
		//	console.log( themes.focusedTheme );
		//	console.log(e);
		//	$( '.inactive-theme' ).append( 'Hello' );
		//
		//} );


	} );


})( jQuery );
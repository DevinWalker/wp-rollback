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
		//var themes;
		//themes = wp.themes = wp.themes || {};
		//themes.data = _wpThemeSettings;


		//var rollback = wp.Backbone.View.extend( {
		//
		//
		//} );


		//var rollback = wp.Backbone.View.extend({
		//			// assign a compiled template function.
		//			template: wp.template
		//		});
		//var rollback = new themes.view.Theme();
		//console.log(rollback);
		//console.log(rollback.views.view);

		//var rollback = themes.view.Theme.extend({
		//  render: function(e){
		//    alert("I was clicked!");
		//  }
		//});

		console.log(wp.themes);
		wp.themes.on('click', function(){
			alert("here");
		});

		//console.log(rollback);
		//rollback.$el.on('click', function(e){
		//	console.log('hello');
		//	alert('here');
		//});

		//console.log( rollback );
		//$( themes.template ).on( 'click', function ( e ) {
		//	console.log( themes.template );
		//	console.log( themes.focusedTheme );
		//	console.log(e);
		//	$( '.inactive-theme' ).append( 'Hello' );
		//
		//} );


	} );


})( jQuery );
/**
 *  Theme Specific WP Rollback
 *
 *  @description: Adds a rollback option to themes
 *  @copyright: http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
var wpr_vars;
jQuery.noConflict();
(function ( $ ) {

	//On DOM Ready
	$( function () {
		var themes;
		themes = wp.themes = wp.themes || {};
		themes.data = _wpThemeSettings;

		// on page load if route renders modal
		if( $('.theme-browser.rendered').length > 0 ) {
			var modal_theme = wpr_get_parameter_by_name( 'theme' );
			wpr_theme_rollback( modal_theme );
		}

		//On clicking a theme template
		$( themes.template, 'button.left', 'button.right' ).on( 'click', function ( e ) {

			//get theme name that was clicked
			var clicked_theme = wpr_get_parameter_from_focused_theme();

			//check that rollback button hasn't been placed
			if ( is_rollback_btn_there() ) {
				//button is there, bail
				return false;
			}

			//pass off to rollback function
			wpr_theme_rollback( clicked_theme );


		} );

		//@TODO: Get left and right buttons working when navgating themes
		$( 'body' ).on( 'click', 'button.left, button.right', function ( e ) {

			console.log( 'here' );

			//get theme name that was clicked
			var clicked_theme = wpr_get_parameter_by_name( 'theme' );

			//check that rollback button hasn't been placed
			if ( is_rollback_btn_there() ) {
				//button is there, bail
				return false;
			}

			//pass off to rollback function
			wpr_theme_rollback( clicked_theme );


		} );


		/**
		 * Check to see if Rollback button is in place
		 *
		 * @returns {boolean}
		 */
		function is_rollback_btn_there() {

			if ( $( '.wpr-theme-rollback' ).length > 0 ) {
				return true;
			}
			return false;


		}

		/**
		 * Is Theme WordPress.org?
		 *
		 * @description Rollback only supports WordPress.org themes so we need to use the website API and some AJAX to figure out if this theme is the
		 *
		 */
		function wpr_theme_rollback( theme ) {


			var data = {
				action: 'is_wordpress_theme',
				theme : theme
			};

			var theme_data = wpr_get_theme_data( theme );

			console.log( theme_data );

			if( theme_data.hasRollback ) {
				var rollback_btn_html = '<a href="' + encodeURI( 'index.php?page=wp-rollback&type=theme&theme=' + theme + '&current_version=' + theme_data.version + '&rollback_name=' + theme_data.name + '' ) + '" style="position:absolute;right: 80px; bottom: 5px;" class="button wpr-theme-rollback">' + wpr_vars.text_rollback_label + '</a>';
				$( '.theme-actions' ).append( rollback_btn_html );
			}else{
				//Can't roll back this theme, display the
				$( '.theme-actions' ).append( '<span class="no-rollback" style="position: absolute;left: 23px;bottom: 16px;font-size: 12px;font-style: italic;color: rgb(181, 181, 181);">' + wpr_vars.text_not_rollbackable + '</span>' );
			}

		}

		/**
		 * Get Theme Data
		 *
		 * @description Loops through the wp.themes.data.themes object, finds a match, and returns the data
		 * @param theme
		 * @returns {*}
		 */
		function wpr_get_theme_data( theme ) {
			var theme_data = wp.themes.data.themes;
			//Loop through complete theme data to find this current theme's data
			for ( var i = 0, len = theme_data.length; i < len; i++ ) {
				if ( theme_data[i].id === theme ) {
					return theme_data[i]; // Return as soon as the object is found
				}
			}
			return null; // The object was not found
		}

		/**
		 * JS Ready Query String (Helper)
		 *
		 * Kinda dirty but whatever...
		 *
		 * @see: http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript
		 * @param name
		 * @returns {string}
		 */
		function wpr_get_parameter_by_name( name ) {
			name = name.replace( /[\[]/, "\\[" ).replace( /[\]]/, "\\]" );
			var regex = new RegExp( "[\\?&]" + name + "=([^&#]*)" ),
				results = regex.exec( location.search );
			return results === null ? "" : decodeURIComponent( results[1].replace( /\+/g, " " ) );
		}

		function wpr_get_parameter_from_focused_theme() {
			var focussedTheme = wp.themes.focusedTheme;
			name = $( focussedTheme ).find('.theme-name').attr('id').replace('-name','');
			return name;
		}


		/**
		 * Theme Rollback Button Clicked
		 *
		 */
		$( 'body' ).on( 'click', '.wpr-theme-rollback', function ( e ) {

			window.location = $( this ).attr( 'href' );

		} );


	} );


})( jQuery );
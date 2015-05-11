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

		//On clicking a theme template
		$( themes.template ).on( 'click', function ( e ) {

			//get theme name that was clicked
			var clicked_theme = wpr_get_parameter_by_name( 'theme' );

			//check that rollback button hasn't been put in place
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
			$.post( ajaxurl, data, function ( response ) {

				//console.log( response );

				//this theme is WordPress
				if ( response === 'wp' ) {

					//Get the data for this theme
					var theme_data = wpr_get_theme_data( theme );

					//console.log( theme_data );

					//Form the rollback uri
					var rollback_btn_html = '<a href="' + encodeURI( 'index.php?page=wp-rollback&type=theme&theme=' + theme + '&current_version=' + theme_data.version + '&rollback_name=' + theme_data.name + '' ) + '" class="button wpr-theme-rollback">Rollback</a>';

					$( '.inactive-theme' ).append( rollback_btn_html );

				}

				return false;

			} );

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


		/**
		 * Theme Rollback Button Clicked
		 *
		 */
		$( 'body' ).on( 'click', '.wpr-theme-rollback', function ( e ) {

			window.location = $( this ).attr( 'href' );

		} );


	} );


})( jQuery );
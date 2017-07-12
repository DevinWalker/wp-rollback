<?php
/**
 * Rollback Menu
 *
 * Provides the rollback screen view with releases.
 */

// Ensure we have our necessary query strings
if ( ( ! isset( $_GET['type'] ) && ! isset( $_GET['theme'] ) ) || ( ! isset( $_GET['type'] ) && ! isset( $_GET['plugin_file'] ) ) ) {
	wp_die( __( 'WP Rollback is missing necessary parameters to continue. Please contact support.', 'wp-rollback' ) );
}

$theme_rollback  = $_GET['type'] == 'theme' ? true : false;
$plugin_rollback = $_GET['type'] == 'plugin' ? true : false;
$plugins         = get_plugins();
?>
<div class="wrap">

	<div class="wpr-content-wrap">

		<h1>
			<img src="<?php echo WP_ROLLBACK_PLUGIN_URL; ?>/assets/images/wprb-icon-final.svg"
			     onerror="this.onerror=null; this.src='<?php echo WP_ROLLBACK_PLUGIN_URL; ?>/assets/images/wprb-logo.png';"><?php _e( 'WP Rollback', 'wp-rollback' ); ?>
		</h1>

		<p><?php echo apply_filters( 'wpr_rollback_description', sprintf( __( 'Please select which %1$s version you would like to rollback to from the releases listed below. You currently have version %2$s installed of %3$s.', 'wp-rollback' ), '<span class="type">' . ( $theme_rollback == true ? __( 'theme', 'wp-rollback' ) : __( 'plugin', 'wp-rollback' ) ) . '</span>', '<span class="current-version">' . esc_html( $args['current_version'] ) . '</span>', '<span class="rollback-name">' . esc_html( $args['rollback_name'] ) . '</span>' ) ); ?></p>

		<div class="wpr-changelog"></div>
	</div>

	<?php if ( isset( $args['plugin_file'] ) && in_array( $args['plugin_file'], array_keys( $plugins ) ) ) {
		$versions = WP_Rollback()->versions_select( 'plugin' );
	} elseif ( $theme_rollback == true && isset( $_GET['theme_file'] ) ) {
		// Theme rollback: set up our theme vars
		$svn_tags = WP_Rollback()->get_svn_tags( 'theme', $_GET['theme_file'] );
		WP_Rollback()->set_svn_versions_data( $svn_tags );
		$this->current_version = $_GET['current_version'];
		$versions              = WP_Rollback()->versions_select( 'theme' );

	} else {
		// Fallback check
		wp_die( __( 'Oh no! We\'re missing required rollback query strings. Please contact support so we can check this bug out and squash it!', 'wp-rollback' ) );
	} ?>

	<form name="check_for_rollbacks" class="rollback-form" action="<?php echo admin_url( '/index.php' ); ?>">
		<?php
		// Output Versions
		if ( ! empty( $versions ) ) { ?>

			<div class="wpr-versions-wrap">

				<?php
				do_action( 'wpr_pre_versions' );

				echo apply_filters( 'wpr_versions_output', $versions );

				do_action( 'wpr_post_version' ); ?>

			</div>

		<?php } ?>

		<div class="wpr-submit-wrap">
			<a href="#wpr-modal-confirm" class="magnific-popup button-primary wpr-rollback-disabled"><?php _e( 'Rollback', 'wp-rollback' ); ?></a>
			<input type="button" value="<?php _e( 'Cancel', 'wp-rollback' ); ?>" class="button" onclick="location.href='<?php echo wp_get_referer(); ?>';" />
		</div>
		<?php do_action( 'wpr_hidden_fields' ); ?>
		<input type="hidden" name="page" value="wp-rollback">
		<?php
		// Important: We need the appropriate file to perform a rollback
		if ( $plugin_rollback == true ) { ?>
			<input type="hidden" name="plugin_file" value="<?php echo esc_attr( $args['plugin_file'] ); ?>">
			<input type="hidden" name="plugin_slug" value="<?php echo esc_attr( $args['plugin_slug'] ); ?>">
		<?php } else { ?>
			<input type="hidden" name="theme_file" value="<?php echo esc_attr( $_GET['theme_file'] ); ?>">
		<?php } ?>
		<input type="hidden" name="rollback_name" value="<?php echo esc_attr( $args['rollback_name'] ); ?>">
		<input type="hidden" name="installed_version" value="<?php echo esc_attr( $args['current_version'] ); ?>">
		<?php wp_nonce_field( 'wpr_rollback_nonce' ); ?>

		<div id="wpr-modal-confirm" class="white-popup mfp-hide">
			<div class="wpr-modal-inner">
				<p class="wpr-rollback-intro"><?php _e( 'Are you sure you want to perform the following rollback?', 'wp-rollback' ); ?></p>

				<div class="rollback-details">
					<table class="widefat">
						<tbody>
						<?php do_action( 'wpr_pre_rollback_table' ); ?>
						<tr>
							<td class="row-title">
								<label for="tablecell"><?php if ( $plugin_rollback == true ) {
										_e( 'Plugin Name:', 'wp-rollback' );
									} else {
										_e( 'Theme Name:', 'wp-rollback' );
									} ?></label>
							</td>
							<td><span class="wpr-plugin-name"></span></td>
						</tr>
						<tr class="alternate">
							<td class="row-title">
								<label for="tablecell"><?php _e( 'Installed Version:', 'wp-rollback' ); ?></label>
							</td>
							<td><span class="wpr-installed-version"></span></td>
						</tr>
						<tr>
							<td class="row-title">
								<label for="tablecell"><?php _e( 'New Version:', 'wp-rollback' ); ?></label>
							</td>
							<td><span class="wpr-new-version"></span></td>
						</tr>
						<?php do_action( 'wpr_post_rollback_table' ); ?>
						</tbody>
					</table>
				</div>
				<div class="wpr-error">
					<p><?php
						_e( '<strong>Notice:</strong> We strongly recommend you perform a test rollback on a staging site and create a complete backup of your WordPress files and database prior to performing a rollback. We are not responsible for any misuse, deletions, white screens, fatal errors, or any other issue arising from using this plugin.', 'wp-rollback' );
						?></p>
				</div>
				<?php do_action( 'wpr_pre_rollback_buttons' ); ?>
				<input type="submit" value="<?php _e( 'Rollback', 'wp-rollback' ); ?>" class="button-primary wpr-go" />
				<a href="#" class="button wpr-close"><?php _e( 'Cancel', 'wp-rollback' ); ?></a>
				<?php do_action( 'wpr_post_rollback_buttons' ); ?>
			</div>
		</div>

	</form>

</div>

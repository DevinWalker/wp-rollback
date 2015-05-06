<?php
/**
 * Rollback Menu
 *
 * @description Provides the rollback screen view with releases and
 *
 */
$plugins = get_plugins();
?>
<div class="wrap">

	<div class="wpr-content-wrap">

		<h2><?php _e( 'WP Rollback', 'wpr' ); ?></h2>

		<p><?php echo apply_filters( 'wpr_rollback_description', sprintf( __( 'Please select which version you would like to rollback to from the releases listed below. You currently have version %1$s installed of %2$s.', 'wpr' ), '<span class="current-version">' . $args['current_version'] . '</span>', '<span class="rollback-name">' . $args['rollback_name'] . '</span>' ) ); ?></p>


	</div>

	<?php if ( isset( $args['plugin_file'] ) && in_array( $args['plugin_file'], array_keys( $plugins ) ) ) {
		$versions = WP_Rollback()->versions_select();
		?>

		<form name="check_for_rollbacks" class="rollback-form" action="<?php echo admin_url( '/index.php' ); ?>">
			<?php
			//Output Versions
			if ( ! empty( $versions ) ) {
				do_action( 'wpr_pre_versions' );
				echo $versions;
				do_action( 'wpr_post_version' );
			} ?>

			<div class="wpr-submit-wrap">
				<a href="#wpr-modal-confirm" class="magnific-popup button-primary wpr-rollback-disabled"><?php _e( 'Rollback', 'wpr' ); ?></a>
				<input type="submit" value="<?php _e( 'Cancel', 'wpr' ); ?>" class="button" />
			</div>
			<?php do_action( 'wpr_hidden_fields' ); ?>
			<input type="hidden" name="page" value="wp-rollback">
			<input type="hidden" name="plugin_file" value="<?php echo $args['plugin_file']; ?>">
			<input type="hidden" name="rollback_name" value="<?php echo $args['rollback_name']; ?>">
			<input type="hidden" name="installed_version" value="<?php echo $args['current_version']; ?>">


			<div id="wpr-modal-confirm" class="white-popup mfp-hide">
				<div class="wpr-modal-inner">
					<p class="wpr-rollback-intro"><?php
						_e( 'Are you sure you want to perform the following rollback?', 'wpr' );
						?></p>

					<div class="rollback-details">
						<table class="widefat">
							<tbody>
							<?php do_action( 'wpr_pre_rollback_table' ); ?>
							<tr>
								<td class="row-title">
									<label for="tablecell"><?php _e( 'Plugin Name:', 'wpr' ); ?></label>
								</td>
								<td><span class="wpr-plugin-name"></span></td>
							</tr>
							<tr class="alternate">
								<td class="row-title">
									<label for="tablecell"><?php _e( 'Installed Version:', 'wpr' ); ?></label>
								</td>
								<td><span class="wpr-installed-version"></span></td>
							</tr>
							<tr>
								<td class="row-title">
									<label for="tablecell"><?php _e( 'New Version:', 'wpr' ); ?></label>
								</td>
								<td><span class="wpr-new-version"></span></td>
							</tr>
							<?php do_action( 'wpr_post_rollback_table' ); ?>
							</tbody>
						</table>
					</div>
					<div class="wpr-error">
						<p><?php
							_e( '<strong>Notice:</strong> We strongly recommend you perform a test rollback on a staging site and create a complete backup of your WordPress files and database prior to performing a rollback. We are not responsible for any misuse, deletions, white screens, fatal errors, or any other issue arising from using this plugin.', 'wpr' );
							?></p>
					</div>
					<?php do_action( 'wpr_pre_rollback_buttons' ); ?>
					<input type="submit" value="<?php _e( 'Rollback', 'wpr' ); ?>" class="button-primary wpr-go" />
					<a href="#" class="button wpr-close"><?php _e( 'Cancel', 'wpr' ); ?></a>
					<?php do_action( 'wpr_post_rollback_buttons' ); ?>
				</div>
			</div>

		</form>

	<?php } ?>


</div>
<?php
/**
 * Rollback Menu
 *
 * @description Provides the rollback screen view with releases and
 *
 */
$plugins  = get_plugins();
?>
<div class="wrap">
	<h2><?php _e( 'WP Rollback', 'wpr' ); ?></h2>

	<p><?php _e( 'Please select which version you would like to rollback to from the releases listed below.', 'wpr' ); ?></p>

	<?php if ( isset( $args['plugin_file'] ) && in_array( $args['plugin_file'], array_keys( $plugins ) ) ) {

		$versions = WP_Rollback()->versions_select(); ?>

		<form name="check_for_rollbacks" class="rollback-form" action="<?php echo admin_url( '/index.php' ); ?>">
			<?php
			//Output Versions
			if ( ! empty( $versions ) ) {
				echo $versions;
			} ?>

			<div class="wpr-submit-wrap">
				<input type="submit" value="Rollback Now" class="button-primary" />
				<input type="submit" value="Cancel" class="button" />
			</div>
			<input type="hidden" name="page" value="wp-rollback">
		</form>

	<?php } ?>
</div>
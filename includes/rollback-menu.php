<?php
$plugins  = get_plugins();
$RB       = WP_Rollback();
$selected = '';
?>
<div class="wrap">
	<h2><?php _e( 'WP Rollback', 'wpr' ); ?></h2>

	<p><?php _e( '.', 'wpr' ); ?></p>
	<?php if ( isset( $args['plugin_file'] ) && in_array( $args['plugin_file'], array_keys( $plugins ) ) ) {

		$selected = $args['plugin_file'];

		$versions = $RB->versions_select();

		if ( ! empty( $plugins ) ) {
			echo '<p>' . __( 'Choose from the list of installed plugins.', 'wpr' ) . '</p>';
			echo '<form name="check_for_rollbacks" action="' . admin_url( '/index.php' ) . '">';
			echo '<select name="plugin_file">';
			foreach ( $plugins as $key => $value ) {
				echo '<option value="' . $key . '" ' . selected( $selected, $key, false ) . ' />' . $value['Name'] . '</option>';
			}
			echo '</select>';
			if ( ! empty( $versions ) ) {
				echo $versions;
			}
		}
		echo '<input type="submit" value="Check" class="button" />';
		echo '<input type="hidden" name="page" value="wp-rollback">';
		echo '</form>';

	} else {
		echo '<a href="' . admin_url( '/index.php' ) . '">' . __( 'View the plugin list', 'wpr' ) . '</a>';
	}


	?>
</div>
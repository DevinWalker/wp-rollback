<?php
$plugins = get_plugins();
$RB = WP_Rollback();
$selected = '';
?><div class="wrap">
	<h2>WP Rollback</h2>
	<p>Page for information relevant to rollback.</p>
	<?php if( isset($args['plugin_file']) && in_array($args['plugin_file'], array_keys($plugins) ) ) { 
		
		$selected = $args['plugin_file'];
		
		$versions = $RB->versions_select();

		if( !empty( $plugins ) ) {
			echo '<p>Choose from the list of installed plugins.</p>';
			echo '<form name="check_for_rollbacks" action="'.admin_url('/plugins.php').'">';
			echo '<select name="plugin_file">';
			foreach ($plugins as $key => $value) {
				echo '<option value="'.$key.'" '. selected( $selected, $key, false ) .' />'.$value['Name'].'</option>';
			}
			echo '</select>';
			if( !empty( $versions ) )
				echo $versions;
			}
			echo '<input type="submit" value="Check" />';
			echo '<input type="hidden" name="page" value="wp-rollback"></form>';

		}else{
			echo '<a href="'.admin_url('/plugins.php').'">Check out the plugin list</a>';
		}


		 ?>
</div>
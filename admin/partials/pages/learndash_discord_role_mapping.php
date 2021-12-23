<?php

//learndash_get_courses_count();
//learndash_get_all_courses_with_groups();
//learndash_course_ge
echo '<pre>';
var_dump(learndash_get_courses_count());
echo '</pre>';

$default_role        = sanitize_text_field( trim( get_option( 'ets_learndash_discord_default_role_id' ) ) );
?>
<div class="notice notice-warning ets-notice">
  <p><i class='fas fa-info'></i> <?php echo __( 'Drag and Drop the Discord Roles over to the Learndash Courses', 'learndash-discord' ); ?></p>
</div>

<div class="row-container">
  <div class="ets-column learndash-discord-roles-col">
	<h2><?php echo __( 'Discord Roles', 'learndash-discord' ); ?></h2>
	<hr>
	<div class="learndash-discord-roles">
	  <span class="spinner"></span>
	</div>
  </div>
  <div class="ets-column">
	<h2><?php echo __( 'Courses', 'learndash-discord' ); ?></h2>
	<hr>
	<div class="learndash-discord-courses">
	<?php
	foreach ( $courses as $key => $value ) {
		
			?>
		  <div class="makeMeDroppable" data-learndash_course_id="<?php echo esc_attr($key); ?>" ><span><?php echo esc_html($value); ?></span></div>
			<?php
		
	}
	?>
	</div>
  </div>
</div>
<form method="post" action="<?php echo get_site_url().'/wp-admin/admin-post.php' ?>">
 <input type="hidden" name="action" value="learndash_discord_save_role_mapping">
  <table class="form-table" role="presentation">
	<tbody>
	  <tr>
		<th scope="row"><label for="learndash-defaultRole"><?php echo __( 'Default Role', 'learndash-discord' ); ?></label></th>
		<td>
		  <?php wp_nonce_field( 'learndash_discord_role_mappings_nonce', 'ets_learndash_discord_role_mappings_nonce' ); ?>
		  <input type="hidden" id="selected_default_role" value="<?php echo esc_attr( $default_role ); ?>">
		  <select id="learndash-defaultRole" name="learndash_defaultRole">
			<option value="none"><?php echo __( '-None-', 'learndash-discord' ); ?></option>
		  </select>
		  <p class="description"><?php echo __( 'This Role will be assigned to all', 'learndash-discord' ); ?></p>
		</td>
	  </tr>

	</tbody>
  </table>
	<br>
  <div class="mapping-json">
	<textarea id="ets_learndash_mapping_json_val" name="ets_learndash_discord_role_mapping">
	<?php
	if ( isset( $ets_discord_roles ) ) {
		echo stripslashes( esc_html( $ets_discord_roles ));}
	?>
	</textarea>
  </div>
  <div class="bottom-btn">
	<button type="submit" name="submit" value="ets_submit" class="ets-submit ets-btn-submit ets-bg-green">
	  <?php echo __( 'Save Settings', 'learndash-discord' ); ?>
	</button>
	<button id="revertMapping" name="flush" class="ets-submit ets-btn-submit ets-bg-red">
	  <?php echo __( 'Flush Mappings', 'learndash-discord' ); ?>
	</button>
  </div>
</form>
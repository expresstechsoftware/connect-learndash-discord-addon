<?php

$args_courses = array(
    'orderby'          => 'title',
    'order'            => 'ASC',
    'post_status'    => 'publish',
    'numberposts' => -1,
    'post_type'   => 'sfwd-courses'
);
$courses = get_posts( $args_courses );

$default_role        = sanitize_text_field( trim( get_option( 'ets_learndash_discord_default_role_id' ) ) );
$allow_none_student  = sanitize_text_field( trim( get_option( 'ets_learndash_discord_allow_none_student' ) ) );
?>
<div class="notice notice-warning ets-notice">
    <p><i class='fas fa-info'></i> <?php esc_html_e ( 'Drag and Drop the Discord Roles over to the Learndash Courses', 'connect-learndash-and-discord' ); ?></p>
</div>

<div class="row-container">
  <div class="ets-column learndash-discord-roles-col">
	<h2><?php esc_html_e( 'Discord Roles', 'connect-learndash-and-discord' ); ?></h2>
	<hr>
	<div class="learndash-discord-roles">
	  <span class="spinner"></span>
	</div>
  </div>
  <div class="ets-column">
	<h2><?php esc_html_e( 'Courses', 'connect-learndash-and-discord' ); ?></h2>
	<hr>
	<div class="learndash-discord-courses">
	<?php
	foreach ( $courses as $course ) {
		
			?>
		  <div class="makeMeDroppable" data-learndash_course_id="<?php echo esc_attr( $course->ID ); ?>" ><span><?php echo esc_html( $course->post_title ); ?></span></div>
			<?php
		
	}
	?>
	</div>
  </div>
</div>
<form method="post" action="<?php echo esc_url( get_site_url().'/wp-admin/admin-post.php' ) ?>">
 <input type="hidden" name="action" value="learndash_discord_save_role_mapping">
 <input type="hidden" name="current_url" value="<?php echo esc_url( ets_learndash_discord_get_current_screen_url() );?>">   
  <table class="form-table" role="presentation">
	<tbody>
	  <tr>
		<th scope="row"><label for="learndash-defaultRole"><?php esc_html_e( 'Default Role', 'connect-learndash-and-discord' ); ?></label></th>
		<td>
		  <?php wp_nonce_field( 'learndash_discord_role_mappings_nonce', 'ets_learndash_discord_role_mappings_nonce' ); ?>
		  <input type="hidden" id="selected_default_role" value="<?php echo esc_attr( $default_role ); ?>">
		  <select id="learndash-defaultRole" name="learndash_defaultRole">
			<option value="none"><?php esc_html_e( '-None-', 'connect-learndash-and-discord' ); ?></option>
		  </select>
		  <p class="description"><?php esc_html_e( 'This Role will be assigned to all', 'connect-learndash-and-discord' ); ?></p>
		</td>
	  </tr>
	  <tr>
		<th scope="row"><label><?php esc_html_e( 'Allow non-student', 'connect-learndash-and-discord' ); ?></label></th>
		<td>
		  <fieldset>
		  <label><input type="radio" name="allow_none_student" value="yes"  
		  <?php
			if ( $allow_none_student == 'yes' ) {
				echo esc_attr( 'checked="checked"' ); }
			?>
			 > <span><?php esc_html_e( 'Yes', 'connect-learndash-and-discord' ); ?></span></label><br>
		  <label><input type="radio" name="allow_none_student" value="no" 
		  <?php
			if ( empty( $allow_none_student ) || $allow_none_student == 'no' ) {
				echo esc_attr( 'checked="checked"' ); }
			?>
			 > <span><?php esc_html_e( 'No', 'connect-learndash-and-discord' ); ?></span></label>
		  <p class="description"><?php esc_html_e( 'Display connect button to normal wordpress site users having learndash account', 'connect-learndash-and-discord' ); ?></p>
		  </fieldset>
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
	  <?php esc_html_e( 'Save Settings', 'connect-learndash-and-discord' ); ?>
	</button>
	<button id="revertMapping" name="flush" class="ets-submit ets-btn-submit ets-bg-red">
	  <?php esc_html_e( 'Flush Mappings', 'connect-learndash-and-discord' ); ?>
	</button>
  </div>
</form>
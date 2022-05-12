<?php
$ets_learndash_discord_send_welcome_dm         = sanitize_text_field( trim( get_option( 'ets_learndash_discord_send_welcome_dm' ) ) );
$ets_learndash_discord_welcome_message         = sanitize_text_field( trim( get_option( 'ets_learndash_discord_welcome_message' ) ) );
$ets_learndash_discord_send_course_complete_dm = sanitize_text_field( trim( get_option( 'ets_learndash_discord_send_course_complete_dm' ) ) );
$ets_learndash_discord_course_complete_message = sanitize_text_field( trim( get_option( 'ets_learndash_discord_course_complete_message' ) ) );
$ets_learndash_discord_send_lesson_complete_dm = sanitize_text_field( trim( get_option( 'ets_learndash_discord_send_lesson_complete_dm' ) ) );
$ets_learndash_discord_lesson_complete_message = sanitize_text_field( trim( get_option( 'ets_learndash_discord_lesson_complete_message' ) ) );
$ets_learndash_discord_send_topic_complete_dm  = sanitize_text_field( trim( get_option( 'ets_learndash_discord_send_topic_complete_dm' ) ) );
$ets_learndash_discord_topic_complete_message  = sanitize_text_field( trim( get_option( 'ets_learndash_discord_topic_complete_message' ) ) );
$ets_learndash_discord_send_quiz_complete_dm   = sanitize_text_field( trim( get_option( 'ets_learndash_discord_send_quiz_complete_dm' ) ) );
$ets_learndash_discord_quiz_complete_message   = sanitize_text_field( trim( get_option( 'ets_learndash_discord_quiz_complete_message' ) ) );

$ets_learndash_discord_send_assignment_approved_dm   = sanitize_text_field( trim( get_option( 'ets_learndash_discord_send_assignment_approved_dm' ) ) );
$ets_learndash_discord_assignment_approved_message   = sanitize_text_field( trim( get_option( 'ets_learndash_discord_assignment_approved_message' ) ) );

$retry_failed_api                              = sanitize_text_field( trim( get_option( 'ets_learndash_discord_retry_failed_api' ) ) );
$kick_upon_disconnect                          = sanitize_text_field( trim( get_option( 'ets_learndash_discord_kick_upon_disconnect' ) ) );
$retry_api_count                               = sanitize_text_field( trim( get_option( 'ets_learndash_discord_retry_api_count' ) ) );
$set_job_cnrc                                  = sanitize_text_field( trim( get_option( 'ets_learndash_discord_job_queue_concurrency' ) ) );
$set_job_q_batch_size                          = sanitize_text_field( trim( get_option( 'ets_learndash_discord_job_queue_batch_size' ) ) );
$log_api_res                                   = sanitize_text_field( trim( get_option( 'ets_learndash_discord_log_api_response' ) ) );
$embed_messaging_feature                       = sanitize_text_field( trim( get_option( 'ets_learndash_discord_embed_messaging_feature' ) ) );
$remove_role_course_expired                    = sanitize_text_field( trim( get_option( 'ets_learndash_discord_remove_role_course_expired' ) ) );

?>
<form method="post" action="<?php echo get_site_url().'/wp-admin/admin-post.php' ?>">
 <input type="hidden" name="action" value="learndash_discord_save_advance_settings">
 <input type="hidden" name="current_url" value="<?php echo ets_learndash_discord_get_current_screen_url()?>">   
<?php wp_nonce_field( 'learndash_discord_advance_settings_nonce', 'ets_learndash_discord_advance_settings_nonce' ); ?>
  <table class="form-table" role="presentation">
	<tbody>
	<tr>
		<th scope="row"><?php echo __( 'Shortcode:', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		[learndash_discord]
		<br/>
		<small><?php echo __( 'Use this shortcode [learndash_discord] to display connect to discord button on any page.', 'learndash-discord' ); ?></small>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"><?php echo __( 'Use rich embed messaging feature?', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="embed_messaging_feature" type="checkbox" id="embed_messaging_feature" 
		<?php
		if ( $embed_messaging_feature == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
                <br/>
                <small>Use [LINEBREAK] to split lines.</small>                
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Remove student discord role when course access expires?', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="remove_role_course_expired" type="checkbox" id="remove_role_course_expired" 
		<?php
		if ( $remove_role_course_expired == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>          
	<tr>
		<th scope="row"><?php echo __( 'Send welcome message', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_learndash_discord_send_welcome_dm" type="checkbox" id="ets_learndash_discord_send_welcome_dm" 
		<?php
		if ( $ets_learndash_discord_send_welcome_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"><?php echo __( 'Welcome message', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<textarea class="ets_learndash_discord_dm_textarea" name="ets_learndash_discord_welcome_message" id="ets_learndash_discord_welcome_message" row="25" cols="50"><?php if ( $ets_learndash_discord_welcome_message ) { echo wp_unslash( $ets_learndash_discord_welcome_message ); } ?></textarea> 
	<br/>
	<small>Merge fields: [LD_STUDENT_NAME], [LD_STUDENT_EMAIL], [LD_COURSES], [SITE_URL], [BLOG_NAME]</small>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"><?php echo __( 'Send Course Complete message', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_learndash_discord_send_course_complete_dm" type="checkbox" id="ets_learndash_discord_send_course_complete_dm" 
		<?php
		if ( $ets_learndash_discord_send_course_complete_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"><?php echo __( 'Course Complete message', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<textarea class="ets_learndash_discord_course_complete_message" name="ets_learndash_discord_course_complete_message" id="ets_learndash_discord_course_complete_message" row="25" cols="50"><?php if ( $ets_learndash_discord_course_complete_message ) { echo wp_unslash( $ets_learndash_discord_course_complete_message ); } ?></textarea> 
	<br/>
	<small>Merge fields: [LD_STUDENT_NAME], [LD_STUDENT_EMAIL], [LD_COURSE_NAME], [LD_COURSE_COMPLETE_DATE], [SITE_URL], [BLOG_NAME]</small>
		</fieldset></td>
	</tr>
	<tr>
		<th scope="row"><?php echo __( 'Send Lesson Complete message', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_learndash_discord_send_lesson_complete_dm" type="checkbox" id="ets_learndash_discord_send_lesson_complete_dm" 
		<?php
		if ( $ets_learndash_discord_send_lesson_complete_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Lesson Complete message', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<textarea class="ets_learndash_discord_lesson_complete_message" name="ets_learndash_discord_lesson_complete_message" id="ets_learndash_discord_lesson_complete_message" row="25" cols="50"><?php if ( $ets_learndash_discord_lesson_complete_message ) { echo wp_unslash( $ets_learndash_discord_lesson_complete_message ); } ?></textarea> 
	<br/>
	<small>Merge fields:  [LD_STUDENT_NAME], [LD_STUDENT_EMAIL], [LD_LESSON_NAME], [LD_COURSE_LESSON_DATE], [SITE_URL], [BLOG_NAME]</small>
		</fieldset></td>
	  </tr>
 	<tr>
		<th scope="row"><?php echo __( 'Send Topic Complete message', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_learndash_discord_send_topic_complete_dm" type="checkbox" id="ets_learndash_discord_send_topic_complete_dm" 
		<?php
		if ( $ets_learndash_discord_send_topic_complete_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Topic Complete message', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<textarea class="ets_learndash_discord_topic_complete_message" name="ets_learndash_discord_topic_complete_message" id="ets_learndash_discord_topic_complete_message" row="25" cols="50"><?php if ( $ets_learndash_discord_topic_complete_message ) { echo wp_unslash( $ets_learndash_discord_topic_complete_message ); } ?></textarea> 
	<br/>
	<small>Merge fields: [LD_STUDENT_NAME], [LD_STUDENT_EMAIL], [LD_TOPIC_NAME], [LD_COURSE_TOPIC_DATE], [SITE_URL], [BLOG_NAME]</small>
		</fieldset></td>
	  </tr>
          
  <tr>
		<th scope="row"><?php echo __( 'Send Quiz Complete message', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_learndash_discord_send_quiz_complete_dm" type="checkbox" id="ets_learndash_discord_send_quiz_complete_dm" 
		<?php
		if ( $ets_learndash_discord_send_quiz_complete_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Quiz Complete message', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<textarea class="ets_learndash_discord_quiz_complete_message" name="ets_learndash_discord_quiz_complete_message" id="ets_learndash_discord_quiz_complete_message" row="25" cols="50"><?php if ( $ets_learndash_discord_quiz_complete_message ) { echo wp_unslash( $ets_learndash_discord_quiz_complete_message ); } ?></textarea> 
	<br/>
	<small>Merge fields: [LD_STUDENT_NAME], [LD_STUDENT_EMAIL], [LD_QUIZ_NAME], [LD_QUIZ_DATE], [SITE_URL], [BLOG_NAME]</small>
		</fieldset></td>
	  </tr>
          
  <tr>
		<th scope="row"><?php echo __( 'Send Assignment Approved message', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_learndash_discord_send_assignment_approved_dm" type="checkbox" id="ets_learndash_discord_send_assignment_approved_dm" 
		<?php
		if ( $ets_learndash_discord_send_assignment_approved_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Assignment Approved message', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<textarea class="ets_learndash_discord_assignment_approved_message" name="ets_learndash_discord_assignment_approved_message" id="ets_learndash_discord_assignment_approved_message" row="25" cols="50"><?php if ( $ets_learndash_discord_assignment_approved_message ) { echo wp_unslash( $ets_learndash_discord_assignment_approved_message ); } ?></textarea> 
	<br/>
	<small>Merge fields: [LD_STUDENT_NAME], [LD_STUDENT_EMAIL], [LD_ASSIGNMENT_COURSE], [LD_ASSIGNMENT_LESSON], [SITE_URL], [BLOG_NAME], [LD_ASSIGNMENT_APPROVED_DATE], [LD_LINK_OF_ASSIGNMENT], [LD_ASSIGNMENT_POINTS_AWARDED]</small>
		</fieldset></td>
	  </tr>          

	  <tr>
		<th scope="row"><?php echo __( 'Retry Failed API calls', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="retry_failed_api" type="checkbox" id="retry_failed_api" 
		<?php
		if ( $retry_failed_api == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	  <tr>
		<th scope="row"><?php echo __( 'Don\'t kick students upon disconnect', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="kick_upon_disconnect" type="checkbox" id="kick_upon_disconnect" 
		<?php
		if ( $kick_upon_disconnect == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'How many times a failed API call should get re-try', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_learndash_retry_api_count" type="number" min="1" id="ets_learndash_retry_api_count" value="<?php if ( isset( $retry_api_count ) ) { echo intval( $retry_api_count ); } else { echo 1; } ?>">
		</fieldset></td>
	  </tr> 
	  <tr>
		<th scope="row"><?php echo __( 'Set job queue concurrency', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="set_job_cnrc" type="number" min="1" id="set_job_cnrc" value="<?php if ( isset( $set_job_cnrc ) ) { echo intval( $set_job_cnrc ); } else { echo 1; } ?>">
		</fieldset></td>
	  </tr>
	  <tr>
		<th scope="row"><?php echo __( 'Set job queue batch size', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="set_job_q_batch_size" type="number" min="1" id="set_job_q_batch_size" value="<?php if ( isset( $set_job_q_batch_size ) ) { echo intval( $set_job_q_batch_size ); } else { echo 10; } ?>">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Log API calls response (For debugging purpose)', 'learndash-discord' ); ?></th>
		<td> <fieldset>
		<input name="log_api_res" type="checkbox" id="log_api_res" 
		<?php
		if ( $log_api_res == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
          	
	</tbody>
  </table>
  <div class="bottom-btn">
	<button type="submit" name="adv_submit" value="ets_submit" class="ets-submit ets-bg-green">
	  <?php echo __( 'Save Settings', 'learndash-discord' ); ?>
	</button>
  </div>
</form>

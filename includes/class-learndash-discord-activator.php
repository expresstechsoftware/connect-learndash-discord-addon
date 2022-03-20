<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Learndash_Discord
 * @subpackage Learndash_Discord/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Learndash_Discord
 * @subpackage Learndash_Discord/includes
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class Learndash_Discord_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		update_option( 'ets_learndash_discord_send_welcome_dm', true );
		update_option( 'ets_learndash_discord_welcome_message', 'Hi [LD_STUDENT_NAME] ([LD_STUDENT_EMAIL]), Welcome, Your courses [LD_COURSES] at [SITE_URL] Thanks, Kind Regards, [BLOG_NAME]' );                
		update_option( 'ets_learndash_discord_send_course_complete_dm', true );
		update_option( 'ets_learndash_discord_course_complete_message', 'Hi [LD_STUDENT_NAME] ([LD_STUDENT_EMAIL]), You have completed the course  [LD_COURSE_NAME] at [LD_COURSE_COMPLETE_DATE] on website [SITE_URL], [BLOG_NAME]' );
		update_option( 'ets_learndash_discord_send_lesson_complete_dm', true );
		update_option( 'ets_learndash_discord_lesson_complete_message', 'Hi [LD_STUDENT_NAME] ([LD_STUDENT_EMAIL]), You have completed the lesson  [LD_LESSON_NAME] at [LD_COURSE_LESSON_DATE] on website [SITE_URL], [BLOG_NAME]' );                
		update_option( 'ets_learndash_discord_send_topic_complete_dm', true );
		update_option( 'ets_learndash_discord_topic_complete_message', 'Hi [LD_STUDENT_NAME] ([LD_STUDENT_EMAIL]), You have completed the topic  [LD_TOPIC_NAME] at [LD_COURSE_TOPIC_DATE] on website [SITE_URL], [BLOG_NAME]' );                 
		update_option( 'ets_learndash_discord_quiz_complete_message', 'Hi [LD_STUDENT_NAME] ([LD_STUDENT_EMAIL]), You have completed the quiz  [LD_QUIZ_NAME] at [LD_QUIZ_DATE] on website [SITE_URL], [BLOG_NAME]' );                                 
		update_option( 'ets_learndash_discord_assignment_approved_message', 'Hi [LD_STUDENT_NAME] ([LD_STUDENT_EMAIL]), your assignments [LD_LINK_OF_ASSIGNMENT] ( [LD_ASSIGNMENT_COURSE], [LD_ASSIGNMENT_LESSON] )  has been approved on [LD_ASSIGNMENT_APPROVED_DATE] , points awarded: [LD_ASSIGNMENT_POINTS_AWARDED] , website [SITE_URL], [BLOG_NAME]' );                                                 
		update_option( 'ets_learndash_discord_retry_failed_api', true );
    update_option( 'ets_learndash_discord_connect_button_bg_color', '#7bbc36' );
    update_option( 'ets_learndash_discord_disconnect_button_bg_color', '#ff0000' );
    update_option( 'ets_learndash_discord_loggedin_button_text', 'Connect With Discord' );
    update_option( 'ets_learndash_discord_non_login_button_text', 'Login With Discord' );
    update_option( 'ets_learndash_discord_disconnect_button_text', 'Disconnect From Discord' );
		update_option( 'ets_learndash_discord_kick_upon_disconnect', false ); 
		update_option( 'ets_learndash_discord_retry_api_count', 5 );
		update_option( 'ets_learndash_discord_job_queue_concurrency', 1 );
		update_option( 'ets_learndash_discord_job_queue_batch_size', 6 );
		update_option( 'ets_learndash_discord_log_api_response', false );
		update_option( 'ets_learndash_discord_uuid_file_name', wp_generate_uuid4() );
	}

}

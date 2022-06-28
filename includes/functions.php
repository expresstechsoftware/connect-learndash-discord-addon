<?php
/*
* common functions file.
*/

/**
 * Get WP Pages list
 * @param INT $ets_learndash_discord_redirect_page_id
 * @return STRING $options
 */
function ets_learndash_discord_pages_list( $ets_learndash_discord_redirect_page_id ){
    $args = array(
    'sort_order' => 'asc',
    'sort_column' => 'post_title',
    'hierarchical' => 1,
    'exclude' => '',
    'include' => '',
    'meta_key' => '',
    'meta_value' => '',
    'exclude_tree' => '',
    'number' => '',
    'offset' => 0,
    'post_type' => 'page',
    'post_status' => 'publish'
        ); 
    $pages = get_pages($args);
       
   
    $options = '<option value="" disabled>-</option>';
    foreach($pages as $page){ 
        $selected = ( esc_attr( $page->ID ) === $ets_learndash_discord_redirect_page_id  ) ? ' selected="selected"' : '';
        $options .= '<option data-page-url="' . ets_get_learndash_discord_formated_discord_redirect_url ( $page->ID ) .'" value="' . esc_attr( $page->ID ) . '" '. $selected .'> ' . $page->post_title . ' </option>';
    }
    
    return $options;
}

/*
function to get formated redirect url
 * @param INT $page_id
 * @return STRING $url
*/
function ets_get_learndash_discord_formated_discord_redirect_url( $page_id ) {
    $url = esc_url( get_permalink( $page_id ) );
    
	$parsed = parse_url( $url, PHP_URL_QUERY );
	if ( $parsed === null ) {
		return $url .= '?via=connect-learndash-discord-addon';
	} else {
		if ( stristr( $url, 'via=connect-learndash-discord-addon' ) !== false ) {
			return $url;
		} else {
			return $url .= '&via=connect-learndash-discord-addon';
		}
	}
}
/**
 * Get current screen URL
 *
 * @param NONE
 * @return STRING $url
 */
function ets_learndash_discord_get_current_screen_url() {
	$parts           = parse_url( home_url() );
	$current_uri = "{$parts['scheme']}://{$parts['host']}" . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' ) . add_query_arg( null, null );
	
        return $current_uri;
}

/**
 * To check settings values saved or not
 *
 * @param NONE
 * @return BOOL $status
 */
function learndash_discord_check_saved_settings_status() {
	$ets_learndash_discord_client_id     = sanitize_text_field( trim( get_option( 'ets_learndash_discord_client_id' ) ) );
	$ets_learndash_discord_client_secret = sanitize_text_field( trim( get_option( 'ets_learndash_discord_client_secret' ) ) );
	$ets_learndash_discord_bot_token     = sanitize_text_field( trim( get_option( 'ets_learndash_discord_bot_token' ) ) );
	$ets_learndash_discord_redirect_url  = sanitize_text_field( trim( get_option( 'ets_learndash_discord_redirect_url' ) ) );
	$ets_learndash_discord_server_id      = sanitize_text_field( trim( get_option( 'ets_learndash_discord_server_id' ) ) );

	if ( $ets_learndash_discord_client_id && $ets_learndash_discord_client_secret && $ets_learndash_discord_bot_token && $ets_learndash_discord_redirect_url && $ets_learndash_discord_server_id ) {
			$status = true;
	} else {
			$status = false;
	}

	return $status;
}

/**
 * Get student's courses ids
 *
 * @param INT $user_id
 * @return ARRAY|NULL $curr_course_id
 */
function ets_learndash_discord_get_student_courses_id( $user_id = 0 ) {
    
	$user_courses = learndash_user_get_enrolled_courses( $user_id );
    
	if ( $user_courses ) {
		return $user_courses;
	} else {
		return null;
	}    

}

/**
 * Get the highest available last attempt schedule time
 */

function ets_learndash_discord_get_highest_last_attempt_timestamp() {
	global $wpdb;
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT aa.last_attempt_gmt FROM ' . $wpdb->prefix . 'actionscheduler_actions as aa INNER JOIN ' . $wpdb->prefix . 'actionscheduler_groups as ag ON aa.group_id = ag.group_id WHERE ag.slug = %s ORDER BY aa.last_attempt_gmt DESC limit 1', LEARNDASH_DISCORD_AS_GROUP_NAME ), ARRAY_A );

	if ( ! empty( $result ) ) {
		return strtotime( $result['0']['last_attempt_gmt'] );
	} else {
		return false;
	}
}

/**
 * Get randon integer between a predefined range.
 *
 * @param INT $add_upon
 */
function ets_learndash_discord_get_random_timestamp( $add_upon = '' ) {
	if ( $add_upon != '' && $add_upon !== false ) {
		return $add_upon + random_int( 5, 15 );
	} else {
		return strtotime( 'now' ) + random_int( 5, 15 );
	}
}

/**
 * Get formatted message to send in DM
 *
 * @param INT $user_id
 * @param ARRAY $courses the student's list of sources
 * Merge fields: [LD_COURSES], [LD_STUDENT_NAME], [LD_STUDENT_EMAIL]
 */
function ets_learndash_discord_get_formatted_dm( $user_id, $courses, $message ) {
    
	$user_obj    = get_user_by( 'id', $user_id );
	$STUDENT_USERNAME = $user_obj->user_login;
	$STUDENT_EMAIL    = $user_obj->user_email;
	$SITE_URL  = get_bloginfo( 'url' );
	$BLOG_NAME = get_bloginfo( 'name' );

	$COURSES = '';
        if( is_array( $courses ) ){
		$args_courses = array(
        	'orderby'          => 'title',
        	'order'            => 'ASC',
		'numberposts' => count( $courses ),
		'post_type'   => 'sfwd-courses',
		'post__in' => $courses
		);
		$enrolled_courses = get_posts( $args_courses );
		$lastKeyCourse = array_key_last( $enrolled_courses );
		$commas = ', ';        
		foreach ($enrolled_courses as $key => $course) {
		if ( $lastKeyCourse === $key )  
			$commas = ' ' ;
			$COURSES .= esc_html( $course->post_title ). $commas;
		}
	}


		$find    = array(
			'[LD_COURSES]',
			'[LD_STUDENT_NAME]',
			'[LD_STUDENT_EMAIL]',
			'[SITE_URL]',
			'[BLOG_NAME]'
		);
		$replace = array(
			$COURSES,                    
			$STUDENT_USERNAME,
			$STUDENT_EMAIL,
			$SITE_URL,
			$BLOG_NAME
		);

		return str_replace( $find, $replace, $message );

}

/**
 * Get formatted Course complete message to send in DM
 *
 * @param INT $user_id
 * @param INT $course_id
 * Merge fields: [LD_COURSE_NAME], [LD_COURSE_COMPLETE_DATE], [LD_STUDENT_NAME], [LD_STUDENT_EMAIL] 
 */
function ets_learndash_discord_get_formatted_course_complete_dm( $user_id, $course_id, $message ) {
    
	$user_obj    = get_user_by( 'id', $user_id );
	$STUDENT_USERNAME = $user_obj->user_login;
	$STUDENT_EMAIL    = $user_obj->user_email;
	$SITE_URL  = get_bloginfo( 'url' );
	$BLOG_NAME = get_bloginfo( 'name' );        
        
	$course = get_post($course_id);
	$COURSES_NAME = $course->post_title;
        
	$COURSES_COMPLETE_DATE = date_i18n( get_option('date_format'), learndash_user_get_course_completed_date ( $user_id, $course_id ) );
        


		$find    = array(
			'[LD_COURSE_NAME]',
			'[LD_COURSE_COMPLETE_DATE]',                    
			'[LD_STUDENT_NAME]',
			'[LD_STUDENT_EMAIL]',
			'[SITE_URL]',
			'[BLOG_NAME]'                    
		);
		$replace = array(
			$COURSES_NAME,
			$COURSES_COMPLETE_DATE,
			$STUDENT_USERNAME,
			$STUDENT_EMAIL,
			$SITE_URL,
			$BLOG_NAME                    
		);

		return str_replace( $find, $replace, $message );

}

/**
 * Get formatted Lesson complete message to send in DM
 *
 * @param INT $user_id
 * @param INT $lesson_id
 * Merge fields: [LD_STUDENT_NAME], [LD_STUDENT_EMAIL], [LD_LESSON_NAME], [LD_COURSE_LESSON_DATE]
 */
function ets_learndash_discord_get_formatted_lesson_complete_dm( $user_id, $lesson_id , $message) {
        
	$user_obj    = get_user_by( 'id', $user_id );
	$STUDENT_USERNAME = $user_obj->user_login;
	$STUDENT_EMAIL    = $user_obj->user_email;
	$SITE_URL  = get_bloginfo( 'url' );
	$BLOG_NAME = get_bloginfo( 'name' );        
        
	$lesson = get_post($lesson_id);
	$LESSON_NAME = $lesson->post_title;
        
	$LESSON_COMPLETE_DATE = date_i18n( get_option('date_format'), time() ) ;
        
       

		$find    = array(
			'[LD_LESSON_NAME]',
			'[LD_COURSE_LESSON_DATE]',                    
			'[LD_STUDENT_NAME]',
			'[LD_STUDENT_EMAIL]',
			'[SITE_URL]',
			'[BLOG_NAME]'                     
		);
		$replace = array(
			$LESSON_NAME,
			$LESSON_COMPLETE_DATE,
			$STUDENT_USERNAME,
			$STUDENT_EMAIL,
			$SITE_URL,
			$BLOG_NAME                     
		);

		return str_replace( $find, $replace, $message );

}

/**
 * Get formatted Topic complete message to send in DM
 *
 * @param INT $user_id
 * @param INT $topic_id
 * Merge fields: [LD_STUDENT_NAME], [LD_STUDENT_EMAIL], [LD_TOPIC_NAME], [LD_COURSE_TOPIC_DATE]
 */
function ets_learndash_discord_get_formatted_topic_complete_dm( $user_id, $topic_id , $message) {
        
	$user_obj    = get_user_by( 'id', $user_id );
	$STUDENT_USERNAME = $user_obj->user_login;
	$STUDENT_EMAIL    = $user_obj->user_email;
	$SITE_URL  = get_bloginfo( 'url' );
	$BLOG_NAME = get_bloginfo( 'name' );        
        
	$topic = get_post( $topic_id );
	$TOPIC_NAME = esc_html( $topic->post_title );
        
	$TOPIC_COMPLETE_DATE = date_i18n( get_option( 'date_format' ), time() ) ;
        
       

		$find    = array(
			'[LD_TOPIC_NAME]',
			'[LD_COURSE_TOPIC_DATE]',                    
			'[LD_STUDENT_NAME]',
			'[LD_STUDENT_EMAIL]',
			'[SITE_URL]',
			'[BLOG_NAME]'                    
		);
		$replace = array(
			$TOPIC_NAME,
			$TOPIC_COMPLETE_DATE,
			$STUDENT_USERNAME,
			$STUDENT_EMAIL,
			$SITE_URL,
			$BLOG_NAME                     
		);

		return str_replace( $find, $replace, $message );

}

/**
 * Get formatted QUIZ complete message to send in DM
 *
 * @param INT $user_id
 * @param INT $quiz_id
 * Merge fields: [LD_STUDENT_NAME], [LD_STUDENT_EMAIL], [LD_QUIZ_NAME], [LD_QUIZ_DATE]
 */
function ets_learndash_discord_get_formatted_quiz_complete_dm( $user_id, $quiz_id , $message) {
        
	$user_obj    = get_user_by( 'id', $user_id );
	$STUDENT_USERNAME = $user_obj->user_login;
	$STUDENT_EMAIL    = $user_obj->user_email;
	$SITE_URL  = get_bloginfo( 'url' );
	$BLOG_NAME = get_bloginfo( 'name' );        
        
	$quiz = get_post($quiz_id);
	$QUIZ_NAME = esc_html( $quiz->post_title );
        
	$QUIZ_COMPLETE_DATE = date_i18n( get_option('date_format'), time() ) ;
        
       

		$find    = array(
			'[LD_QUIZ_NAME]',
			'[LD_QUIZ_DATE]',                    
			'[LD_STUDENT_NAME]',
			'[LD_STUDENT_EMAIL]',
			'[SITE_URL]',
			'[BLOG_NAME]'                    
		);
		$replace = array(
			$QUIZ_NAME,
			$QUIZ_COMPLETE_DATE,
			$STUDENT_USERNAME,
			$STUDENT_EMAIL,
			$SITE_URL,
			$BLOG_NAME                     
		);

		return str_replace( $find, $replace, $message );

}

/**
 * Get assignment approved message to send in DM
 *
 * @param INT $user_id
 * @param INT $assignment_id
 * Merge fields: [LD_STUDENT_NAME], [LD_STUDENT_EMAIL], [LD_ASSIGNMENT_COURSE], [LD_ASSIGNMENT_LESSON], [SITE_URL], [BLOG_NAME], [LD_ASSIGNMENT_APPROVED_DATE], [LD_LINK_OF_ASSIGNMENT], [LD_ASSIGNMENT_POINTS_AWARDED]
 */
function ets_learndash_discord_get_formatted_assignment_approved_dm( $user_id, $assignment_id, $message ) {
        
	$user_obj    = get_user_by( 'id', $user_id );
	$STUDENT_USERNAME = $user_obj->user_login;
	$STUDENT_EMAIL    = $user_obj->user_email;
	$SITE_URL  = get_bloginfo( 'url' );
	$BLOG_NAME = get_bloginfo( 'name' );        
        
//	$assignment = get_post( $assignment_id );
	$ASSIGNMENT_LINK = get_post_meta( $assignment_id, 'file_link', true ) ;
        
	$assignment_course_id = intval( get_post_meta( $assignment_id, 'course_id', true ) );
	$assignment_lesson_id = intval( get_post_meta( $assignment_id, 'lesson_id', true ) );
        
	$assignment_course = get_post( $assignment_course_id );
	$assignment_lesson = get_post( $assignment_lesson_id );
        
	$ASSIGNMENT_COURSE = esc_html( $assignment_course->post_title );
	$ASSIGNMENT_LESSON = esc_html( $assignment_lesson->post_title );        
        
	$ASSIGNMENT_APPROVED_DATE = date_i18n( get_option( 'date_format' ), time() ) ;
	$ASSIGNMENT_POINTS_AWARDED= '';
	if ( learndash_assignment_is_points_enabled( $assignment_id ) ){
		$ASSIGNMENT_POINTS = learndash_get_points_awarded_array( $assignment_id );
		$ASSIGNMENT_POINTS_AWARDED = $ASSIGNMENT_POINTS['current'] . '/' . $ASSIGNMENT_POINTS['max'];
	}
		$find    = array(
			'[LD_STUDENT_NAME]',
			'[LD_STUDENT_EMAIL]',                    
			'[LD_ASSIGNMENT_COURSE]',
			'[LD_ASSIGNMENT_LESSON]',
			'[LD_ASSIGNMENT_APPROVED_DATE]',
			'[LD_LINK_OF_ASSIGNMENT]',
			'[LD_ASSIGNMENT_POINTS_AWARDED]',
			'[SITE_URL]',
			'[BLOG_NAME]'                    
		);
		$replace = array(
			$STUDENT_USERNAME,
			$STUDENT_EMAIL,
			$ASSIGNMENT_COURSE,                    
			$ASSIGNMENT_LESSON,
			$ASSIGNMENT_APPROVED_DATE,
			$ASSIGNMENT_LINK,                    
			$ASSIGNMENT_POINTS_AWARDED,
			$SITE_URL,
			$BLOG_NAME                     
		);

		return str_replace( $find, $replace, $message );        

}

  /**
   * Log API call response
   *
   * @param INT          $user_id
   * @param STRING       $api_url
   * @param ARRAY        $api_args
   * @param ARRAY|OBJECT $api_response
   */
function ets_learndash_discord_log_api_response( $user_id, $api_url = '', $api_args = array(), $api_response = '' ) {
	$log_api_response = sanitize_text_field( trim( get_option( 'ets_learndash_discord_log_api_response' ) ) );
	if ( $log_api_response == true ) {
		$log_string  = '==>' . $api_url;
		$log_string .= '-::-' . serialize( $api_args );
		$log_string .= '-::-' . serialize( $api_response );

		$logs = new LearnDash_Discord_Add_On_Logs();
		$logs->write_api_response_logs( $log_string, $user_id );
	}
}

/**
 * Check API call response and detect conditions which can cause of action failure and retry should be attemped.
 *
 * @param ARRAY|OBJECT $api_response
 * @param BOOLEAN
 */
function ets_learndash_discord_check_api_errors( $api_response ) {
	// check if response code is a WordPress error.
	if ( is_wp_error( $api_response ) ) {
		return true;
	}

	// First Check if response contain codes which should not get re-try.
	$body = json_decode( wp_remote_retrieve_body( $api_response ), true );
	if ( isset( $body['code'] ) && in_array( $body['code'], LEARNDASH_DISCORD_DONOT_RETRY_THESE_API_CODES ) ) {
		return false;
	}

	$response_code = strval( $api_response['response']['code'] );
	if ( isset( $api_response['response']['code'] ) && in_array( $response_code, LEARNDASH_DISCORD_DONOT_RETRY_HTTP_CODES ) ) {
		return false;
	}

	// check if response code is in the range of HTTP error.
	if ( ( 400 <= absint( $response_code ) ) && ( absint( $response_code ) <= 599 ) ) {
		return true;
	}
}

/**
 * Get Action data from table `actionscheduler_actions`
 *
 * @param INT $action_id
 */
function ets_learndash_discord_as_get_action_data( $action_id ) {
	global $wpdb;
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT aa.hook, aa.status, aa.args, ag.slug AS as_group FROM ' . $wpdb->prefix . 'actionscheduler_actions as aa INNER JOIN ' . $wpdb->prefix . 'actionscheduler_groups as ag ON aa.group_id=ag.group_id WHERE `action_id`=%d AND ag.slug=%s', $action_id, LEARNDASH_DISCORD_AS_GROUP_NAME ), ARRAY_A );
        
	if ( ! empty( $result ) ) {
		return $result[0];
	} else {
		return false;
	}
}

/**
 * Get how many times a hook is failed in a particular day.
 *
 * @param STRING $hook
 */
function ets_learndash_discord_count_of_hooks_failures( $hook ) {
	global $wpdb;
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT count(last_attempt_gmt) as hook_failed_count FROM ' . $wpdb->prefix . 'actionscheduler_actions WHERE `hook`=%s AND status="failed" AND DATE(last_attempt_gmt) = %s', $hook, date( 'Y-m-d' ) ), ARRAY_A );
	
        if ( ! empty( $result ) ) {
		return $result['0']['hook_failed_count'];
	} else {
		return false;
	}
}

/**
 * Get pending jobs 
 */
function ets_learndash_discord_get_all_pending_actions() {
	global $wpdb;
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT aa.* FROM ' . $wpdb->prefix . 'actionscheduler_actions as aa INNER JOIN ' . $wpdb->prefix . 'actionscheduler_groups as ag ON aa.group_id = ag.group_id WHERE ag.slug = %s AND aa.status="pending" ', LEARNDASH_DISCORD_AS_GROUP_NAME ), ARRAY_A );

	if ( ! empty( $result ) ) {
		return $result['0'];
	} else {
		return false;
	}
}

/*
Get All failed actions from action scheduler.
*/

function ets_learndash_discord_get_all_failed_actions(){
	global $wpdb;
	$result = $wpdb->get_results( $wpdb->prepare( 'SELECT aa.action_id, aa.hook, ag.slug AS as_group FROM ' . $wpdb->prefix . 'actionscheduler_actions as aa INNER JOIN ' . $wpdb->prefix . 'actionscheduler_groups as ag ON aa.group_id=ag.group_id WHERE  ag.slug=%s AND aa.status = "failed" ' , LEARNDASH_DISCORD_AS_GROUP_NAME ), ARRAY_A );

	if ( ! empty( $result ) ) {
		return $result ;
	} else {
		return false;
	}        
}

/*
Get BOT name
*/
function ets_learndash_discord_update_bot_name_option ( ){
 
	$guild_id          = sanitize_text_field( trim( get_option( 'ets_learndash_discord_server_id' ) ) );
	$discord_bot_token = sanitize_text_field( trim( get_option( 'ets_learndash_discord_bot_token' ) ) );
	if ( $guild_id && $discord_bot_token ) {
            
                $discod_current_user_api = LEARNDASH_DISCORD_API_URL . 'users/@me';
                
		$app_args              = array(
			'method'  => 'GET',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bot ' . $discord_bot_token,
			),
		);                
                
		$app_response = wp_remote_post( $discod_current_user_api, $app_args );

		$response_arr =  json_decode ( wp_remote_retrieve_body( $app_response ), true );
                
		if( is_array( $response_arr ) && array_key_exists( 'username', $response_arr ) ){
                    
			update_option( 'ets_learndash_discord_connected_bot_name', $response_arr ['username'] );
		}else{
			delete_option( 'ets_learndash_discord_connected_bot_name' );
                }
                        
                
	}

}
/*
  Remove all usermeta created by this plugin.
  @param INT $user_id
*/
function ets_learndash_discord_remove_usermeta ( $user_id ){
 
	global $wpdb;
        
        
	$usermeta_table = $wpdb->prefix . "usermeta";
	$usermeta_sql = "DELETE FROM " . $usermeta_table . " WHERE `user_id` = %d AND  `meta_key` LIKE '_ets_learndash_discord%'; ";
	$delete_usermeta_sql = $wpdb->prepare( $usermeta_sql, $user_id );
	$wpdb->query( $delete_usermeta_sql );
             
}

/*
  Get message for what role is assigned to the member.
  @param STRING $mapped_role_name
  @param STRING $default_role_name
  @param STRING $restrictcontent_discord
*/

function ets_learndash_discord_roles_assigned_message ( $mapped_role_name, $default_role_name, $restrictcontent_discord ) {
    
	if ( $mapped_role_name ) {
		$restrictcontent_discord .= '<p class="ets_assigned_role">';
					
		$restrictcontent_discord .= esc_html__( 'Following Roles will be assigned to you in Discord: ', 'connect-learndash-discord-addon' );
		$restrictcontent_discord .=  $mapped_role_name  ;
		if ( $default_role_name ) {
			$restrictcontent_discord .=   $default_role_name  ; 
                                                
		}
					
		$restrictcontent_discord .= '</p>';
	} elseif( $default_role_name ) {
		$restrictcontent_discord .= '<p class="ets_assigned_role">';
					
		$restrictcontent_discord .= esc_html__( 'Following Role will be assigned to you in Discord: ', 'connect-learndash-discord-addon' );
		$restrictcontent_discord .= $default_role_name  ; 
					
		$restrictcontent_discord .= '</p>';
                                         
	}
	return $restrictcontent_discord;
}
/**
 * Get student's roles ids
 *
 * @param INT $user_id
 * @return ARRAY|NULL $roles
 */
function ets_learndash_discord_get_user_roles ( $user_id ){
	global $wpdb;

	$usermeta_table = $wpdb->prefix . "usermeta";
	$user_roles_sql = "SELECT * FROM " . $usermeta_table . " WHERE `user_id` = %d AND ( `meta_key` like '_ets_learndash_discord_role_id_for_%' OR `meta_key` = 'ets_learndash_discord_default_role_id' OR `meta_key` = '_ets_learndash_discord_last_default_role' ); ";
	$user_roles_prepare = $wpdb->prepare( $user_roles_sql, $user_id );
	
	$user_roles = $wpdb->get_results( $user_roles_prepare , ARRAY_A );
        
	if ( is_array( $user_roles ) && count( $user_roles ) ){
		$roles = array();
		foreach ( $user_roles as  $role ) {
                
			array_push( $roles, $role['meta_value'] );
		}
		
                return $roles;
            
	}else{
            
		return null;
	}
   
}

/**
 * Get rich embed message
 *
 * @param STRING $message
 * @return STRING $rich_embed_message
 */

function ets_learndash_discord_get_rich_embed_message ( $message ){
    
	$blog_logo_full = esc_url( wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' )[0] );
	$blog_logo_thumbnail =  esc_url( wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'thumbnail' )[0] );
	
	$SITE_URL  = get_bloginfo( 'url' );
	$BLOG_NAME = get_bloginfo( 'name' );
	$BLOG_DESCRIPTION = get_bloginfo( 'description' );
    
	$timestamp = date( "c", strtotime( "now" ) );
	$convert_lines = preg_split( "/\[LINEBREAK\]/", $message );
	$fields = [];
	if ( is_array ( $convert_lines ) ){
		for ( $i = 0; $i< count( $convert_lines ); $i++ ){
			array_push( $fields, ["name" => ".", "value" => $convert_lines[$i], "inline" => false ] );
		}
	}

	$rich_embed_message = json_encode( [
		"content" => "",
		"username" =>  $BLOG_NAME,
		"avatar_url" => $blog_logo_thumbnail,
		"tts" => false,
		"embeds" => [
			[
				"title" => "",
				"type" => "rich",
				"description" => $BLOG_DESCRIPTION,
				"url" => '',
				"timestamp" => $timestamp,
				"color" => hexdec( "3366ff" ),
				"footer" => [
					"text" => $BLOG_NAME,
					"icon_url" => $blog_logo_thumbnail
				],
				"image" => [
					"url" => $blog_logo_full
				],
				"thumbnail" => [
					"url" => $blog_logo_thumbnail
				],
				"author" => [
					"name" => $BLOG_NAME,
					"url" => $SITE_URL
				],
				"fields" => $fields
                            
			]
		]

	], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

	return $rich_embed_message ; 
}

/**
 * Get allowed html using Wordpress API function wp_kses
 *
 * @param STRING $html_message
 * @return STRING $html_message
 */

function ets_learndash_discord_allowed_html( ) {
	$allowed_html = array(
		'div' => array(
			'class' => array()
		),
		'p' => array(               
			'class' => array()
		),
		'a' => array(                                
			'id' => array(),
			'data-user-id' => array(),                    
			'href' => array(), 
			'class' => array(),
			'style' => array(),                    
		),
		'label' => array(
			'class'=>array() 
		),
		'h3' => array(),            
		'span' => array(
			'class' => array()
		),
		'i' => array(
			'style' => array(),
			'class' => array()                    
		),
		'button' => array(
			'class' => array(),
			'data-user-id' => array(),
			'id' => array(),                    
		)            
	);

	return $allowed_html;
}

/**
 * Get formatted complete course achievement message to send in DM
 *
 * @param INT $user_id
 * @param INT $course_id
 * 
 */
function ets_learndash_discord_get_formatted_complete_course_achievement_dm( $user_id, $course_id ) {
//	global $wpdb;
//	$user_obj    = get_user_by( 'id', $user_id );
//	$STUDENT_USERNAME = $user_obj->user_login;
//	$STUDENT_EMAIL    = $user_obj->user_email;
//	$SITE_URL  = get_bloginfo( 'url' );
//	$BLOG_NAME = get_bloginfo( 'name' );        
//        
//	$lesson = get_post( $course_id );
//	$COURSE_NAME = $lesson->post_title;
//    
//	$table_name = "{$wpdb->prefix}ld_achievements";
//	$sql = $wpdb->prepare( "SELECT `points` FROM `{$table_name}` WHERE `user_id` = %d and `post_id` = %d and  `trigger` = 'complete_course'", $user_id , $course_id );        
//	$points = $wpdb->get_results( $sql, ARRAY_A );        
//        
//	return 'HI ' . $STUDENT_USERNAME . '(' . $STUDENT_EMAIL . ')'. ' You Complete Course  Achievement : '. $COURSE_NAME  . ' , Points:' . $points[0]['points']  ; 

}


/**
 * Get formatted complete lesson achievement message to send in DM
 *
 * @param INT $user_id
 * @param INT $lesson_id
 * 
 */
function ets_learndash_discord_get_formatted_complete_lesson_achievement_dm( $user_id, $lesson_id ) {
	global $wpdb;
        update_option('achisvement_lesson_id', $lesson_id);
	$user_obj    = get_user_by( 'id', $user_id );
	$STUDENT_USERNAME = $user_obj->user_login;
	$STUDENT_EMAIL    = $user_obj->user_email;

        
	$blog_logo_full = esc_url( wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' )[0] );
	$blog_logo_thumbnail =  esc_url( wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'thumbnail' )[0] );
	
	$SITE_URL  = get_bloginfo( 'url' );
	$BLOG_NAME = get_bloginfo( 'name' );
	$BLOG_DESCRIPTION = get_bloginfo( 'description' );
    
	$timestamp = date( "c", strtotime( "now" ) );        
        
//	$lesson = get_post( $lesson_id );
//	$LESSON_NAME = $lesson->post_title;
    
	$sql = "SELECT `pm`.`post_id` FROM `{$wpdb->prefix}postmeta` as `pm` 
	inner join `{$wpdb->prefix}posts`  as `ps`
	on `pm`.`post_id` = `ps`.`ID`
	where `ps`.`post_type` = 'ld-achievement'
	and `pm`.`meta_key` = 'lesson_id'
	and `pm`.`meta_value` =". $lesson_id;
	$achievements = $wpdb->get_results( $sql, ARRAY_A );
	if ( is_array ( $achievements ) && count( $achievements ) > 0 ){
		foreach( $achievements as $achievement){
			$achievement_id = $achievement['post_id'];
			$post  = get_post( $achievement_id );
			if ( ! is_object( $post ) ) {
				continue;
			}
			$title   = $post->post_title;
			$message = get_post_meta( $achievement_id, 'achievement_message', true );
			$image = get_post_meta( $achievement_id, 'image', true );
                        
	$rich_embed_message = json_encode( [
		"content" => "",
		"username" =>  $BLOG_NAME,
		"avatar_url" => $blog_logo_thumbnail,
		"tts" => false,
		"embeds" => [
			[
				"title" => $title,
				"type" => "rich",
				"description" => $message,
				"url" => '',
				"timestamp" => $timestamp,
				"color" => hexdec( "3366ff" ),
				"footer" => [
					"text" => $BLOG_NAME,
					"icon_url" => $blog_logo_thumbnail
				],
				"image" => [
					"url" => $image
				],
				"thumbnail" => [
					"url" => $blog_logo_thumbnail
				],
				"author" => [
					"name" => $BLOG_NAME,
					"url" => $SITE_URL
				],
				"fields" => []
                            
			]
		]

	], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

	return $rich_embed_message ;                        
		}
	}
        
}


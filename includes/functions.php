<?php
/*
* common functions file.
*/

/**
 * 
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
       
   
    
    foreach($pages as $page){ 
        $selected = ( esc_attr( $page->ID ) === $ets_learndash_discord_redirect_page_id  ) ? ' selected="selected"' : '';
        $options .= '<option value="' . esc_attr( $page->ID ) . '" '. $selected .'> ' . $page->post_title . ' </option>';
    }
    
    return $options;
}

// function to get formated redirect url
function ets_get_learndash_discord_formated_discord_redirect_url( $page_id ) {
    $url = esc_url( get_permalink( $page_id ) );
    
	$parsed = parse_url( $url, PHP_URL_QUERY );
	if ( $parsed === null ) {
		return $url .= '?via=learndash-discord';
	} else {
		if ( stristr( $url, 'via=learndash-discord' ) !== false ) {
			return $url;
		} else {
			return $url .= '&via=learndash-discord';
		}
	}
}

/**
 * To check settings values saved or not
 *
 * @param NONE
 * @return BOOL $status
 */
function learndash_discord_check_saved_settings_status() {
	$ets_learndash_discord_client_id     = get_option( 'ets_learndash_discord_client_id' );
	$ets_learndash_discord_client_secret = get_option( 'ets_learndash_discord_client_secret' );
	$ets_learndash_discord_bot_token     = get_option( 'ets_learndash_discord_bot_token' );
	$ets_learndash_discord_redirect_url  = get_option( 'ets_learndash_discord_redirect_url' );
	$ets_learndash_discord_server_id      = get_option( 'ets_learndash_discord_server_id' );

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
 * Merge fields: [LD_COURSES], [LD_STUDENT_NAME], [LD_STUDENT_EMAIL]
 */
function ets_learndash_discord_get_formatted_dm( $user_id, $courses, $message ) {

	$user_obj    = get_user_by( 'id', $user_id );
	$STUDENT_USERNAME = $user_obj->user_login;
	$STUDENT_EMAIL    = $user_obj->user_email;
        
        $args_courses = array(
        'orderby'          => 'title',
        'order'            => 'ASC',
        'numberposts' => -1,
        'post__in' => $courses
        );
        $courses = get_posts( $args_courses );
        foreach ($courses as $course) {
            $COURSES .= $course->post_title . ', ';
        }


		$find    = array(
			'[LD_COURSES]',
			'[LD_STUDENT_NAME]',
			'[LD_STUDENT_EMAIL]',
		);
		$replace = array(
			$COURSES,                    
			$STUDENT_USERNAME,
			$STUDENT_EMAIL,
		);

		return str_replace( $find, $replace, $message );

}
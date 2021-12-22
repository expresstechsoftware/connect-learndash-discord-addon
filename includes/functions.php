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
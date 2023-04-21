<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Learndash_Discord
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
$ets_learndash_discord_data_erases = sanitize_text_field( trim( get_option( 'ets_learndash_discord_data_erases' ) ) );
if ( defined( 'WP_UNINSTALL_PLUGIN' )
		&& $_REQUEST['plugin'] === 'connect-learndash-discord-addon/learndash-discord.php'
		&& $_REQUEST['slug'] === 'connect-learndash-and-discord'
	&& wp_verify_nonce( $_REQUEST['_ajax_nonce'], 'updates' )
	&& $ets_learndash_discord_data_erases
  ) {

	global $wpdb;
	$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . "usermeta WHERE `meta_key` LIKE '_ets_learndash_discord%'" );
	$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . "options WHERE `option_name` LIKE 'ets_learndash_discord_%'" );

}


<?php
/**

 *
 * @link              https://www.expresstechsoftwares.com
 * @since             1.0.0
 * @package           Learndash_Discord
 *
 * @wordpress-plugin
 * Plugin Name:       Connect LearnDash and Discord
 * Plugin URI:        https://www.expresstechsoftwares.com/learndash-and-discord-integration
 * Description:       Connect LearnDash with Discord and open an oppurtunity of creating a vibrating community of your course learners.
 * Version:           1.0.12
 * Author:            ExpressTech Softwares Solutions Pvt Ltd
 * Author URI:        https://www.expresstechsoftwares.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       connect-learndash-and-discord
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'LEARNDASH_DISCORD_VERSION', '1.0.12' );

/**
 * Define plugin directory path
 */
define( 'LEARNDASH_DISCORD_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Define plugin directory URL
 */
define( 'LEARNDASH_DISCORD_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Discord Bot Permissions.
 */
define( 'LEARNDASH_DISCORD_BOT_PERMISSIONS', 8 );

/**
 * Discord api call scopes.
 */
define( 'LEARNDASH_DISCORD_OAUTH_SCOPES', 'identify email connections guilds guilds.join gdm.join rpc rpc.notifications.read rpc.voice.read rpc.voice.write rpc.activities.write bot webhook.incoming applications.builds.upload applications.builds.read applications.commands applications.store.update applications.entitlements activities.read activities.write relationships.read' );

/**
 * Define group name for action scheduler actions
 */
define( 'LEARNDASH_DISCORD_AS_GROUP_NAME', 'ets-learndash-discord' );

/**
 * Discord API url.
 */
define( 'LEARNDASH_DISCORD_API_URL', 'https://discord.com/api/v10/' );

/**
 * Follwing response codes not cosider for re-try API calls.
 */
define( 'LEARNDASH_DISCORD_DONOT_RETRY_THESE_API_CODES', array( 0, 10003, 50033, 10004, 50025, 10013, 10011 ) );

/**
 * Define plugin directory url
 */
define( 'LEARNDASH_DISCORD_DONOT_RETRY_HTTP_CODES', array( 400, 401, 403, 404, 405, 502 ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-learndash-discord-activator.php
 */
function activate_learndash_discord() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-learndash-discord-activator.php';
	Learndash_Discord_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-learndash-discord-deactivator.php
 */
function deactivate_learndash_discord() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-learndash-discord-deactivator.php';
	Learndash_Discord_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_learndash_discord' );
register_deactivation_hook( __FILE__, 'deactivate_learndash_discord' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-learndash-discord.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_learndash_discord() {

	$plugin = new Learndash_Discord();
	$plugin->run();

}
run_learndash_discord();

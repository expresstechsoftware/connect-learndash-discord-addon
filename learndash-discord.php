<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.expresstechsoftwares.com
 * @since             1.0.0
 * @package           Learndash_Discord
 *
 * @wordpress-plugin
 * Plugin Name:       LearnDash Discord
 * Plugin URI:        https://www.expresstechsoftwares.com/learndash-and-discord-integration
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            ExpressTech Softwares Solutions Pvt Ltd
 * Author URI:        https://www.expresstechsoftwares.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       learndash-discord
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LEARNDASH_DISCORD_VERSION', '1.0.0' );

/**
 * Define plugin directory path
 */
define( 'LEARNDASH_DISCORD_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Discord Bot Permissions.
 */
define( 'LEARNDASH_DISCORD_BOT_PERMISSIONS', 8 );

/**
 * Discord api call scopes.
 */
define( 'LEARNDASH_DISCORD_OAUTH_SCOPES', 'identify email connections guilds guilds.join gdm.join rpc rpc.notifications.read rpc.voice.read rpc.voice.write rpc.activities.write bot webhook.incoming messages.read applications.builds.upload applications.builds.read applications.commands applications.store.update applications.entitlements activities.read activities.write relationships.read' );

/**
 * Define group name for action scheduler actions
 */
define( 'LEARNDASH_DISCORD_AS_GROUP_NAME', 'ets-learndash-discord' );

/**
 * Discord API url. 
 */
define( 'LEARNDASH_DISCORD_API_URL', 'https://discord.com/api/v6/' );

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

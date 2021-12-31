<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Learndash_Discord
 * @subpackage Learndash_Discord/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Learndash_Discord
 * @subpackage Learndash_Discord/includes
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class Learndash_Discord {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Learndash_Discord_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'LEARNDASH_DISCORD_VERSION' ) ) {
			$this->version = LEARNDASH_DISCORD_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'learndash-discord';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Learndash_Discord_Loader. Orchestrates the hooks of the plugin.
	 * - Learndash_Discord_i18n. Defines internationalization functionality.
	 * - Learndash_Discord_Admin. Defines all hooks for the admin area.
	 * - Learndash_Discord_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
            
		/**
		 * Common functions file.
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-learndash-discord-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-learndash-discord-i18n.php';
                
		/**
		 * The class responsible for Checking plugin dependencies.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-learndash-discord-dependencies.php';                

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-learndash-discord-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-learndash-discord-public.php';

		$this->loader = new Learndash_Discord_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Learndash_Discord_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Learndash_Discord_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Learndash_Discord_Admin( $this->get_plugin_name(), $this->get_version() );
                $plugin_dependencies = new Learndash_Discord_Dependencies( $this->get_plugin_name(), $this->get_version() );
                
		$this->loader->add_action( 'admin_init', $plugin_dependencies, 'check_environment' );
		$this->loader->add_action( 'admin_notices', $plugin_dependencies, 'admin_notices' , 15 );                

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'ets_learndash_discord_connect_to_bot' );
                $this->loader->add_action( 'admin_menu', $plugin_admin, 'ets_learndash_Discord_add_settings_menu' );
                $this->loader->add_action( 'admin_post_learndash_discord_application_settings', $plugin_admin, 'ets_learndash_discord_application_settings' );
                $this->loader->add_action( 'admin_post_learndash_discord_save_role_mapping', $plugin_admin, 'ets_learndash_discord_save_role_mapping' );
                $this->loader->add_action( 'wp_ajax_ets_learndash_discord_load_discord_roles', $plugin_admin, 'ets_learndash_discord_load_discord_roles' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Learndash_Discord_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'do_shortcode_tag', $plugin_public, 'ets_learndash_show_discord_button' , 10 , 3  );
		$this->loader->add_shortcode( 'learndash_discord', $plugin_public, 'ets_learndash_discord_add_connect_discord_button' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Learndash_Discord_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

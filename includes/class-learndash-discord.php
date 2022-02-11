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
		$this->define_common_hooks();                

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
       * The class responsible for defining all methods that help to schedule actions.
    */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/libraries/action-scheduler/action-scheduler.php';

		/**
		 * The class responsible for Logs
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-learndash-discord-add-on-logs.php';                
            
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

		$plugin_admin = new Learndash_Discord_Admin( $this->get_plugin_name(), $this->get_version(), Learndash_Discord_Public::get_learndash_discord_public_instance( $this->get_plugin_name(), $this->get_version() ) );
		$plugin_dependencies = new Learndash_Discord_Dependencies( $this->get_plugin_name(), $this->get_version() );
                
		$this->loader->add_action( 'admin_init', $plugin_dependencies, 'check_environment' );
		$this->loader->add_action( 'admin_notices', $plugin_dependencies, 'admin_notices' , 15 );                

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'ets_learndash_discord_connect_to_bot' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'ets_learndash_Discord_add_settings_menu' );
		$this->loader->add_filter( 'manage_users_columns', $plugin_admin, 'ets_learndash_discord_add_learndash_discord_column' );                                                                                
		$this->loader->add_filter( 'manage_users_custom_column', $plugin_admin, 'ets_learndash_discord_run_learndash_discord_api', 10, 3 );                
		$this->loader->add_action( 'admin_post_learndash_discord_application_settings', $plugin_admin, 'ets_learndash_discord_application_settings' );
		$this->loader->add_action( 'admin_post_learndash_discord_save_role_mapping', $plugin_admin, 'ets_learndash_discord_save_role_mapping' );
		$this->loader->add_action( 'admin_post_learndash_discord_save_advance_settings', $plugin_admin, 'ets_learndash_discord_save_advance_settings' );
		$this->loader->add_action( 'wp_ajax_ets_learndash_discord_load_discord_roles', $plugin_admin, 'ets_learndash_discord_load_discord_roles' );
		$this->loader->add_action( 'wp_ajax_ets_learndash_discord_run_api', $plugin_admin, 'ets_learndash_discord_run_api' );                
                
		$this->loader->add_action( 'learndash_update_course_access', $plugin_admin, 'ets_learndash_discord_admin_update_course_access' , 99 , 4 );                                
		$this->loader->add_action( 'learndash_assignment_approved', $plugin_admin, 'ets_learndash_discord_admin_assignment_approved', 10, 1 );                                                                
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = Learndash_Discord_Public::get_learndash_discord_public_instance( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'do_shortcode_tag', $plugin_public, 'ets_learndash_show_discord_button' , 10 , 3  );
		$this->loader->add_shortcode( 'learndash_discord', $plugin_public, 'ets_learndash_discord_add_connect_discord_button' );
		$this->loader->add_action( 'init', $plugin_public, 'ets_learndash_discord_api_callback' );
		$this->loader->add_action( 'ets_learndash_discord_as_handle_add_member_to_guild', $plugin_public, 'ets_learndash_discord_as_handler_add_member_to_guild', 10, 3 );
		$this->loader->add_action( 'ets_learndash_discord_as_schedule_member_put_role', $plugin_public, 'ets_learndash_discord_as_handler_put_member_role', 10, 3 );                
		$this->loader->add_action( 'ets_learndash_discord_as_send_dm', $plugin_public, 'ets_learndash_discord_handler_send_dm', 10, 3 );                
		$this->loader->add_action( 'learndash_course_completed', $plugin_public, 'ets_learndash_course_completed', 10, 1 );                                
		$this->loader->add_action( 'learndash_lesson_completed', $plugin_public, 'ets_learndash_lesson_completed', 10, 1 );                                                
		$this->loader->add_action( 'learndash_topic_completed', $plugin_public, 'ets_learndash_topic_completed', 10, 1 );                                                                
		$this->loader->add_action( 'learndash_quiz_completed', $plugin_public, 'ets_learndash_quiz_completed', 10, 2 );                                                                                
		$this->loader->add_action( 'wp_ajax_learndash_disconnect_from_discord', $plugin_public, 'ets_learndash_discord_disconnect_from_discord' );
		$this->loader->add_action( 'ets_learndash_discord_as_schedule_delete_member', $plugin_public, 'ets_learndash_discord_as_handler_delete_member_from_guild', 10, 3 );
		$this->loader->add_action( 'ets_learndash_discord_as_schedule_delete_role',  $plugin_public, 'ets_learndash_discord_as_handler_delete_memberrole' , 10, 3 );
//		$this->loader->add_filter( 'learndash_get_user_activity', $plugin_public, 'ets_learndash_discord_get_user_activity' , 10, 2 );                
//		$this->loader->add_action( 'learndash_certification_content_write_cell_after', $plugin_public, 'ets_learndash_discord_certification_created' , 10 , 3 );  

        }
        
	/**
	 * Define actions which are not in admin or not public
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_common_hooks() {
		$this->loader->add_action( 'action_scheduler_failed_execution',  $this, 'ets_learndash_discord_reschedule_failed_action' );		     		
		$this->loader->add_filter( 'action_scheduler_queue_runner_batch_size', $this, 'ets_learndash_discord_queue_batch_size' );                
		$this->loader->add_filter( 'action_scheduler_queue_runner_concurrent_batches', $this, 'ets_learndash_discord_concurrent_batches' );            
		
        }

        /**
	 * Re-schedule  failed action 
	 *
	 * @param INT            $action_id
	 * @param OBJECT         $e
	 * @param OBJECT context
	 * @return NONE
	 */
	public function ets_learndash_discord_reschedule_failed_action( $action_id  ) {
		// First check if the action is for LearnDash discord.
		$action_data = ets_learndash_discord_as_get_action_data( $action_id );
		if ( $action_data !== false ) {
			$hook              = $action_data['hook'];
			$args              = json_decode( $action_data['args'] );
			$retry_failed_api  = sanitize_text_field( trim( get_option( 'ets_learndash_discord_retry_failed_api' ) ) );
			$hook_failed_count = ets_learndash_discord_count_of_hooks_failures( $hook );
			$retry_api_count   = absint( sanitize_text_field( trim( get_option( 'ets_learndash_discord_retry_api_count' ) ) ) );
			if ( $hook_failed_count < $retry_api_count && $retry_failed_api == true && $action_data['as_group'] == LEARNDASH_DISCORD_AS_GROUP_NAME && $action_data['status'] === 'failed' ) {
				as_schedule_single_action( ets_learndash_discord_get_random_timestamp( ets_learndash_discord_get_highest_last_attempt_timestamp() ), $hook, array_values( $args ), LEARNDASH_DISCORD_AS_GROUP_NAME );
			}
		}
	}
        
	/**
	 * Set action scheuduler batch size.
	 *
	 * @param INT $batch_size
	 * @return INT $concurrent_batches
	 */
	public function ets_learndash_discord_queue_batch_size( $batch_size ) {
		if ( ets_learndash_discord_get_all_pending_actions() !== false ) {
			return absint( get_option( 'ets_learndash_discord_job_queue_batch_size' ) );
		} else {
			return $batch_size;
		}
	}
        
	/**
	 * Set action scheuduler concurrent batches.
	 *
	 * @param INT $concurrent_batches
	 * @return INT $concurrent_batches
	 */
	public function ets_learndash_discord_concurrent_batches( $concurrent_batches ) {
		if ( ets_learndash_discord_get_all_pending_actions() !== false ) {
			return absint( get_option( 'ets_learndash_discord_job_queue_concurrency' ) );
		} else {
			return $concurrent_batches;
		}
	}
     
	public static function get_discord_logo_white(){
		$img = file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'public/images/discord-logo-white.svg' );
		$data = base64_encode( $img );
                
		return '<img src="data:image/svg+xml;base64,' . $data . '" />';
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

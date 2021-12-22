<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Learndash_Discord
 * @subpackage Learndash_Discord/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Learndash_Discord
 * @subpackage Learndash_Discord/admin
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class Learndash_Discord_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Learndash_Discord_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Learndash_Discord_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name .'-select2', plugin_dir_url( __FILE__ ) . 'css/select2.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . 'discord_tabs_css', plugin_dir_url( __FILE__ ) . 'css/skeletabs.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/learndash-discord-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Learndash_Discord_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Learndash_Discord_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
            
		wp_enqueue_script( $this->plugin_name . '-select2',  plugin_dir_url( __FILE__ ) . 'js/select2.js', array( 'jquery' ), $this->version, false );
            
		wp_enqueue_script( $this->plugin_name . '-tabs-js', plugin_dir_url( __FILE__ ) . 'js/skeletabs.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/learndash-discord-admin.js', array( 'jquery' ), $this->version, false );

	}
        
	/**
	 * Method to add discord setting sub-menu under top level menu of learndash-lms
	 *
	 * @since    1.0.0
	 */
	public function ets_learndash_Discord_add_settings_menu() {
		add_submenu_page( 'learndash-lms', __( 'Discord Settings', 'learndash-discord' ), __( 'Discord Settings', 'learndash-discord' ), 'manage_options', 'learndash-discord', array( $this, 'ets_learndash_discord_setting_page' ) );
	}
        
	/**
	 * Callback to Display settings page
	 *
	 * @since    1.0.0
	 */
	public function ets_learndash_discord_setting_page() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
                require_once LEARNDASH_DISCORD_PLUGIN_DIR_PATH . 'admin/partials/learndash-discord-admin-display.php';
	}
        
	/*
	Save application details
	*/
	public function ets_learndash_discord_application_settings() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( 'You do not have sufficient rights', 403 );
			exit();
		}
		$ets_learndash_discord_client_id = isset( $_POST['ets_learndash_discord_client_id'] ) ? sanitize_text_field( trim( $_POST['ets_learndash_discord_client_id'] ) ) : '';

		$ets_learndash_discord_client_secret = isset( $_POST['ets_learndash_discord_client_secret'] ) ? sanitize_text_field( trim( $_POST['ets_learndash_discord_client_secret'] ) ) : '';

		$ets_learndash_discord_bot_token = isset( $_POST['ets_learndash_discord_bot_token'] ) ? sanitize_text_field( trim( $_POST['ets_learndash_discord_bot_token'] ) ) : '';

		$ets_learndash_discord_redirect_url = isset( $_POST['ets_learndash_discord_redirect_url'] ) ? sanitize_text_field( trim( $_POST['ets_learndash_discord_redirect_url'] ) ) : '';
                
                $ets_learndash_discord_redirect_page_id  = isset( $_POST['ets_learndash_discord_redirect_page_id'] ) ? sanitize_text_field( trim( $_POST['ets_learndash_discord_redirect_page_id'] ) ) : '';

		$ets_learndash_discord_server_id = isset( $_POST['ets_learndash_discord_server_id'] ) ? sanitize_text_field( trim( $_POST['ets_learndash_discord_server_id'] ) ) : '';

		if ( isset( $_POST['submit'] ) ) {
			if ( isset( $_POST['ets_learndash_discord_save_settings'] ) && wp_verify_nonce( $_POST['ets_learndash_discord_save_settings'], 'save_learndash_discord_general_settings' ) ) {
				if ( $ets_learndash_discord_client_id ) {
					update_option( 'ets_learndash_discord_client_id', $ets_learndash_discord_client_id );
				}

				if ( $ets_learndash_discord_client_secret ) {
					update_option( 'ets_learndash_discord_client_secret', $ets_learndash_discord_client_secret );
				}

				if ( $ets_learndash_discord_bot_token ) {
					update_option( 'ets_learndash_discord_bot_token', $ets_learndash_discord_bot_token );
				}

				if ( $ets_learndash_discord_redirect_url ) {
					update_option( 'ets_learndash_discord_redirect_page_id', $ets_learndash_discord_redirect_url );					
					$ets_learndash_discord_redirect_url = ets_get_learndash_discord_formated_discord_redirect_url( $ets_learndash_discord_redirect_url );
					update_option( 'ets_learndash_discord_redirect_url', $ets_learndash_discord_redirect_url );
                                        
				}

				if ( $ets_learndash_discord_server_id ) {
					update_option( 'ets_learndash_discord_server_id', $ets_learndash_discord_server_id );
				}

				$message = 'Your settings are saved successfully.';
				if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
					
					
					$pre_location = $_SERVER['HTTP_REFERER'] . '&save_settings_msg=' . $message . '#ets_learndash_application_details';
					wp_safe_redirect( $pre_location );
				}
			}
		}
	}

}

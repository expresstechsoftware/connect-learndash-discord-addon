<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Learndash_Discord
 * @subpackage Learndash_Discord/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Learndash_Discord
 * @subpackage Learndash_Discord/public
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class Learndash_Discord_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/learndash-discord-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/learndash-discord-public.js', array( 'jquery' ), $this->version, false );

	}
        
        /**
        * Add button to make connection in between user and discord
        *
        * @param NONE
        * @return NONE
        */
        public function ets_learndash_discord_add_connect_discord_button(){
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}
		$user_id = sanitize_text_field( trim( get_current_user_id() ) );

		$access_token = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_access_token', true ) ) );

		$default_role                   = sanitize_text_field( trim( get_option( 'ets_learndash_discord_default_role_id' ) ) );
		$ets_learndash_discord_role_mapping = json_decode( get_option( 'ets_learndash_discord_role_mapping' ), true );
		$all_roles                      = unserialize( get_option( 'ets_learndash_discord_all_roles' ) );
		$enrolled_courses                  = ets_learndash_discord_get_student_courses_id( $user_id );
		$mapped_role_name               = '';
		if ( is_array ( $enrolled_courses ) && is_array( $all_roles ) ) {
                   // $lastKey = array_key_last( $enrolled_courses );
                    $spacer = count( $enrolled_courses ) > 1 ? ', ' : '';
                    foreach ( $enrolled_courses as $enrolled_course_id ){
			if ( is_array( $ets_learndash_discord_role_mapping ) && array_key_exists( 'learndash_course_id_' . $enrolled_course_id, $ets_learndash_discord_role_mapping ) ) {
				
                            $mapped_role_id = $ets_learndash_discord_role_mapping[ 'learndash_course_id_' . $enrolled_course_id ];
				
                                if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
                                    // if $lastKey
					$mapped_role_name .= $all_roles[ $mapped_role_id ] . $spacer;
				}
			}
                    }
		}
		$default_role_name = '';
		if ( $default_role != 'none' && is_array( $all_roles ) && array_key_exists( $default_role, $all_roles ) ) {
			$default_role_name = $all_roles[ $default_role ];
		}
                $restrictcontent_discord = '';
		if ( learndash_discord_check_saved_settings_status() ) {

			if ( $access_token ) {
				
                                $restrictcontent_discord .= '<div class="">';
                                $restrictcontent_discord .='<div class="">';
				$restrictcontent_discord .= '<label class="ets-connection-lbl">' . esc_html__( 'Discord connection', 'learndash-discord' ) . '</label>';
                                $restrictcontent_discord .= '</div>';
                                $restrictcontent_discord .= '<div class="">';
				$restrictcontent_discord .= '<a href="#" class="ets-btn learndash-discord-btn-disconnect" id="learndash-discord-disconnect-discord" data-user-id="'. esc_attr( $user_id ) .'">'. esc_html__( 'Disconnect From Discord ', 'learndash-discord' ) . '<i class="fab fa-discord"></i></a>';
				$restrictcontent_discord .= '<span class="ets-spinner"></span>';
                                $restrictcontent_discord .= '</div>';
                                $restrictcontent_discord .= '</div>';
				
		
                        } else {
				
                                $restrictcontent_discord .= '<div class="">';
				$restrictcontent_discord .= '<h3>' . esc_html__( 'Discord connection', 'learndash-discord' ) .'</h3>';
                                $restrictcontent_discord .= '<div class="">';
				$restrictcontent_discord .= '<a href="?action=learndas-discord-login" class="learndash-discord-btn-connect ets-btn" >' . esc_html__( 'Connect To Discord', 'learndash-discord' ) . '<i class="fab fa-discord"></i></a>';
                                $restrictcontent_discord .= '</div>';
				if ( $mapped_role_name ) {
					$restrictcontent_discord .= '<p class="ets_assigned_role">';
					
					$restrictcontent_discord .= __( 'Following Roles will be assigned to you in Discord: ', 'learndash-discord' );
					$restrictcontent_discord .= esc_html( $mapped_role_name  );
					if ( $default_role_name ) {
						$restrictcontent_discord .= ', ' . esc_html( $default_role_name ); 
                                                
                                        }
					
					$restrictcontent_discord .= '</p>';
				 } elseif( $default_role_name ) {
                                        $restrictcontent_discord .= '<p class="ets_assigned_role">';
					
					$restrictcontent_discord .= esc_html__( 'Following Role will be assigned to you in Discord: ', 'learndash-discord' );
                                        $restrictcontent_discord .= esc_html( $default_role_name ); 
					
                                        $restrictcontent_discord .= '</p>';
                                         
                                 }
                                   
                                $restrictcontent_discord .= '</div>';
			
			}
		}
                wp_enqueue_style( $this->plugin_name );
                wp_enqueue_script( $this->plugin_name ); 
                
                return $restrictcontent_discord ;
        }        
        
	/**
	 * Add connect discord button on student profile page at the end of a [ls_profile] shortcode.
	 *
	 * @since    1.0.0
	 */
	public function ets_learndash_show_discord_button( $output, $tag, $attr ){
            
		if( 'ld_profile' != $tag ){
                    return $output;
                }
                $output .= $this->ets_learndash_discord_add_connect_discord_button();
		
		return $output;
        }

	}

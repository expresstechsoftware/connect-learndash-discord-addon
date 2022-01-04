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
				$restrictcontent_discord .= '<a href="?action=learndash-discord-login" class="learndash-discord-btn-connect ets-btn" >' . esc_html__( 'Connect To Discord', 'learndash-discord' ) . '<i class="fab fa-discord"></i></a>';
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
        
	/**
	 * For authorization process call discord API
	 *
	 * @param NONE
	 * @return OBJECT REST API response
	 */
	public function ets_learndash_discord_api_callback() {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			if ( isset( $_GET['action'] ) && $_GET['action'] == 'learndash-discord-login' ) {
				$params                    = array(
					'client_id'     => sanitize_text_field( trim( get_option( 'ets_learndash_discord_client_id' ) ) ),
					'redirect_uri'  => sanitize_text_field( trim( get_option( 'ets_learndash_discord_redirect_url' ) ) ),
					'response_type' => 'code',
					'scope'         => 'identify email connections guilds guilds.join messages.read',
				);
				$discord_authorise_api_url = LEARNDASH_DISCORD_API_URL . 'oauth2/authorize?' . http_build_query( $params );

				wp_redirect( $discord_authorise_api_url, 302, get_site_url() );
				exit;
			}

			if ( isset( $_GET['code'] ) && isset( $_GET['via'] ) && $_GET['via'] == 'learndash-discord' ) {
				$code     = sanitize_text_field( trim( $_GET['code'] ) );
				$response = $this->create_discord_auth_token( $code, $user_id );

				if ( ! empty( $response ) && ! is_wp_error( $response ) ) {
					$res_body              = json_decode( wp_remote_retrieve_body( $response ), true );
					$discord_exist_user_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_user_id', true ) ) );
					if ( is_array( $res_body ) ) {

						if ( array_key_exists( 'access_token', $res_body ) ) {

							$access_token = sanitize_text_field( trim( $res_body['access_token'] ) );
							update_user_meta( $user_id, '_ets_learndash_discord_access_token', $access_token );
							if ( array_key_exists( 'refresh_token', $res_body ) ) {
								$refresh_token = sanitize_text_field( trim( $res_body['refresh_token'] ) );
								update_user_meta( $user_id, '_ets_learndash_discord_refresh_token', $refresh_token );
							}
							if ( array_key_exists( 'expires_in', $res_body ) ) {
								$expires_in = $res_body['expires_in'];
								$date       = new DateTime();
								$date->add( DateInterval::createFromDateString( '' . $expires_in . ' seconds' ) );
								$token_expiry_time = $date->getTimestamp();
								update_user_meta( $user_id, '_ets_learndash_discord_expires_in', $token_expiry_time );
							}
							$user_body = $this->get_discord_current_user( $access_token );

							if ( is_array( $user_body ) && array_key_exists( 'discriminator', $user_body ) ) {
								$discord_user_number           = $user_body['discriminator'];
								$discord_user_name             = $user_body['username'];
								$discord_user_name_with_number = $discord_user_name . '#' . $discord_user_number;
								update_user_meta( $user_id, '_ets_learndash_discord_username', $discord_user_name_with_number );
							}
							if ( is_array( $user_body ) && array_key_exists( 'id', $user_body ) ) {
								$_ets_learndash_discord_user_id = sanitize_text_field( trim( $user_body['id'] ) );
								if ( $discord_exist_user_id == $_ets_learndash_discord_user_id ) {
									$_ets_learndash_discord_role_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_role_id', true ) ) );
									if ( ! empty( $_ets_learndash_discord_role_id ) && $_ets_learndash_discord_role_id != 'none' ) {
										//$this->delete_discord_role( $user_id, $_ets_learndash_discord_role_id );
									}
								}
								update_user_meta( $user_id, '_ets_learndash_discord_user_id', $_ets_learndash_discord_user_id );
								//$this->add_discord_member_in_guild( $_ets_learndash_discord_user_id, $user_id, $access_token );
							}
						} else {

						}
					} else {

					}
				}
			}
		}
	}
        
	/**
	 * Create authentication token for discord API
	 *
	 * @param STRING $code
	 * @param INT    $user_id
	 * @return OBJECT API response
	 */
	public function create_discord_auth_token( $code, $user_id ) {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}
		// stop users who having the direct URL of discord Oauth.
		// We must check IF NONE Student is set to NO and user having no learndash account.
		$allow_none_member = sanitize_text_field( trim( get_option( 'ets_learndash_allow_none_member' ) ) );
		$curr_level_id     = sanitize_text_field( ets_learndash_discord_get_student_courses_id( $user_id ) );
		if ( $curr_level_id == null && $allow_none_member == 'no' ) {
			return;
		}
		$response              = '';
		$refresh_token         = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_refresh_token', true ) ) );
		$token_expiry_time     = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_expires_in', true ) ) );
		$discord_token_api_url = LEARNDASH_DISCORD_API_URL . 'oauth2/token';
		if ( $refresh_token ) {
			$date              = new DateTime();
			$current_timestamp = $date->getTimestamp();
			if ( $current_timestamp > $token_expiry_time ) {
				$args     = array(
					'method'  => 'POST',
					'headers' => array(
						'Content-Type' => 'application/x-www-form-urlencoded',
					),
					'body'    => array(
						'client_id'     => sanitize_text_field( trim( get_option( 'ets_learndash_discord_client_id' ) ) ),
						'client_secret' => sanitize_text_field( trim( get_option( 'ets_learndash_discord_client_secret' ) ) ),
						'grant_type'    => 'refresh_token',
						'refresh_token' => $refresh_token,
						'redirect_uri'  => sanitize_text_field( trim( get_option( 'ets_learndash_discord_redirect_url' ) ) ),
						'scope'         => LEARNDASH_DISCORD_OAUTH_SCOPES,
					),
				);
				$response = wp_remote_post( $discord_token_api_url, $args );
				//ets_learndash_discord_log_api_response( $user_id, $discord_token_api_url, $args, $response );
				//if ( ets_learndash_discord_check_api_errors( $response ) ) {
					//$response_arr = json_decode( wp_remote_retrieve_body( $response ), true );
					//LearnDash_Discord_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				//}
			}
		} else {
			$args     = array(
				'method'  => 'POST',
				'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
				),
				'body'    => array(
					'client_id'     => sanitize_text_field( trim( get_option( 'ets_learndash_discord_client_id' ) ) ),
					'client_secret' => sanitize_text_field( trim( get_option( 'ets_learndash_discord_client_secret' ) ) ),
					'grant_type'    => 'authorization_code',
					'code'          => $code,
					'redirect_uri'  => sanitize_text_field( trim( get_option( 'ets_learndash_discord_redirect_url' ) ) ),
					'scope'         => LEARNDASH_DISCORD_OAUTH_SCOPES,
				),
			);
			$response = wp_remote_post( $discord_token_api_url, $args );
			//ets_learndash_discord_log_api_response( $user_id, $discord_token_api_url, $args, $response );
			//if ( ets_learndash_discord_check_api_errors( $response ) ) {
				//$response_arr = json_decode( wp_remote_retrieve_body( $response ), true );
				//LearnDash_Discord_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
			//}
		}
		return $response;
	}
        
	/**
	 * Get Discord user details from API
	 *
	 * @param STRING $access_token
	 * @return OBJECT REST API response
	 */
	public function get_discord_current_user( $access_token ) {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}
		$user_id = get_current_user_id();

		$discord_cuser_api_url = LEARNDASH_DISCORD_API_URL. 'users/@me';
		$param                 = array(
			'headers' => array(
				'Content-Type'  => 'application/x-www-form-urlencoded',
				'Authorization' => 'Bearer ' . $access_token,
			),
		);
		$user_response         = wp_remote_get( $discord_cuser_api_url, $param );
		//ets_learndash_discord_log_api_response( $user_id, $discord_cuser_api_url, $param, $user_response );

		$response_arr = json_decode( wp_remote_retrieve_body( $user_response ), true );
		//write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
		$user_body = json_decode( wp_remote_retrieve_body( $user_response ), true );
		return $user_body;

	}
        

        


}

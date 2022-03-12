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
	 * The single object Learndash_Discord_Public
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var Learndash_Discord_Public
	 */
	private static $instance;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string $plugin_name       The name of the plugin.
		 * @param      string $version    The version of this plugin.
		 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	public static function get_learndash_discord_public_instance( $plugin_name, $version ) {

		if ( ! self::$instance ) {
			self::$instance = new Learndash_Discord_Public( $plugin_name, $version );

		}
		return self::$instance;
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
		$script_params = array(
			'admin_ajax'                  => admin_url( 'admin-ajax.php' ),
			'permissions_const'           => LEARNDASH_DISCORD_BOT_PERMISSIONS,
			'ets_learndash_discord_nonce' => wp_create_nonce( 'ets-learndash-ajax-nonce' ),
		);
		wp_localize_script( $this->plugin_name, 'etsLearnDashParams', $script_params );

	}

	/**
	 * Disconnect user from discord, and , if the case, kick students on disconnect
	 *
	 * @param NONE
	 * @return OBJECT JSON response
	 */
	public function ets_learndash_discord_disconnect_from_discord() {

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}

		// Check for nonce security
		if ( ! wp_verify_nonce( $_POST['ets_learndash_discord_nonce'], 'ets-learndash-ajax-nonce' ) ) {
				wp_send_json_error( 'You do not have sufficient rights', 403 );
				exit();
		}
		$user_id              = sanitize_text_field( trim( $_POST['user_id'] ) );
		$kick_upon_disconnect = sanitize_text_field( trim( get_option( 'ets_learndash_discord_kick_upon_disconnect' ) ) );
		if ( $user_id ) {
			delete_user_meta( $user_id, '_ets_learndash_discord_access_token' );
			delete_user_meta( $user_id, '_ets_learndash_discord_refresh_token' );
			$user_roles = ets_learndash_discord_get_user_roles( $user_id );
			if ( $kick_upon_disconnect ) {

				if ( is_array( $user_roles ) ) {
					foreach ( $user_roles as $user_role ) {
						$this->delete_discord_role( $user_id, $user_role );
					}
				}
			} else {
				$this->delete_member_from_guild( $user_id, false );
			}
		}
		$event_res = array(
			'status'  => 1,
			'message' => 'Successfully disconnected',
		);
		wp_send_json( $event_res );
	}

	/**
	 * Schedule delete existing user from guild
	 *
	 * @param INT  $user_id
	 * @param BOOL $is_schedule
	 * @param NONE
	 */
	public function delete_member_from_guild( $user_id, $is_schedule = true ) {
		if ( $is_schedule && isset( $user_id ) ) {

			as_schedule_single_action( ets_learndash_discord_get_random_timestamp( ets_learndash_discord_get_highest_last_attempt_timestamp() ), 'ets_learndash_discord_as_schedule_delete_member', array( $user_id, $is_schedule ), LEARNDASH_DISCORD_AS_GROUP_NAME );
		} else {
			if ( isset( $user_id ) ) {
				$this->ets_learndash_discord_as_handler_delete_member_from_guild( $user_id, $is_schedule );
			}
		}
	}

	/**
	 * AS Handling member delete from huild
	 *
	 * @param INT  $user_id
	 * @param BOOL $is_schedule
	 * @return OBJECT API response
	 */
	public function ets_learndash_discord_as_handler_delete_member_from_guild( $user_id, $is_schedule ) {
		$guild_id                       = sanitize_text_field( trim( get_option( 'ets_learndash_discord_server_id' ) ) );
		$discord_bot_token              = sanitize_text_field( trim( get_option( 'ets_learndash_discord_bot_token' ) ) );
		$_ets_learndash_discord_user_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_user_id', true ) ) );
		$guilds_delete_memeber_api_url  = LEARNDASH_DISCORD_API_URL . 'guilds/' . $guild_id . '/members/' . $_ets_learndash_discord_user_id;
		$guild_args                     = array(
			'method'  => 'DELETE',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bot ' . $discord_bot_token,
			),
		);
		$guild_response                 = wp_remote_post( $guilds_delete_memeber_api_url, $guild_args );

		ets_learndash_discord_log_api_response( $user_id, $guilds_delete_memeber_api_url, $guild_args, $guild_response );
		if ( ets_learndash_discord_check_api_errors( $guild_response ) ) {
			$response_arr = json_decode( wp_remote_retrieve_body( $guild_response ), true );
			LearnDash_Discord_Add_On_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
			if ( $is_schedule ) {
				// this exception should be catch by action scheduler.
				throw new Exception( 'Failed in function ets_learndash_discord_as_handler_delete_member_from_guild' );
			}
		}

		/*Delete all usermeta related to discord connection*/
		ets_learndash_discord_remove_usermeta( $user_id );

	}

		/**
		 * Add button to make connection in between user and discord
		 *
		 * @param NONE
		 * @return STRING
		 */
	public function ets_learndash_discord_add_connect_discord_button() {

		$user_id = sanitize_text_field( trim( get_current_user_id() ) );

		$access_token                    = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_access_token', true ) ) );
		$_ets_learndash_discord_username = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_username', true ) ) );
		$allow_none_student              = sanitize_text_field( trim( get_option( 'ets_learndash_discord_allow_none_student' ) ) );

		$default_role                       = sanitize_text_field( trim( get_option( 'ets_learndash_discord_default_role_id' ) ) );
		$ets_learndash_discord_role_mapping = json_decode( get_option( 'ets_learndash_discord_role_mapping' ), true );
		$all_roles                          = unserialize( get_option( 'ets_learndash_discord_all_roles' ) );
		$enrolled_courses                   = ets_learndash_discord_get_student_courses_id( $user_id );
		$mapped_role_name                   = '';
		if ( is_array( $enrolled_courses ) && is_array( $all_roles ) && is_array( $ets_learndash_discord_role_mapping ) ) {
			$lastKey = array_key_last( $enrolled_courses );
			$spacer  = ', ';
			foreach ( $enrolled_courses as $key => $enrolled_course_id ) {
				if ( array_key_exists( 'learndash_course_id_' . $enrolled_course_id, $ets_learndash_discord_role_mapping ) ) {

									$mapped_role_id = $ets_learndash_discord_role_mapping[ 'learndash_course_id_' . $enrolled_course_id ];

					if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
						if ( $lastKey === $key ) {
							$spacer = '.';
						}
							$mapped_role_name .= $all_roles[ $mapped_role_id ] . $spacer;
					}
				}
			}
		}

		$default_role_name = '';
		if ( is_array( $all_roles ) ) {
			if ( $default_role != 'none' && array_key_exists( $default_role, $all_roles ) ) {
				$default_role_name = $all_roles[ $default_role ];
			}
		}

			$restrictcontent_discord = '';
		if ( learndash_discord_check_saved_settings_status() ) {

			if ( $access_token ) {

				$restrictcontent_discord .= '<div class="">';
				$restrictcontent_discord .= '<div class="">';
				$restrictcontent_discord .= '<label class="ets-connection-lbl">' . esc_html__( 'Discord connection', 'learndash-discord' ) . '</label>';
				$restrictcontent_discord .= '</div>';
				$restrictcontent_discord .= '<div class="">';
				$restrictcontent_discord .= '<a href="#" class="ets-btn learndash-discord-btn-disconnect" id="learndash-discord-disconnect-discord" data-user-id="' . esc_attr( $user_id ) . '">' . esc_html__( 'Disconnect From Discord', 'learndash-discord' ) . Learndash_Discord::get_discord_logo_white() . '</a>';
				$restrictcontent_discord .= '<span class="ets-spinner"></span>';
				$restrictcontent_discord .= '<p>' . esc_html__( sprintf( 'Connected account: %s', $_ets_learndash_discord_username ), 'learndash-discord' ) . '</p>';
				$restrictcontent_discord  = ets_learndash_discord_roles_assigned_message( $mapped_role_name, $default_role_name, $restrictcontent_discord );
				$restrictcontent_discord .= '</div>';
				$restrictcontent_discord .= '</div>';

			} elseif ( ( ets_learndash_discord_get_student_courses_id( $user_id ) && $mapped_role_name )
								|| ( ets_learndash_discord_get_student_courses_id( $user_id ) && ! $mapped_role_name && $default_role_name )
								|| ( $allow_none_student == 'yes' ) ) {

				$restrictcontent_discord .= '<div class="">';
				$restrictcontent_discord .= '<h3>' . esc_html__( 'Discord connection', 'learndash-discord' ) . '</h3>';
				$restrictcontent_discord .= '<div class="">';
				$restrictcontent_discord .= '<a href="?action=learndash-discord-login" class="learndash-discord-btn-connect ets-btn" >' . esc_html__( 'Connect To Discord', 'learndash-discord' ) . Learndash_Discord::get_discord_logo_white() . '</a>';
				$restrictcontent_discord .= '</div>';
				$restrictcontent_discord  = ets_learndash_discord_roles_assigned_message( $mapped_role_name, $default_role_name, $restrictcontent_discord );

					$restrictcontent_discord .= '</div>';

			}
		}
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name );

			return $restrictcontent_discord;
	}

	/**
	 * Add connect discord button on student profile page at the end of a [ls_profile] shortcode.
	 *
	 * @since    1.0.0
	 */
	public function ets_learndash_show_discord_button( $output, $tag, $attr ) {

		if ( 'ld_profile' != $tag ) {
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
								if ( $discord_exist_user_id === $_ets_learndash_discord_user_id ) {
									$courses = map_deep( ets_learndash_discord_get_student_courses_id( $user_id ), 'sanitize_text_field' );
									if ( is_array( $courses ) ) {
										foreach ( $courses as $course_id ) {
											$_ets_learndash_discord_role_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_role_id_for_' . $course_id, true ) ) );
											if ( ! empty( $_ets_learndash_discord_role_id ) && $_ets_learndash_discord_role_id != 'none' ) {
												$this->delete_discord_role( $user_id, $_ets_learndash_discord_role_id );
											}
										}
									}
								}
								update_user_meta( $user_id, '_ets_learndash_discord_user_id', $_ets_learndash_discord_user_id );
								$this->add_discord_member_in_guild( $_ets_learndash_discord_user_id, $user_id, $access_token );
							}
						} else {

						}
					} else {

					}
				}
			}
		} else {
			if ( isset( $_GET['code'] ) && isset( $_GET['via'] ) && $_GET['via'] == 'learndash-discord' ) {
                                update_option('check_redirect_connect', $_GET );
				$code     = sanitize_text_field( trim( $_GET['code'] ) );                            
				$response = $this->create_discord_auth_token( $code, 'new_learndash_student' );
				if ( ! empty( $response ) && ! is_wp_error( $response ) ) {
					$res_body = json_decode( wp_remote_retrieve_body( $response ), true );
                                        update_option('check_res_body', $res_body );
					if ( is_array( $res_body ) ) {
						if ( array_key_exists( 'access_token', $res_body ) ) {
							$access_token       = sanitize_text_field( trim( $res_body['access_token'] ) );
							$user_body          = $this->get_discord_current_user( $access_token , 'new_learndash_student' );
                                                        update_option('check_user_body', $user_body );
							$discord_user_email = $user_body['email'];
							$password           = wp_generate_password( 12, true, false );
                                                        update_option('check_user_email', $discord_user_email );
							if ( email_exists( $discord_user_email ) ) {
								$current_user = get_user_by( 'email', $discord_user_email );
								$user_id      = $current_user->ID;
							} else {
								$user_id = wp_create_user( $discord_user_email, $password, $discord_user_email );
								wp_new_user_notification( $user_id, null, $password );
							}
							update_user_meta( $user_id, '_ets_learndash_discord_access_token', $access_token );
                                                        $discord_exist_user_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_user_id', true ) ) );
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
							

							if ( is_array( $user_body ) && array_key_exists( 'discriminator', $user_body ) ) {
								$discord_user_number           = $user_body['discriminator'];
								$discord_user_name             = $user_body['username'];
								$discord_user_name_with_number = $discord_user_name . '#' . $discord_user_number;
								update_user_meta( $user_id, '_ets_learndash_discord_username', $discord_user_name_with_number );
							}
							if ( is_array( $user_body ) && array_key_exists( 'id', $user_body ) ) {
								$_ets_learndash_discord_user_id = sanitize_text_field( trim( $user_body['id'] ) );
								if ( $discord_exist_user_id === $_ets_learndash_discord_user_id ) {
									$courses = map_deep( ets_learndash_discord_get_student_courses_id( $user_id ), 'sanitize_text_field' );
									if ( is_array( $courses ) ) {
										foreach ( $courses as $course_id ) {
											$_ets_learndash_discord_role_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_role_id_for_' . $course_id, true ) ) );
											if ( ! empty( $_ets_learndash_discord_role_id ) && $_ets_learndash_discord_role_id != 'none' ) {
												$this->delete_discord_role( $user_id, $_ets_learndash_discord_role_id );
											}
										}
									}
								}
								update_user_meta( $user_id, '_ets_learndash_discord_user_id', $_ets_learndash_discord_user_id );
								
							
                                                        
							$credentials = array(
								'user_login'    => $discord_user_email,
								'user_password' => $password,
							);
							wp_set_auth_cookie( $user_id, false, '', '' );
							wp_signon( $credentials, '' );
							$discord_user_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_user_id', true ) ) );
                                                        $this->add_discord_member_in_guild( $discord_user_id, $user_id, $access_token );                                                        
							if ( $_COOKIE['ets_learndash_current_location_storage'] ) {
								wp_safe_redirect( urldecode_deep( $_COOKIE['ets_learndash_current_location_storage'] ) );
								exit();
							}
						}
					}
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
			if( isset( $code ) && !empty( $code ) && $user_id == 'new_learndash_student' ){
				$discord_token_api_url = LEARNDASH_DISCORD_API_URL . 'oauth2/token';
				$params     = array(
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
					),
				);
				$response = wp_remote_post( $discord_token_api_url, $params );
				ets_learndash_discord_log_api_response( $user_id, $discord_token_api_url, $params, $response );
				if ( ets_learndash_discord_check_api_errors( $response ) ) {
					$response_arr = json_decode( wp_remote_retrieve_body( $response ), true );
					write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				}
				return $response;                            
                            
			} else{                    
				wp_send_json_error( 'Unauthorized user', 401 );
				exit();
			}
		}
		// stop users who having the direct URL of discord Oauth.
		// We must check IF NONE Student is set to NO and user having no learndash account.
		$allow_none_student = sanitize_text_field( trim( get_option( 'ets_learndash_discord_allow_none_student' ) ) );
		$enrolled_courses   = sanitize_text_field( ets_learndash_discord_get_student_courses_id( $user_id ) );
		if ( $enrolled_courses === null && $allow_none_student == 'no' ) {
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
				ets_learndash_discord_log_api_response( $user_id, $discord_token_api_url, $args, $response );
				if ( ets_learndash_discord_check_api_errors( $response ) ) {
					$response_arr = json_decode( wp_remote_retrieve_body( $response ), true );
					LearnDash_Discord_Add_On_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				}
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
			ets_learndash_discord_log_api_response( $user_id, $discord_token_api_url, $args, $response );
			if ( ets_learndash_discord_check_api_errors( $response ) ) {
				$response_arr = json_decode( wp_remote_retrieve_body( $response ), true );
				LearnDash_Discord_Add_On_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
			}
		}
		return $response;
	}

	/**
	 * Get Discord user details from API
	 *
	 * @param STRING $access_token
	 * @return OBJECT REST API response
	 */
	public function get_discord_current_user( $access_token , $new_learndash_student = '' ) {
		if ( ! is_user_logged_in() && $new_learndash_student == '' ) {
			wp_send_json_error( 'Unauthorized user', 401 );
			exit();
		}
		$user_id = get_current_user_id();

		$discord_cuser_api_url = LEARNDASH_DISCORD_API_URL . 'users/@me';
		$param                 = array(
			'headers' => array(
				'Content-Type'  => 'application/x-www-form-urlencoded',
				'Authorization' => 'Bearer ' . $access_token,
			),
		);
		$user_response         = wp_remote_get( $discord_cuser_api_url, $param );
		ets_learndash_discord_log_api_response( $user_id, $discord_cuser_api_url, $param, $user_response );

		$response_arr = json_decode( wp_remote_retrieve_body( $user_response ), true );
		LearnDash_Discord_Add_On_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
		$user_body = json_decode( wp_remote_retrieve_body( $user_response ), true );
		return $user_body;

	}

	/**
	 * Add new member into discord guild
	 *
	 * @param INT    $_ets_learndash_discord_user_id
	 * @param INT    $user_id
	 * @param STRING $access_token
	 * @return NONE
	 */
	public function add_discord_member_in_guild( $_ets_learndash_discord_user_id, $user_id, $access_token ) {
//		if ( ! is_user_logged_in() ) {
//			wp_send_json_error( 'Unauthorized user', 401 );
//			exit();
//		}
		$enrolled_courses = map_deep( ets_learndash_discord_get_student_courses_id( $user_id ), 'sanitize_text_field' );
		if ( $enrolled_courses !== null ) {
			// It is possible that we may exhaust API rate limit while adding members to guild, so handling off the job to queue.
			as_schedule_single_action( ets_learndash_discord_get_random_timestamp( ets_learndash_discord_get_highest_last_attempt_timestamp() ), 'ets_learndash_discord_as_handle_add_member_to_guild', array( $_ets_learndash_discord_user_id, $user_id, $access_token ), LEARNDASH_DISCORD_AS_GROUP_NAME );
		}
	}

	/**
	 * Method to add new members to discord guild.
	 *
	 * @param INT    $_ets_learndash_discord_user_id
	 * @param INT    $user_id
	 * @param STRING $access_token
	 * @return NONE
	 */
	public function ets_learndash_discord_as_handler_add_member_to_guild( $_ets_learndash_discord_user_id, $user_id, $access_token ) {
		// Since we using a queue to delay the API call, there may be a condition when a member is delete from DB. so put a check.
		if ( get_userdata( $user_id ) === false ) {
			return;
		}
		$guild_id                           = sanitize_text_field( trim( get_option( 'ets_learndash_discord_server_id' ) ) );
		$discord_bot_token                  = sanitize_text_field( trim( get_option( 'ets_learndash_discord_bot_token' ) ) );
		$default_role                       = sanitize_text_field( trim( get_option( 'ets_learndash_discord_default_role_id' ) ) );
		$ets_learndash_discord_role_mapping = json_decode( get_option( 'ets_learndash_discord_role_mapping' ), true );
		$discord_role                       = '';
		$discord_roles                      = array();
		$courses                            = map_deep( ets_learndash_discord_get_student_courses_id( $user_id ), 'sanitize_text_field' );

		$ets_learndash_discord_send_welcome_dm = sanitize_text_field( trim( get_option( 'ets_learndash_discord_send_welcome_dm' ) ) );
		if ( is_array( $courses ) ) {
			foreach ( $courses as $course_id ) {

				if ( is_array( $ets_learndash_discord_role_mapping ) && array_key_exists( 'learndash_course_id_' . $course_id, $ets_learndash_discord_role_mapping ) ) {
					$discord_role = sanitize_text_field( trim( $ets_learndash_discord_role_mapping[ 'learndash_course_id_' . $course_id ] ) );
					array_push( $discord_roles, $discord_role );
					update_user_meta( $user_id, '_ets_learndash_discord_role_id_for_' . $course_id, $discord_role );
				}
			}
		}

		$guilds_memeber_api_url = LEARNDASH_DISCORD_API_URL . 'guilds/' . $guild_id . '/members/' . $_ets_learndash_discord_user_id;
		$guild_args             = array(
			'method'  => 'PUT',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bot ' . $discord_bot_token,
			),
			'body'    => json_encode(
				array(
					'access_token' => $access_token,
				)
			),
		);
		$guild_response         = wp_remote_post( $guilds_memeber_api_url, $guild_args );

		ets_learndash_discord_log_api_response( $user_id, $guilds_memeber_api_url, $guild_args, $guild_response );
		if ( ets_learndash_discord_check_api_errors( $guild_response ) ) {

			$response_arr = json_decode( wp_remote_retrieve_body( $guild_response ), true );
			LearnDash_Discord_Add_On_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
			// this should be catch by Action schedule failed action.
			throw new Exception( 'Failed in function ets_learndash_discord_as_handler_add_member_to_guild' );
		}

		foreach ( $discord_roles as $discord_role ) {

			if ( $discord_role && $discord_role != 'none' && isset( $user_id ) ) {
				$this->put_discord_role_api( $user_id, $discord_role );

			}
		}

		if ( $default_role && $default_role != 'none' && isset( $user_id ) ) {
			update_user_meta( $user_id, '_ets_learndash_discord_last_default_role', $default_role );
			$this->put_discord_role_api( $user_id, $default_role );
		}
		if ( empty( get_user_meta( $user_id, '_ets_learndash_discord_join_date', true ) ) ) {
			update_user_meta( $user_id, '_ets_learndash_discord_join_date', current_time( 'Y-m-d H:i:s' ) );
		}

		// Send welcome message.
		if ( $ets_learndash_discord_send_welcome_dm == true ) {
			as_schedule_single_action( ets_learndash_discord_get_random_timestamp( ets_learndash_discord_get_highest_last_attempt_timestamp() ), 'ets_learndash_discord_as_send_dm', array( $user_id, $courses, 'welcome' ), LEARNDASH_DISCORD_AS_GROUP_NAME );
		}
	}

	/**
	 * API call to change discord user role
	 *
	 * @param INT  $user_id
	 * @param INT  $role_id
	 * @param BOOL $is_schedule
	 * @return object API response
	 */
	public function put_discord_role_api( $user_id, $role_id, $is_schedule = true ) {
		if ( $is_schedule ) {
			as_schedule_single_action( ets_learndash_discord_get_random_timestamp( ets_learndash_discord_get_highest_last_attempt_timestamp() ), 'ets_learndash_discord_as_schedule_member_put_role', array( $user_id, $role_id, $is_schedule ), LEARNDASH_DISCORD_AS_GROUP_NAME );
		} else {
			$this->ets_learndash_discord_as_handler_put_member_role( $user_id, $role_id, $is_schedule );
		}
	}

	/**
	 * Action Schedule handler for mmeber change role discord.
	 *
	 * @param INT  $user_id
	 * @param INT  $role_id
	 * @param BOOL $is_schedule
	 * @return object API response
	 */
	public function ets_learndash_discord_as_handler_put_member_role( $user_id, $role_id, $is_schedule ) {
		$access_token                   = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_access_token', true ) ) );
		$guild_id                       = sanitize_text_field( trim( get_option( 'ets_learndash_discord_server_id' ) ) );
		$_ets_learndash_discord_user_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_user_id', true ) ) );
		$discord_bot_token              = sanitize_text_field( trim( get_option( 'ets_learndash_discord_bot_token' ) ) );
		$discord_change_role_api_url    = LEARNDASH_DISCORD_API_URL . 'guilds/' . $guild_id . '/members/' . $_ets_learndash_discord_user_id . '/roles/' . $role_id;

		if ( $access_token && $_ets_learndash_discord_user_id ) {
			$param = array(
				'method'  => 'PUT',
				'headers' => array(
					'Content-Type'   => 'application/json',
					'Authorization'  => 'Bot ' . $discord_bot_token,
					'Content-Length' => 0,
				),
			);

			$response = wp_remote_get( $discord_change_role_api_url, $param );

			ets_learndash_discord_log_api_response( $user_id, $discord_change_role_api_url, $param, $response );
			if ( ets_learndash_discord_check_api_errors( $response ) ) {
				$response_arr = json_decode( wp_remote_retrieve_body( $response ), true );
				LearnDash_Discord_Add_On_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				if ( $is_schedule ) {
					// this exception should be catch by action scheduler.
					throw new Exception( 'Failed in function ets_learndash_discord_as_handler_put_member_role' );
				}
			}
		}
	}


	/**
	 * Discord DM a member using bot.
	 *
	 * @param INT    $user_id
	 * @param
	 * @param STRING $type (warning|expired)
	 */
	public function ets_learndash_discord_handler_send_dm( $user_id, $courses, $type = 'warning' ) {
		$discord_user_id   = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_user_id', true ) ) );
		$discord_bot_token = sanitize_text_field( trim( get_option( 'ets_learndash_discord_bot_token' ) ) );

		$ets_learndash_discord_welcome_message             = sanitize_text_field( trim( get_option( 'ets_learndash_discord_welcome_message' ) ) );
		$ets_learndash_discord_course_complete_message     = sanitize_text_field( trim( get_option( 'ets_learndash_discord_course_complete_message' ) ) );
		$ets_learndash_discord_lesson_complete_message     = sanitize_text_field( trim( get_option( 'ets_learndash_discord_lesson_complete_message' ) ) );
		$ets_learndash_discord_topic_complete_message      = sanitize_text_field( trim( get_option( 'ets_learndash_discord_topic_complete_message' ) ) );
		$ets_learndash_discord_quiz_complete_message       = sanitize_text_field( trim( get_option( 'ets_learndash_discord_quiz_complete_message' ) ) );
		$ets_learndash_discord_assignment_approved_message = sanitize_text_field( trim( get_option( 'ets_learndash_discord_assignment_approved_message' ) ) );
		// Check if DM channel is already created for the user.
		$user_dm = get_user_meta( $user_id, '_ets_learndash_discord_dm_channel', true );

		if ( ! isset( $user_dm['id'] ) || $user_dm === false || empty( $user_dm ) ) {
			$this->ets_learndash_discord_create_member_dm_channel( $user_id );
			$user_dm       = get_user_meta( $user_id, '_ets_learndash_discord_dm_channel', true );
			$dm_channel_id = $user_dm['id'];
		} else {
			$dm_channel_id = $user_dm['id'];
		}

		if ( $type == 'welcome' ) {
			if ( is_array( $courses ) ) {
				update_user_meta( $user_id, '_ets_learndash_discord_welcome_dm_for_' . implode( '_', $courses ), true );
			}
			$message = ets_learndash_discord_get_formatted_dm( $user_id, $courses, $ets_learndash_discord_welcome_message );
		}
		if ( $type == 'course_complete' ) {
			update_user_meta( $user_id, '_ets_learndash_discord_course_complete_dm_for' . $courses, true );
			$message = ets_learndash_discord_get_formatted_course_complete_dm( $user_id, $courses, $ets_learndash_discord_course_complete_message );
		}
		if ( $type == 'lesson_complete' ) {
			update_user_meta( $user_id, '_ets_learndash_discord_lesson_complete_dm_for_' . $courses, true );
			$message = ets_learndash_discord_get_formatted_lesson_complete_dm( $user_id, $courses, $ets_learndash_discord_lesson_complete_message );
		}
		if ( $type == 'topic_complete' ) {
			update_user_meta( $user_id, '_ets_learndash_discord_topic_complete_dm_for_' . $courses, true );
			$message = ets_learndash_discord_get_formatted_topic_complete_dm( $user_id, $courses, $ets_learndash_discord_topic_complete_message );
		}
		if ( $type == 'quiz_complete' ) {
			update_user_meta( $user_id, '_ets_learndash_discord_quiz_complete_dm_for_' . $courses, true );
			$message = ets_learndash_discord_get_formatted_quiz_complete_dm( $user_id, $courses, $ets_learndash_discord_quiz_complete_message );
		}
		if ( $type == 'course_certificate_link' ) {

			$message = $courses;
		}

		if ( $type == 'assignment_approved' ) {

			$message = ets_learndash_discord_get_formatted_assignment_approved_dm( $user_id, $courses, $ets_learndash_discord_assignment_approved_message );
		}

		$creat_dm_url = LEARNDASH_DISCORD_API_URL . '/channels/' . $dm_channel_id . '/messages';
		$dm_args      = array(
			'method'  => 'POST',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bot ' . $discord_bot_token,
			),
			'body'    => json_encode(
				array(
					'content' => sanitize_text_field( trim( wp_unslash( $message ) ) ),
				)
			),
		);
		$dm_response  = wp_remote_post( $creat_dm_url, $dm_args );
		ets_learndash_discord_log_api_response( $user_id, $creat_dm_url, $dm_args, $dm_response );
		$dm_response_body = json_decode( wp_remote_retrieve_body( $dm_response ), true );
		if ( ets_learndash_discord_check_api_errors( $dm_response ) ) {
			LearnDash_Discord_Add_On_Logs::write_api_response_logs( $dm_response_body, $user_id, debug_backtrace()[0] );
			// this should be catch by Action schedule failed action.
			throw new Exception( 'Failed in function ets_learndash_discord_handler_send_dm' );
		}
	}

	/**
	 * Create DM channel for a give user_id
	 *
	 * @param INT $user_id
	 * @return MIXED
	 */
	public function ets_learndash_discord_create_member_dm_channel( $user_id ) {
		$discord_user_id       = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_user_id', true ) ) );
		$discord_bot_token     = sanitize_text_field( trim( get_option( 'ets_learndash_discord_bot_token' ) ) );
		$create_channel_dm_url = LEARNDASH_DISCORD_API_URL . '/users/@me/channels';
		$dm_channel_args       = array(
			'method'  => 'POST',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bot ' . $discord_bot_token,
			),
			'body'    => json_encode(
				array(
					'recipient_id' => $discord_user_id,
				)
			),
		);

		$created_dm_response = wp_remote_post( $create_channel_dm_url, $dm_channel_args );
		ets_learndash_discord_log_api_response( $user_id, $create_channel_dm_url, $dm_channel_args, $created_dm_response );
		$response_arr = json_decode( wp_remote_retrieve_body( $created_dm_response ), true );

		if ( is_array( $response_arr ) && ! empty( $response_arr ) ) {
			// check if there is error in create dm response
			if ( array_key_exists( 'code', $response_arr ) || array_key_exists( 'error', $response_arr ) ) {
				LearnDash_Discord_Add_On_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				if ( ets_learndash_discord_check_api_errors( $created_dm_response ) ) {
					// this should be catch by Action schedule failed action.
					throw new Exception( 'Failed in function ets_learndash_discord_create_member_dm_channel' );
				}
			} else {
				update_user_meta( $user_id, '_ets_learndash_discord_dm_channel', $response_arr );
			}
		}
		return $response_arr;
	}

	/**
	 * Schedule delete discord role for a student
	 *
	 * @param INT  $user_id
	 * @param INT  $ets_learndash_discord_role_id
	 * @param BOOL $is_schedule
	 * @return OBJECT API response
	 */
	public function delete_discord_role( $user_id, $ets_learndash_discord_role_id, $is_schedule = true ) {
		if ( $is_schedule ) {
			as_schedule_single_action( ets_learndash_discord_get_random_timestamp( ets_learndash_discord_get_highest_last_attempt_timestamp() ), 'ets_learndash_discord_as_schedule_delete_role', array( $user_id, $ets_learndash_discord_role_id, $is_schedule ), LEARNDASH_DISCORD_AS_GROUP_NAME );
		} else {
			$this->ets_learndash_discord_as_handler_delete_memberrole( $user_id, $ets_learndash_discord_role_id, $is_schedule );
		}
	}

	/**
	 * Action Schedule handler to process delete role of a student.
	 *
	 * @param INT  $user_id
	 * @param INT  $ets_learndash_discord_role_id
	 * @param BOOL $is_schedule
	 * @return OBJECT API response
	 */
	public function ets_learndash_discord_as_handler_delete_memberrole( $user_id, $ets_learndash_discord_role_id, $is_schedule = true ) {

		$guild_id                       = sanitize_text_field( trim( get_option( 'ets_learndash_discord_server_id' ) ) );
		$_ets_learndash_discord_user_id = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_user_id', true ) ) );
		$discord_bot_token              = sanitize_text_field( trim( get_option( 'ets_learndash_discord_bot_token' ) ) );
		$discord_delete_role_api_url    = LEARNDASH_DISCORD_API_URL . 'guilds/' . $guild_id . '/members/' . $_ets_learndash_discord_user_id . '/roles/' . $ets_learndash_discord_role_id;
		if ( $_ets_learndash_discord_user_id ) {
			$param = array(
				'method'  => 'DELETE',
				'headers' => array(
					'Content-Type'   => 'application/json',
					'Authorization'  => 'Bot ' . $discord_bot_token,
					'Content-Length' => 0,
				),
			);

			$response = wp_remote_request( $discord_delete_role_api_url, $param );
			ets_learndash_discord_log_api_response( $user_id, $discord_delete_role_api_url, $param, $response );
			if ( ets_learndash_discord_check_api_errors( $response ) ) {
				$response_arr = json_decode( wp_remote_retrieve_body( $response ), true );
				LearnDash_Discord_Add_On_Logs::write_api_response_logs( $response_arr, $user_id, debug_backtrace()[0] );
				if ( $is_schedule ) {
					// this exception should be catch by action scheduler.
					throw new Exception( 'Failed in function ets_learndash_discord_as_handler_delete_memberrole' );
				}
			}
			return $response;
		}
	}
	/**
	 * Check if the student has completed courses that he has registered.
	 *
	 * @param ARRAY $args Course complete data.
	 */
	public function ets_learndash_course_completed( $args = array() ) {
		$user_id                                       = $args['user']->ID;
		$course_id                                     = $args['course']->ID;
		$ets_learndash_discord_send_course_complete_dm = sanitize_text_field( trim( get_option( 'ets_learndash_discord_send_course_complete_dm' ) ) );

		// Send Course Complete message.
		if ( $ets_learndash_discord_send_course_complete_dm == true ) {
			as_schedule_single_action( ets_learndash_discord_get_random_timestamp( ets_learndash_discord_get_highest_last_attempt_timestamp() ), 'ets_learndash_discord_as_send_dm', array( $user_id, $course_id, 'course_complete' ), LEARNDASH_DISCORD_AS_GROUP_NAME );
		}

	}

	/**
	 * Check if the student has completed a lesson.
	 *
	 * @param ARRAY $lesson_data Lesson complete data.
	 */
	public function ets_learndash_lesson_completed( $lesson_data ) {
		$user_id                                       = $lesson_data['user']->ID;
		$lesson_id                                     = $lesson_data['lesson']->ID;
		$ets_learndash_discord_send_lesson_complete_dm = sanitize_text_field( trim( get_option( 'ets_learndash_discord_send_lesson_complete_dm' ) ) );

		// Send Lesson Complete message.
		if ( $ets_learndash_discord_send_lesson_complete_dm == true ) {
			as_schedule_single_action( ets_learndash_discord_get_random_timestamp( ets_learndash_discord_get_highest_last_attempt_timestamp() ), 'ets_learndash_discord_as_send_dm', array( $user_id, $lesson_id, 'lesson_complete' ), LEARNDASH_DISCORD_AS_GROUP_NAME );
		}

	}

	/**
	 * Check if the student has completed a topic.
	 *
	 * @param ARRAY $topic_data Topic complete data.
	 */
	public function ets_learndash_topic_completed( $topic_data ) {

		$user_id                                      = $topic_data['user']->ID;
		$topic_id                                     = $topic_data['topic']->ID;
		$ets_learndash_discord_send_topic_complete_dm = sanitize_text_field( trim( get_option( 'ets_learndash_discord_send_topic_complete_dm' ) ) );

		// Send Topic Complete message.
		if ( $ets_learndash_discord_send_topic_complete_dm == true ) {
			as_schedule_single_action( ets_learndash_discord_get_random_timestamp( ets_learndash_discord_get_highest_last_attempt_timestamp() ), 'ets_learndash_discord_as_send_dm', array( $user_id, $topic_id, 'topic_complete' ), LEARNDASH_DISCORD_AS_GROUP_NAME );
		}

	}

	/**
	 * Check if the student has completed a quiz.
	 *
	 * @param ARRAY  $quiz_data Topic complete data.
	 * @param OBJECT $user WP_User object
	 */
	public function ets_learndash_quiz_completed( $quiz_data, $user ) {

		$user_id = $user->ID;
		$quiz_id = $quiz_data['quiz'];

		$ets_learndash_discord_send_quiz_complete_dm = sanitize_text_field( trim( get_option( 'ets_learndash_discord_send_quiz_complete_dm' ) ) );

		// Send Quiz Complete message.
		if ( $ets_learndash_discord_send_quiz_complete_dm == true ) {
			as_schedule_single_action( ets_learndash_discord_get_random_timestamp( ets_learndash_discord_get_highest_last_attempt_timestamp() ), 'ets_learndash_discord_as_send_dm', array( $user_id, $quiz_id, 'quiz_complete' ), LEARNDASH_DISCORD_AS_GROUP_NAME );
		}

	}
	/**
	 * Assign the role associated with the course to the student when they enroll in this course.
	 */
	public function ets_learndash_discord_update_course_access( $user_id, $course_id, $course_access_list, $remove ) {

		$access_token                       = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_access_token', true ) ) );
		$refresh_token                      = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_refresh_token', true ) ) );
		$ets_learndash_discord_role_mapping = json_decode( get_option( 'ets_learndash_discord_role_mapping' ), true );
		$default_role                       = sanitize_text_field( trim( get_option( 'ets_learndash_discord_default_role_id' ) ) );

		if ( is_array( $ets_learndash_discord_role_mapping ) && array_key_exists( 'learndash_course_id_' . $course_id, $ets_learndash_discord_role_mapping ) ) {
			$discord_role = sanitize_text_field( trim( $ets_learndash_discord_role_mapping[ 'learndash_course_id_' . $course_id ] ) );
			if ( $discord_role && $discord_role != 'none' ) {
				if ( $access_token && $refresh_token ) {

					if ( $remove ) {
						delete_user_meta( $user_id, '_ets_learndash_discord_role_id_for_' . $course_id, $discord_role );
						$this->delete_discord_role( $user_id, $discord_role );
					} else {
						update_user_meta( $user_id, '_ets_learndash_discord_role_id_for_' . $course_id, $discord_role );
						$this->put_discord_role_api( $user_id, $discord_role );
					}
				}
			}
		}
		if ( $access_token && $refresh_token ) {
			if ( $default_role && $default_role != 'none' && isset( $user_id ) ) {
				update_user_meta( $user_id, '_ets_learndash_discord_last_default_role', $default_role );
				$this->put_discord_role_api( $user_id, $default_role );
			} else {
				$default_role = sanitize_text_field( trim( get_user_meta( $user_id, '_ets_learndash_discord_last_default_role', true ) ) );
				$this->delete_discord_role( $user_id, $default_role );
			}
		}
	}

	/**
	 * Send DM about assignment approval
	 *
	 * @param int $assignment_id Assignment ID.
	 * @return NONE
	 */
	public function ets_learndash_discord_assignment_approved( $assignment_id ) {

		$assignment = get_post( $assignment_id );
		$user_id    = $assignment->post_author;
		$ets_learndash_discord_send_assignment_approved_dm = sanitize_text_field( trim( get_option( 'ets_learndash_discord_send_assignment_approved_dm' ) ) );

		// Send DM about assignment approval.
		if ( $ets_learndash_discord_send_assignment_approved_dm == true ) {

			as_schedule_single_action( ets_learndash_discord_get_random_timestamp( ets_learndash_discord_get_highest_last_attempt_timestamp() ), 'ets_learndash_discord_as_send_dm', array( $user_id, $assignment_id, 'assignment_approved' ), LEARNDASH_DISCORD_AS_GROUP_NAME );
		}

	}

	public function ets_learndash_discord_certification_created( $pdf, $cert_args ) {

		$user_id       = $cert_args['user_id'];
		$ceticat_title = sanitize_file_name( $cert_args['pdf_title'] );

		$upload_dir      = wp_upload_dir();
		$upload_dir_name = $upload_dir['basedir'];

		$pdf->Output( $upload_dir['basedir'] . '/' . $ceticat_title . '.pdf', 'F' );

		$certificat_link = $upload_dir['baseurl'] . '/' . $ceticat_title . '.pdf';

		as_schedule_single_action( ets_learndash_discord_get_random_timestamp( ets_learndash_discord_get_highest_last_attempt_timestamp() ), 'ets_learndash_discord_as_send_dm', array( $user_id, $certificat_link, 'course_certificate_link' ), LEARNDASH_DISCORD_AS_GROUP_NAME );

	}

	/*
	 * Login with Discord button 
	 */
	public function ets_learndash_discord_registration_form (){
		global $post;
		if ( ! is_user_logged_in() ) {
			$course_id = $post->ID;
			$current_location_url = ets_learndash_discord_get_current_screen_url();
			$default_role                       = sanitize_text_field( trim( get_option( 'ets_learndash_discord_default_role_id' ) ) );
			$ets_learndash_discord_role_mapping = json_decode( get_option( 'ets_learndash_discord_role_mapping' ), true );
			$all_roles                          = unserialize( get_option( 'ets_learndash_discord_all_roles' ) );
			$mapped_role_name                   = '';                        
			$login_with_discord_button = ''; 
                        
			if ( is_array( $all_roles ) && is_array( $ets_learndash_discord_role_mapping ) ) {


				if ( array_key_exists( 'learndash_course_id_' . $course_id, $ets_learndash_discord_role_mapping ) ) {

					$mapped_role_id = $ets_learndash_discord_role_mapping[ 'learndash_course_id_' . $course_id ];

					if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
						$mapped_role_name .= $all_roles[ $mapped_role_id ] . ', ';
					}
				}

			}                        
			$default_role_name = '';
			if ( is_array( $all_roles ) ) {
				if ( $default_role != 'none' && array_key_exists( $default_role, $all_roles ) ) {
					$default_role_name = $all_roles[ $default_role ];
				}
			}
			$login_with_discord_button .= ets_learndash_discord_roles_assigned_message( $mapped_role_name, $default_role_name, $login_with_discord_button );
                        
			echo '<a href="?action=learndash-discord-login&current-location=' . $current_location_url . '">' . esc_html( 'Login with Discord', 'learndash-discord' ) . Learndash_Discord::get_discord_logo_white() . '</a>';
			echo $login_with_discord_button;
		}            

	}

	public function ets_learndash_discord_login_with_discord (){
	
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'learndash-discord-login' ) {
			$params                    = array(
				'client_id'     => sanitize_text_field( trim( get_option( 'ets_learndash_discord_client_id' ) ) ),
				'redirect_uri'  => sanitize_text_field( trim( get_option( 'ets_learndash_discord_redirect_url' ) ) ),
				'response_type' => 'code',
				'scope'         => 'identify email connections guilds guilds.join',
			);
			$discord_authorise_api_url = LEARNDASH_DISCORD_API_URL . 'oauth2/authorize?' . http_build_query( $params );
			
			if ( isset( $_GET['current-location'] ) ) {
				setcookie( 'ets_learndash_current_location_storage', $_GET['current-location'], time() + 300, '/' );
			}
                        
			wp_redirect( $discord_authorise_api_url, 302, get_site_url() );
                        
			exit;
		}        
            
            
            
	}
}

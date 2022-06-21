<?php

/**
 * Check the plugin dependencies
 *
 * @link       https://www.expresstechsoftwares.com
 * @since      1.0.0
 *
 * @package    Learndash_Discord
 * @subpackage Learndash_Discord/includes
 */

/**
 * Check the plugin dependencies
 *
 * 
 *
 * @since      1.0.0
 * @package    Learndash_Discord
 * @subpackage Learndash_Discord/includes
 * @author     ExpressTech Softwares Solutions Pvt Ltd <contact@expresstechsoftwares.com>
 */
class Learndash_Discord_Dependencies {
    
	/**
	 * Minimum LearnDash LMS Plugin version required by this plugin
	 *
	 * @since    1.0.0
	 * @var      string    MINIMUM_LEARNDASH_VERSION
	 */
	const MINIMUM_LEARNDASH_VERSION = '3.6.0.2';
        
        /**
         * Save notices messages
         *
         * @var array the admin notices to add 
         */
	protected $notices = array();
        
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name ) {

		$this->plugin_name = $plugin_name;

	}        
        
	/**
	 * Checks LearnDash LMS Plugin is installed, activated and compatible.
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool Return true if the LearnDash LMS Plugin is installed , activated and the version is compatible.Otherwise, will return false.
	 */    
	public function check_learndash() {
        
            if ( ! self::MINIMUM_LEARNDASH_VERSION ){
                
                return true;
            }
        
            // Get learndash version 
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            $plugins_dir = dirname ( dirname( plugin_dir_path( __FILE__ ) ) );
            $learndash_path = $plugins_dir .  '/sfwd-lms/sfwd_lms.php';
            $learndash_data = get_plugin_data( $learndash_path );
            $learndash_version = $learndash_data['Version'];


            if ( in_array( 'sfwd-lms/sfwd_lms.php', apply_filters('active_plugins', get_option( 'active_plugins' ) ) ) 
                    && version_compare( $learndash_version, self::MINIMUM_LEARNDASH_VERSION , '>=' ) ){

                return true;

            }  else {

                return false;
            }

	}

	/**
	* Gets the message for display when LearnDash version is not installed , not activated or incompatible with this plugin.
	* 
	* @return string Return an informative message.
	*/    
	public function get_learndash_notice(  ) {
        
        
		return sprintf(
                        esc_html__( '%1$s,The %2$sLearnDash LMS Plugin%3$s must be active and at least version %4$s or higher. Please %5$supdate%6$s or activate LearnDash LMS Plugin', 'connect-learndash-discord-addon' )
				,'<strong>' . $this->plugin_name . ' is inactive </strong>',
                                '<a href="' . esc_url( 'https://www.learndash.com/' ) . '">', '</a>',
				self::MINIMUM_LEARNDASH_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
			);
        
    }

	/** 
	* Adds an admin notice to be displayed.
	*
	* @since 1.0.0
	*
	* @param string $slug message slug
	* @param string $class CSS classes
	* @param string $message notice message
	*/
	public function add_admin_notice( $slug, $class, $message ) {

		$this->notices[ $slug ] = array(
			'class'   => $class,
			'message' => $message
		);
	}
        
        /**
         * 
         */
	public function check_environment() {
            
		if ( ! $this->check_learndash() ){
			$this->deactivate_plugin();
			$this->add_admin_notice( 'update_learndash', 'error', $this->get_learndash_notice() );
		}
        }
        
	/** 
	* Displays any admin notices added.
	*
	* @since 1.0.0
	*/
	public function admin_notices() {
        
		foreach ( (array) $this->notices as $notice_key => $notice ) {

			echo "<div class='" . esc_attr( $notice['class'] ) . "'><p>";
			echo wp_kses( $notice['message'], array( 
                            'a' => array( 
                                'href' => array() 
                                ),
                            'strong' => array() 
                            ));
			echo "</p></div>";
		}
	}
        
	/**
	* Deactivate the plugin
	* 
	* @since 1.0.0
	*/
	protected function deactivate_plugin(){
            
            
        
		deactivate_plugins( 'learndash-discord-addon/learndash-discord.php' );
        
		if ( isset( $_GET['activate'] ) ){
			unset( $_GET['activate'] );
		}
	}

}

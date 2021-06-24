<?php
/**
 * Plugin Name: TieLabs Instagram Feed
 * Plugin URI: https://tielabs.com
 * Description: Display Instagram feeds.
 * Version: 1.0.0
 * Author: TieLabs
 * Author URI: https://tielabs.com
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

// ---
function tielabs_instagram_feed_error(){
	if( function_exists( 'sb_instagram_feed_init' ) ){
		return esc_html__( 'The TieLabs Instagram Feed plugin can\'t be used while the Smash Balloon Instagram Feed plugin is active.', 'tielabs-instagram-feed' );
	}

	return false;
}


/*
function tielabs_instagram_sb_notice(){
	$message = esc_html__( 'The TieLabs Instagram Feed plugin can\'t be used while the Smash Balloon Instagram Feed plugin is active.', 'tielabs-instagram-feed' );
	printf( '<div class="notice notice-error"><p>%s</p></div>', $message );
}

if( function_exists( 'sb_instagram_feed_init' ) ){
	add_action( 'admin_notices', 'tielabs_instagram_sb_notice' );
	return;
}
*/


// ---
final class TieLabs_Instagram_Feed{

	/**
	 * @var string
	 */
	public $version = '1.0.0';


	/**
	 * @var The single instance of the class
	 */
	protected static $_instance = null;


	/**
	 * Main TieLabs_Instagram_Feed Instance
	 *
	 * Ensures only one instance of TieLabs_Instagram_Feed is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see tielabs_instagram_feed()
	 * @return TieLabs_Instagram_Feed - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();

			self::$_instance->define_constants();
			self::$_instance->includes();
			self::$_instance->localization();

			self::$_instance->helper  = new TieLabs_Instagram_Helper();
			self::$_instance->account = new TieLabs_Instagram_Account();
			self::$_instance->api     = new TieLabs_Instagram_Api();
		}

		return self::$_instance;
	}


	/**
	 * Initialise the rest of the plugin
	 */
	private function __construct() {

	}


	/**
	 * Define Constants
	 *
	 * @since  1.0.0
	 * @return void
	 */
	private function define_constants() {

		define( 'TIELABS_INSTAGRAM_URL', plugin_dir_url( __FILE__ ) );
		define( 'TIELABS_INSTAGRAM_DIR', plugin_dir_path( __FILE__ ) );

		define( 'TIELABS_INSTAGRAM_FEED_ACCOUNT', apply_filters( 'TieLabs/Instagram_Feed/option_name', 'tie_instagram_feed_account' ) );

		//delete_option( TIELABS_INSTAGRAM_FEED_ACCOUNT );
	}


	/**
	 * Load the includes
	 *
	 * @since  1.0.0
	 * @return void
	 */
	private function includes() {

		require_once( TIELABS_INSTAGRAM_DIR .'includes/helper-class.php');
		require_once( TIELABS_INSTAGRAM_DIR .'includes/account-class.php');
		require_once( TIELABS_INSTAGRAM_DIR .'includes/api-class.php');

		if( is_admin() ) {
			require_once( TIELABS_INSTAGRAM_DIR .'includes/admin-class.php');
		}
	}


	/**
	 * Load the localization files
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function localization() {
		load_plugin_textdomain( 'tielabs-instagram', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

}


/**
 * Returns the main instance of TieLabs_Instagram_Feed to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return TieLabs_Instagram_Feed
 */
function tielabs_instagram_feed() {
	return TieLabs_Instagram_Feed::instance();
}
add_action( 'plugins_loaded', 'tielabs_instagram_feed' );

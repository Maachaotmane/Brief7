<?php
/**
 * Disable resources
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


class JANNAH_OPTIMIZATION_RESOURCES {


	/**
	 * Fire Filters and actions
	 */
	function __construct(){

		// Check if the theme is enabled
		if( ! class_exists( 'TIELABS_HELPER' ) || ! function_exists( 'jannah_theme_name' ) ){
			return;
		}

		// Dequeue the JS and CSS files
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_gutenberg' ),   99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_buddypress' ),  99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_bbpress' ),     99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_woocommerce' ), 99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_for_builder' ), 99 );
	}


	/**
	 * dequeue_gutenberg
	 */
	function dequeue_gutenberg(){

		if( is_singular() && ! TIELABS_HELPER::has_builder() ){

			$post_id = get_the_ID();

			if( function_exists( 'has_blocks' ) && has_blocks( $post_id ) ){
				$has_blocks = true;
			}
		}

		if( empty( $has_blocks ) ){
			wp_dequeue_style('wp-block-library');
			wp_dequeue_style('wp-block-library-theme');

			if( TIELABS_WOOCOMMERCE_IS_ACTIVE ){
				wp_dequeue_style('wc-block-style');
			}
		}
	}


	/**
	 * dequeue_buddypress
	 */
	function dequeue_buddypress(){

		if( ! TIELABS_BUDDYPRESS_IS_ACTIVE ){
			return;
		}

		$is_disabled = self::plugins_resources_disabled( 'buddypress' );

		if( $is_disabled ){
			wp_dequeue_style('tie-css-buddypress');
			wp_dequeue_style('bp-mentions-css');
			wp_dequeue_style('bp-group-block');
			wp_dequeue_style('bp-member-block');

			wp_dequeue_script('bp-mentions');
			wp_dequeue_script('bp-nouveau');
			wp_dequeue_script('jquery-atwho');
			wp_dequeue_script('jquery-caret');
			wp_dequeue_script('bp-widget-members');
			wp_dequeue_script('bp-jquery-query');
			wp_dequeue_script('bp-jquery-cookie');
			wp_dequeue_script('bp-jquery-scroll-to');
			wp_dequeue_script('bp_core_widget_friends-js');
			wp_dequeue_script('groups_widget_groups_list-js');
		}

		// BuddyPress Force add this file to all pages
		if( TIELABS_HELPER::has_builder() ){
			wp_dequeue_script( 'comment-reply' );
		}
	}


	/**
	 * dequeue_bbpress
	 */
	function dequeue_bbpress(){

		if( ! TIELABS_BBPRESS_IS_ACTIVE ){
			return;
		}

		$is_disabled = self::plugins_resources_disabled( 'bbpress' );

		if( $is_disabled ){
			wp_dequeue_style('tie-css-bbpress');
			wp_dequeue_script('bbpress-editor');
		}
	}


	/**
	 * dequeue_woocommerce
	 */
	function dequeue_woocommerce(){

		if( ! TIELABS_WOOCOMMERCE_IS_ACTIVE ){
			return;
		}

		$is_disabled = self::plugins_resources_disabled( 'woocommerce' );

		if( $is_disabled && ! is_store_notice_showing() ){
			wp_dequeue_script( 'woocommerce' );
			wp_dequeue_script( 'wc-add-to-cart' );
			wp_dequeue_script( 'wc-cart-fragments' );
			wp_dequeue_style( 'tie-css-woocommerce' );
			wp_dequeue_style( 'wc-block-style' );
		}
	}


	/**
	 * dequeue_for_builder
	 */
	function dequeue_for_builder(){

		// LightBox
		if( tie_get_option( 'jso_homepage_lightbox' ) && ( is_home() || is_front_page() ) && ! tie_get_option( 'footer_instagram' ) ){
			wp_dequeue_style( 'tie-css-ilightbox' );
			wp_dequeue_script( 'tie-js-ilightbox' );
		}

		// wp-embed
		if( TIELABS_HELPER::has_builder() ){
			wp_deregister_script( 'wp-embed' );
		}
	}


	/**
	 * plugins_resources_disabled
	 */
	public static function plugins_resources_disabled( $plugin ){

		$page = false;

		if( is_home() || is_front_page() ){
			$page = 'homepage';
		}
		elseif ( is_category() ) {
			$page = 'category';
		}
		elseif ( is_tag() ) {
			$page = 'tag';
		}
		elseif ( is_author() ) {
			$page = 'author';
		}
		elseif ( TIELABS_HELPER::has_builder() ) {

			$page = 'builder';

			if( $exclude_pages = tie_get_option( 'jso_exclude_'. $plugin .'_pages' ) ){

				$exclude_pages = explode( ',', $exclude_pages );
				$page_id = get_the_ID();

				if( is_array( $exclude_pages ) && in_array( $page_id, $exclude_pages ) ){
					$page = false;
				}
			}
		}
		elseif ( is_singular( 'post' ) ) {
			$page = 'post';
		}

		// --
		if( empty( $plugin ) ||  empty( $page ) ){
			return false;
		}

		// --
		return tie_get_option( 'jso_disable_'. $plugin .'_'. $page );
	}


} // class


//
add_filter( 'init', 'jannah_optimization_resources_init' );
function jannah_optimization_resources_init(){

	// This method available in v4.0.0 and above
	if( method_exists( 'TIELABS_HELPER','has_builder' ) ){
		new JANNAH_OPTIMIZATION_RESOURCES();
	}
}



/*
 * dequeue_jquery_migrate
 *
 * Dequeue the default WordPress jQuery-migrate file
 */
add_action( 'wp_default_scripts', 'jso_dequeue_jquery_migrate' );
function jso_dequeue_jquery_migrate( $scripts ){

	// Check if the theme is enabled
	if( ! class_exists( 'TIELABS_HELPER' ) || ! function_exists( 'jannah_theme_name' ) ){
		return;
	}

	if ( ! is_admin() && ! empty( $scripts->registered['jquery'] ) && tie_get_option( 'jso_dequeue_jquery_migrate' ) ) {
		$jquery_dependencies = $scripts->registered['jquery']->deps;
		$scripts->registered['jquery']->deps = array_diff( $jquery_dependencies, array( 'jquery-migrate' ) );
	}
}


/**
 * dequeue_jquery_migrate_3
 * Dequeue the jQuery-migrate 3.0.0 file added by the jQuery Updater plugin
 */
add_action( 'wp_enqueue_scripts', 'jso_dequeue_jquery_migrate_3', 99 );
function jso_dequeue_jquery_migrate_3(){

	// Check if the theme is enabled
	if( ! class_exists( 'TIELABS_HELPER' ) || ! function_exists( 'jannah_theme_name' ) ){
		return;
	}

	global $wp_scripts;

	if( ! empty( $wp_scripts->registered['jquery-migrate'] ) && $wp_scripts->registered['jquery-migrate']->ver >= 3 && tie_get_option( 'jso_dequeue_jquery_migrate' ) ){
		$jquery_dependencies = $wp_scripts->registered['jquery']->deps;
		$wp_scripts->registered['jquery']->deps = array_diff( $jquery_dependencies, array( 'jquery-migrate' ) );
	}
}

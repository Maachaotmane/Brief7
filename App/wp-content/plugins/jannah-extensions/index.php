<?php
/*
	Plugin Name: Jannah - Extensions
	Plugin URI: https://tielabs.com
	Description: Add additional features to Jannah theme: Shortcodes, Custom Sliders post type
	Author: TieLabs
	Version: 1.0.12
	Author URI: http://tielabs.com
*/


require_once( 'shortcodes/shortcodes.php' );
require_once( 'custom-sliders/custom-sliders.php' );



/*-----------------------------------------------------------------------------------*/
# Load Text Domain
/*-----------------------------------------------------------------------------------*/
add_action( 'plugins_loaded', 'jannah_extensions_init' );
function  jannah_extensions_init() {
	load_plugin_textdomain( 'jannah-extensions' , false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}



/*-----------------------------------------------------------------------------------*/
# Register main Scripts and Styles
/*-----------------------------------------------------------------------------------*/
add_action( 'admin_enqueue_scripts', 'jannah_extensions_admin_enqueue_scripts' );
function jannah_extensions_admin_enqueue_scripts() {
	wp_enqueue_style( 'jannah-extensions-admin-css', plugins_url( 'assets/admin-styles.css', __FILE__ ) );
}

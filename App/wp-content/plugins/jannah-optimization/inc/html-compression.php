<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


$jso_html_compression_run = false;


function jso_html_compression_start(){

	if( function_exists( 'tie_get_option' ) && tie_get_option( 'jso_minify_html' ) && ! current_user_can( 'switch_themes' ) ){

		// AMP
		if( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ){
			return;
		}

		// Load the Minify Library
		if( ! class_exists( 'HTML_Minify' ) ){
			require_once('libs/html-minify.php');
		}

		global $jso_html_compression_run;

		if ( ! $jso_html_compression_run ){
			$jso_html_compression_run = true;

			// "Humans TXT" plugin support
			$is_humans = ( !function_exists('is_humans') ) ? false : is_humans();

			if ( ! $is_humans && ! is_feed() && ! is_robots() ){
				ob_start('html_minify_buffer');
			}
		}
	}
}


if ( ! is_admin() && ! function_exists( 'wp_html_compression_start' ) ){

	add_action('template_redirect', 'jso_html_compression_start', -1 );

	// In case above fails (it does sometimes.. ??)
	add_action('get_header', 'jso_html_compression_start');
}

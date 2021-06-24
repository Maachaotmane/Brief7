<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


class JANNAH_OPTIMIZATION_GENERAL {

	/**
	 * Fire Filters and actions
	 */
	function __construct(){

		// Check if the theme is enabled
		if( ! class_exists( 'TIELABS_HELPER' ) || ! function_exists( 'jannah_theme_name' ) ){
			return;
		}

		add_filter( 'TieLabs/api_connect_body', array( $this, 'api_connect_body' ) );

		if( ! is_admin() ){

			// Add defer attr
			if( tie_get_option( 'jso_js_deferred' ) ){
				add_filter( 'script_loader_tag',  array( $this, 'add_defer_attribute' ), 10, 2 );
			}

			// Remove Query Strings From Static Resources
			if( tie_get_option( 'jso_remove_query_strings' ) ){
				add_filter( 'script_loader_src',  array( $this, 'remove_query_strings' ), 15 );
				add_filter( 'style_loader_src',   array( $this, 'remove_query_strings' ), 15 );
			}

			// Preload resources
			add_action( 'wp_enqueue_scripts', array( $this, 'preload_resources' ), 8 );

			// Dns prefetch
			add_action( 'wp_enqueue_scripts', array( $this, 'dns_prefetch' ), 7 );

			// Disable Google Fonts On Slow Connections
			if( tie_get_option( 'jso_disable_fonts_2g' ) ){
				add_filter( 'TieLabs/google_fonts/js_code', array( $this, 'google_fonts_disable_2g' ), 10, 2 );
			}

			// Disable Google Fonts On Mobiles
			if( tie_get_option( 'jso_disable_fonts_mobile' ) ){
				add_filter( 'TieLabs/google_fonts/js_code', array( $this, 'google_fonts_disable_mobile' ), 15, 2 );
			}

			// Emojis and Smilies
			if( tie_get_option( 'jso_disable_emoji_smilies' ) ){
				remove_action( 'wp_print_styles',            'print_emoji_styles');
				remove_action( 'wp_head',                    'print_emoji_detection_script', 7);
				remove_filter( 'the_excerpt',                'convert_smilies' );
				remove_filter( 'the_post_thumbnail_caption', 'convert_smilies' );
				remove_filter( 'the_content',                'convert_smilies', 20 );
				remove_filter( 'comment_text',               'convert_smilies', 20 );
				remove_filter( 'widget_text_content',        'convert_smilies', 20 );
			}

			// Disable XML-RPC and RSD Link
			if( tie_get_option( 'jso_disable_xml_rpc' ) ){
				add_filter( 'xmlrpc_enabled', '__return_false', 5 );
				remove_action( 'wp_head', 'rsd_link' );
			}

			// Remove wlwmanifest Link
			if( tie_get_option( 'jso_disable_wlwmanifest' ) ){
				remove_action( 'wp_head', 'wlwmanifest_link' );
			}

			// No Need For this
			remove_filter( 'the_content', 'capital_P_dangit', 11 );
			remove_filter( 'the_title',   'capital_P_dangit', 11 );
			remove_filter( 'wp_title',    'capital_P_dangit', 11 );

			// Test Mode
			if( tie_get_option( 'jso_test_mode' ) ){
				add_filter( 'TieLabs/Ad_widget/code', '__return_false', 999999 );
				add_filter( 'TieLabs/block/ad_code',  '__return_false', 999999 );
				add_filter( 'TieLabs/custom_ad_code', '__return_false', 999999 );
				add_filter( 'TieLabs/header_code',    '__return_false', 999999 );
				add_filter( 'TieLabs/body_code',      '__return_false', 999999 );
				add_filter( 'TieLabs/footer_code',    '__return_false', 999999 );
			}

			// Ajax Requests
			/* This Feature is disabled right now, it caused 403 error on some servers
			if( tie_get_option( 'jso_ajax' ) ){
				add_filter( 'TieLabs/js_main_vars', array( $this, 'ajax_file_path' ) );
			}
			*/
		}

	}


	/**
	 * api_connect_body
	 */
	function api_connect_body( $body ){
		$body['performance'] = true;
		return $body;
	}


	/**
	 * remove_query_strings
	 * Remove Query Strings From Static Resources
	 */
	function remove_query_strings( $src ){

		if( ! is_admin() && ! current_user_can( 'switch_themes' ) ){
			$src = remove_query_arg( 'ver', $src );
		}

		return $src;
	}


	/**
	 * add_defer_attribute
	 * Add Defer to the JS files
	 */
	function add_defer_attribute( $tag, $handle ) {

		if ( strpos( $handle, 'tie-') !== false && ! is_admin() ) {
			return str_replace( ' src', ' defer="defer" src', $tag );
		}

		return $tag;
	}


	/**
	 * dns_prefetch
	 * DNS prefetch for the common used domains
	 */
	function dns_prefetch() {

		echo "\n<meta http-equiv='x-dns-prefetch-control' content='on'>\n";

		$dns_domains = array(
			"//cdnjs.cloudflare.com",
			"//ajax.googleapis.com",
			"//fonts.googleapis.com",
			"//fonts.gstatic.com",
			"//s.gravatar.com",
			"//www.google-analytics.com"
		);

		foreach ( $dns_domains as $domain ) {
			if ( ! empty( $domain ) ){
				echo "<link rel='dns-prefetch' href='$domain' />\n";
			}
		}
	}


	/**
	 * preload_resources
	 */
	function preload_resources(){

		// Styles
		/*
		$min = TIELABS_STYLES::is_minified();

		$styles = apply_filters( 'TieLabs/preload_resources/styles', array(
			TIELABS_TEMPLATE_URL . '/assets/css/style'. $min .'.css',
		));

		foreach ( $styles as $style ) {
			echo "<link rel='preload' as='style' href='$style' />\n";
		}
		*/

		// Logo
		$logo = tie_logo_args();

		if( $logo['logo_type'] != 'title' ){

			$logos = array(
				'image'  => $logo['logo_img'],
			);

			if( $logo['logo_retina'] != $logo['logo_img'] ){
				$logos['retina'] = $logo['logo_retina'];
			}

			foreach ( $logos as $logo_img ) {
				if( ! empty( $logo_img ) ){
				$file_type = wp_check_filetype( $logo_img );
					if( ! empty( $ext = $file_type['ext'] ) ){
						echo "<link rel='preload' as='image' href='$logo_img' type='image/$ext'>\n";
					}
				}
			}
		}

		// Fonts
		$fonts = array();
		if( TIELABS_DB_VERSION >= '5.0.0' ){
			$fonts['tielabs-fonticon/tielabs-fonticon.woff'] = 'woff'; // The New TieLabs Font

			if( ! tie_get_option( 'jso_disable_fontawesome' ) ){
				$fonts['fontawesome/fa-solid-900.woff2']   = 'woff2';
				$fonts['fontawesome/fa-brands-400.woff2']  = 'woff2';
				$fonts['fontawesome/fa-regular-400.woff2'] = 'woff2';
			}
		}
		else{
			$fonts['tiefonticon/tiefonticon.woff'] = 'woff'; // Old Font

			if( ! tie_get_option( 'jso_disable_fontawesome' ) ){
				$fonts['fontawesome/fontawesome-webfont.woff2'] = 'woff2';
			}
		}

		$fonts = apply_filters( 'TieLabs/preload_resources/fonts', $fonts );

		foreach ( $fonts as $font => $type ) {
			echo "<link rel='preload' as='font' href='".TIELABS_TEMPLATE_URL."/assets/fonts/$font' type='font/$type' crossorigin='anonymous' />\n";
		}

		// Google Fonts Loader
		if( tie_get_option( 'jso_disable_fonts_mobile' ) && tie_is_mobile() ){
			echo '<!-- Google Fonts Disabled on Mobiles -->';
		}
		else{
			if( ! empty( $GLOBALS['tie_google_fonts'] ) ){
				echo "<link rel='preload' as='script' href='https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js'>\n";
			}
		}
	}


	/**
	 * Get the CSS file path
	 */
	function style_path( $handle = '' ){

		global $wp_styles;

		if ( is_a( $wp_styles, 'WP_Styles' ) && ! empty( $wp_styles->registered[$handle]->src ) ){
			return $wp_styles->registered[$handle];
		}

		return false;
	}


	/**
	 * google_fonts_disable_2g
	 * Disable Google Fonts On Slow Connections
	 */
	function google_fonts_disable_2g( $js_code, $fonts ){

		if( ! empty( $js_code ) ){

			$js_code = "
				var connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
				if ( typeof connection != 'undefined' && (/\slow-2g|2g/.test(connection.effectiveType))) {
					console.warn( 'Slow Connection Google Fonts Disabled' );
				}
				else{
					$js_code
				}
			";
		}

		return $js_code;
	}


	/**
	 * google_fonts_disable_mobile
	 * Disable Google Fonts On Mobiles
	 */
	function google_fonts_disable_mobile( $js_code, $fonts ){

		if( tie_is_mobile() ){
			return '';
		}

		return $js_code;
	}


	/**
	 * ajax_file_path
	 */
	function ajax_file_path( $vars ){

		if( ! empty( $vars['ajaxurl'] ) ){
			$vars['ajaxurl'] = plugins_url( 'ajax.php', __FILE__ );
		}

		return $vars;
	}

} // class


//
add_filter( 'init', 'jannah_optimization_general_init' );
function jannah_optimization_general_init(){

	// This method available in v4.0.0 and above
	if( method_exists( 'TIELABS_HELPER','has_builder' ) ){
		new JANNAH_OPTIMIZATION_GENERAL();
	}
}

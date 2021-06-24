<?php
/**
 * LazyLoad
 *
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


class JANNAH_OPTIMIZATION_LAZYLOAD {

	/**
	 * Runs on class initialization. Adds filters and actions.
	 */
	function __construct() {

		// Check if the theme is enabled
		if( ! class_exists( 'TIELABS_HELPER' ) || ! function_exists( 'jannah_theme_name' ) ){
			return;
		}

		add_filter( 'get_avatar',            array( $this, 'lazyload_avatar' ) );
		add_filter( 'the_content',           array( $this, 'lazyload_post_content' ) );
		add_action( 'enqueue_embed_scripts', array( $this, 'lazyload_embed_iframe' ) );
		add_filter( 'wp_kses_allowed_html',  array( $this, 'lazyload_allow_attrs' ), 10, 2 );
		add_filter( 'wp_calculate_image_srcset',          array( $this, 'lazyload_disable_srcset' ) );
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'lazyload_image_attributes' ), 8, 3 );

		add_filter( 'TieLabs/CSS/after_theme_color', array( $this, 'inline_css_code' ), 100 );

		// Ads Lazyload images
		add_filter( 'TieLabs/Ad_widget/code',  array( $this, 'lazyload_ads' ) );
		add_filter( 'TieLabs/Ad_widget/image', array( $this, 'lazyload_ads' ) );
		add_filter( 'TieLabs/block/ad_code',   array( $this, 'lazyload_ads' ) );
		add_filter( 'TieLabs/block/ad_image',  array( $this, 'lazyload_ads' ) );

		// TieLabs Instagaram Plugin
		add_filter( 'TieLabs/Instagram_Feed/avatar_img', array( $this, 'instagaram_lazyload' ) );
		add_filter( 'TieLabs/Instagram_Feed/media_img',  array( $this, 'instagaram_lazyload' ) );
	}



	/**
	 * Lazyload Featured images
	 */
	function is_lazyload_active(){

		// Return Early, avoid expensive checking
		if( ! tie_get_option( 'lazy_load' ) || is_admin() || is_feed() ){
			return false;
		}

		// Avoid lazyLoad in the AMP pages
		if( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ){
			return false;
		}

		// JetPack Plugin is active & the Photon option is enabled & Current images displayed in the post content
		if( TIELABS_JETPACK_IS_ACTIVE && in_array( 'photon', Jetpack::get_active_modules() ) && in_array( 'the_content', $GLOBALS['wp_current_filter'] ) ){
			return false;
		}

		// WooCommerce
		if( TIELABS_WOOCOMMERCE_IS_ACTIVE && in_array( 'woocommerce_review_before', $GLOBALS['wp_current_filter'] ) ){
			return false;
		}

		// Adminbar avatars
		if( in_array( 'admin_bar_menu', $GLOBALS['wp_current_filter'] ) ){
			return false;
		}

		// Active
		return true;
	}


	/**
	 * Lazyload Featured images
	 */
	function lazyload_image_attributes( $attr, $attachment, $size = false ) {

		if( ! $this->is_lazyload_active() ){
			return $attr;
		}

		// Get the LazyLoad placeholder image
		$blank_image = ( $size == TIELABS_THEME_SLUG.'-image-small' ) ? tie_lazyload_placeholder('small') : tie_lazyload_placeholder();

		$attr['class']   .= ' lazy-img';
		$attr['data-src'] = $attr['src'];
		$attr['src']      = $blank_image;

		if( TIELABS_DB_VERSION >= '5.0.0' ){
			$attr['loading']  = 'lazy';
		}

		return $attr;
	}


	/**
	 * Lazyload images in post content
	 */
	function lazyload_post_content( $content ){

		if( ! $this->is_lazyload_active() || ! tie_get_option( 'lazy_load_post_content' ) || ! is_singular('post') ){
			return $content;
		}

		return preg_replace_callback( '/(<\s*img[^>]+)(src\s*=\s*"[^"]+")([^>]+>)/i', array( $this, '_lazyload_post_content_preg' ), $content );
	}

	function _lazyload_post_content_preg( $img_match ){

		$site_url   = is_multisite() ? network_site_url() : get_site_url();
		$image_path = substr( $img_match[2], 5); // there is " at the end

		$site_host  = wp_parse_url( $site_url );
		$image_host = wp_parse_url( $image_path );

		if( strpos( $image_host['host'], $site_host['host'] ) !== false ) {

			if( TIELABS_DB_VERSION >= '5.0.0' ){
				return $img_match[1] . 'src="'. tie_lazyload_placeholder() . '" loading="lazy" data-src="'. $image_path . $img_match[3];
			}

			return $img_match[1] . 'src="'. tie_lazyload_placeholder() . '" data-src="'. $image_path . $img_match[3];
		}

		return $img_match[1] . 'src="'. $image_path . $img_match[3];
	}


	/**
	 * Disable srcset if LazyLoad is active
	 */
	function lazyload_disable_srcset( $sources ) {

		if( $this->is_lazyload_active() ){
			return false;
		}

		return $sources;
	}


	/**
	 * Allow the data-src in the wp_kses function
	 * WooCommerce uses the wp_kses to output the products thumbs.
	 */
	function lazyload_allow_attrs( $allowedtags, $context ){

		if( $this->is_lazyload_active() ){
			$allowedtags['img']['data-src'] = true;
			$allowedtags['img']['loading']  = true;
		}

		return $allowedtags;
	}


	/**
	 * Run the lazy load on the embed iframe
	 */
	function lazyload_embed_iframe(){

		if( ! $this->is_lazyload_active() ){
			return;
		}

		echo '
			<script>
				document.addEventListener("DOMContentLoaded", function(){
					var x = document.getElementsByClassName("lazy-img"), i;
					for (i = 0; i < x.length; i++) {
						x[i].setAttribute("src", x[i].getAttribute("data-src"));
					}
				});
			</script>
		';
	}


	/**
	 * Avatar Lazyload
	 */
	function lazyload_avatar( $avatar ){

		// Check if LazyLoad is active and the data-src didn't add before
		if( ! $this->is_lazyload_active() || strpos( $avatar, 'data-src' ) !== false ){
			return $avatar;
		}

		$blank_image = tie_lazyload_placeholder('square');

		$avatar = str_replace( '"', "'", $avatar );
		$avatar = str_replace( 'srcset=', 'data-2x=', $avatar );
		$avatar = str_replace( "src='", "src='". $blank_image ."' data-src='", $avatar );
		$avatar = str_replace( "class='", "class='lazy-img ", $avatar );
		return $avatar;
	}


	/**
	 * ads_lazyload
	 */
	function lazyload_ads( $image ){

		if( empty( $image ) ){
			return false;
		}

		if( $this->is_lazyload_active() && tie_get_option( 'lazy_load_ads' ) ){

			// Get the LazyLoad placeholder image
			$blank_image = tie_lazyload_placeholder('wide');

			if( TIELABS_DB_VERSION >= '5.0.0' ){
				$image = str_replace( 'src', 'src="'.$blank_image.'" loading="lazy" data-src', $image );
			}
			else{
				$image = str_replace( 'src', 'src="'.$blank_image.'" data-src', $image );
			}

		}

		return $image;
	}


	/**
	 * instagaram_lazyload
	 */
	function instagaram_lazyload( $image ){

		if( empty( $image ) ){
			return false;
		}

		if( $this->is_lazyload_active() ){

			// Get the LazyLoad placeholder image
			$blank_image = tie_lazyload_placeholder('square');

			$image = str_replace( 'src', 'src="'.$blank_image.'" loading="lazy" data-src', $image );
		}

		return $image;
	}


	/**
	 * inline_css_code
	 */
	function inline_css_code( $css = '' ){

		if( empty( $css ) ){
			return;
		}

		// Not active
		if( ! $this->is_lazyload_active() ){
			return $css;
		}

		// LazyLoad Image
		if( tie_get_option( 'lazy_load_img' ) ){
			$css .='
				.tie-slick-slider:not(.slick-initialized) .lazy-bg,
				.lazy-img[data-src],
				[data-lazy-bg] .post-thumb,
				[data-lazy-bg].post-thumb{
					background-image: url('. tie_get_option( 'lazy_load_img' ) .');
				}
			';
		}

		if( tie_get_option( 'lazy_load_dark_img' ) ){
			$css .='
				.dark-skin .tie-slick-slider:not(.slick-initialized) .lazy-bg,
				.dark-skin .lazy-img[data-src],
				.dark-skin [data-lazy-bg] .post-thumb,
				.dark-skin [data-lazy-bg].post-thumb{
					background-image: url('. tie_get_option( 'lazy_load_dark_img' ) .');
				}
			';
		}

		return $css;
	}

}


//
add_filter( 'init', 'jannah_optimization_lazyload_init' );
function jannah_optimization_lazyload_init(){

	// This method available in v4.0.0 and above
	if( method_exists( 'TIELABS_HELPER','has_builder' ) ){
		new JANNAH_OPTIMIZATION_LAZYLOAD();
	}
}

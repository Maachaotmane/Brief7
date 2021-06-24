<?php
/*
 * This is the child theme for BusinessFocus theme.
 *
 * (Please see https://developer.wordpress.org/themes/advanced-topics/child-themes/#how-to-create-a-child-theme)
 */

/**
 * eCommerceFocus only with PhotoFocus 1.2.1 or later.
 */

if ( version_compare( wp_get_theme()->parent()->Version, '1.2.1', '<' ) ) {
	require get_stylesheet_directory() . '/inc/back-compat.php';
	return;
}

/**
 * Enqueue Styles ans scripts.
 */
function ecommercefocus_enqueue_styles() {
    // Include parent theme CSS.
    wp_enqueue_style( 'photofocus-style', get_template_directory_uri() . '/style.css', null, date( 'Ymd-Gis', filemtime( get_template_directory() . '/style.css' ) ) );
    
    // Include child theme CSS.
    wp_enqueue_style( 'ecommercefocus-style', get_stylesheet_directory_uri() . '/style.css', array( 'photofocus-style' ), date( 'Ymd-Gis', filemtime( get_stylesheet_directory() . '/style.css' ) ) );

	// Load the rtl.
	if ( is_rtl() ) {
		wp_enqueue_style( 'photofocus-rtl', get_template_directory_uri() . '/rtl.css', array( 'photofocus-style' ), $version );
	}

	// Enqueue child block styles after parent block style.
	wp_enqueue_style( 'ecommercefocus-block-style', get_stylesheet_directory_uri() . '/assets/css/child-blocks.css', array( 'photofocus-block-style' ), date( 'Ymd-Gis', filemtime( get_stylesheet_directory() . '/assets/css/child-blocks.css' ) ) );
}
add_action( 'wp_enqueue_scripts', 'ecommercefocus_enqueue_styles' );

/**
 * Add child theme editor styles
 */
function ecommercefocus_editor_style() {
	add_editor_style( array(
			'assets/css/child-editor-style.css',
			photofocus_fonts_url(),
			get_theme_file_uri( 'assets/css/font-awesome/css/font-awesome.css' ),
		)
	);
}
add_action( 'after_setup_theme', 'ecommercefocus_editor_style', 11 );

/**
 * Enqueue editor styles for Gutenberg
 */
function ecommercefocus_block_editor_styles() {
	// Enqueue child block editor style after parent editor block css.
	wp_enqueue_style( 'ecommercefocus-block-editor-style', get_stylesheet_directory_uri() . '/assets/css/child-editor-blocks.css', array( 'photofocus-block-editor-style' ), date( 'Ymd-Gis', filemtime( get_stylesheet_directory() . '/assets/css/child-editor-blocks.css' ) ) );
}
add_action( 'enqueue_block_editor_assets', 'ecommercefocus_block_editor_styles', 11 );

/**
 * Register Google fonts Poppin for BusinessFociu
 *
 * @since BusinessFocus 1.0.0
 *
 * @return string Google fonts URL for the theme.
 */
function photofocus_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	* supported by Poppins, translate this to 'off'. Do not translate
	* into your own language.
	*/
	$poppins = _x( 'on', 'Poppins: on or off', 'ecommercefocus' );

	if ( 'off' !== $poppins ) {
		$font_families = array();

		$font_families[] = 'Poppins:200,300,400,500,600,700,400italic,700italic';
		
		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return esc_url_raw( $fonts_url );
}

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function ecommercefocus_body_classes( $classes ) {
	// Added color scheme to body class.
	$classes['color-scheme'] = 'color-scheme-ecommerce';

	return $classes;
}
add_filter( 'body_class', 'ecommercefocus_body_classes', 100 );

/**
 * Change default header text color
 */
function ecommercefocus_dark_header_default_color( $args ) {
	$args['default-image'] =  get_theme_file_uri( 'assets/images/header-image.jpg' );

	return $args;
}
add_filter( 'photofocus_custom_header_args', 'ecommercefocus_dark_header_default_color' );

/**
 * Override parent theme layout to add woocommerce layout.
 */

function photofocus_get_theme_layout() {
	$layout = '';

	if ( is_page_template( 'templates/full-width-page.php' ) ) {
		$layout = 'no-sidebar-full-width';
	} elseif ( is_page_template( 'templates/right-sidebar.php' ) ) {
		$layout = 'right-sidebar';
	} else {
		$layout = get_theme_mod( 'photofocus_default_layout', 'right-sidebar' );

		if ( is_home() || is_archive() ) {
			$layout = get_theme_mod( 'photofocus_homepage_archive_layout', 'no-sidebar-full-width' );
		}

		if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_woocommerce() || is_cart() || is_checkout() ) ) {
			$layout = get_theme_mod( 'photofocus_woocommerce_layout', 'right-sidebar' );
		}
	}

	return $layout;
}

/**
 * Override parent theme to add promotion headline section.
 */
function photofocus_sections( $selector = 'header' ) {
	get_template_part( 'template-parts/header/header', 'media' );
	get_template_part( 'template-parts/slider/display', 'slider' );
	get_template_part( 'template-parts/hero-content/content','hero' );
	get_template_part( 'template-parts/promotion-headline/post-type-promotion' );
	get_template_part( 'template-parts/woo-products-showcase/display', 'products' );
	get_template_part( 'template-parts/services/display', 'services' );
	get_template_part( 'template-parts/portfolio/display', 'portfolio' );
	get_template_part( 'template-parts/testimonial/display', 'testimonial' );
	get_template_part( 'template-parts/featured-content/display', 'featured' );
	
}

/**
 * Disable WooCommerce CSS.
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Load Promotion Headline Options
 */
require trailingslashit( get_stylesheet_directory() ) . 'inc/customizer/promotion-headline.php';

/**
 * Load Woo Commerce Recent Products Options
 */
require trailingslashit( get_stylesheet_directory() ) . 'inc/customizer/woo-recent-products.php';

/**
 * Load WooCommerce Options
 */
require trailingslashit( get_stylesheet_directory() ) . 'inc/customizer/woocommerce.php';

<?php
/**
 * Promotion Headline Options
 *
 * @package BusinessFocus
 */

/**
 * Add promotion headline options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function businessfocus_promo_head_options( $wp_customize ) {
	$wp_customize->add_section( 'photofocus_promotion_headline', array(
			'title' => esc_html__( 'Promotion Headline', 'ecommercefocus' ),
			'panel' => 'photofocus_theme_options',
		)
	);

	photofocus_register_option( $wp_customize, array(
			'name'              => 'photofocus_promo_head_visibility',
			'default'           => 'disabled',
			'sanitize_callback' => 'photofocus_sanitize_select',
			'choices'           => photofocus_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'ecommercefocus' ),
			'section'           => 'photofocus_promotion_headline',
			'type'              => 'select',
		)
	);

	/* Promotion Headline Image */
	photofocus_register_option( $wp_customize, array(
			'name'              => 'photofocus_promo_head_logo_image',
			'sanitize_callback' => 'photofocus_sanitize_image',
			'custom_control'    => 'WP_Customize_Image_Control',
			'active_callback'   => 'photofocus_is_promotion_headline_active',
			'label'             => esc_html__( 'Promotion Headline Image', 'ecommercefocus' ),
			'section'           => 'photofocus_promotion_headline',
		)
	);

	photofocus_register_option( $wp_customize, array(
			'name'              => 'photofocus_promotion_headline',
			'default'           => '0',
			'sanitize_callback' => 'photofocus_sanitize_post',
			'active_callback'   => 'photofocus_is_promotion_headline_active',
			'label'             => esc_html__( 'Page', 'ecommercefocus' ),
			'section'           => 'photofocus_promotion_headline',
			'type'              => 'dropdown-pages',
		)
	);

	photofocus_register_option( $wp_customize, array(
			'name'              => 'photofocus_promo_head_sub_title',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'photofocus_is_promotion_headline_active',
			'label'             => esc_html__( 'Sub Title', 'ecommercefocus' ),
			'section'           => 'photofocus_promotion_headline',
			'type'              => 'textarea',
		)
	);
	
}
add_action( 'customize_register', 'businessfocus_promo_head_options', 10 );

/** Active Callback Functions **/
if ( ! function_exists( 'photofocus_is_promotion_headline_active' ) ) :
	/**
	* Return true if promotion headline is active
	*
	* @since BusinessFocus 1.0
	*/
	function photofocus_is_promotion_headline_active( $control ) {
		$enable = $control->manager->get_setting( 'photofocus_promo_head_visibility' )->value();

		return photofocus_check_section( $enable );
	}
endif;

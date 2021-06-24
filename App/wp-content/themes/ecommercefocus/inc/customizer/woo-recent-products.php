<?php
/**
 * Adding support for WooCommerce Products Showcase Option
 */

if ( ! class_exists( 'WooCommerce' ) ) {
    // Bail if WooCommerce is not installed
    return;
}

/**
 * Add WooCommerce Product Showcase Options to customizer
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function ecommercefocus_woo_recent_products( $wp_customize ) {
   $wp_customize->add_section( 'photofocus_woo_recent_products', array(
        'title' => esc_html__( 'WooCommerce Recent Products', 'ecommercefocus' ),
        'panel' => 'photofocus_theme_options',
    ) );

    photofocus_register_option( $wp_customize, array(
            'name'              => 'photofocus_woo_recent_products_option',
            'default'           => 'disabled',
            'sanitize_callback' => 'photofocus_sanitize_select',
            'choices'           => photofocus_section_visibility_options(),
            'label'             => esc_html__( 'Enable on', 'ecommercefocus' ),
            'section'           => 'photofocus_woo_recent_products',
            'type'              => 'select',
        )
    );

    photofocus_register_option( $wp_customize, array(
            'name'              => 'photofocus_woo_recent_products_headline',
            'default'           => esc_html__( 'Recent Products', 'ecommercefocus' ),
            'sanitize_callback' => 'wp_kses_post',
            'label'             => esc_html__( 'Headline', 'ecommercefocus' ),
            'active_callback'   => 'photofocus_is_woo_recent_products_active',
            'section'           => 'photofocus_woo_recent_products',
            'type'              => 'text',
        )
    );

    photofocus_register_option( $wp_customize, array(
            'name'              => 'photofocus_woo_recent_products_subheadline',
            'default'           => esc_html__( 'This season\'s top sold products', 'ecommercefocus' ),
            'sanitize_callback' => 'wp_kses_post',
            'label'             => esc_html__( 'Sub headline', 'ecommercefocus' ),
            'active_callback'   => 'photofocus_is_woo_recent_products_active',
            'section'           => 'photofocus_woo_recent_products',
            'type'              => 'text',
        )
    );

    photofocus_register_option( $wp_customize, array(
            'name'              => 'photofocus_woo_recent_products_number',
            'default'           => 4,
            'sanitize_callback' => 'photofocus_sanitize_number_range',
            'active_callback'   => 'photofocus_is_woo_recent_products_active',
            'description'       => esc_html__( 'Save and refresh the page if No. of Products is changed. Set -1 to display all', 'ecommercefocus' ),
            'input_attrs'       => array(
                'style' => 'width: 50px;',
                'min'   => -1,
            ),
            'label'             => esc_html__( 'No of Products', 'ecommercefocus' ),
            'section'           => 'photofocus_woo_recent_products',
            'type'              => 'number',
        )
    );

    photofocus_register_option( $wp_customize, array(
            'name'              => 'photofocus_woo_recent_products_text',
            'sanitize_callback' => 'sanitize_text_field',
            'active_callback'   => 'photofocus_is_woo_recent_products_active',
            'label'             => esc_html__( 'Button Text', 'ecommercefocus' ),
            'section'           => 'photofocus_woo_recent_products',
            'type'              => 'text',
        )
    );

    $shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
    photofocus_register_option( $wp_customize, array(
            'name'              => 'photofocus_woo_recent_products_link',
            'default'           =>  esc_url( $shop_page_url ),
            'sanitize_callback' => 'esc_url_raw',
            'active_callback'   => 'photofocus_is_woo_recent_products_active',
            'label'             => esc_html__( 'Button Link', 'ecommercefocus' ),
            'section'           => 'photofocus_woo_recent_products',
        )
    );

    photofocus_register_option( $wp_customize, array(
            'name'              => 'photofocus_woo_recent_products_target',
            'sanitize_callback' => 'photofocus_sanitize_checkbox',
            'active_callback'   => 'photofocus_is_woo_recent_products_active',
            'label'             => esc_html__( 'Open Link in New Window/Tab', 'ecommercefocus' ),
            'section'           => 'photofocus_woo_recent_products',
            'custom_control'    => 'Photofocus_Toggle_Control',
        )
    );
}
add_action( 'customize_register', 'ecommercefocus_woo_recent_products', 10 );

/** Active Callback Functions **/
if( ! function_exists( 'photofocus_is_woo_recent_products_active' ) ) :
    /**
    * Return true if featured content is active
    *
    * @since Catch_Store Pro 1.0
    */
    function photofocus_is_woo_recent_products_active( $control ) {
        $enable = $control->manager->get_setting( 'photofocus_woo_recent_products_option' )->value();

        return ( photofocus_check_section( $enable ) );
    }
endif;

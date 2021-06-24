<?php
/**
 * Adding support for WooCommerce Plugin
 */

if ( ! class_exists( 'WooCommerce' ) ) {
    // Bail if WooCommerce is not installed
    return;
}

if ( ! function_exists( 'photofocus_woocommerce_setup' ) ) :
    /**
     * Sets up support for various WooCommerce features.
     */
    function photofocus_woocommerce_setup() {
        add_theme_support( 'woocommerce', array(
            'thumbnail_image_width'         => 480,
            'single_image_width'            => 580,
            'gallery_thumbnail_image_width' => 120,
        ) );

        if ( get_theme_mod( 'photofocus_product_gallery_zoom', 1 ) ) {
            add_theme_support('wc-product-gallery-zoom');
        }

        if ( get_theme_mod( 'photofocus_product_gallery_lightbox', 1 ) ) {
            add_theme_support('wc-product-gallery-lightbox');
        }

        if ( get_theme_mod( 'photofocus_product_gallery_slider', 1 ) ) {
            add_theme_support('wc-product-gallery-slider');
        }
    }
endif; //photofocus_woocommerce_setup
add_action( 'after_setup_theme', 'photofocus_woocommerce_setup' );

/**
 * Add WooCommerce Options to customizer
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function photofocus_woocommerce_options( $wp_customize ) {
    photofocus_register_option( $wp_customize, array(
            'name'              => 'photofocus_woocommerce_layout',
            'default'           => 'right-sidebar',
            'sanitize_callback' => 'photofocus_sanitize_select',
            'description'       => esc_html__( 'Layout for WooCommerce Pages', 'ecommercefocus' ),
            'label'             => esc_html__( 'WooCommerce Layout', 'ecommercefocus' ),
            'section'           => 'photofocus_layout_options',
            'type'              => 'radio',
            'choices'           => array(
                'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'ecommercefocus' ),
                'no-sidebar-full-width' => esc_html__( 'No Sidebar: Full Width', 'ecommercefocus' ),
            ),
        )
    );

    // WooCommerce Options
    $wp_customize->add_section( 'photofocus_woocommerce_options', array(
        'title'       => esc_html__( 'WooCommerce Options', 'ecommercefocus' ),
        'panel'       => 'photofocus_theme_options',
        'description' => esc_html__( 'Since these options are added via theme support, you will need to save and refresh the customizer to view the full effect.', 'ecommercefocus' ),
    ) );

    //WooCommerce Shop Page Subtitle Option
    photofocus_register_option( $wp_customize, array(
            'name'              => 'photofocus_shop_subtitle',
            'sanitize_callback' => 'wp_kses_post',
            'label'             => esc_html__( 'Shop Page Subtitle', 'ecommercefocus' ),
            'default'           => esc_html__( 'This is where you can add new products to your store.', 'ecommercefocus' ),
            'section'           => 'photofocus_woocommerce_options',
            'type'              => 'textarea',
        )
    );

    photofocus_register_option( $wp_customize, array(
            'name'              => 'photofocus_product_gallery_zoom',
            'default'           => 1,
            'sanitize_callback' => 'photofocus_sanitize_checkbox',
            'label'             => esc_html__( 'Product Gallery Zoom', 'ecommercefocus' ),
            'section'           => 'photofocus_woocommerce_options',
            'custom_control'    => 'PhotoFocus_Toggle_Control',
        )
    );

    photofocus_register_option( $wp_customize, array(
            'name'              => 'photofocus_product_gallery_lightbox',
            'default'           => 1,
            'sanitize_callback' => 'photofocus_sanitize_checkbox',
            'label'             => esc_html__( 'Product Gallery Lightbox', 'ecommercefocus' ),
            'section'           => 'photofocus_woocommerce_options',
            'custom_control'    => 'PhotoFocus_Toggle_Control',
        )
    );

    photofocus_register_option( $wp_customize, array(
            'name'              => 'photofocus_product_gallery_slider',
            'default'           => 1,
            'sanitize_callback' => 'photofocus_sanitize_checkbox',
            'label'             => esc_html__( 'Product Gallery Slider', 'ecommercefocus' ),
            'section'           => 'photofocus_woocommerce_options',
            'custom_control'    => 'PhotoFocus_Toggle_Control',
        )
    );

    photofocus_register_option( $wp_customize, array(
            'name'               => 'photofocus_shop_page_header_image',
            'sanitize_callback'  => 'photofocus_sanitize_checkbox',
            'label'              => esc_html__( 'Header Image on Single Product page', 'ecommercefocus' ),
            'section'            => 'header_image',
            'custom_control'    => 'PhotoFocus_Toggle_Control',
        )
    );
}
add_action( 'customize_register', 'photofocus_woocommerce_options' );

/**
 * Make Shop Page Sub Title dynamic
 */
function photofocus_woocommerce_shop_subtitle( $args ) {
    if ( is_shop() ) {
        return wp_kses_post( get_theme_mod( 'photofocus_shop_subtitle', esc_html__( 'This is where you can add new products to your store.', 'ecommercefocus' ) ) );
    }

    return $args;
}
add_filter( 'get_the_archive_description', 'photofocus_woocommerce_shop_subtitle', 20 );

/**
* woo_hide_page_title
*
* Removes the "shop" title on the main shop page
*
* @access      public
* @since       1.0
* @return      void
*/

function photofocus_woocommerce_hide_page_title() {
    if ( is_shop() && photofocus_has_header_media_text() ) {
        return false;
    }

    return true;
}
add_filter( 'woocommerce_show_page_title', 'photofocus_woocommerce_hide_page_title' );

/**
 * uses remove_action to remove the WooCommerce Wrapper and add_action to add Main Wrapper
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'photofocus_woocommerce_start' ) ) :
    function photofocus_woocommerce_start() {
    	echo '<div id="primary" class="content-area"><main role="main" class="site-main woocommerce" id="main"><div class="woocommerce-posts-wrapper">';
    }
endif; //photofocus_woocommerce_start
add_action( 'woocommerce_before_main_content', 'photofocus_woocommerce_start', 15 );

if ( ! function_exists( 'photofocus_woocommerce_end' ) ) :
    function photofocus_woocommerce_end() {
    	echo '</div><!-- .woocommerce-posts-wrapper --></main><!-- #main --></div><!-- #primary -->';
    }
endif; //photofocus_woocommerce_end
add_action( 'woocommerce_after_main_content', 'photofocus_woocommerce_end', 15 );

function photofocus_woocommerce_shorting_start() {
	echo '<div class="woocommerce-shorting-wrapper">';
}
add_action( 'woocommerce_before_shop_loop', 'photofocus_woocommerce_shorting_start', 10 );

function photofocus_woocommerce_shorting_end() {
	echo '</div><!-- .woocommerce-shorting-wrapper -->';
}
add_action( 'woocommerce_before_shop_loop', 'photofocus_woocommerce_shorting_end', 40 );

function photofocus_woocommerce_product_container_start() {
	echo '<div class="product-container">';
}
add_action( 'woocommerce_before_shop_loop_item_title', 'photofocus_woocommerce_product_container_start', 20 );

function photofocus_woocommerce_product_container_end() {
	echo '</div><!-- .product-container -->';
}
add_action( 'woocommerce_after_shop_loop_item', 'photofocus_woocommerce_product_container_end', 20 );

if ( ! function_exists( 'photofocus_myaccount_icon_link' ) ) {
    /**
     * The account callback function
     */
    function photofocus_myaccount_icon_link() {
        echo '<a class="my-account" href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '" title="' . esc_attr__( 'Go to My Account', 'ecommercefocus' ) . '"><span class="screen-reader-text">' . esc_attr__( 'My Account', 'ecommercefocus' ) . '</span>' . photofocus_get_svg( array( 'icon' => 'user' ) ) . '</a>';
    }
}

if ( ! function_exists( 'photofocus_header_cart' ) ) {
    /**
     * Display Header Cart
     *
     * @since  1.0.0
     * @uses  photofocus_is_woocommerce_activated() check if WooCommerce is activated
     * @return void
     */
    function photofocus_header_cart() {
       //account class
        if ( is_account_page() ) {
            $accountclass = 'menu-inline current-menu-item';
        } else {
            $accountclass = 'menu-inline';
        }
        //cart class
        if ( is_cart() ) {
            $cartclass = 'menu-inline site-cart current-menu-item';
        } else {
            $cartclass = 'menu-inline site-cart';
        }
        ?>

        <ul id="site-header-cart" class="site-header-cart menu">
            <li class="<?php echo esc_attr( $accountclass ); ?>">
                <?php photofocus_myaccount_icon_link(); ?>
            </li>

            <li class="<?php echo esc_attr( $cartclass ); ?>">
                <?php photofocus_cart_link(); ?>
                <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
            </li>
        </ul>
        <?php
    }
}

if ( ! function_exists( 'photofocus_cart_link' ) ) {
    /**
     * Cart Link
     * Displayed a link to the cart including the number of items present and the cart total
     *
     * @return void
     * @since  1.0.0
     */
    function photofocus_cart_link() {
        ?>
        <a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'ecommercefocus' ); ?>"><?php echo photofocus_get_svg( array( 'icon' => 'shopping-bag', 'title' => esc_html__( 'View your shopping cart', 'ecommercefocus' ) ) ); ?></a>
        <?php
    }
}

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function photofocus_woocommerce_active_body_class( $classes ) {
    $classes[] = 'woocommerce-active';

    return $classes;
}
add_filter( 'body_class', 'photofocus_woocommerce_active_body_class' );

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function photofocus_woocommerce_scripts() {
    $font_path   = WC()->plugin_url() . '/assets/fonts/';
    $inline_font = '@font-face {
            font-family: "star";
            src: url("' . $font_path . 'star.eot");
            src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
                url("' . $font_path . 'star.woff") format("woff"),
                url("' . $font_path . 'star.ttf") format("truetype"),
                url("' . $font_path . 'star.svg#star") format("svg");
            font-weight: normal;
            font-style: normal;
        }';

    wp_add_inline_style( 'photofocus-style', $inline_font );
}
add_action( 'wp_enqueue_scripts', 'photofocus_woocommerce_scripts' );

if ( ! function_exists( 'photofocus_woocommerce_product_columns_wrapper' ) ) {
    /**
     * Product columns wrapper.
     *
     * @return  void
     */
    function photofocus_woocommerce_product_columns_wrapper() {
        // Get option from Customizer=> WooCommerce=> Product Catlog=> Products per row.
        echo '<div class="wocommerce-section-content-wrapper columns-' . absint( get_option( 'woocommerce_catalog_columns', 4 ) ) . '">';
    }
}
add_action( 'woocommerce_before_shop_loop', 'photofocus_woocommerce_product_columns_wrapper', 40 );

if ( ! function_exists( 'photofocus_woocommerce_product_columns_wrapper_close' ) ) {
    /**
     * Product columns wrapper close.
     *
     * @return  void
     */
    function photofocus_woocommerce_product_columns_wrapper_close() {
        echo '</div>';
    }
}
add_action( 'woocommerce_after_shop_loop', 'photofocus_woocommerce_product_columns_wrapper_close', 40 );

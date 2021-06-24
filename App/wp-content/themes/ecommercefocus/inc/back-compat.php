<?php
/**
 * eCommerceFocus back compatibility functionality
 *
 * Prevents eCommerceFocus from running PhotoFocus version prior to 1.2.1,
 * since this theme is not meant to be backward compatible beyond that and
 * relies on many newer functions and markup changes introduced in 1.2.1.
 *
 * @package eCommerceFocus
 */

/**
 * Prevent switching to eCommerceFocus on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 * @since eCommerceFocus 1.0.1
 */
function ecommercefocus_switch_theme( $old_name ) {
	add_action( 'admin_notices', 'ecommercefocus_upgrade_notice' );
    unset( $_GET['activated'] );
	switch_theme( $old_name );
}
add_action( 'after_switch_theme', 'ecommercefocus_switch_theme' );

/**
 * Adds a message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * eCommerceFocus PhotoFocus version prior to 1.2.1.
 *
 * @since eCommerceFocus 1.0.1
 *
 * @global string $wp_version Photofocus version.
 */
function ecommercefocus_upgrade_notice() {
    ?>
    <div class="error">
        <p><?php
    	esc_html_e( 'eCommerceFocus requires at least Photofocus version 1.2.1. Please update Photofocus theme to latest version. and try again.', 'ecommercefocus' );
        ?></p>
    </div>
    <?php
}

/**
 * Prevents the Customizer from being loaded PhotoFocus version prior to 1.2.1.
 *
 * @since eCommerceFocus 1.0.1
 *
 * @global string $wp_version Photofocus version.
 */
function ecommercefocus_customize() {
	wp_die(
		esc_html__( 'eCommerceFocus requires at least Photofocus version 1.2.1. Please update Photofocus theme to latest version. and try again.', 'ecommercefocus' ),
		'',
		array(
			'back_link' => true,
		)
	);
}
add_action( 'load-customize.php', 'ecommercefocus_customize' );

/**
 * Prevents the Theme Preview from being loaded PhotoFocus version prior to 1.2.1.
 *
 * @since eCommerceFocus 1.0.1
 *
 * @global string $wp_version Photofocus version.
 */
function ecommercefocus_preview() {
	if ( isset( $_GET['preview'] ) ) {
		wp_die( esc_html__( 'eCommerceFocus requires at least Photofocus version 1.2.1. Please update Photofocus theme to latest version. and try again.', 'ecommercefocus' ) );
	}
}
add_action( 'template_redirect', 'ecommercefocus_preview' );

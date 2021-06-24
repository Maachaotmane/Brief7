<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


if( ! class_exists( 'TieLabs_Instagram_Admin' ) ) {

	class TieLabs_Instagram_Admin {

		/**
		 *
		 */
		function __construct() {

			add_action( 'init', array( $this, 'connect' ) );

			add_action( 'admin_notices',  array( $this, 'theme_support_notice' ) );
		}


		/**
		 * connect
		 *
		 */
		function connect( ){

			if( ! is_admin() || ! current_user_can( 'manage_options' ) || tielabs_instagram_feed()->helper->is_sb_active() ){
				return;
			}

			if( ! empty( $_GET['page'] ) && $_GET['page'] == 'sb-instagram-feed' ){

				if( ! empty( $_GET['access_token'] ) ){

					$account = array(
						'id'           => sanitize_text_field( $_GET['id'] ),
						'username'     => sanitize_text_field( $_GET['username'] ),
						'access_token' => tielabs_instagram_feed()->helper->clean( sanitize_text_field( $_GET['access_token'] ) ),
						'expires_on'   => (int) $_GET['expires_in'] + time(),
					);

					update_option( TIELABS_INSTAGRAM_FEED_ACCOUNT, $account );
				}

				// Redirect
				$redirect = apply_filters( 'TieLabs/Instagram_Feed/connect_redirect', admin_url('admin.php?page=tie-theme-options') );
				wp_redirect( $redirect );

				exit;
			}
		}


		/**
		 * theme_support_notice
		 */
		function theme_support_notice(){

			if( ! get_theme_support( 'TieLabs_Instagram_Feed' ) ){
				$message = esc_html__( 'Your current theme doesn\'t support The TieLabs Instagram Feed plugin, update the theme to the latest version.', 'tielabs-instagram-feed' );
				printf( '<div class="notice notice-error"><p>%s</p></div>', $message );
			}
		}

	}
}

new TieLabs_Instagram_Admin();

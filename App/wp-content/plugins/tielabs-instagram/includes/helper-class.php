<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly



if( ! class_exists( 'TieLabs_Instagram_Helper' ) ) {

	class TieLabs_Instagram_Helper {

		/**
		 * Get the Error Messages
		 */
		public function print_error( $message ){
			echo apply_filters( 'TieLabs/Instagram_Feed/error', $message );
		}


		/**
		 * Get the Error Messages
		 */
		public function get_error( $error_id = false ){

			if( ! empty( $error_id ) ){

				switch ( $error_id ) {
					case 'inactive':
						return esc_html__( 'Go to the Theme options page > Integrations, to connect your Instagram account.', 'tielabs-instagram-feed' );
						break;

					case 'expired':
						return esc_html__( 'The Instagram Access Token is expired, Go to the Theme options page > Integrations, to to refresh it.', 'tielabs-instagram-feed' );
						break;
				}
			}
		}


		/**
		 * Check if the SB plugin is active
		 */
		public function is_sb_active( $error_id = false ){

			return function_exists( 'sb_instagram_feed_init' );
		}


		/**
		 * Activate the links and mentiones in the image description
		 */
		public function links_mentions( $text = '', $html = false ){

			$text = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1&lt;a href='\\2' target='_blank'&gt;\\2&lt;/a&gt;", $text);
			$text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1&lt;a href='http://\\2' target='_blank'&gt;\\2&lt;/a&gt;", $text);
			$text = preg_replace("/@(\w+)/", "&lt;a href='http://instagram.com/\\1' target='_blank'&gt;@\\1&lt;/a&gt;", $text);
			$text = preg_replace("/#(\w+)/", "&lt;a href='http://instagram.com/explore/tags/\\1' target='_blank'&gt;#\\1&lt;/a&gt; ", $text);

			if( $html ){
				$text = htmlspecialchars_decode( $text );
			}

			return $text;
		}


		/**
		 * Clean Access Token
		 */
		public function clean( $maybe_dirty ) {

			if ( substr_count ( $maybe_dirty , '.' ) < 3 ) {
				return str_replace( '634hgdf83hjdj2', '', $maybe_dirty );
			}

			$parts = explode( '.', trim( $maybe_dirty ) );
			$last_part = $parts[2] . $parts[3];
			$cleaned = $parts[0] . '.' . base64_decode( $parts[1] ) . '.' . base64_decode( $last_part );

			return $cleaned;
		}



	}
}

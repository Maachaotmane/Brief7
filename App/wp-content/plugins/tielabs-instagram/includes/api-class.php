<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly



if( ! class_exists( 'TieLabs_Instagram_Api' ) ) {

	class TieLabs_Instagram_Api {

		private $userid;
		private $access_token;
		private $transient_id;

		/**
		 *
		 */
		function __construct() {

			$this->userid       = tielabs_instagram_feed()->account->get('id');
			$this->access_token = tielabs_instagram_feed()->account->get('access_token');
			$this->transient_id = TIELABS_INSTAGRAM_FEED_ACCOUNT.'_'.$this->userid;

			// --
			add_action( 'admin_init', array( $this, 'refresh_access_token' ) );
		}


		/**
		 * Authorize URL
		 */
		public function authorize(){
			return 'https://api.instagram.com/oauth/authorize?app_id=423965861585747&redirect_uri=https://api.smashballoon.com/instagram-basic-display-redirect.php&response_type=code&scope=user_profile,user_media&state='. admin_url('admin.php?page=sb-instagram-feed');
		}


		/**
		 * Make the connection to Instagram
		 */
		private function remote_get( $url = false ){

			$args = array(
				'timeout'    => 30,
				'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
			);

			$args = apply_filters( 'TieLabs/Instagram_Feed/Api/remote_get/args', $args, $url, $this->userid );

			$request = wp_remote_get( $url, $args );

			return $this->check_for_errors( $request );
		}


		/**
		 * Make the connection to Instagram
		 */
		public function get_data(){

			if( ! tielabs_instagram_feed()->account->is_active() ){
				return false;
			}

			// Check if we have a cached version
			if( get_transient( $this->transient_id ) !== false ){
				return get_transient( $this->transient_id );
			}

			// Check if we need to refresh the Access Token
			$this->refresh_access_token();

			// Request media
			$args = array(
				'fields'       => 'media_url,thumbnail_url,caption,id,media_type,timestamp,username,comments_count,like_count,permalink',
				'limit'        => 18,
				'access_token' => $this->access_token,
			);

			$args = apply_filters( 'TieLabs/Instagram_Feed/Api/get_data/args', $args, $this->userid );
			$url  = add_query_arg( $args, "https://graph.instagram.com/$this->userid/media" );

			$request = $this->remote_get( $url );

			// Error
			if( is_wp_error( $request ) ){
				return $request;
			}

			// ---
			$media = wp_remote_retrieve_body( $request );
			$media = json_decode( $media, true );
			$media = apply_filters( 'TieLabs/Instagram_Feed/Api/media', $media, $this->userid );

			$expiration = apply_filters( 'TieLabs/Instagram_Feed/Api/expiration', 6 * HOUR_IN_SECONDS );

			// Set the cache
			set_transient( $this->transient_id, $media['data'], $expiration );

			return $media['data'];
		}



		/**
		 * Check if the reply has error
		 */
		function check_for_errors( $response = false ){

			// Check Response for errors
			$response_code    = wp_remote_retrieve_response_code( $response );
			$response_message = wp_remote_retrieve_response_message( $response );

			if ( is_wp_error( $response ) ){
				return new WP_Error( 'http_error', $response->get_error_message() );
			}
			elseif ( ! empty( $response->errors ) && isset( $response->errors['http_request_failed'] ) ) {
				return new WP_Error( 'http_error', esc_html( current( $response->errors['http_request_failed'] ) ) );
			}
			elseif ( 200 !== $response_code ){

				// Get value of Error - contains more details
				$response = wp_remote_retrieve_body( $response );
				$response = json_decode( $response, true );

				if( ! empty( $response['error']['message'] ) ){
					return new WP_Error( $response_code, $response['error']['message'] );
				}

				if( empty( $response_message ) ) {
					return new WP_Error( $response_code, 'Connection Error' );
				}
				else{
					return new WP_Error( $response_code, $response_message );
				}
			}

			return $response;
		}


		/**
		 * Check if we need to refresh the Access Token
		 * Access Token is valid for 60 days, we will refresh it every 30 days.
		 */
		private function time_passed_threshold() {

			$expiration_time   = tielabs_instagram_feed()->account->get('expires_on');
			$refresh_threshold = $expiration_time - ( 30 * DAY_IN_SECONDS );

			if ( $refresh_threshold < time() ) {
				return true;
			}

			return false;
		}


		/**
		 * Check if we need to refresh the Access Token
		 * Access Token is valid for 60 days, we will refresh it every 30 days.
		 */
		public function refresh_access_token() {

			if( ! tielabs_instagram_feed()->account->is_active() ){
				return false;
			}

			if( ! $this->time_passed_threshold() ){
				return;
			}

			$url = add_query_arg(
				array(
					'grant_type'   => 'ig_refresh_token',
					'access_token' => $this->access_token,
				),
				'https://graph.instagram.com/refresh_access_token'
			);

			$request = $this->remote_get( $url );

			// Error
			if( is_wp_error( $request ) ){
				return $request;
			}

			// ---
			$data = wp_remote_retrieve_body( $request );
			$data = json_decode( $data, true );

			if( ! empty( $data['access_token'] ) ){
				$access_token = tielabs_instagram_feed()->helper->clean( sanitize_text_field( $data['access_token'] ) );
				$expires_on   = (int) $data['expires_in'] + time();

				tielabs_instagram_feed()->account->update( 'access_token', $access_token );
				tielabs_instagram_feed()->account->update( 'expires_on',   $expires_on );
			}
		}


	}
}

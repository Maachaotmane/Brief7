<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



if( ! class_exists( 'ARQAM_LITE_COUNTERS' )){

	class ARQAM_LITE_COUNTERS{


		/**
		 * Variables
		 */
		public $arq_transient = array();
		public $arq_options   = array();
		public $arq_data      = array();


		/**
		 * __construct
		 *
		 * Class constructor where we will call our filter and action hooks.
		 */
		function __construct(){

			// Check if the current theme is supporting this plugin
			if( ! get_theme_support( 'Arqam_Lite' ) ){
				return;
			}

			// Get Options and Transient
			$this->arq_transient = get_transient( 'arq_counters' );
			$this->arq_options   = get_option( 'arq_options', array() );
		}


		/**
		 * remote_get
		 *
		 * Get Data From API's
		 */
		static function remote_get( $url, $json = true, $args = array( 'sslverify' => false ) ){

			$get_request = preg_replace( '/\s+/', '', $url );
			$get_request = wp_remote_get( $url, $args );
			$the_request = wp_remote_retrieve_body( $get_request );

			if( $json ){
				$the_request = @json_decode( $the_request, true );
			}

			return $the_request;
		}


		/**
		 * update_count
		 *
		 * Update Options and Transient
		 */
		function update_count(){

			if( empty( $this->arq_data ) || ! is_array( $this->arq_data ) ){
				return;
			}

			// Make sure the data field is an Array
			if( empty( $this->arq_options['data'] ) || ! is_array( $this->arq_options['data'] ) ){
				$this->arq_options['data'] = array();
			}

			// Add The counters
			foreach( $this->arq_data as $item => $value ){
				$this->arq_transient[$item]       = $value;
				$this->arq_options['data'][$item] = $value;
			}

			// Update the transient and the option
			set_transient( 'arq_counters', $this->arq_transient, rand(5,20)*HOUR_IN_SECONDS );
			update_option( 'arq_options', $this->arq_options );
		}


		/**
		 * format_number
		 *
		 * Number Format Function
		 */
		function format_number( $number ){

			if( ! is_numeric( $number ) ){
				return $number;
			}

			if( $number >= 1000000 ){
				return round( ($number/1000)/1000, 1) . 'M';
			}
			elseif($number >= 100000){
				return round( $number/1000, 0) . 'k';
			}

			return number_format_i18n( $number );
		}


		/**
		 * counters_data
		 *
		 * Show the Counters data
		 */
		function counters_data(){

			$arqam_data   = array();
			$arq_data     = $this->arq_data;
			$arq_options  = $this->arq_options;

			$social_items = array(
				'rss',
				'facebook',
				'twitter',
				'youtube',
				'vimeo',
				'dribbble',
				'soundcloud',
				'behance',
				'instagram',
				'github'
			);


			// Prepare the Counters data
			foreach ( $social_items as $item ){

				// Reset the include variable
				$include = false;

				switch($item){

					// Facebook
					case 'facebook':
						if ( ! empty($arq_options['social']['facebook']['id']) ){
							$include = true;
							$text    = empty( $arq_options['social']['facebook']['text'] ) ? esc_html__('Fans', 'arqam-lite') : $arq_options['social']['facebook']['text'];
							$count   = $this->format_number( $this->facebook_count() );
							$icon    = '<span class="counter-icon fa fa-facebook"></span>';
							$url     = 'https://www.facebook.com/' . $arq_options['social']['facebook']['id'];
						}
						break;


					// Twitter
					case 'twitter':
						if ( ! empty($arq_options['social']['twitter']['id']) ){
							$include = true;
							$text    = empty( $arq_options['social']['twitter']['text'] ) ? esc_html__('Followers', 'arqam-lite') : $arq_options['social']['twitter']['text'];
							$count   = $this->format_number( $this->twitter_count() );
							$icon    = '<span class="counter-icon fa fa-twitter"></span>';
							$url     = 'https://twitter.com/' . $arq_options['social']['twitter']['id'];
						}
						break;


					// Youtube
					case 'youtube':
						if ( ! empty($arq_options['social']['youtube']['id']) ){

							$include = true;
							$text    = empty( $arq_options['social']['youtube']['text'] ) ? esc_html__('Subscribers', 'arqam-lite') : $arq_options['social']['youtube']['text'];
							$count   = $this->format_number( $this->youtube_count() );
							$icon    = '<span class="counter-icon fa fa-youtube"></span>';

							$type    = 'user';
							if (! empty($arq_options['social']['youtube']['type']) && $arq_options['social']['youtube']['type'] == 'Channel'){
								$type  = 'channel';
							}
							$url     = 'https://youtube.com/' . $type . '/' . $arq_options['social']['youtube']['id'];
						}
						break;


					// Vimeo
					case 'vimeo':
						if ( ! empty($arq_options['social']['vimeo']['id']) ){
							$include = true;
							$text    = empty( $arq_options['social']['vimeo']['text'] ) ? esc_html__('Subscribers', 'arqam-lite') : $arq_options['social']['vimeo']['text'];
							$count   = $this->format_number( $this->vimeo_count() );
							$icon    = '<span class="counter-icon fa fa-vimeo"></span>';
							$url     = 'https://vimeo.com/channels/' . $arq_options['social']['vimeo']['id'];
						}
						break;


					// Github
					case 'github':
						if ( ! empty($arq_options['social']['github']['id']) ){
							$include = true;
							$text    = empty( $arq_options['social']['github']['text'] ) ? esc_html__('Followers', 'arqam-lite') : $arq_options['social']['github']['text'];
							$count   = $this->format_number( $this->github_count() );
							$icon    = '<span class="counter-icon fa fa-github"></span>';
							$url     = 'https://github.com/' . $arq_options['social']['github']['id'];
						}
						break;


					// Dribbble
					case 'dribbble':
						if ( ! empty($arq_options['social']['dribbble']['id']) ){
							$include = true;
							$text    = empty( $arq_options['social']['dribbble']['text'] ) ? esc_html__('Followers', 'arqam-lite') : $arq_options['social']['dribbble']['text'];
							$count   = $this->format_number( $this->dribbble_count() );
							$icon    = '<span class="counter-icon fa fa-dribbble"></span>';
							$url     = 'https://dribbble.com/' . $arq_options['social']['dribbble']['id'];
						}
						break;


					// SoundCloud
					case 'soundcloud':
						if ( ! empty($arq_options['social']['soundcloud']['id'] ) && ! empty($arq_options['social']['soundcloud']['api']) ){
							$include = true;
							$text    = empty( $arq_options['social']['soundcloud']['text'] ) ? esc_html__('Followers', 'arqam-lite') : $arq_options['social']['soundcloud']['text'];
							$count   =$this->format_number( $this->soundcloud_count() );
							$icon    = '<span class="counter-icon fa fa-soundcloud"></span>';
							$url     = 'https://soundcloud.com/' . $arq_options['social']['soundcloud']['id'];
						}
						break;


					// Behance
					case 'behance':
						if ( ! empty($arq_options['social']['behance']['id']) && ! empty($arq_options['social']['behance']['api']) ){
							$include = true;
							$text    = empty( $arq_options['social']['behance']['text'] ) ? esc_html__('Followers', 'arqam-lite') : $arq_options['social']['behance']['text'];
							$count   = $this->format_number( $this->behance_count() );
							$icon    = '<span class="counter-icon fa fa-behance"></span>';
							$url     = 'https://www.behance.net/' . $arq_options['social']['behance']['id'];
						}
						break;


					// Instagram
					case 'instagram':
						if ( ! empty($arq_options['social']['instagram']['id'] ) ){
							$include = true;
							$text    = empty( $arq_options['social']['instagram']['text'] ) ? esc_html__('Followers', 'arqam-lite') : $arq_options['social']['instagram']['text'];
							$count   = $this->format_number( $this->instagram_count() );
							$icon    = '<span class="counter-icon fa fa-instagram"></span>';
							$url     = 'https://instagram.com/' . $arq_options['social']['instagram']['id'];
						}
						break;


					// Rss
					case 'rss':
						if ( ! empty($arq_options['social']['rss']['url']) ){
							$include = true;
							$text    = empty( $arq_options['social']['rss']['text'] ) ? esc_html__('Subscribers', 'arqam-lite') : $arq_options['social']['rss']['text'];
							$count   = $this->format_number( $this->rss_count() );
							$icon    = '<span class="counter-icon fa fa-feed"></span>';
							$url     = esc_url($arq_options['social']['rss']['url']);
						}
						break;

				}

				// Add to the counters Array
				if ( $include ){
					$arqam_data[ $item ] = array(
						'text'  => $text,
						'count' => $count,
						'icon'  => $icon,
						'url'   => $url,
					);
				}

			} //End Foreach


			// Update the counters cache
			$this->update_count();

			return $arqam_data;
		}


		/**
		 * update_array
		 *
		 * Update the social counters array
		 */
		function update_array( $social, $counter ){

			if( ! empty( $counter ) ){
				return $this->arq_data[ $social ] = $counter;
			}

			return 0;
		}


		/**
		 * get_cached_data
		 *
		 * Get the stored cache data
		 */
		function get_cached_data( $social ){

			// Get the cached value
			if( ! empty( $this->arq_transient[ $social ] )){
				return $this->arq_transient[ $social ];
			}

			// If the transient is empty and we already have done an API request, get the stored value to avoid making a new API request and slow the site.
			elseif( ! empty( $this->arq_data ) && ! empty( $this->arq_options['data'][ $social ] )){
				return $this->arq_options['data'][ $social ];
			}

			return false;
		}


		/**
		 * facebook_count
		 *
		 * Facebook Page Fans Counter
		 */
		function facebook_count(){

			$counter = '';

			// Get the cached data
			if( $counter = $this->get_cached_data( 'facebook' ) ){
				return $counter;
			}

			// Get Using the API
			$social_id = $this->arq_options['social']['facebook']['id'];

			if( ! empty( $social_id ) ){

				// The request URL
				$url = "https://www.facebook.com/plugins/likebox.php?href=https://facebook.com/$social_id&show_faces=true&header=false&stream=false&show_border=false&locale=en_US";

				// Due a change on Facebook this request takes 11 second to complete
				/*
				$get_request = wp_remote_get( $url,
					array(
						'timeout'    => 20,
						'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:20.0) Gecko/20100101 Firefox/20.0'
					));
				*/

				// Check if CURL is enabled
				if( ! function_exists( 'curl_version' ) ){
					return;
				}

				//---
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, $url );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
				curl_setopt( $ch, CURLOPT_MAXREDIRS, 3 );
				curl_setopt( $ch, CURLOPT_ENCODING, '' );
				curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
				curl_setopt( $ch, CURLOPT_HEADER, false );
				curl_setopt( $ch, CURLOPT_AUTOREFERER,true );
				curl_setopt( $ch, CURLOPT_HTTPHEADER, ['Accept-Language: en'] );
				curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0' );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
				$the_request = curl_exec($ch);

				//error checking
				if ($the_request === false) {
					curl_close($ch);
				}
				else{

					$pattern = '/_1drq[^>]+>(.*?)<\/a/s';
					preg_match( $pattern, $the_request, $matches );

					if ( ! empty( $matches[1] ) ) {
						$number  = strip_tags( $matches[1] );

						foreach ( str_split( $number ) as $char ) {
							if ( is_numeric( $char ) ){
								$counter .= $char;
							}
						}
					}
				}
			}

			// To update the stored data Later
			return $counter = (int) $this->update_array( 'facebook', $counter );
		}


		/**
		 * twitter_count
		 *
		 * Twitter Account Followers
		 */
		function twitter_count(){

			// Get the cached data
			if( $counter = $this->get_cached_data( 'twitter' ) ){
				return $counter;
			}

			// Get Using the API
			$social_id = $this->arq_options['social']['twitter']['id'];
			$api_token = get_option( 'arqam_TwitterToken' );

			if( ! empty( $social_id ) && ! empty( $api_token ) ){

				add_filter('https_ssl_verify', '__return_false');

				$args = array(
					'httpversion' => '1.1',
					'blocking' 		=> true,
					'timeout'     => 10,
					'headers'     => array(
						'Authorization' => "Bearer $api_token"
					)
				);

				$get_data = $this->remote_get( "https://api.twitter.com/1.1/users/show.json?screen_name=$social_id", true, $args );
				$counter  = ! empty( $get_data['followers_count'] ) ? (int) $get_data['followers_count'] : 0 ;
			}

			// To update the stored data Later
			return $counter = $this->update_array( 'twitter', $counter );
		}


		/**
		 * youtube_count
		 *
		 * Youtube Subscribers
		 */
		function youtube_count(){

			// Get the cached data
			if( $counter = $this-> get_cached_data( 'youtube' ) ){
				return $counter;
			}

			// Get Using the API
			$social_id = $this->arq_options['social']['youtube']['id'];
			$api_token = $this->arq_options['social']['youtube']['key'];

			if( ! empty( $social_id ) && ! empty( $api_token ) ){

				if( ! empty( $this->arq_options['social']['youtube']['type']) && $this->arq_options['social']['youtube']['type'] == 'Channel' ){
					$api_url = "https://www.googleapis.com/youtube/v3/channels?part=statistics&id=$social_id&key=$api_token";
				}
				else{
					$api_url = "https://www.googleapis.com/youtube/v3/channels?part=statistics&forUsername=$social_id&key=$api_token";
				}

				$get_data  = $this->remote_get( $api_url );
				$counter   = ! empty( $get_data['items'][0]['statistics']['subscriberCount'] ) ? (int) $get_data['items'][0]['statistics']['subscriberCount'] : 0 ;
			}

			// To update the stored data Later
			return $counter = $this->update_array( 'youtube', $counter );
		}


		/**
		 * vimeo_count
		 *
		 * Vimeo Subscribers
		 */
		function vimeo_count(){

			// Get the cached data
			if( $counter = $this-> get_cached_data( 'vimeo' ) ){
				return $counter;
			}

			// Get Using the API
			$social_id = $this->arq_options['social']['vimeo']['id'];

			if( ! empty( $social_id ) ){
				$get_data  = $this->remote_get( "http://vimeo.com/api/v2/channel/$social_id/info.json" );
				$counter   = ! empty( $get_data['total_subscribers'] ) ? (int) $get_data['total_subscribers'] : 0 ;
			}

			// To update the stored data Later
			return $counter = $this->update_array( 'vimeo', $counter );
		}


		/**
		 * dribbble_count
		 *
		 * Dribbble Followers
		 */
		function dribbble_count(){

			// Get the cached data
			if( $counter = $this-> get_cached_data( 'dribbble' ) ){
				return $counter;
			}

			// Get Using the API
			$social_id = $this->arq_options['social']['dribbble']['id'];
			$api_token = get_option( 'dribbble_access_token' );

			if( ! empty( $social_id ) && ! empty( $api_token ) ){
				$get_data  = $this->remote_get( "https://api.dribbble.com/v2/user/?access_token=$api_token" );
				$counter   = ! empty( $get_data['followers_count'] ) ? (int) $get_data['followers_count'] : 0 ;
			}

			// To update the stored data Later
			return $counter = $this->update_array( 'dribbble', $counter );
		}


		/**
		 * github_count
		 *
		 * Github Followers
		 */
		function github_count(){

			// Get the cached data
			if( $counter = $this-> get_cached_data( 'github' ) ){
				return $counter;
			}

			// Get Using the API
			$social_id = $this->arq_options['social']['github']['id'];

			if( ! empty( $social_id ) ){
				$get_data  = $this->remote_get( "https://api.github.com/users/$social_id" );
				$counter   = ! empty( $get_data['followers'] ) ? (int) $get_data['followers'] : 0 ;
			}

			// To update the stored data Later
			return $counter = $this->update_array( 'github', $counter );
		}


		/**
		 * soundcloud_count
		 *
		 * SoundCloud Followers
		 */
		function soundcloud_count(){

			// Get the cached data
			if( $counter = $this-> get_cached_data( 'soundcloud' ) ){
				return $counter;
			}

			// Get Using the API
			$social_id = $this->arq_options['social']['soundcloud']['id'];
			$api_token = $this->arq_options['social']['soundcloud']['api'];

			if( ! empty( $social_id ) && ! empty( $api_token ) ){
				$get_data  = $this->remote_get( "http://api.soundcloud.com/users/$social_id.json?consumer_key=$api_token" );
				$counter   = ! empty( $get_data['followers_count'] ) ? (int) $get_data['followers_count'] : 0 ;
			}

			// To update the stored data Later
			return $counter = $this->update_array( 'soundcloud', $counter );
		}


		/**
		 * behance_count
		 *
		 * Behance Followers
		 */
		function behance_count(){

			// Get the cached data
			if( $counter = $this-> get_cached_data( 'behance' ) ){
				return $counter;
			}

			// Get Using the API
			$social_id = $this->arq_options['social']['behance']['id'];
			$api_token = $this->arq_options['social']['behance']['api'];

			if( ! empty( $social_id ) && ! empty( $api_token ) ){
				$get_data  = $this->remote_get( "http://www.behance.net/v2/users/$social_id?api_key=$api_token" );
				$counter   = ! empty( $get_data['user']['stats']['followers'] ) ? (int) $get_data['user']['stats']['followers'] : 0 ;
			}

			// To update the stored data Later
			return $counter = $this->update_array( 'behance', $counter );
		}


		/**
		 * instagram_count
		 *
		 * Instagram Followers
		 */
		function instagram_count(){

			// Get the cached data
			if( $counter = $this->get_cached_data( 'instagram' ) ){
				return $counter;
			}

			$counter = ! empty( $this->arq_options['social']['instagram']['number'] ) ? $this->arq_options['social']['instagram']['number'] : 0;

			// To update the stored data Later
			return $counter = $this->update_array( 'instagram', $counter );
		}


		/**
		 * rss_count
		 *
		 * Rss Subscribers
		 */
		function rss_count(){

			// Get the cached data
			if( $counter = $this-> get_cached_data( 'rss' ) ){
				return $counter;
			}

			// Get Using the API
			if( $this->arq_options['social']['rss']['type'] == 'feedpress.it' && ! empty( $this->arq_options['social']['rss']['feedpress'] )){

				$feedpress_url = esc_url( $this->arq_options['social']['rss']['feedpress'] );
				$feedpress_url = str_replace( 'feedpress.it', 'feed.press', $feedpress_url );

				$get_data = $this->remote_get( $feedpress_url );
				$counter  = ! empty( $get_data['subscribers'] ) ? (int) $get_data['subscribers'] : 0 ;
			}
			else{
				$counter = $this->arq_options['social']['rss']['manual'] ;
			}

			// To update the stored data Later
			return $counter = $this->update_array( 'rss', $counter );
		}


	}
}

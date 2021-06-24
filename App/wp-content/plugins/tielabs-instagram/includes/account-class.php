<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly



if( ! class_exists( 'TieLabs_Instagram_Account' ) ) {

	class TieLabs_Instagram_Account {

		private $userid;
		private $username;
		private $access_token;

		/**
		 *
		 */
		function __construct() {

			// Set the global variables
			$this->userid       = $this->get('id');
			$this->username     = $this->get('username');
			$this->access_token = $this->get('access_token');
		}


		/**
		 * Get single info
		 */
		public function get( $key = false ){

			$account = get_option( TIELABS_INSTAGRAM_FEED_ACCOUNT );

			if( empty( $account ) ){
				return false;
			}

			if( ! empty( $account[ $key ] ) ){
				return $account[ $key ];
			}

			return false;
		}


		/**
		 * Update single info
		 */
		public function update( $key = false, $value = false ){

			if( empty( $key ) || empty( $value ) ){
				return;
			}

			$account = get_option( TIELABS_INSTAGRAM_FEED_ACCOUNT, array() );

			$account[ $key ] = $value;

			update_option( TIELABS_INSTAGRAM_FEED_ACCOUNT, $account );
		}


		/**
		 * Check if there is a connected account
		 */
		public function is_active(){

			$is_active = false;

			if( $this->userid && $this->access_token ){
				$is_active = true;
			}

			return apply_filters( 'TieLabs/Instagram_Feed/Account/is_active', $is_active, $this->username, $this->userid );
		}


		/**
		 * Check if the Access token is expired
		 */
		public function is_expired(){

			if( ! $this->is_active() ){
				return false;
			}

			$expires_on = $this->get('expires_on');

			if( empty( $expires_on ) || ( ! empty( $expires_on ) && $expires_on < time() ) ){
				return true;
			}

			return false;
		}


		/**
		 * Get the profile URL
		 */
		public function profile_url(){
			return apply_filters( 'TieLabs/Instagram_Feed/Account/profile_url', 'https://instagram.com/'. $this->username, $this->username, $this->userid );
		}


		/**
		 * Show the user info section
		 */
		public function user_card( $user_data = false ){

			if( empty( $user_data['name'] ) && empty( $user_data['avatar'] ) && empty( $user_data['bio'] ) ){
				return;
			}

			$out = '<div class="tie-insta-header">';

			// Avatar
			if( ! empty( $user_data['avatar'] ) ){

				$avatar_img = apply_filters( 'TieLabs/Instagram_Feed/avatar_img', '<img src="'. $user_data['avatar'] .'" alt="'. esc_attr( $this->username ) .'" width="200" height="200" />', $user_data['avatar'], $user_data, $this->username, $this->userid );

				$out .= '
					<div class="tie-insta-avatar">
						<a href="'. $this->profile_url() .'" target="_blank" rel="nofollow noopener">'. $avatar_img .'</a>
					</div>
				';
			}

			// Account Name
			if( ! empty( $user_data['name'] ) ){
				$out .= '
					<div class="tie-insta-info">
						<a href="'. $this->profile_url() .'" target="_blank" rel="nofollow noopener" class="tie-instagram-username">'. esc_attr( $user_data['name'] ) .'</a>
					</div>
				';
			}

			// Account Bio
			if( ! empty( $user_data['bio'] ) ){
				$out .= '
					<div class="tie-insta-desc">
						'. tielabs_instagram_feed()->helper->links_mentions( $user_data['bio'], true ) .'
					</div>
				';
			}

			$out .= '</div><!-- .tie-insta-header -->';

			echo apply_filters( 'TieLabs/Instagram_Feed/Account/user_card', $out, $this->username, $this->userid );
		}


		/**
		 * Show the photos
		 */
		public function display( $args ){

			// Return errors early
			if( ! $this->is_active() ){
				tielabs_instagram_feed()->helper->print_error( tielabs_instagram_feed()->helper->get_error('inactive') );
				return;
			}
			elseif( $this->is_expired() ){
				tielabs_instagram_feed()->helper->print_error( tielabs_instagram_feed()->helper->get_error('expired') );
				return;
			}

			$args = apply_filters( 'TieLabs/Instagram_Feed/Account/args', $args, $this->username, $this->userid );

			// Show User info section if it is active
			$this->user_card( $args );

			// Get the photos
			$user_data = tielabs_instagram_feed()->api->get_data();

			// Check fo errors
			if( is_wp_error( $user_data ) ){
				tielabs_instagram_feed()->helper->print_error( $user_data->get_error_message() );
				return;
			}

			// ---
			if( empty( $user_data ) || ! is_array( $user_data ) ){
				return;
			}

			$link_to = ! empty( $args['link'] )   ? $args['link']   : 'file';
			$number  = ! empty( $args['number'] ) ? $args['number'] : 6;

			// Has a lightbox?
			$class = ( $link_to == 'file' ) ? 'instagram-lightbox' : '';


			do_action( 'TieLabs/Instagram_Feed/before_media_section', $args, $this->username, $this->userid );

			?>

			<div class="tie-insta-box <?php echo $class ?>">
				<div class="tie-insta-photos">

					<?php
						$count = 0;
						foreach ( $user_data as $image ) {

							// Just in case!
							if( empty( $image['media_url'] ) ){
								return;
							}

							// Default URL
							$img_link = $image['permalink'];

							// Is video?
							$is_video = $image['media_type'] == 'VIDEO' ? true : false;

							// Thumbnail
							$thumbnail = $is_video ? $image['thumbnail_url'] : $image['media_url']; // thumbnail_url is available for Videos only

							// Misc
							$lightbox   = array();
							$photo_alt  = 'Instagram Photo';
							$photo_desc = '';

							// If the image has caption use it as title and image ALT
							if( ! empty( $image['caption'] ) ){
								$photo_alt  = esc_attr( wp_trim_words( $image['caption'], 40 ) );
								$photo_desc = $photo_alt;
								$photo_desc = tielabs_instagram_feed()->helper->links_mentions( $photo_desc );
							}

							// LightBox is not available for Videos
							if( $link_to == 'file' && ! $is_video ){

								// Set the URL to the image file
								$img_link = $thumbnail;

								// LightBox
								$lightbox[] = 'aria-label="Instagram Photo"';
								$lightbox[] = 'class="lightbox-enabled"';
								$lightbox[] = 'data-options="thumbnail: \''. $thumbnail .'\', width: 800, height: 800"';
								$lightbox[] = 'data-title="'. $photo_desc .'"';
							}
							?>

							<div class="tie-insta-post">
								<?php
									if( ! empty( $img_link ) ){
										echo '<a href="'. esc_url( $img_link ) .'" '. join( ' ', $lightbox ) .' target="_blank" rel="nofollow noopener">';
									}

									echo apply_filters( 'TieLabs/Instagram_Feed/media_img', '<img src="'. $thumbnail .'" width="320" height="320" alt="'. $photo_alt .'" />', $thumbnail, $image, $this->username, $this->userid );

									if( $is_video ){
										echo '<span class="media-video"><span class="tie-icon-video-camera"></span></span>';
									}

									if( ! empty( $img_link ) ){
										echo '</a>';
									}
								?>
							</div>
						<?php

						$count++;

						if( $count == $number ){
							break;
						}
					}

					?>
				</div>
			</div>

			<?php
			do_action( 'TieLabs/Instagram_Feed/after_media_section', $args, $this->username, $this->userid );
		}

	}
}

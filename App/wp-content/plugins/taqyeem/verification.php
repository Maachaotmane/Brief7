<?php
/**
 * Theme Validation
 *
 */


defined( 'ABSPATH' ) || exit; // Exit if accessed directly


if( ! class_exists( 'TAQYEEM_VERIFICATION' ) ){

	class TAQYEEM_VERIFICATION {

		/**
		 * Runs on class initialization. Adds filters and actions.
		 */
		function __construct() {

			add_action( 'admin_notices',         array( $this, 'live_message' ), 105 );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_notices' ), 5 );
		}


		/**
		 * Get the authorize url
		 */
		public static function api_url(){

			return add_query_arg(
				array(
					'envato_verify_purchase' => '',
					'redirect_url' => esc_url( add_query_arg( array( 'page' => 'taqyeem' ), admin_url( 'admin.php' ) )),
					'item'  => TAQYEEM_PLUGIN_ID,
					'theme' => get_stylesheet(),
					'blog'  => esc_url( home_url( '/' ) ),
				),
				'https://tielabs.com'
			);
		}

		/**
		 * Theme validation notices
		 */
		function load_notices(){

			// Disable the verification system
			if( apply_filters( 'Taqyeem/Verification/disable', false ) ){
				return;
			}

			if( isset( $_GET['tie-envato-authorize'] ) && ( isset( $_GET['item'] ) && $_GET['item'] == TAQYEEM_PLUGIN_ID ) ){

				if( isset($_GET['sucess']) && ! empty($_GET['token']) ){

					$theme_data = self::get_latest_plugin_data( '', $_GET['token'] );

					if( ! empty( $theme_data['status'] ) && $theme_data['status'] == 1 ){
						add_action( 'admin_notices', array( $this, 'success' ), 1 );
					}
					else{
						add_action( 'admin_notices', array( $this, 'error' ), 1 );
					}
				}
				elseif( isset($_GET['fail']) ){
					add_action( 'admin_notices', array( $this, 'error' ), 1 );
				}
			}

			elseif( get_option( 'tie_token_error_'.TAQYEEM_PLUGIN_ID ) ){
				add_action( 'admin_notices', array( $this, 'error' ), 1 );
			}

			elseif( ! get_option( 'tie_token_'.TAQYEEM_PLUGIN_ID ) ){
				add_action( 'admin_notices', array( $this, 'authorize_notice' ), 1 );
			}
		}

		/**
		 * Authorized Successfully
		 */
		function success(){

			self::message( array(
				'notice_id'   => 'theme_authorized',
				'title'       => esc_html__( 'Congratulations', 'taq' ),
				'message'     => esc_html__( 'Your Taqyeem License is now validated.', 'taq' ),
				'dismissible' => false,
				'class'       => 'success',
			));
		}


		/**
		 * Theme Not Authorized Yet
		 */
		public static function authorize_notice( $standard = true ){

			$notice_content = esc_html__( 'Your Taqyeem license is not validated. Click on the link below to unlock auto update and access to premium support, please note, a separate license is required for each site using the plugin.', 'taq' );

			self::message( array(
				'notice_id'   => 'theme_not_authorized',
				'message'     => $notice_content,
				'dismissible' => false,
				'class'       => 'warning',
				'standard'    => $standard,
				'button_text' => esc_html__( 'Verify Now!', 'taq' ),
				'button_url'  => self::api_url(),
				'button_class'=> 'green',
				'button_2_text'  => esc_html__( 'Buy a License', 'taq' ),
				'button_2_url'   => self::purchase_link(),
			));
		}


		/**
		 * Authorize Error
		 */
		function error(){

			$notice_content = '<p>'. esc_html__( 'Authorization Failed', 'taq' ) .'</p>';

			if( isset($_GET['error-description']) ){
				$notice_content .= '<p>'. $_GET['error-description'] .'</p>';
			}

			$error_description = self::get_latest_plugin_data( 'error' );

			if( ! empty( $error_description ) ){
				$notice_content .= '<p>'. $error_description .'</p>';
			}

			if( $error = get_option( 'tie_token_error_'.TAQYEEM_PLUGIN_ID ) ){
				$notice_content .= '<p>'. $error .'</p>';
			}

			self::message( array(
				'notice_id'     => 'theme_authorized_error',
				'title'         => esc_html__( 'ERROR', 'taq' ),
				'message'       => $notice_content,
				'dismissible'   => false,
				'class'         => 'error',
				'button_text'   => esc_html__( 'Try again', 'taq' ),
				'button_url'    => self::api_url(),
				'button_class'  => 'green',
				'button_2_text' => esc_html__( 'Buy a License', 'taq' ),
				'button_2_url'  => self::purchase_link(),
			));
		}


		/**
		 * The Message
		 */
		public static function message( $args = array() ){

			$defaults = array(
				'notice_id'      => '',
				'title'          => false,
				'img'            => false,
				'message'        => '',
				'dismissible'    => true,
				'color'          => '',
				'class'          => '',
				'standard'       => true,
				'button_text'    => '',
				'button_class'   => '',
				'button_url'     => '',
				'button_2_text'  => '',
				'button_2_class' => '',
				'button_2_url'   => '',
			);

			$args = wp_parse_args( $args, $defaults );


			if( ! empty( $args['color'] ) ){
				$args['color'] = 'background-color:'. $args['color'];
			}

			if( $args['class'] ){
				$args['class'] = 'taq-'. $args['class'];
			}

			if( $args['standard'] ){
				$args['class'] .= ' notice';
			}

			if( $args['dismissible'] ){
				$args['class'] .= ' is-dismissible';
			}

			if( ! empty( $args['button_class'] ) ){
				$args['button_class'] = 'taq-button-'. $args['button_class'];
			}

			if( ! empty( $args['button_2_class'] ) ){
				$args['button_2_class'] = 'taq-button-'. $args['button_2_class'];
			}

			?>

			<div id="<?php echo esc_attr( sanitize_key( $args['notice_id'] ) ) ?>" class="taqyeem-notice <?php echo esc_attr( $args['class'] ); ?>">

				<?php if( $args['title'] ){ ?>
					<h3 style="<?php echo esc_attr( $args['color'] ); ?>"><?php echo wp_kses_post( $args['title'] ) ?></h3>
				<?php } ?>

				<div class="taqyeem-notice-content">

					<?php
					if( ! empty( $args['img'] ) ){ ?>
						<img src="<?php echo esc_attr( $args['img'] ); ?>" class="taqyeem-notice-img" alt="">
						<?php
					}
					?>

					<?php

						if( strpos( $args['message'], '<p>' ) === false ){
							$args['message'] = '<p>'. $args['message'] .'</p>';
						}

						echo wp_kses_post( $args['message'] );

					?>

					<?php
					if( ! empty( $args['button_text'] ) ){ ?>
						<a class="taq-primary-button button button-primary <?php echo esc_attr( $args['button_class'] ) ?>" href="<?php echo esc_url( $args['button_url'] ) ?>"><?php echo esc_html( $args['button_text'] ) ?></a>
						<?php
					}
					?>

					<?php
					if( ! empty( $args['button_2_text'] ) ){ ?>
						<a class="taq-primary-button button button-primary <?php echo esc_attr( $args['button_2_class'] ) ?>" href="<?php echo esc_url( $args['button_2_url'] ) ?>"><?php echo esc_html( $args['button_2_text'] ) ?></a>
						<?php
					}
					?>

				</div>
			</div>

			<?php
		}

		/**
		 * Get theme purchase link
		 */
		public static function purchase_link( $utm_data = array() ){

			// Let's track the source of purchase
			return add_query_arg(
				wp_parse_args( $utm_data, array(
				'utm_source'   => 'plugin-panel',
				'utm_medium'   => 'link',
				'utm_campaign' => TIE_TAQYEEM,
				'utm_content'  => false
				)),
				'https://tielabs.com/buy/taqyeem'
			);
		}


		/**
		 * Live Message
		 */
		function live_message(){

			// Disable the verification system
			if( apply_filters( 'Taqyeem/Verification/disable', false ) ){
				return;
			}

			$data = self::get_latest_plugin_data( 'message' );
			if( ! $data ){
				$data = self::get_custom_plugin_data( 'message' );
			}

			if( ! empty( $data ) && is_array( $data ) && ! empty( $data['notice_id'] ) && ! self::is_dismissed( $data['notice_id'] ) ){

				$today = strtotime( date('Y-m-d') );

				// Start date
				if( ! empty( $data['start_date'] )){
					$start_date = strtotime( $data['start_date'] );

					if( $start_date > $today ){
						return false;
					}
				}

				// Expire date
				if( ! empty( $data['expire_date'] )){
					$expire_date = strtotime( $data['expire_date'] );

					if( $expire_date <= $today ){
						return false;
					}
				}

				self::message( $data );
			}
		}


		/**
		 *
		 */
		public static function get_latest_plugin_data( $key = '', $token = false, $force_update = false, $update_files = false, $revoke = false ){

			$cache_field     = 'tie-data-'.TAQYEEM_PLUGIN_ID;
			$token_key       = 'tie_token_'.TAQYEEM_PLUGIN_ID;
			$token_error_key = 'tie_token_error_'.TAQYEEM_PLUGIN_ID;
			$request_url     = 'http://tielabs.com/?envato_get_data';

			# Debug
			//delete_option( $token_key );
			//delete_transient( $cache_field );

			// Use the given $token and force update the TieLabs data from Envato
			if( $token !== false ){

				delete_option( $token_error_key );
				delete_transient( $cache_field );
				$force_update = true;
			}

			// Revoke the theme
			elseif( $revoke !== false || $force_update !== false ){

				delete_transient( $cache_field );
				delete_option( $token_error_key );
				$token = get_option( $token_key );
			}

			// Get data by the stored token
			else{
				$cached_data = get_transient( $cache_field );
				$token = get_option( $token_key );
			}

			// Get the Cached data
			if( empty( $cached_data ) && ! empty( $token ) && ! get_option( $token_error_key ) ){

				$body = array(
					'tie_token'     => $token,
					'item_id'       => TAQYEEM_PLUGIN_ID,
					'force_update'  => $force_update,
					'revoke_theme'  => $revoke,
					'blog_url'      => esc_url( home_url( '/' ) ),
					'php_version'   => phpversion(),
					'wp_version'    => get_bloginfo( 'version' ),
					'local'         => get_locale(),
					'theme_version' => TIE_Plugin_ver,
					'theme'         => get_stylesheet(),
				);

				// Prepare the remote post
				$response = wp_remote_post( $request_url, array(
					'headers' => array(
						'User-Agent' => 'Stripe Connect',
					),
					'body' => apply_filters( 'Taqyeem/api_connect_body', $body ),
					'sslverify' => false,
					//'timeout'   => 15,
				));

				// Check if it is a valid responce
				if ( is_wp_error( $response ) ){
					update_option( $token_error_key, $response->get_error_message(), false );
				}
				else{
					$cached_data = wp_remote_retrieve_body( $response );
					$cached_data = json_decode( $cached_data, true );

					if( ! empty( $cached_data['status'] ) && $cached_data['status'] == 1 ){

						delete_option( $token_error_key );

						set_transient( $cache_field, $cached_data, 24 * HOUR_IN_SECONDS );
						update_option( $token_key, $token, false );

						// Use this action to run functions after updating the theme data
					  do_action( 'TieLabs/after_taqyeem_data_update' );
					}
					else{

						if( isset( $cached_data['status'] ) && $cached_data['status'] == 0 ){
							update_option( $token_error_key, $cached_data['error'], false );

							delete_option( $token_key );
							delete_transient( $cache_field );
						}
					}
				}

			}

			// Debug
			//var_dump( $cached_data );

			// return the data
			if( empty( $cached_data ) ){
				return false;
			}

			if( ! empty( $key ) ){
				if( ! empty( $cached_data[ $key ] ) ){
					return $cached_data[ $key ];
				}

				return false;
			}

			return $cached_data;
		}


		/**
		 * Check Dismissed Notices
		 */
		public static function is_dismissed( $name ){

			$dismissed_notices = explode( ',', get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ));

			if( in_array( sanitize_key( $name ), $dismissed_notices ) ){
				return true;
			}

			return false;
		}


		/**
		 * Get Custom Live Message
		 */
		public static function get_custom_plugin_data( $key ){

			$cache_field = 'tie-data-custom-'.TAQYEEM_PLUGIN_ID;
			$request_url = 'http://tielabs.net/json/'. TAQYEEM_PLUGIN_ID .'.php';

			if( $cached_data = get_transient( $cache_field ) ){

			}
			else{
				// Prepare the remote get
				$response = wp_remote_get( $request_url, array(
					'headers' => array(
						'User-Agent' => 'Stripe Connect',
					),
					'sslverify' => false,
				));

				// Check if it is a valid responce
				if ( ! is_wp_error( $response ) ){
					$cached_data = wp_remote_retrieve_body( $response );
					$cached_data = json_decode( $cached_data, true );
				}
			}

			if( empty( $cached_data ) || ! is_array( $cached_data ) ){
				return false;
			}

			set_transient( $cache_field, $cached_data, 24 * HOUR_IN_SECONDS );

			if( ! empty( $key ) ){
				if( ! empty( $cached_data[ $key ] ) ){
					return $cached_data[ $key ];
				}

				return false;
			}

			return $cached_data;
		}


	}

	// Single instance.
	$TAQYEEM_VERIFICATION = new TAQYEEM_VERIFICATION();
}

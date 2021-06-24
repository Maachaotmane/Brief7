<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



if( ! class_exists( 'ARQAM_LITE_ADMIN' )){

	class ARQAM_LITE_ADMIN{


		public $default_docs_url = '';


		/**
		 * __construct
		 *
		 * Class constructor where we will call our filter and action hooks.
		 */
		function __construct(){

			add_action( 'admin_menu',  array( $this, 'admin_menu' ) );
			add_action( 'admin_init',  array( $this, 'api_processes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			$this->default_docs_url = 'https://tielabs.com';
		}


		/**
		 * enqueue_scripts
		 *
		 * Register main Scripts and Styles
		 */
		function enqueue_scripts(){
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'arqam_lite' ){

				wp_enqueue_style( 'arqam-lite-admin-css', plugins_url( 'assets/admin-styles.css', __FILE__ ) );
			}
		}


		/**
		 * add_admin
		 *
		 * Add Plugin's Menu
		 */
		function admin_menu(){

			add_menu_page(
				$page_title = esc_html__( 'Arqam Lite - Social Counters', 'arqam-lite' ),
				$menu_title = esc_html__( 'Arqam Lite', 'arqam-lite' ),
				$capability = 'install_plugins',
				$menu_slug  = 'arqam_lite',
				$function   = array( $this, 'plugin_options' ),
				$icon_url   = 'dashicons-heart'
			);
		}


		/**
		 * api_processes
		 *
		 * Add Plugin's Menu
		 */
		function api_processes(){

			$current_page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';

			if( isset( $_REQUEST['action'] ) && $current_page == 'arqam_lite' ){

				// Save the plugin settings
				if( 'save' == $_REQUEST['action'] ){

					check_admin_referer( 'arqam-lite-save' );

					$arq_options['social'] = $_REQUEST['social'];
					$arq_options['data']   = '';

					update_option( 'arq_options', $arq_options );
					delete_transient( 'arq_counters' );

					// Redirect to the theme options page
					wp_safe_redirect( add_query_arg( array( 'page' => 'arqam_lite', 'saved' => 'true' ), admin_url( 'admin.php' ) ) );
					exit;
				}

				// Twitter
				elseif( 'twitter' == $_REQUEST['action'] ){

					check_admin_referer( 'arqam-lite-twitter-button' );

					$consumerKey 		= $_REQUEST['app_id'];
					$consumerSecret = $_REQUEST['app_secret'];

					// preparing credentials
					$credentials  = $consumerKey . ':' . $consumerSecret;
					$data_to_send = base64_encode( $credentials );

					// http post arguments
					$args = array(
						'method'      => 'POST',
						'httpversion' => '1.1',
						'blocking' 		=> true,
						'headers' 		=> array(
							'Authorization' => 'Basic ' . $data_to_send,
							'Content-Type' 	=> 'application/x-www-form-urlencoded;charset=UTF-8'
						),
						'body' 				=> array( 'grant_type' => 'client_credentials' )
					);

					add_filter('https_ssl_verify', '__return_false');
					$response = wp_remote_post( 'https://api.twitter.com/oauth2/token', $args );

					$keys = json_decode(wp_remote_retrieve_body($response));

					// Save token to wp_options table
					if( ! empty($keys->access_token) ){
						update_option('arqam_TwitterToken', $keys->access_token);
					}

					wp_safe_redirect( add_query_arg( array( 'page' => 'arqam_lite' ), admin_url( 'admin.php' ) ) );
					exit;
				}

				// Dribbble
				elseif( 'dribbble' == $_REQUEST['action'] ){

					$dribbble_client_id     = $_REQUEST['app_id'];
					$dribbble_client_secret = $_REQUEST['app_secret'];

					$rand = substr(md5(microtime()),rand(0,26),10);
					$cur_page =  urlencode ( admin_url( 'admin.php?page=arqam_lite&service=arq-dribbble' ) );

					set_transient( 'arq_dribbble_client_id',     $dribbble_client_id, 	  60*60 );
					set_transient( 'arq_dribbble_client_secret', $dribbble_client_secret, 60*60 );

					$url = "https://dribbble.com/oauth/authorize?client_id=$dribbble_client_id&redirect_uri=$cur_page&scope=public&state=$rand";

					wp_redirect( $url );

					exit;
				}
			}
		}


		/**
		 * plugin_options
		 *
		 * Plugin Options page
		 */
		function plugin_options(){

			if( isset( $_REQUEST['service'] ) ){

				if( 'arq-dribbble' == $_REQUEST['service'] ){

					if( ! empty( $_REQUEST['code'] ) ){
						$code                   = $_REQUEST['code'];
						$cur_page               = admin_url( 'admin.php?page=arqam_lite&service=arq-dribbble' );
						$dribbble_client_id     = get_transient( 'arq_dribbble_client_id' );
						$dribbble_client_secret = get_transient( 'arq_dribbble_client_secret' );

						// http post arguments
						$args = array(
							'body'            => array(
								'client_id'     => $dribbble_client_id,
								'client_secret' => $dribbble_client_secret ,
								'redirect_uri'  => $cur_page,
								'code'          => $code,
							)
						);

						add_filter('https_ssl_verify', '__return_false');
						$response = wp_remote_post('https://dribbble.com/oauth/token', $args);
						$response = json_decode(wp_remote_retrieve_body($response) );

						if( ! empty( $response ) ){

							$access_token = $response->access_token;

							update_option( 'dribbble_access_token' , $access_token );

							delete_transient( 'arq_dribbble_client_id' );
							delete_transient( 'arq_dribbble_client_secret' );
						}

						echo "<script type='text/javascript'>window.location='". add_query_arg( array( 'page' => 'arqam_lite' ), admin_url( 'admin.php' )) ."';</script>";

						exit;
					}
					else{
						?>
						<div class="wrap">
							<h1><?php esc_html_e( 'Dribbble App info', 'arqam-lite' ) ?></h1>
							<br />
							<form method="post">
								<div id="poststuff">
									<div id="post-body" class="metabox-holder columns-2">
										<div id="post-body-content" class="arq-lite-content">
											<div class="postbox">
												<h3 class="hndle"><span><?php esc_html_e( 'Dribbble App info', 'arqam-lite' ) ?></span></h3>
												<div class="inside">
													<table class="links-table" cellpadding="0">
														<tbody>
															<tr>
																<th scope="row"><label for="app_id"><?php esc_html_e( 'App ID', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="app_id" id="client_id" value=""></td>
															</tr>
															<tr>
																<th scope="row"><label for="app_secret"><?php esc_html_e( 'App Secret', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="app_secret" id="app_secret" value=""></td>
															</tr>
														</tbody>
													</table>
													<div>
														<strong><?php esc_html_e( 'Need Help?', 'arqam-lite' ) ?></strong><p><em><?php printf( wp_kses_post( __( 'Enter Your App ID and App Secret, <a href="%s" target="_blank">Click Here</a> For More Details.', 'arqam-lite' )), apply_filters( 'arqam_lite_docs_url', $this->default_docs_url ) ) ?> </em></p></div>
													<div class="clear"></div>
												</div>
											</div> <!-- Box end /-->
										</div> <!-- Post Body COntent -->

										<div id="publishing-action">
											<input type="hidden" name="action" value="dribbble" />
											<input name="save" type="submit" class="button-large button-primary" id="publish" value="<?php esc_html_e( 'Submit', 'arqam-lite' ) ?>">
										</div>
										<div class="clear"></div>

									</div><!-- post-body /-->
								</div><!-- poststuff /-->
							</form>
						</div>
						<?php
					}
				}

				elseif( 'arq-twitter' == $_REQUEST['service'] ){
					check_admin_referer( 'arqam-lite-twitter' );
					?>

					<div class="wrap">
						<h1><?php esc_html_e( 'Twitter App info', 'arqam-lite' ) ?></h1>
						<br />
						<form method="post">
							<div id="poststuff">
								<div id="post-body" class="metabox-holder columns-2">
									<div id="post-body-content" class="arq-lite-content">
										<div class="postbox">
											<h3 class="hndle"><span><?php esc_html_e( 'Twitter App info', 'arqam-lite' ) ?></span></h3>
											<div class="inside">
												<table class="links-table" cellpadding="0">
													<tbody>
														<tr>
															<th scope="row"><label for="app_id"><?php esc_html_e( 'Consumer key:', 'arqam-lite' ) ?></label></th>
															<td><input type="text" name="app_id" id="app_id" value=""></td>
														</tr>
														<tr>
															<th scope="row"><label for="app_secret"><?php esc_html_e( 'Consumer secret:', 'arqam-lite' ) ?></label></th>
															<td><input type="text" name="app_secret" id="app_secret" value=""></td>
														</tr>
													</tbody>
												</table>
												<div>
													<strong><?php esc_html_e( 'Need Help?', 'arqam-lite' ) ?></strong><p><em><?php printf( wp_kses_post( __( 'Enter your APP Consumer key and Consumer secret, <a href="%s" target="_blank">Click Here</a> For More Details.', 'arqam-lite' )), apply_filters( 'arqam_lite_docs_url', $this->default_docs_url ) ) ?> </em></p>
												<div class="clear"></div>
											</div>
										</div> <!-- Box end /-->
									</div> <!-- Post Body COntent -->

									<div id="publishing-action">
										<input type="hidden" name="action" value="twitter" />
										<?php wp_nonce_field( 'arqam-lite-twitter-button' ); ?>
										<input name="save" type="submit" class="button-large button-primary" id="publish" value="<?php esc_html_e( 'Submit', 'arqam-lite' ) ?>">
									</div>
									<div class="clear"></div>

								</div><!-- post-body /-->
							</div><!-- poststuff /-->
						</form>
					</div>
					<?php
				}

			}

			else{
				$arq_options  = get_option( 'arq_options' );

				if ( isset($_REQUEST['saved'])){
					echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>'. esc_html__( 'Settings saved.', 'arqam-lite' ) .'</strong></p></div>';
				} ?>

				<div class="wrap">
					<h1><?php esc_html_e( 'Arqam Lite Settings', 'arqam-lite' ) ?> <a href="<?php echo esc_url( 'http://codecanyon.net/item/arqam-retina-responsive-wp-social-counter-plugin/5085289?ref=tielabs&utm_source=arqam-options&utm_medium=link&utm_campaign=arqam-lite&utm_content=need-more' ); ?>" target="_blank" class="page-title-action"><?php esc_html_e( 'Need More?', 'arqam-lite' ) ?></a> </h1>

					<?php

						if( ! get_theme_support( 'Arqam_Lite' ) ){
							echo '
								<div class="notice wp-notice notice-error error">
									<p>
										' . esc_html__( "This Theme doesn't support Arqam Lite, Please Install one of TieLabs's Themes", 'arqam-lite' ) .'
									</p>
								</div>
							';
						}

						else{ ?>

							<br />
							<form method="post">
								<div id="poststuff">
									<div id="post-body" class="columns-2">
										<div id="post-body-content" class="arq-lite-content">
											<div class="postbox">
												<h3 class="hndle"><span><?php esc_html_e( 'Facebook', 'arqam-lite' ) ?></span></h3>
												<div class="inside">
													<table class="links-table" cellpadding="0">
														<tbody>
															<tr>
																<th scope="row"><label for="social[facebook][id]"><?php esc_html_e( 'Page ID/Name', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[facebook][id]" class="code" id="social[facebook][id]" value="<?php if( ! empty($arq_options['social']['facebook']['id']) ) echo esc_attr( $arq_options['social']['facebook']['id'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[facebook][text]"><?php esc_html_e( 'Text Below The Number', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[facebook][text]" class="code" id="social[facebook][text]" value="<?php if( ! empty($arq_options['social']['facebook']['text']) ) echo esc_attr( $arq_options['social']['facebook']['text'] ) ?>"></td>
															</tr>
														</tbody>
													</table>
													<div><strong><?php esc_html_e( 'Need Help?', 'arqam-lite' ) ?></strong><p><em><?php esc_html_e( 'Enter Your Facebook Page Name.', 'arqam-lite' ); ?></em></p></div>
													<div class="clear"></div>
												</div>
											</div> <!-- Box end /-->

											<div class="postbox">
												<h3 class="hndle"><span><?php esc_html_e( 'Twitter', 'arqam-lite' ) ?></span></h3>
												<div class="inside">
													<table class="links-table" cellpadding="0">
														<tbody>
															<tr>
																<th scope="row"><label for="social[twitter][id]"><?php esc_html_e( 'Username', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[twitter][id]" class="code" id="social[twitter][id]" value="<?php if( ! empty($arq_options['social']['twitter']['id']) ) echo esc_attr( $arq_options['social']['twitter']['id'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[twitter][text]"><?php esc_html_e( 'Text Below The Number', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[twitter][text]" class="code" id="social[twitter][text]" value="<?php if( ! empty($arq_options['social']['twitter']['text']) ) echo esc_attr( $arq_options['social']['twitter']['text'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[twitter][api]"><?php esc_html_e( 'Access Token Key', 'arqam-lite' ) ?></label></th>
																<td>
																	<input type="text" style="color: #999;" name="social[twitter][api]" disabled="disabled" id="social[twitter][api]" value="<?php if( get_option( 'arqam_TwitterToken' ) ) echo esc_attr( get_option( 'arqam_TwitterToken' ) ) ?>">
																	<a class="button-large button-primary tie-get-api-key" href="<?php echo wp_nonce_url( add_query_arg( array( 'page' => 'arqam_lite', 'service' => 'arq-twitter' ), admin_url( 'admin.php' ) ), 'arqam-lite-twitter' ) ?>"><?php esc_html_e( 'Get Access Token', 'arqam-lite' ) ?></a>
																</td>
															</tr>
														</tbody>
													</table>
													<div><strong><?php esc_html_e( 'Need Help?', 'arqam-lite' ) ?></strong><p><em><?php printf( wp_kses_post( __( 'Enter Your Twitter Account Username, your APP Consumer key and Consumer secret, <a href="%s" target="_blank">Click Here</a> For More Details.', 'arqam-lite' )), apply_filters( 'arqam_lite_docs_url', $this->default_docs_url ) ) ?></em></p></div>
													<div class="clear"></div>
												</div>
											</div> <!-- Box end /-->

											<div class="postbox">
												<h3 class="hndle"><span><?php esc_html_e( 'YouTube', 'arqam-lite' ) ?></span></h3>
												<div class="inside">
													<table class="links-table" cellpadding="0">
														<tbody>
															<tr>
																<th scope="row"><label for="social[youtube][id]"><?php esc_html_e( 'Username or Channel ID', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[youtube][id]" class="code" id="social[youtube][id]" value="<?php if( ! empty($arq_options['social']['youtube']['id']) ) echo esc_attr( $arq_options['social']['youtube']['id'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[youtube][text]"><?php esc_html_e( 'Text Below The Number', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[youtube][text]" class="code" id="social[youtube][text]" value="<?php if( ! empty($arq_options['social']['youtube']['text']) ) echo esc_attr( $arq_options['social']['youtube']['text'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[youtube][key]"><?php esc_html_e( 'API Key', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[youtube][key]" class="code" id="social[youtube][key]" value="<?php if( ! empty($arq_options['social']['youtube']['key']) ) echo esc_attr( $arq_options['social']['youtube']['key'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[youtube][type]"><?php esc_html_e( 'Type', 'arqam-lite' ) ?></label></th>
																<td>
																	<select name="social[youtube][type]" id="social[youtube][type]">
																	<?php
																	$youtube_type = array('User', 'Channel');
																	foreach ( $youtube_type as $type ){ ?>
																		<option <?php if( ! empty($arq_options['social']['youtube']['type']) && $arq_options['social']['youtube']['type'] == $type ) echo'selected="selected"' ?> value="<?php echo esc_attr( $type ) ?>"><?php echo esc_html( $type ) ?></option>
																	<?php } ?>
																	</select>
																</td>
															</tr>
														</tbody>
													</table>
													<div><strong><?php esc_html_e( 'Need Help?', 'arqam-lite' ) ?></strong><p><em><?php printf( wp_kses_post( __( 'Enter Your YouTube username or Channel ID, API Key and choose User or Channel from Type menu, <a href="%s" target="_blank">Click Here</a> For More Details.', 'arqam-lite' )), apply_filters( 'arqam_lite_docs_url', $this->default_docs_url ) ) ?></em></p></div>
													<div class="clear"></div>
												</div>
											</div> <!-- Box end /-->

											<div class="postbox">
												<h3 class="hndle"><span><?php esc_html_e( 'Vimeo', 'arqam-lite' ) ?></span></h3>
												<div class="inside">
													<table class="links-table" cellpadding="0">
														<tbody>
															<tr>
																<th scope="row"><label for="social[vimeo][id]"><?php esc_html_e( 'Channel Name', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[vimeo][id]" class="code" id="social[vimeo][id]" value="<?php if( ! empty($arq_options['social']['vimeo']['id']) ) echo esc_attr( $arq_options['social']['vimeo']['id'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[vimeo][text]"><?php esc_html_e( 'Text Below The Number', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[vimeo][text]" class="code" id="social[vimeo][text]" value="<?php if( ! empty($arq_options['social']['vimeo']['text']) ) echo esc_attr( $arq_options['social']['vimeo']['text'] ) ?>"></td>
															</tr>
														</tbody>
													</table>
													<div><strong><?php esc_html_e( 'Need Help?', 'arqam-lite' ) ?></strong><p><em><?php esc_html_e( 'Enter Your Vimeo Channel Name.', 'arqam-lite' ) ?> </em></p></div>

													<div class="clear"></div>
												</div>
											</div> <!-- Box end /-->

											<div class="postbox">
												<h3 class="hndle"><span><?php esc_html_e( 'Dribbble', 'arqam-lite' ) ?></span></h3>
												<div class="inside">
													<table class="links-table" cellpadding="0">
														<tbody>
															<tr>
																<th scope="row"><label for="social[dribbble][id]"><?php esc_html_e( 'Username', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[dribbble][id]" class="code" id="social[dribbble][id]" value="<?php if( ! empty($arq_options['social']['dribbble']['id']) ) echo esc_attr( $arq_options['social']['dribbble']['id'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[dribbble][text]"><?php esc_html_e( 'Text Below The Number', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[dribbble][text]" class="code" id="social[dribbble][text]" value="<?php if( ! empty($arq_options['social']['dribbble']['text']) ) echo esc_attr( $arq_options['social']['dribbble']['text'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[dribbble][api]"><?php esc_html_e( 'Access Token Key', 'arqam-lite' ) ?></label></th>
																<td>
																	<input type="text" style="color: #999;" name="social[dribbble][api]" disabled="disabled" class="code" id="social[dribbble][api]" value="<?php if( get_option( 'dribbble_access_token' ) ) echo get_option( 'dribbble_access_token' ) ?>">
																	<a class="button-large button-primary tie-get-api-key" href="<?php echo add_query_arg( array( 'page' => 'arqam_lite', 'service' => 'arq-dribbble' ), admin_url( 'admin.php' ) ); ?>"><?php esc_html_e( 'Get Access Token', 'arqam-lite' ) ?></a>
																</td>
															</tr>
														</tbody>
													</table>
													<div><strong><?php esc_html_e( 'Need Help?', 'arqam-lite' ) ?></strong><p><em><?php printf( wp_kses_post( __( 'Enter Your Dribbble Account Username and the Access Token Key, <a href="%s" target="_blank">Click Here</a> For More Details.', 'arqam-lite' )), apply_filters( 'arqam_lite_docs_url', $this->default_docs_url ) ) ?></em></p></div>
													<div class="clear"></div>
												</div>
											</div> <!-- Box end /-->

											<div class="postbox">
												<h3 class="hndle"><span><?php esc_html_e( 'SoundCloud', 'arqam-lite' ) ?></span></h3>
												<div class="inside">
													<table class="links-table" cellpadding="0">
														<tbody>
															<tr>
																<th scope="row"><label for="social[soundcloud][id]"><?php esc_html_e( 'Username', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[soundcloud][id]" class="code" id="social[soundcloud][id]" value="<?php if( ! empty($arq_options['social']['soundcloud']['id']) ) echo esc_attr( $arq_options['social']['soundcloud']['id'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[soundcloud][text]"><?php esc_html_e( 'Text Below The Number', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[soundcloud][text]" class="code" id="social[soundcloud][text]" value="<?php if( ! empty($arq_options['social']['soundcloud']['text']) ) echo esc_attr( $arq_options['social']['soundcloud']['text'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[soundcloud][api]"><?php esc_html_e( 'API Key', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[soundcloud][api]" class="code" id="social[soundcloud][api]" value="<?php if( ! empty($arq_options['social']['soundcloud']['api']) ) echo esc_attr( $arq_options['social']['soundcloud']['api'] ) ?>"></td>
															</tr>
														</tbody>
													</table>
													<div><strong><?php esc_html_e( 'Need Help?', 'arqam-lite' ) ?></strong><p><em><?php printf( wp_kses_post( __( 'Enter Your SoundCloud Account Username and the API Key, <a href="%s" target="_blank">Click Here</a> For More Details.', 'arqam-lite' )), apply_filters( 'arqam_lite_docs_url', $this->default_docs_url ) ) ?></em></p></div>
													<div class="clear"></div>
												</div>
											</div> <!-- Box end /-->

											<div class="postbox">
												<h3 class="hndle"><span><?php esc_html_e( 'Behance', 'arqam-lite' ) ?></span></h3>
												<div class="inside">
													<table class="links-table" cellpadding="0">
														<tbody>
															<tr>
																<th scope="row"><label for="social[behance][id]"><?php esc_html_e( 'Username', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[behance][id]" class="code" id="social[behance][id]" value="<?php if( ! empty($arq_options['social']['behance']['id']) ) echo esc_attr( $arq_options['social']['behance']['id'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[behance][text]"><?php esc_html_e( 'Text Below The Number', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[behance][text]" class="code" id="social[behance][text]" value="<?php if( ! empty($arq_options['social']['behance']['text']) ) echo esc_attr( $arq_options['social']['behance']['text'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[behance][api]"><?php esc_html_e( 'API Key', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[behance][api]" class="code" id="social[behance][api]" value="<?php if( ! empty($arq_options['social']['behance']['api']) ) echo esc_attr( $arq_options['social']['behance']['api'] ) ?>"></td>
															</tr>
														</tbody>
													</table>
													<div><strong><?php esc_html_e( 'Need Help?', 'arqam-lite' ) ?></strong><p><em><?php printf( wp_kses_post( __( 'Enter Your Behance Account Username and the API Key, <a href="%s" target="_blank">Click Here</a> For More Details.', 'arqam-lite' )), apply_filters( 'arqam_lite_docs_url', $this->default_docs_url ) ) ?></em></p></div>
													<div class="clear"></div>
												</div>
											</div> <!-- Box end /-->

											<div class="postbox">
												<h3 class="hndle"><span><?php esc_html_e( 'GitHub', 'arqam-lite' ) ?></span></h3>
												<div class="inside">
													<table class="links-table" cellpadding="0">
														<tbody>
															<tr>
																<th scope="row"><label for="social[github][id]"><?php esc_html_e( 'Username', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[github][id]" class="code" id="social[github][id]" value="<?php if( ! empty($arq_options['social']['github']['id']) ) echo esc_attr( $arq_options['social']['github']['id'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[github][text]"><?php esc_html_e( 'Text Below The Number', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[github][text]" class="code" id="social[github][text]" value="<?php if( ! empty($arq_options['social']['github']['text']) ) echo esc_attr( $arq_options['social']['github']['text'] ) ?>"></td>
															</tr>
														</tbody>
													</table>
													<div><strong><?php esc_html_e( 'Need Help?', 'arqam-lite' ) ?></strong><p><em><?php esc_html_e( 'Enter Your Github Account Username.', 'arqam-lite' ) ?></em></p></div>
													<div class="clear"></div>
												</div>
											</div> <!-- Box end /-->

											<div class="postbox">
												<h3 class="hndle"><span><?php esc_html_e( 'Instagram', 'arqam-lite' ) ?></span></h3>
												<div class="inside">
													<table class="links-table" cellpadding="0">
														<tbody>
															<tr>
																<th scope="row"><label for="social[instagram][id]"><?php esc_html_e( 'Username', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[instagram][id]" class="code" id="social[instagram][id]" value="<?php if( ! empty($arq_options['social']['instagram']['id']) ) echo esc_attr( $arq_options['social']['instagram']['id'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[instagram][text]"><?php esc_html_e( 'Text Below The Number', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[instagram][text]" class="code" id="social[instagram][text]" value="<?php if( ! empty($arq_options['social']['instagram']['text']) ) echo esc_attr( $arq_options['social']['instagram']['text'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[instagram][number]"><?php esc_html_e( 'Number of Followers', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[instagram][number]" id="social[instagram][number]" value="<?php if( ! empty($arq_options['social']['instagram']['number']) ) echo esc_attr( $arq_options['social']['instagram']['number'] ) ?>"></td>
															</tr>
															<?php
															/*
																<tr>
																	<th scope="row"><label for="social[instagram][api]"><?php esc_html_e( 'Access Token Key', 'arqam-lite' ) ?></label></th>
																	<td><input type="text" name="social[instagram][api]" class="code" id="social[instagram][api]" value="<?php if( ! empty($arq_options['social']['instagram']['api']) ) echo $arq_options['social']['instagram']['api'] ?>"></td>
																</tr>
															*/
															?>
														</tbody>
													</table>
													<div>
														<strong><?php _e( 'Need Help?' , 'arqam-lite' ) ?></strong><p><em><?php _e( 'Enter Your Instagram Username and the number of followers.', 'arqam-lite' ) ?></em></p>
														<p><em><a href="https://developers.facebook.com/docs/instagram-basic-display-api/reference/user#edges" target="_blank"><?php _e( 'Instagram has removed the number of followers from their API.', 'arqam-lite' ) ?></a></em></p>
													</div>

													<div class="clear"></div>
												</div>
											</div> <!-- Box end /-->

											<div class="postbox" id="rss">
												<h3 class="hndle"><span><?php esc_html_e( 'RSS', 'arqam-lite' ) ?></span></h3>
												<div class="inside">
												<script>
												jQuery(document).ready(function(){
													var selected_item = jQuery("select[name='social[rss][type]'] option:selected").val();

													if (selected_item == 'Manual'){jQuery('#tie_rss_manual').show();}
													if (selected_item == 'feedpress.it'){jQuery('#tie_rss_feedpress').show();}

													jQuery("select[name='social[rss][type]']").change(function(){
														var selected_item = jQuery("select[name='social[rss][type]'] option:selected").val();
														if (selected_item == 'feedpress.it'){
															jQuery( '#tie_rss_manual' ).hide();
															jQuery( '#tie_rss_feedpress' ).fadeIn();
														}
														if (selected_item == 'Manual'){
															jQuery( '#tie_rss_feedpress' ).hide();
															jQuery( '#tie_rss_manual' ).fadeIn();
														}
													 });
												});</script>
													<table class="links-table" cellpadding="0">
														<tbody>
															<tr>
																<th scope="row"><label for="social[rss][url]"><?php esc_html_e( 'Feed URL', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[rss][url]" class="code" id="social[rss][url]" value="<?php if( ! empty($arq_options['social']['rss']['url']) ) echo esc_attr( $arq_options['social']['rss']['url'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[rss][text]"><?php esc_html_e( 'Text Below The Number', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[rss][text]" class="code" id="social[rss][text]" value="<?php if( ! empty($arq_options['social']['rss']['text']) ) echo esc_attr( $arq_options['social']['rss']['text'] ) ?>"></td>
															</tr>
															<tr>
																<th scope="row"><label for="social[rss][type]"><?php esc_html_e( 'Type', 'arqam-lite' ) ?></label></th>
																<td>
																	<select name="social[rss][type]" id="social[rss][type]">
																	<?php
																	$rss_type = array('feedpress.it', 'Manual');
																	foreach ( $rss_type as $type ){ ?>
																		<option <?php if( ! empty($arq_options['social']['rss']['type']) && $arq_options['social']['rss']['type'] == $type ) echo'selected="selected"' ?> value="<?php echo esc_attr( $type ) ?>"><?php echo esc_html( $type ) ?></option>
																	<?php } ?>
																	</select>
																</td>
															</tr>
															<tr id="tie_rss_feedpress">
																<th scope="row"><label for="social[rss][feedpress]"><?php esc_html_e( 'Feedpress Json file URL', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[rss][feedpress]" class="code" id="social[rss][feedpress]" value="<?php if( ! empty($arq_options['social']['rss']['feedpress']) ) echo esc_attr( $arq_options['social']['rss']['feedpress'] ) ?>"></td>
															</tr>
															<tr id="tie_rss_manual">
																<th scope="row"><label for="social[rss][manual]"><?php esc_html_e( 'Number of Subscribers', 'arqam-lite' ) ?></label></th>
																<td><input type="text" name="social[rss][manual]" class="code" id="social[rss][manual]" value="<?php if( ! empty($arq_options['social']['rss']['manual']) ) echo esc_attr( $arq_options['social']['rss']['manual'] ) ?>"></td>
															</tr>
														</tbody>
													</table>
													<div><strong><?php esc_html_e( 'Need Help?', 'arqam-lite' ) ?></strong><p><em><?php printf( wp_kses_post( __( 'Enter Your Feed URl and the Feedpress Json file URL or Number of Subscribers manually <a href="%s" target="_blank">Click Here</a> For More Details.', 'arqam-lite' )), apply_filters( 'arqam_lite_docs_url', $this->default_docs_url ) ) ?></em></p></div>
													<div class="clear"></div>
												</div>
											</div> <!-- Box end /-->

										</div> <!-- Post Body COntent -->

										<div id="postbox-container-1" class="postbox-container">
											<a href="https://tielabs.com/buy/arqam?utm_source=settings&utm_medium=link&utm_campaign=arqam-lite" target="_blank">
												<img style="max-width:100%;" src="<?php echo plugins_url( 'assets/images/get-arqam.png', __FILE__ ) ?>" alt="" />
											</a>
												<div class="inside" style="background-color: #E8FBFF; border:1px solid #43D1EC; padding:10px; margin-bottom:15px;">
													<strong><?php esc_html_e( 'Need More?', 'arqam-lite' ) ?></strong>
													<p>
														<?php esc_html_e( 'Purchase the full version of Arqam plugin to get all following features', 'arqam-lite' ) ?>
														<ul style="list-style-type: disc;list-style-position: inside;">
															<li><?php esc_html_e( 'Drag an Drop feature to sort icons as you wish !', 'arqam-lite' ) ?></li>
															<li><?php esc_html_e( 'Option To set the Cache time to reduce load time and API calls.', 'arqam-lite' ) ?></li>
															<li><?php esc_html_e( 'More Layout options.', 'arqam-lite' ) ?></li>
															<li><strong><?php esc_html_e( 'More Social Networks', 'arqam-lite' ) ?></strong>

																<ol>
																	<li><?php esc_html_e( 'Goodreads', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'Mixcloud', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'Twitch', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'Pinterest', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'LinkedIn', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'Tumblr', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'Flickr', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'Foursquare', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( '500px', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'Vk', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'MailChimp List', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'Steam', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'myMail plugin list', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'Mailpoet plugin List', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'Members', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'Posts Number', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'Comments Number', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'bbPress topics, replies and forums counters', 'arqam-lite' ) ?></li>
																	<li><?php esc_html_e( 'BuddyPress groups counter', 'arqam-lite' ) ?></li>
																</ol>
															</li>

														</ul>
													</p>
													<div class="clear"></div>
												</div>


											<div class="postbox">
												<h3 class="hndle"><span><?php esc_html_e( 'Save Changes', 'arqam-lite' ) ?></span></h3>
												<div class="inside">

													<div id="publishing-action">
														<?php wp_nonce_field( 'arqam-lite-save' ); ?>
														<input type="hidden" name="action" value="save" />
														<input name="save" type="submit" class="button-large button-primary" id="publish" value="<?php esc_html_e( 'Save Changes', 'arqam-lite' ) ?>">
													</div>
													<div class="clear"></div>
												</div>
											</div>
										</div><!-- postbox-container /-->
									</div><!-- post-body /-->

								</div><!-- poststuff /-->
							</form>
						</div>
					<?php
				}
			}
		}
	}

	new ARQAM_LITE_ADMIN();
}


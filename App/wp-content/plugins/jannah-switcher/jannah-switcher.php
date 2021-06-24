<?php
/*
	Plugin Name:  Jannah Switcher
	Plugin URI:
	Description:  Switch your current posts to Jannah Theme
	Version:      1.0.4
	Author:       TieLabs
	Author URI:   https://tielabs.com/
*/



/**
 * Switch Posts
 *
 * @since 1.0
 */
class JANNAH_SWITCHER_CLASS {


	/**
	 * Register ID of management page
	 *
	 * @var
	 * @since 1.0
	 */
	var $menu_id;



	/**
	 * User capability
	 *
	 * @access public
	 * @since 1.0
	 */
	public $capability;



	/**
	 * Themes Array
	 *
	 * @access public
	 * @since 1.0
	 */
	public $themes;



	/**
	 * Plugin initialization
	 *
	 * @access public
	 * @since 1.0
	 */
	function __construct() {

		load_plugin_textdomain( 'jannah_switcher', false );

		add_action( 'jannah_switcher_content',     array( &$this, 'switcher_interface' ) );
		add_action( 'admin_enqueue_scripts',       array( &$this, 'admin_enqueues' ) );
		add_action( 'wp_ajax_jannah_switch_post',  array( &$this, 'ajax_process_post' ) );

		$GLOBALS['switched'] = 0;

		# -----
		$this->menu_id = 'tietheme_page_tie-posts-switcher';

		# Allow people to change what capability is required to use this plugin
		$this->capability = 'manage_options';

		$tielabs_themes = array(

			'sahifa' => array(
				'title' => 'Sahifa - TieLabs',
				'file'  => 'tielabs'
			),

			'jarida' => array(
				'title' => 'Jarida - TieLabs',
				'file'  => 'tielabs'
			),
		);


		# Themes List
		$this->themes = array(

			'newspaper' => array(
				'title' => 'Newspaper - tagDiv',
				'file'  => 'tagdiv'
			),

			'newsmag' => array(
				'title' => 'Newsmag - tagDiv',
				'file'  => 'tagdiv'
			),

			'goodnews' => array(
				'title' => 'Goodnews - Momizat',
				'file'  => 'momizat'
			),

			'multinews' => array(
				'title' => 'Multi News - Momizat',
				'file'  => 'momizat'
			),

			'publisher' => array(
				'title' => 'Publisher - BetterStudio',
				'file'  => 'publisher'
			),

			'simplemag' => array(
				'title' => 'SimpleMag - ThemesIndep',
				'file'  => 'simplemag'
			),

			'valenti' => array(
				'title' => 'Valenti - Codetipi',
				'file'  => 'codetipi'
			),

			'thevoux' => array(
				'title' => 'The Voux - fuelthemes',
				'file'  => 'fuelthemes'
			),

			'goodlife' => array(
				'title' => 'GoodLife - fuelthemes',
				'file'  => 'fuelthemes'
			),

			'flex-mag' => array(
				'title' => 'Flex Mag - MVPThemes',
				'file'  => 'mvpthemes'
			),

			'click-mag' => array(
				'title' => 'Click Mag - MVPThemes',
				'file'  => 'mvpthemes'
			),

			'braxton' => array(
				'title' => 'Braxton - MVPThemes',
				'file'  => 'mvpthemes'
			),

			'maxmag' => array(
				'title' => 'Max Mag - MVPThemes',
				'file'  => 'mvpthemes'
			),

			'topnews' => array(
				'title' => 'Top News - MVPThemes',
				'file'  => 'mvpthemes'
			),

			'hottopix' => array(
				'title' => 'Hot Topix - MVPThemes',
				'file'  => 'mvpthemes'
			),
		);

		ksort( $this->themes );


		$this->themes = $tielabs_themes + $this->themes;


	}



	/**
	 * Enqueue the needed Javascript and CSS
	 *
	 * @param string $hook_suffix
	 * @access public
	 * @since 1.0
	 */
	function admin_enqueues( $hook_suffix ) {

		if ( $hook_suffix != $this->menu_id ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-progressbar', plugins_url( 'assets/jquery-ui/jquery.ui.progressbar.min.1.7.2.js', __FILE__ ), array('jquery-ui-core') );
		wp_enqueue_style ( 'jquery-ui-regenthumbs', plugins_url( 'assets/jquery-ui/redmond/jquery-ui-1.7.2.custom.css', __FILE__ ) );
		wp_enqueue_style ( 'plugin-custom-style',   plugins_url( 'assets/style.css', __FILE__), array() );
	}



	/**
	 * The user interface plus thumbnail regenerator
	 *
	 * @access public
	 * @since 1.0
	 */
	function switcher_interface() {
		global $wpdb;

		$settings_url = menu_page_url( 'tie-theme-options', false ) .'#tie-options-tab-advanced-target';
		?>
			<div id="theme-posts-switcher" class="wrap">

				<div id="switcher-message" class="switcher-message finished" style="display:none">

					<div id="tie-saving-settings">
						<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
							<circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
							<path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
							<path class="checkmark__error_1" d="M38 38 L16 16 Z"/>
							<path class="checkmark__error_2" d="M16 38 38 16 Z" />
						</svg>
					</div>

					<div class="switcher-message-content">
						<p><strong><?php printf( __('Now You can disable the Switcher from the theme options page > %1sAdvanced Settings%2s.', 'jannah_switcher' ), '<a href="'. $settings_url .'">','</a>' ); ?></strong></p>
					</div>
				</div>
		<?php

		if( ! function_exists( 'jannah_theme_setup' ) ){
			wp_die( __( 'Please Activate Jannah theme first.') );
		}

		# If the button was clicked ----------
		if ( ! empty( $_POST['jannah-switcher'] ) ) {

			if ( empty( $_POST['the_theme'] ) ) {
				wp_die( __( 'Please choose a theme first.') );
			}


			# Capability check ----------
			if ( !current_user_can( $this->capability ) ){
				wp_die( __( 'Cheatin&#8217; uh?') );
			}


			# Form nonce check ----------
			check_admin_referer( 'jannah_switcher' );


			# Directly querying the database ----------
			if( ! $posts = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_status != 'inherit' AND post_status != 'auto-draft' AND post_status != 'trash' ORDER BY ID DESC")) {

				echo '	<p>' . sprintf(__("Unable to find any posts. Are you sure <a href='%s'>some exist</a>?", 'jannah_switcher'), admin_url( 'edit.php' )) . "</p></div>";
				return;
			}

			# Generate the list of IDs ----------
			$ids = array();

			foreach( $posts as $post ){
				$ids[] = $post->ID;
			}
			$ids = implode(',', $ids);


			# Display the Message ----------
			echo '	<p>' . __("Please be patient while the posts are switched. You will be notified via this page when the switching is completed.", 'jannah_switcher') . '</p>';

			# Count the Total Number of posts ----------
			$count = count( $posts );


			# Update the Switcher from option ----------
			$yes_update = true;

			if( $is_switched = get_option( 'tie_switch_to_'. TIELABS_THEME_ID ) ){

				$switch_info = explode( '.', $is_switched );

				# Don't update if the new posts count is less than the stored ----------
				if( ! empty( $switch_info[1] ) && $switch_info[1] > $count ){
					$yes_update = false;
				}
			}

			if( $yes_update == true ){
				update_option( 'tie_switch_to_'. TIELABS_THEME_ID, $_POST['the_theme'] .'.'. $count );

				# Delete the stored cache to re-connect to our server to store the switched theme ----------
				delete_transient( 'tie-data-'.TIELABS_THEME_SLUG );
			}


			# Dismiss the switcher notice ----------
			$pointer   = 'tie_switcher_notice_'. TIELABS_THEME_ID;
			$dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) ) );

			if ( ! in_array( $pointer, $dismissed ) ){

				$dismissed[] = $pointer;
				$dismissed = implode( ',', $dismissed );

				update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', $dismissed );
			}


			$text_goback = (!empty($_GET['goback']))
						 ? sprintf(__('To go back to the previous page, <a href="%s">click here</a>.', 'jannah_switcher'), 'javascript:history.go(-1)')
						 : '';

			$text_failures   = sprintf(__('All done! %1$s post(s) were successfully switched in %2$s seconds and there were %3$s failure(s). To try regenerating the failed posts again, <a href="%4$s">click here</a>. %5$s', 'jannah_switcher'), "' + rt_successes + '", "' + rt_totaltime + '", "' + rt_errors + '", esc_url(wp_nonce_url(admin_url('admin.php?page=tie-posts-switcher&goback=1'), 'jannah_switcher') . '&ids=') . "' + rt_failedlist + '", $text_goback);
			$text_nofailures = sprintf(__('All done! %1$s post(s) were successfully switched in %2$s seconds and there were 0 failures. %3$s', 'jannah_switcher'), "' + rt_successes + '", "' + rt_totaltime + '", $text_goback);
			?>

			<noscript><p><em><?php _e('You must enable Javascript in order to proceed!', 'jannah_switcher') ?></em></p></noscript>

			<div id="posts-switcher-bar" style="position:relative;height:25px;">
				<div id="posts-switcher-bar-percent" style="position:absolute;left:50%;top:50%;width:300px;margin-left:-150px;height:25px;margin-top:-9px;font-weight:bold;text-align:center;"></div>
			</div>

			<p><input type="button" class="button hide-if-no-js" name="posts-switcher-stop" id="posts-switcher-stop" value="<?php _e('Abort Process', 'jannah_switcher') ?>" /></p>

			<h3 class="title"><?php _e('Process Information', 'jannah_switcher'); ?></h3>

			<p>
				<?php printf(__('Total: %s', 'jannah_switcher'), $count); ?><br />
				<?php printf(__('Success: %s', 'jannah_switcher'), '<span id="posts-switcher-debug-successcount">0</span>'); ?><br />
				<?php printf(__('Failure: %s', 'jannah_switcher'), '<span id="posts-switcher-debug-failurecount">0</span>'); ?>
			</p>

			<ol id="posts-switcher-debuglist">
				<li style="display:none"></li>
			</ol>

			<script type="text/javascript">
			// <![CDATA[
				jQuery(document).ready(function($){
					var i;
					var rt_images = [<?php echo $ids; ?>];
					var the_theme =  '<?php echo $_POST['the_theme']; ?>';
					var force     =  '<?php echo ! empty( $_POST['force_switch_posts'] ) ? true : false; ?>';
					var rt_total = rt_images.length;
					var rt_count = 1;
					var rt_percent = 0;
					var rt_successes = 0;
					var rt_errors = 0;
					var rt_failedlist = '';
					var rt_resulttext = '';
					var rt_timestart = new Date().getTime();
					var rt_timeend = 0;
					var rt_totaltime = 0;
					var rt_continue = true;

					// Create the progress bar
					$("#posts-switcher-bar").progressbar();
					$("#posts-switcher-bar-percent").html("0%");

					// Stop button
					$("#posts-switcher-stop").click(function() {
						rt_continue = false;
						$('#posts-switcher-stop').val("<?php echo $this->esc_quotes(__('Stopping...', 'jannah_switcher')); ?>");
					});

					// Clear out the empty list element that's there for HTML validation purposes
					$("#posts-switcher-debuglist li").remove();

					// Called after each resize. Updates debug information and the progress bar.
					function switch_posts_update_status(id, success, response) {
						$("#posts-switcher-bar").progressbar("value", (rt_count / rt_total) * 100);
						$("#posts-switcher-bar-percent").html(Math.round((rt_count / rt_total) * 1000) / 10 + "%");
						rt_count = rt_count + 1;

						if (success) {
							rt_successes = rt_successes + 1;
							$("#posts-switcher-debug-successcount").html(rt_successes);
							$("#posts-switcher-debuglist").append("<li>" + response.success + "</li>");
						}
						else {
							rt_errors = rt_errors + 1;
							rt_failedlist = rt_failedlist + ',' + id;
							$("#posts-switcher-debug-failurecount").html(rt_errors);
							$("#posts-switcher-debuglist").append("<li>" + response.error + "</li>");
						}
					}

					// Called when all images have been processed. Shows the results and cleans up.
					function RegenThumbsFinishUp() {
						rt_timeend = new Date().getTime();
						rt_totaltime = Math.round((rt_timeend - rt_timestart) / 1000);

						$('#posts-switcher-stop').hide();

						if (rt_errors > 0) {
							rt_resulttext = '<?php echo $text_failures; ?>';
						} else {
							rt_resulttext = '<?php echo $text_nofailures; ?>';
						}

						$("#switcher-message").find('.switcher-message-content').prepend("<p><strong>" + rt_resulttext + "</strong></p>");
						$("#switcher-message").show();
						$("#tie-saving-settings").addClass('is-success').show();
						$(".checkmark__circle").remove();
					}

					// Regenerate a specified image via AJAX
					function RegenThumbs(id) {
						$.ajax({
							type: 'POST',
							cache: false,
							url: ajaxurl,
							data: { action: "jannah_switch_post", id: id, theme: the_theme, force: force },
							success: function(response) {

								//Catch unknown error
								if(response === null) {
									response = {};
									response.success = false;
									response.error = 'Unknown error occured.';
								}

								if (response.success) {
									switch_posts_update_status(id, true, response);
								}
								else {
									switch_posts_update_status(id, false, response);
								}

								if (rt_images.length && rt_continue) {
									RegenThumbs(rt_images.shift());
								} else {
									RegenThumbsFinishUp();
								}
							},
							error: function(response) {
								switch_posts_update_status(id, false, response);

								if (rt_images.length && rt_continue) {
									RegenThumbs(rt_images.shift());
								} else {
									RegenThumbsFinishUp();
								}
							}
						});
					}

					RegenThumbs(rt_images.shift());
				});
			// ]]>
			</script>
			<?php
		}

		# No button click? Display the form ----------
		else { ?>

			<form method="post" action="">
				<?php wp_nonce_field('jannah_switcher') ?>

				<h3><?php _e("Select your previous theme and Press the following button.", 'jannah_switcher'); ?></h3>

				<noscript><p><em><?php _e('You must enable Javascript in order to proceed!', 'jannah_switcher') ?></em></p></noscript>

				<?php

					$previous_themes = TIELABS_POSTS_SWITCHER::_detect_themes( false );

					if( ! empty( $previous_themes ) && is_array( $previous_themes ) ){
						$previous_themes = array_map('strtolower', $previous_themes);
						?>

						<p><?php printf( __('Themes have been detected in the database and supported by the Switcher are highlighted in %1sOrange%2s.', 'jannah_switcher' ), '<strong style="color:orange">', '</strong>' )?></p>

						<?php
						echo '<style type="text/css">li.visual-option-'. implode( ', li.visual-option-', $previous_themes ) .'{ border-color: orange !important }</style>';
					}


			 		$options  = array( '' => ' ' );

			 		foreach( $this->themes as $theme => $data ){
			 			$data['title'] = '<strong>'. str_replace( ' - ', '</strong><small>', $data['title'] ) .'</small>';
			 			$options[ $theme ] = array( $data['title'] => plugins_url( 'assets/images/'. $theme .'.png', __FILE__) );
			 		}

					tie_build_option(
						array(
							'id'              => 'post-switcher',
							'type'            => 'visual',
							'options'         => $options,
							'external_images' => true,
						),
						'the_theme',
						false
					);

				?>

				<div id="posts-switcher-notes" class="tie-message-hint">
					<ol>
						<li class="switcher-green"><?php esc_html_e( 'The Switcher works on the post\'s data only such as videos url, audio files, galleries, Post Views.', 'jannah' ); ?></li>
						<li class="switcher-red"><?php esc_html_e( 'The Switcher will not migrate any custom pages\' data.', 'jannah' ); ?></li>
						<li class="switcher-red"><?php esc_html_e( 'The Switcher will not migrate the pages built via a page builder.', 'jannah' ); ?></li>
						<li class="switcher-red"><?php esc_html_e( 'The Switcher will not migrate any Custom post types data.', 'jannah' ); ?></li>
						<li class="switcher-red"><?php esc_html_e( 'The Switcher will not migrate any widgets.', 'jannah' ); ?></li>
						<li class="switcher-red"><?php esc_html_e( 'The Switcher will not migrate any menus.', 'jannah' ); ?></li>
						<li class="switcher-red"><?php esc_html_e( 'The Switcher will not remove any data, the old data will remain in the DB.', 'jannah' ); ?></li>
					</ol>
				</div>

				<p>
					<label for="force_switch_posts">
						<input name="force_switch_posts" type="checkbox" id="force_switch_posts" value="1">
						<?php

							echo '<strong>'. esc_html__( 'Force Switch Posts.' ) .'</strong><br />';
							echo '<small>'. esc_html__( 'Use this option if you want to run the switcher on the posts that already switched before!', 'jannah' ) .'</small>';
						?>
					</label>
				</p>

				<p>
					<input type="submit" class="button button-primary button-hero hide-if-no-js" name="jannah-switcher" id="jannah-switcher" value="<?php _e('Let\'s Begin the MAGIC', 'jannah_switcher') ?>" />
				</p>

			</form>
			<?php
		} // End if button ?>

	</div>

	<?php
	}


	/**
	 * Process a single image ID (this is an AJAX handler)
	 *
	 * @access public
	 * @since 1.0
	 */
	function ajax_process_post() {

		// No timeout limit
		set_time_limit(0);

		// Don't break the JSON result
		error_reporting(0);

		$id    = (int) $_REQUEST['id'];
		$theme = $_REQUEST['theme'];

		try {

			header('Content-type: application/json');

			$message = sprintf(__('<b>&quot;%s&quot; (ID %s)</b><br />', 'jannah_switcher' ), esc_html( get_the_title( $id ) ), $id );

			if( $date = get_post_meta( $id, 'jnh_switched_'.$theme, true ) && empty( $_REQUEST['force'] ) ){

				$message .= __('<br />Run Before - Skip.', 'jannah_switcher' );
			}
			else{

				$file = $this->themes;
				$file = $file[ $theme ]['file'];

				include( 'themes/'. $file .'.php' );

				$run_the_magic = jannah_switcher_ajax_process_post( $id, $theme );

				$message .= '<ul><li><span class="dashicons dashicons-yes"></span> '. implode( '</li><li> <span class="dashicons dashicons-yes"></span> ', $run_the_magic ) . '</li></ul>';

				update_post_meta( $id, 'jnh_switched_'.$theme, 'yes' );
			}


      /**
       * Display results
       */
			die( json_encode( array('success' => '<div class="switcher-list-log updated fade"><p>' . $message . '</p></div>')));


		} catch (Exception $e) {
			$this->die_json_failure_msg($id, '<b><span style="color: #DD3D36;">' . $e->getMessage() . '</span></b>');
		}

		exit;
	}


	/**
	 * Helper to make a JSON failure message
	 *
	 * @param integer $id
	 * @param string #message
	 * @access public
	 * @since 1.8
	 */
	function die_json_failure_msg($id, $message) {
		die(json_encode(array('error' => sprintf(__('(ID %s)<br />%s', 'jannah_switcher'), $id, $message))));
	}

	/**
	 * Helper function to escape quotes in strings for use in Javascript
	 *
	 * @param string #message
	 * @return string
	 * @access public
	 * @since 1.0
	 */
	function esc_quotes($string) {
		return str_replace('"', '\"', $string);
	}
}



/**
 * Start
 */
add_action('init', 'Jannah_Switcher');
function Jannah_Switcher() {

	global $Jannah_Switcher;
	$Jannah_Switcher = new JANNAH_SWITCHER_CLASS();
}

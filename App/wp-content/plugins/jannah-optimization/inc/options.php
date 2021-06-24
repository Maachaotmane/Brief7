<?php
/**
 * Options
 **/

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


class JANNAH_OPTIMIZATION_OPTIONS{


	/**
	 * Fire Filters and actions
	 */
	function __construct(){

		if( defined( 'DOING_AJAX' ) && DOING_AJAX ){
			return;
		}

		// Check if the theme is enabled
		if( ! class_exists( 'TIELABS_HELPER' ) || ! function_exists( 'jannah_theme_name' ) ){
			return;
		}

		add_action( 'TieLabs/before_theme_panel',         array( $this, 'test_mode_notice' ), 1 );

		add_action( 'TieLabs/Options/before_update',      array( $this, 'update_critical_css' ) );

		add_filter( 'admin_head',                         array( $this, 'admin_head' ) );
		add_filter( 'TieLabs/options_tab_title',          array( $this, 'tab_title' ) );
		add_action( 'tie_theme_options_tab_optimization', array( $this, 'tab_content' ) );
	}


	/**
	 * update_critical_css
	 */
	function update_critical_css( $settings ){

		if ( empty( $settings['jso_critical_css'] ) && false !== get_transient( 'tie_critical_css_'.TIELABS_THEME_ID ) ) {
			delete_transient( 'tie_critical_css_'.TIELABS_THEME_ID );
		}
	}


	/**
	 * tab_title
	 *
	 * Add a tab for the optimization settings in the theme options page
	 */
	function tab_title( $settings_tabs ){

		$settings_tabs['optimization'] = array(
			'icon'  => 'dashboard',
			'title' => esc_html__( 'Performance', TIELABS_TEXTDOMAIN ),
		);

		return $settings_tabs;
	}


	/**
	 * tab_content
	 *
	 * Add new section for the optimization settings in the theme options page
	 */
	function tab_content(){

		tie_build_theme_option(
			array(
				'title' => esc_html__( 'Speed Optimization', TIELABS_TEXTDOMAIN ),
				'id'    => 'speed-optimization-tab',
				'type'  => 'tab-title',
			));


		// This method available in v4.0.0 and above
		if( ! method_exists( 'TIELABS_HELPER','has_builder' ) ){

			tie_build_theme_option(
				array(
					'text' => esc_html__( 'You need to upgrade your theme to v4.0.0 to access these options. ', TIELABS_TEXTDOMAIN ),
					'type' => 'error',
				));

			return;
		}

		$speed_settings = 'block';

		if( ! get_option( 'tie_token_'.TIELABS_THEME_ID ) ){

			$speed_settings = 'none !important';

			tie_build_theme_option(
				array(
					'text' => esc_html__( 'Verify your license to unlock this section.', TIELABS_TEXTDOMAIN ),
					'type' => 'error',
				));
		}

		echo '<div style="display:'. $speed_settings .'" >';

		tie_build_theme_option(
			array(
				'text' => esc_html__( 'Deactivate if you notice any visually broken items on your website.', TIELABS_TEXTDOMAIN ),
				'type' => 'message',
			));

		tie_build_theme_option(
			array(
				'title' =>	esc_html__( 'General', TIELABS_TEXTDOMAIN ),
				'type'  => 'header',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Cache Static Sections', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_cache',
				'type' => 'checkbox',
				'hint' => esc_html__( 'If enabled, some static parts like widgets, main menu and breaking news will be cached to reduce MySQL queries. Saving the theme settings, adding/editing/removing posts, adding comments, updating menus, activating/deactivating plugins, adding/editing/removing terms or updating WordPress, will flush the cache.', TIELABS_TEXTDOMAIN ),
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Disable Lightbox Resources on Homepage', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_homepage_lightbox',
				'type' => 'checkbox',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Remove query strings from static resources', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_remove_query_strings',
				'type' => 'checkbox',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Disable Emoji and Smilies', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_disable_emoji_smilies',
				'type' => 'checkbox',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Disable XML-RPC and RSD Link', TIELABS_TEXTDOMAIN ),
				'hint' => esc_html__( 'More info', TIELABS_TEXTDOMAIN ) .' https://codex.wordpress.org/XML-RPC_Support',
				'id'   => 'jso_disable_xml_rpc',
				'type' => 'checkbox',
			));


		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Remove wlwmanifest Link', TIELABS_TEXTDOMAIN ),
				'hint' => esc_html__( 'If you don’t use Windows Live Writer', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_disable_wlwmanifest',
				'type' => 'checkbox',
			));

		tie_build_theme_option(
			array(
				'title' =>	esc_html__( 'CSS', TIELABS_TEXTDOMAIN ),
				'type'  => 'header',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Optimize CSS delivery', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_css_delivery',
				'type' => 'checkbox',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Inline Critical Path CSS', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_critical_css',
				'type' => 'checkbox',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Inline the custom CSS code of the builder in the body tag', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_inline_builder_css',
				'type' => 'checkbox',
			));


		tie_build_theme_option(
			array(
				'title' =>	esc_html__( 'JavaScript', TIELABS_TEXTDOMAIN ),
				'type'  => 'header',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Load JS files deferred', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_js_deferred',
				'type' => 'checkbox',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Dequeue jQuery Migrate File', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_dequeue_jquery_migrate',
				'type' => 'checkbox',
			));

		tie_build_theme_option(
			array(
				'title' =>	esc_html__( 'HTML', TIELABS_TEXTDOMAIN ),
				'type'  => 'header',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Minify HTML', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_minify_html',
				'type' => 'checkbox',
			));

		/*
		tie_build_theme_option(
			array(
				'title' =>	esc_html__( 'Ajax Requests', TIELABS_TEXTDOMAIN ),
				'type'  => 'header',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Optimize Ajax Requests', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_ajax',
				'type' => 'checkbox',
			));
		*/

		tie_build_theme_option(
			array(
				'title' =>	esc_html__( 'Google Fonts', TIELABS_TEXTDOMAIN ),
				'type'  => 'header',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Disable Google Fonts on slow connections', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_disable_fonts_2g',
				'type' => 'checkbox',
				'hint' => esc_html__( 'Partially Supported', TIELABS_TEXTDOMAIN ) .' https://caniuse.com/#feat=netinfo',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Disable Google Fonts on mobiles', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_disable_fonts_mobile',
				'type' => 'checkbox',
			));


		tie_build_theme_option(
			array(
				'title' =>	esc_html__( 'Font Awesome 5', TIELABS_TEXTDOMAIN ),
				'type'  => 'header',
			));


		tie_build_theme_option(
			array(
				'text' => '<strong>'. esc_html__( 'Don\'t enable this option if you are using icons in the Main Navigation, Sections title, Block title, Social Networks, Buttons Shortcode or Reviews Buttons.', TIELABS_TEXTDOMAIN ) .'</strong>',
				'type' => 'message',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Disable Font Awesome', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_disable_fontawesome',
				'type' => 'checkbox',
				'hint' => esc_html__( 'This option will prevent loading of the CSS and fonts file of the Font Awesome icons.', TIELABS_TEXTDOMAIN ),
			));


		tie_build_theme_option(
			array(
				'title' =>	esc_html__( 'Lazy Load', TIELABS_TEXTDOMAIN ),
				'id'    => 'lazy-load-head',
				'type'  => 'header',
			));

		tie_build_theme_option(
			array(
				'name'   => esc_html__( 'Lazy Load For Images', TIELABS_TEXTDOMAIN ),
				'id'     => 'lazy_load',
				'type'   => 'checkbox',
				'toggle' => '#lazy_load_img-item, #lazy_load_dark_img-item, #lazy_load_post_content-item, #lazy_load_ads-item',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Image Placeholder', TIELABS_TEXTDOMAIN ),
				'id'   => 'lazy_load_img',
				'type' => 'upload',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Dark Skin Image Placeholder', TIELABS_TEXTDOMAIN ),
				'id'   => 'lazy_load_dark_img',
				'type' => 'upload',
			));

		tie_build_theme_option(
			array(
				'name'   => esc_html__( 'Lazy Load For Images in Post Content', TIELABS_TEXTDOMAIN ),
				'id'     => 'lazy_load_post_content',
				'type'   => 'checkbox',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'LazyLoad for blocks and Widgets Images Ads', TIELABS_TEXTDOMAIN ),
				'id'   => 'lazy_load_ads',
				'type' => 'checkbox',
			));


		// Plugins
		$plugins = array();

		if( TIELABS_WOOCOMMERCE_IS_ACTIVE ){
			$plugins['woocommerce'] = esc_html__( 'WooCommerce', TIELABS_TEXTDOMAIN );
		}

		if( TIELABS_BBPRESS_IS_ACTIVE ){
			$plugins['bbpress'] = esc_html__( 'bbPress', TIELABS_TEXTDOMAIN );
		}

		if( TIELABS_BUDDYPRESS_IS_ACTIVE ){
			$plugins['buddypress'] = esc_html__( 'BuddyPress', TIELABS_TEXTDOMAIN );
		}

		if( TIELABS_EXTENSIONS_IS_ACTIVE ){
			$plugins['shortcodes'] = esc_html__( 'Shortcodes', TIELABS_TEXTDOMAIN );
		}

		if( ! empty( $plugins ) ){

			$pages = array(
				'homepage' => esc_html__( 'The Homepage', TIELABS_TEXTDOMAIN ),
				'builder'  => esc_html__( 'Pages built by the TieLabs Page Builder', TIELABS_TEXTDOMAIN ),
				'post'     => esc_html__( 'Posts', TIELABS_TEXTDOMAIN ),
				'category' => esc_html__( 'Categories', TIELABS_TEXTDOMAIN ),
				'tag'      => esc_html__( 'Tags', TIELABS_TEXTDOMAIN ),
				'author'   => esc_html__( 'Author Pages', TIELABS_TEXTDOMAIN ),
			);

			foreach ( $plugins as $plugin => $text ){

				tie_build_theme_option(
					array(
						'title' => $text . ' | '. esc_html__( 'Don\'t load CSS and JS files on', TIELABS_TEXTDOMAIN ),
						'type' => 'header',
					));

				foreach ( $pages as $page => $text ) {

					if( ( $plugin == 'shortcodes' || $plugin == 'woocommerce' ) && $page == 'builder' ){

						tie_build_theme_option(
							array(
								'name'   => $text,
								'id'     => 'jso_disable_'. $plugin .'_'. $page,
								'type'   => 'checkbox',
								'toggle' => '#jso_exclude_'. $plugin .'_pages-item',
							));

						tie_build_theme_option(
							array(
								'name' => esc_html__( 'Exclude these pages', TIELABS_TEXTDOMAIN ),
								'hint' => esc_html__( 'Enter a page ID, or IDs separated by comma.', TIELABS_TEXTDOMAIN ),
								'id'   => 'jso_exclude_'. $plugin .'_pages',
								'type' => 'text',
							));
					}
					elseif( ! ( $plugin == 'shortcodes' && $page == 'post' ) ){

						tie_build_theme_option(
							array(
								'name'   => $text,
								'id'     => 'jso_disable_'. $plugin .'_'. $page,
								'type'   => 'checkbox',
							));
					}

				}
			}
		}


		// Test Mode Option
		tie_build_theme_option(
			array(
				'title' =>	esc_html__( 'Test Mode', TIELABS_TEXTDOMAIN ),
				'id'    => 'test-mode-head',
				'type'  => 'header',
			));

		tie_build_theme_option(
			array(
				'name' => esc_html__( 'Enable Test Mode', TIELABS_TEXTDOMAIN ),
				'hint' => esc_html__( 'This option will disable all custom codes added in the General Tab &gt; Custom Codes section, and all custom Coded Ads added in the theme options page, theme Ad widgets and page builder blocks.', TIELABS_TEXTDOMAIN ),
				'id'   => 'jso_test_mode',
				'type' => 'checkbox',
			));

		echo '</div>';
	}

	/**
	 * test_mode_notice
	 *
	 * Show notice at the top of the theme options page if the Test Mode is Active
	 */
	function test_mode_notice() {

		if( tie_get_option( 'jso_test_mode' ) ){

			echo '<p id="test-mode-notice">'. esc_html__( 'Test Mode is active, do not forget to disable it from the Performance tab.', TIELABS_TEXTDOMAIN ) .'</p>';
		}
	}


	/**
	 * admin_head
	 *
	 * Set custom style for the optimization tab title
	 */
	function admin_head() {
		echo '
			<style>
				#test-mode-head{
					color: red;
					font-weight: bold;
				}

				#test-mode-notice{
					background-color: red;
					color: #ffffff;
					padding: 10px;
					margin-right: 20px;
					font-weight: bold;
					text-align: center;
					line-height: 1.9;
				}

				.tie-panel-tabs li.tie-options-tab-optimization:not(.active) a{
					background-color: #109010;
				}
			</style>
		';
	}


} // class


//
add_action( 'admin_init', 'jannah_optimization_options_init' );
function jannah_optimization_options_init(){
	new JANNAH_OPTIMIZATION_OPTIONS();
}

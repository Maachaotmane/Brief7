<?php
/**
 * Disable the resources of plugins in the homepage.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


class JANNAH_OPTIMIZATION_PLUGINS {

	public $resources = array();


	/**
	 * Fire Filters and actions
	 */
	function __construct(){

		// Check if the theme is enabled
		if( ! class_exists( 'TIELABS_HELPER' ) || ! function_exists( 'jannah_theme_name' ) ){
			return;
		}

		// Check the page builder blocks
		add_action( 'TieLabs/builder/after_save', array( $this, 'check_sections' ), 10, 2 );

		// Check Sidebars
		add_action( 'update_option',              array( $this, 'check_global_sidebars' ), 10, 3 );

		// Dequeue the JS and CSS files
		add_action( 'wp_enqueue_scripts',         array( $this, 'dequeue_scripts_styles' ), 99 );
	}


	/**
	 * check_sections
	 * Check the blocks and the sidebars of the builder
	 */
	function check_sections( $post_id, $sections ){

		if( ! empty( $sections ) && is_array( $sections ) ){

			foreach ( $sections as $section ){
				self::check_builder_sidebars( $section );
				self::check_blocks( $section );
			}

			$extra_resources = ! empty( $this->resources ) ? $this->resources : 'no';

			update_post_meta( $post_id, 'tie_extra_resources', $extra_resources );
		}
	}


	/**
	 * check_global_sidebars
	 * Ccheck the footers and the slide-sidebar-area if they have any widgets
	 */
	function check_global_sidebars( $option, $old_value, $value ){

		if( empty( $option ) || empty( $value ) || ! is_array( $value ) ){
			return;
		}

		if( $option == 'sidebars_widgets' ){

			foreach ( $value as $sidebar => $widgets  ) {
				if( strpos( $sidebar, '-footer-' ) !== false || $sidebar == 'slide-sidebar-area' ){
					if( is_dynamic_sidebar( $sidebar ) ){
						$this->check_widgets( $widgets );
					}
				}
			}

			$global_resources = ! empty( $this->resources ) ? 'yes' : 'no';
			update_option( 'tie_global_sidebars_resources', $global_resources, false );
		}
	}


	/**
	 * check_blocks
	 * Check the blocks if the WooConnerce blocks are active
	 */
	function check_blocks( $section ){

		if( ! in_array( 'woocommerce', $this->resources ) && ! empty( $section['blocks'] ) && is_array( $section['blocks'] ) ) {
			foreach( $section['blocks'] as $b_id => $block ){
				if( ! empty( $block['style'] ) && strpos( $block['style'], 'woocommerce' ) !== false ){
					$this->resources[] = 'woocommerce';
				}
			}
		}
	}


	/**
	 * check_builder_sidebars
	 * Check the builder sidebars
	 */
	function check_builder_sidebars( $section ){

		$sidebar_id = self::get_sidebar( $section );

		$site_sidebars = wp_get_sidebars_widgets();

		if( ! empty( $site_sidebars[ $sidebar_id ] ) ){
			$this->check_widgets( $site_sidebars[ $sidebar_id ] );
		}
	}


	/**
	 * check_widgets
	 */
	function check_widgets( $sidebar_widgets ){

		if( ! empty( $sidebar_widgets ) && is_array( $sidebar_widgets ) ){
			foreach ( $sidebar_widgets as $widget ) {

				if( ! in_array( 'bbpress', $this->resources ) && strpos( $widget, 'bp_' ) === 0){
					$this->resources[] = 'bbpress';
				}
				elseif( ! in_array( 'buddypress', $this->resources ) && strpos( $widget, 'bbp_' ) === 0 ){
					$this->resources[] = 'buddypress';
				}
				elseif( ! in_array( 'woocommerce', $this->resources ) && strpos( $widget, 'woocommerce_' ) === 0 ){
					$this->resources[] = 'woocommerce';
				}
			}
		}
	}


	/**
	 * get_sidebar
	 */
	function get_sidebar( $section ){

		// No Pre defined Sidebar
		if( empty( $section['settings']['predefined_sidebar'] ) && ! empty( $section['settings']['section_id'] ) ){
			return $section['settings']['section_id'];
		}

		// Pre Defined Sidebar
		if( ! empty( $section['settings']['sidebar_id'] ) ){
			return $section['settings']['sidebar_id'];
		}

		// Default sidebar if there is no a custom sidebar
		$sidebar = tie_get_option( 'sidebar_page' );
		if( empty( $sidebar ) || ( ! empty( $sidebar ) && ! TIELABS_HELPER::is_sidebar_registered( $sidebar ) ) ) {
			$sidebar = 'primary-widget-area';
		}

		return $sidebar;
	}


	/**
	 * is_plugin_load
	 */
	function is_plugin_load( $check ){

		// If the resources used globally import all
		if( get_option( 'tie_global_sidebars_resources', 'yes' ) == 'yes' ){
			return true;
		}

		$resources = tie_get_postdata( 'tie_extra_resources' );

		// backword compatability for older versions - The option is not exist
		if( empty( $resources ) ){
			return true;
		}

		// is in the extra resources array
		elseif( is_array( $resources ) && in_array( $check, $resources ) ){
			return true;
		}

		// $resources == no
		return false;
	}


	/**
	 * dequeue_scripts_styles
	 * Dequeue 3rd party plugins resources from the pages built by the Page builder
	 */
	function dequeue_scripts_styles(){

		if( ! TIELABS_HELPER::has_builder() || ! is_singular('post') ){
			return;
		}

		// WooCommerce
		if( TIELABS_WOOCOMMERCE_IS_ACTIVE && ! $this->is_plugin_load('woocommerce') ){
			wp_dequeue_style('tie-css-woocommerce');
			wp_dequeue_style('wc-block-style');
			wp_dequeue_style('woocommerce-inline');
			wp_dequeue_script('woocommerce');
		}

		// BuddyPress
		if( TIELABS_BUDDYPRESS_IS_ACTIVE && ! $this->is_plugin_load('buddypress') ){
			wp_dequeue_style('tie-css-buddypress');
			wp_dequeue_style('bp-mentions-css');
			wp_dequeue_script('bp-mentions');
			wp_dequeue_script('bp-nouveau');
			wp_dequeue_script('jquery-atwho');
			wp_dequeue_script('jquery-caret');
			wp_dequeue_script('bp-widget-members');
			wp_dequeue_script('bp-jquery-query');
			wp_dequeue_script('bp-jquery-cookie');
			wp_dequeue_script('bp-jquery-scroll-to');
			wp_dequeue_script('bp_core_widget_friends-js');
			wp_dequeue_script('groups_widget_groups_list-js');
		}

		// BBPress
		if( TIELABS_BBPRESS_IS_ACTIVE && ! $this->is_plugin_load('bbpress') ){
			wp_dequeue_style('tie-css-bbpress');
			wp_dequeue_script('bbpress-editor');
		}
	}

} // class


//
add_filter( 'init', 'jannah_optimization_plugins_init' );
function jannah_optimization_plugins_init(){

	// This method available in v4.0.0 and above
	if( method_exists( 'TIELABS_HELPER','has_builder' ) ){
		new JANNAH_OPTIMIZATION_PLUGINS();
	}
}

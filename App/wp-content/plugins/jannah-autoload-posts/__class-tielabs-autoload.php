<?php
/**
 * Auto Load Posts
 *
 * The idea of using iframes is inspired by the tag Div team :)
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly



class TIELABS_AUTOLOAD_POSTS {


	/**
	 * Runs on class initialization. Adds filters and actions.
	 */
	function __construct() {

		add_action( 'wp', array( $this, 'init' ) ); // is_single() and other Conditional Tags don't work before this action

		//
		add_action( 'init', array( $this, 'early_hook' ), 5 );
	}


	/**
	 * init
	 */
	function init(){

		// Return if current page is not a single post page or the Auto Load posts option is disabled.
		if( ! tie_get_option( 'autoload_posts' ) || ! is_single() ){
			return;
		}

		// This is an Auto Loaded Post
		if( tie_is_auto_loaded_post() ){

			$this->do_child_actions();
		}

		// This is the parent post page
		else{
			$this->do_parent_actions();
		}

	}


	/**
	 * early_hook
	 */
	function early_hook(){

		// Redirect after posting a comment
		add_filter( 'comment_post_redirect', array( $this, 'comment_post_redirect' ) );

		// Jannah Speed Optimization
		if( tie_get_option( 'autoload_posts' ) ){

			// Old versions
			if( ! defined( 'JANNAH_SPEED_OPTIMIZATION' ) || defined( 'JANNAH_SPEED_OPTIMIZATION' ) && JANNAH_SPEED_OPTIMIZATION < '1.0.3' ){
				remove_filter( 'init', 'jannah_optimization_styles_init' );
			}
		}
	}


	/**
	 * The Parent Post
	 */
	function do_parent_actions(){

		// Insert the iframe code after the post content
		add_action( 'TieLabs/main_content_row/after', array( $this, 'get_next_iframe' ), 50 );

		// Insert the iframe Js code at the footer after loading jQuery
		add_action( 'wp_footer', array( $this, 'insert_iframe_js_codes' ), 250 );

		// Add custom class for the main post div, same class will be used for the iframe
		add_action('TieLabs/post_classes', function( $classes, $post_id, $standard, $main_post ){

			if( is_array( $classes ) && $main_post ){
				$classes[] = 'tie-autoloaded-post';
			}

			return $classes;

		}, 10, 4 );

		// Add data attr to the main post div
		add_action('TieLabs/post_class_attr', function( $attr, $post_id, $standard, $main_post ){

			if( $main_post ){

				// Current post object
				$entry = get_post();

				// Current Post URL
				$url = get_permalink( $entry );

				// Edit link,
				$post_type_object = get_post_type_object( $entry->post_type );
				$edit_url = admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=edit', $entry->ID ) ); // get_edit_post_link() Adds extra query for each post!!

				// --
				$attr .= ' data-post-url="'. $url .'" data-post-title="'. get_the_title( $entry ) .'" data-post-edit="'. $edit_url .'"';

				// Sticky Share links
				if( tie_get_option( 'share_post_mobile' ) || tie_get_option( 'share_post_sticky' ) ){
					$args = tie_share_button_url_args( $entry );
					extract( $args );
					$attr .= 'data-share-title="'. $share_title .'" data-share-link="'. $share_link .'" data-share-image="'. $share_image .'"';
				}
			}

			return $attr;

		}, 10, 4);
	}


	/**
	 * Insert the iframe Js codes
	 *
	 * We don't use wp_enqueue_script to avoid this file to got concatenated by cache plugins
	 */
	function insert_iframe_js_codes(){
		?>
		<script data-cfasync="false" type="text/javascript" src="<?php echo TIELABS_TEMPLATE_URL ?>/assets/js/autoload-parent.js"></script>
		<?php
	}



	/**
	 * Get the Iframe Code
	 */
	function get_next_iframe(){

		$autoload_post_type = tie_get_option( 'autoload_post_type' );

		switch ( $autoload_post_type ) {

			// get the next post
			case 'next':
				$next_posts = $this->get_adjacent_post( false );
				break;

			// get the previous post from the same category
			case 'same_cat_prev':
				$next_posts = $this->get_adjacent_post( true, true );
				break;

			// get the next post from the same category
			case 'same_cat_next':
				$next_posts = $this->get_adjacent_post( false, true );
				break;

			// get the previous post
			default:
				$next_posts = $this->get_adjacent_post();
		}

		if ( ! empty( $next_posts ) ) {

			echo '
				<script>var tieAutoLoadPosts = '. wp_json_encode( $next_posts ) .';</script>
				<div id="tie-infinte-posts-iframes-wrapper">
					<div id="tie-infinte-posts-iframes">
					</div>

					<div id="tie-infinte-posts-loading">'. tie_get_ajax_loader( false ) .'</div>
				</div>
			';
		}
	}


	/**
	 * Child Post
	 */
	function do_child_actions(){

		// Disable Admin bar
		add_filter('show_admin_bar', '__return_false');

		// Disable Header
		add_filter( 'TieLabs/is_header_active', '__return_false' );

		// Disable Footer
		add_filter( 'TieLabs/is_footer_active', '__return_false' );

		// Disable Theme Layout classes for <body>
		add_filter( 'TieLabs/body_class/theme_layout', '__return_false' );

		// <head> of the Ajax Loaded post
		add_action( 'wp_head', array( $this, 'child_head' ), 5 );

		// Before </body> of the Ajax Loaded post
		add_action( 'wp_footer', array( $this, 'child_footer' ), 2 );

		// Custom body class in the Ajax loaded posts
		add_action( 'body_class', array( $this, 'child_body_class' ) );

		// Get single post below header layouts
		add_action( 'TieLabs/before_main_content', function(){
			TIELABS_HELPER::get_template_part( 'templates/header/posts-layout' );
		},1);

		// --
		add_filter( 'JANNAH_OPTIMIZATION_STYLES/dequeue_theme_styles', '__return_false' );
		add_filter( 'JANNAH_OPTIMIZATION_STYLES/do_style', '__return_false' );

		// Hide all HTML except <script> and <link> added into the footer, most PopUp, Cookies notices and Newsletter plugins uses this filter
		add_action( 'wp_footer', function(){
			echo '
				<style>
					html body + *:not(script):not(link),
					html body ~ *:not(script):not(link),
					html body div#tie-autoloaded-post-footer-tag *:not(script):not(link),
					html body div#tie-autoloaded-post-footer-tag + *:not(script):not(link),
					html body div#tie-autoloaded-post-footer-tag ~ *:not(script):not(link){
						display: none !important;
						visibility: hidden !important;
						z-index: -1 !important;
						opacity: 0 !important;
						height: 0 !important;
						width: 0!important;
					}
				</style>
			';
			echo '<div id="tie-autoloaded-post-footer-tag">';
		}, 0 );

		add_action( 'wp_footer', function(){
			echo '</div><!-- #tie-autoloaded-post-footer-tag -->';
		}, 9999999999 );
	}



	/**
	 * <head> of the Ajax Loaded post
	 */
	function child_head(){

		// Prevent Search engines from indexing the Auto Loaded post
		echo '<meta name="robots" content="noindex, nofollow" />';

		// We don't use wp_enqueue_script to avoid this file to got concatenated by cache plugins
		echo '<script data-cfasync="false" type="text/javascript" src="'. TIELABS_TEMPLATE_URL . '/assets/js/autoload-child.js"></script>';

		// CSS
		$css_codes = apply_filters( 'TieLabs/Autoload_Posts/Child/CSS', '
			html{
				overflow: hidden !important;
			}

			html,
			body#tie-body,
			body#tie-body .background-overlay{
				background: transparent !important;
				padding: 0 !important;;
				margin 0 !important;;
			}

			a[href^="#go-to-"]{
				display: none !important;
			}
		');

		if( ! empty( $css_codes ) ){
			echo '<style>'.  $css_codes .'</style>';
		}
	}


	/**
	 * before </body> of the Ajax Loaded post
	 */
	function child_footer(){

		// JS
		$js_codes = apply_filters( 'TieLabs/Autoload_Posts/Child/JS', '
			var link = document.getElementsByTagName("a");
			var i;
			for (i = 0; i < link.length; i++) {

				/*if( link[i].href === "#" ){
					link[i].addEventListener( "click", function(e){
						e.preventDefault();
					});
				}
				*/

				if( link[i].href !== "#" && "undefined" !== typeof link[i].target ){
					link[i].setAttribute("target", "_parent")
				}
			}

			var iFrameResizer = {
				onMessage: function(message) {
					alert(message, parentIFrame.getId())
				},
				onReady: function() {
					parentIFrame.sendMessage( "iamReadyDad" );
				}
			}

			// Listen to parent
			window.addEventListener("message", handleParentMessage, false);
			function handleParentMessage(e) {
				if( e.data && typeof e.data === "object"){
			    html = document.getElementsByTagName("html")[0].classList;
					if( "undefined" !== typeof e.data.addClass ) {
						var Classes = e.data.addClass.split(" ");
						for (i = 0; i < Classes.length; i++) {
							if( Classes[i] ){
								html.add( Classes[i] );
							}
						}
					}
					if( "undefined" !== typeof e.data.removeClass ) {
						var Classes = e.data.removeClass.split(" ");
						for (i = 0; i < Classes.length; i++) {
							if( Classes[i] ){
								html.remove( Classes[i] );
							}
						}
					}
					//---
				}
			}
		');

		if( ! empty( $js_codes ) ){
			echo '<script>'.  $js_codes .'</script>';
		}
	}


	/**
	 * Custom Class in the Ajax Loaded Posts
	 */
	function child_body_class( $classes ){

		$classes[] = 'is-ajax-loaded-post';

		return $classes;
	}


	/**
	 * When posting a comment on ab autoloaded posts, redirect to the ?tie-ajax-post
	 */
	function comment_post_redirect( $url ){

		if ( strpos( $_SERVER["HTTP_REFERER"], 'tie-ajax-post' ) !== false ) {
			$url = add_query_arg( 'tie-ajax-post', 'true', $url );
		}

		return $url;
	}


	/**
	 * when posting a comment on a ajax autoloaded posts make sure the redirect sets the ajax state
	 */
	function get_adjacent_post( $previous = true, $in_same_term = false, $excluded_terms = '', $taxonomy = 'category' ) {
		global $wpdb;

		$post = get_post();
		if ( ! $post || ! taxonomy_exists( $taxonomy ) ) {
			return null;
		}

		$current_post_date = $post->post_date;

		$join     = '';
		$where    = '';
		$adjacent = $previous ? 'previous' : 'next';

		if ( ! empty( $excluded_terms ) && ! is_array( $excluded_terms ) ) {
			$excluded_terms = explode( ',', $excluded_terms );
			$excluded_terms = array_map( 'intval', $excluded_terms );
		}

		if ( $in_same_term || ! empty( $excluded_terms ) ) {
			if ( $in_same_term ) {
				$join  .= " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
				$where .= $wpdb->prepare( 'AND tt.taxonomy = %s', $taxonomy );

				if ( ! is_object_in_taxonomy( $post->post_type, $taxonomy ) ) {
					return '';
				}
				$term_array = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );

				// Remove any exclusions from the term array to include.
				$term_array = array_diff( $term_array, (array) $excluded_terms );
				$term_array = array_map( 'intval', $term_array );

				if ( ! $term_array || is_wp_error( $term_array ) ) {
					return '';
				}

				$where .= ' AND tt.term_id IN (' . implode( ',', $term_array ) . ')';
			}

			if ( ! empty( $excluded_terms ) ) {
				$where .= " AND p.ID NOT IN ( SELECT tr.object_id FROM $wpdb->term_relationships tr LEFT JOIN $wpdb->term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE tt.term_id IN (" . implode( ',', array_map( 'intval', $excluded_terms ) ) . ') )';
			}
		}

		// Only Published posts
		$where .= " AND p.post_status = 'publish'";

		$op    = $previous ? '<' : '>';
		$order = $previous ? 'DESC' : 'ASC';
		$limit = 5;

		$where = $wpdb->prepare( "WHERE p.post_date $op %s AND p.post_type = %s $where", $current_post_date, $post->post_type );

		$query     = "SELECT * FROM $wpdb->posts AS p $join $where ORDER BY p.post_date $order LIMIT $limit";
		$query_key = 'autoload_posts_' . md5( $query );
		$result    = wp_cache_get( $query_key, 'counts' );

		if ( false === $result ) {

			$result = $wpdb->get_results( $query );

			if ( null === $result ) {
				$result = '';
			}

			wp_cache_set( $query_key, $result, 'counts' );
		}

		$entries = array();

		if( ! empty( $result ) || is_array( $result ) ){
			foreach ( $result as $entry ) {

				if( tie_get_option( 'share_post_mobile' ) || tie_get_option( 'share_post_sticky' ) ){
					$share = tie_share_button_url_args( $entry );
				}
				else{
					$share = array(
						'share_title'     => '',
						'share_link'      => '',
						'share_full_link' => '',
						'share_image'     => '',
					);
				}

				//
				$url = get_permalink( $entry );

				// Edit link,  get_edit_post_link( $entry ), // Adds extra query for each post!!
				$post_type_object = get_post_type_object( $entry->post_type );
				$edit_url = admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=edit', $entry->ID ) );

				$entries[] = array_merge( $share, array(
					'id'       => $entry->ID,
					'url'      => $url,
					'edit_url' => $edit_url,
					'title'    => get_the_title( $entry ),
					'src'      => add_query_arg( 'tie-ajax-post', '1', $url ),
				));

			}

			return $entries;
		}

		return false;
	}


}

// Single instance.
$TIELABS_AUTOLOAD_POSTS = new TIELABS_AUTOLOAD_POSTS();

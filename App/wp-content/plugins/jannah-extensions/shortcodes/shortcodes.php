<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



/*-----------------------------------------------------------------------------------*/
# Register main Scripts and Styles
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_enqueue_scripts', 'jannah_extensions_shortcodes_enqueue_scripts', 25 );
function jannah_extensions_shortcodes_enqueue_scripts() {

	$load_css_js = apply_filters( 'tie_plugin_shortcodes_enqueue_assets', true );

	if( true === $load_css_js ) {
		wp_enqueue_style ( 'jannah-extensions-shortcodes-styles',  plugins_url( 'assets/style.css', __FILE__ ),     array(), '', 'all' );
		wp_enqueue_script( 'jannah-extensions-shortcodes-scripts', plugins_url( 'assets/js/scripts.js', __FILE__ ), array( 'jquery' ), false, true );
	}
}



/*-----------------------------------------------------------------------------------*/
# Shortcode styles and scripts
/*-----------------------------------------------------------------------------------*/
add_action( 'admin_enqueue_scripts', 'jannah_extensions_shortcodes_scripts', 100 );
function jannah_extensions_shortcodes_scripts() {

	$lang_text = array(
		'jannah_theme_active'     => function_exists( 'tie_get_option') ? true : false,
		'shortcode_tielabs'       => esc_html__( 'Jannah Shortcodes', 'jannah-extensions' ),
		'shortcode_help'          => esc_html__( 'Need help?',   'jannah-extensions' ),
		'shortcode_docs_url'      => apply_filters( 'tie_extensions_shortcodes_docs_url', 'https://tielabs.com/help/' ),
		'shortcode_blockquote'    => esc_html__( 'Quote',        'jannah-extensions' ),
		'shortcode_author'        => esc_html__( 'Author',       'jannah-extensions' ),
		'shortcode_box'           => esc_html__( 'Box',          'jannah-extensions' ),
		'shortcode_alignment'     => esc_html__( 'Alignment',    'jannah-extensions' ),
		'shortcode_class'         => esc_html__( 'Custom Class', 'jannah-extensions' ),
		'shortcode_style'         => esc_html__( 'Style',        'jannah-extensions' ),
		'shortcode_dark'          => esc_html__( 'Dark',         'jannah-extensions' ),
		'shortcode_light'         => esc_html__( 'Light',        'jannah-extensions' ),
		'shortcode_simple'        => esc_html__( 'Simple',       'jannah-extensions' ),
		'shortcode_shadow'        => esc_html__( 'Shadow',       'jannah-extensions' ),
		'shortcode_info'          => esc_html__( 'Info',         'jannah-extensions' ),
		'shortcode_success'       => esc_html__( 'Success',      'jannah-extensions' ),
		'shortcode_warning'       => esc_html__( 'Warning',      'jannah-extensions' ),
		'shortcode_error'         => esc_html__( 'Error',        'jannah-extensions' ),
		'shortcode_download'      => esc_html__( 'Download',     'jannah-extensions' ),
		'shortcode_note'          => esc_html__( 'Note',         'jannah-extensions' ),
		'shortcode_right'         => esc_html__( 'Right',        'jannah-extensions' ),
		'shortcode_left'          => esc_html__( 'Left',         'jannah-extensions' ),
		'shortcode_center'        => esc_html__( 'Center',       'jannah-extensions' ),
		'shortcode_width'         => esc_html__( 'Width',        'jannah-extensions' ),
		'shortcode_content'       => esc_html__( 'Content',      'jannah-extensions' ),
		'shortcode_button'        => esc_html__( 'Button',       'jannah-extensions' ),
		'shortcode_nofollow'      => esc_html__( 'Nofollow?',    'jannah-extensions' ),
		'shortcode_color'         => esc_html__( 'Color',        'jannah-extensions' ),
		'shortcode_primary_color' => esc_html__( 'Primary Color','jannah-extensions' ),
		'shortcode_red'           => esc_html__( 'Red',          'jannah-extensions' ),
		'shortcode_orange'        => esc_html__( 'Orange',       'jannah-extensions' ),
		'shortcode_blue'          => esc_html__( 'Blue',         'jannah-extensions' ),
		'shortcode_green'         => esc_html__( 'Green',        'jannah-extensions' ),
		'shortcode_black'         => esc_html__( 'Black',        'jannah-extensions' ),
		'shortcode_gray'          => esc_html__( 'Gray',         'jannah-extensions' ),
		'shortcode_white'         => esc_html__( 'White',        'jannah-extensions' ),
		'shortcode_pink'          => esc_html__( 'Pink',         'jannah-extensions' ),
		'shortcode_purple'        => esc_html__( 'Purple',       'jannah-extensions' ),
		'shortcode_yellow'        => esc_html__( 'Yellow',       'jannah-extensions' ),
		'shortcode_size'          => esc_html__( 'Size',         'jannah-extensions' ),
		'shortcode_small'         => esc_html__( 'Small',        'jannah-extensions' ),
		'shortcode_medium'        => esc_html__( 'Medium',       'jannah-extensions' ),
		'shortcode_big'           => esc_html__( 'Big',          'jannah-extensions' ),
		'shortcode_link'          => esc_html__( 'Link',         'jannah-extensions' ),
		'shortcode_text'          => esc_html__( 'Text',         'jannah-extensions' ),
		'shortcode_icon'          => esc_html__( 'Icon (use full Font Awesome name)', 'jannah-extensions' ),
		'shortcode_new_window'    => esc_html__( 'Open link in a new window/tab',     'jannah-extensions' ),
		'shortcode_tabs'          => esc_html__( 'Tabs',                   'jannah-extensions' ),
		'shortcode_tab_title1'    => esc_html__( 'Tab 1 Title',            'jannah-extensions' ),
		'shortcode_tab_title2'    => esc_html__( 'Tab 2 Title',            'jannah-extensions' ),
		'shortcode_tab_title3'    => esc_html__( 'Tab 3 Title',            'jannah-extensions' ),
		'shortcode_tab_content1'  => esc_html__( 'Tab 1 | Your Content',   'jannah-extensions' ),
		'shortcode_tab_content2'  => esc_html__( 'Tab 2 | Your Content',   'jannah-extensions' ),
		'shortcode_tab_content3'  => esc_html__( 'Tab 3 | Your Content',   'jannah-extensions' ),
		'shortcode_slide1'        => esc_html__( 'Slide 1 | Your Content', 'jannah-extensions' ),
		'shortcode_slide2'        => esc_html__( 'Slide 2 | Your Content', 'jannah-extensions' ),
		'shortcode_slide3'        => esc_html__( 'Slide 3 | Your Content', 'jannah-extensions' ),
		'shortcode_vertical'      => esc_html__( 'Vertical',               'jannah-extensions' ),
		'shortcode_horizontal'    => esc_html__( 'Horizontal',             'jannah-extensions' ),
		'shortcode_toggle'        => esc_html__( 'Toggle Box',             'jannah-extensions' ),
		'shortcode_title'         => esc_html__( 'Title',                  'jannah-extensions' ),
		'shortcode_state'         => esc_html__( 'State',                  'jannah-extensions' ),
		'shortcode_opened'        => esc_html__( 'Opened',                 'jannah-extensions' ),
		'shortcode_closed'        => esc_html__( 'Closed',                 'jannah-extensions' ),
		'shortcode_slideshow'     => esc_html__( 'Content Slideshow',      'jannah-extensions' ),
		'shortcode_bio'           => esc_html__( 'Author Bio',             'jannah-extensions' ),
		'shortcode_avatar'        => esc_html__( 'Author Image URL',       'jannah-extensions' ),
		'shortcode_flickr'        => esc_html__( 'Flickr',                 'jannah-extensions' ),
		'shortcode_add_flickr'    => esc_html__( 'Add photos from Flickr', 'jannah-extensions' ),
		'shortcode_flickr_id'     => esc_html__( 'Account ID  ( get it from http//idgettr.com )', 'jannah-extensions' ),
		'shortcode_flickr_num'    => esc_html__( 'Number of photos',       'jannah-extensions' ),
		'shortcode_sorting'       => esc_html__( 'Sorting',                'jannah-extensions' ),
		'shortcode_recent'        => esc_html__( 'Recent',                 'jannah-extensions' ),
		'shortcode_random'        => esc_html__( 'Random',                 'jannah-extensions' ),
		'shortcode_feed'          => esc_html__( 'Feeds',                  'jannah-extensions' ),
		'shortcode_feed_url'      => esc_html__( 'URL of the RSS feed',    'jannah-extensions' ),
		'shortcode_feeds_num'     => esc_html__( 'Number of Feeds',        'jannah-extensions' ),
		'shortcode_map'           => esc_html__( 'Google Maps',            'jannah-extensions' ),
		'shortcode_map_url'       => esc_html__( 'Google Maps URL',        'jannah-extensions' ),
		'shortcode_height'        => esc_html__( 'Height',                 'jannah-extensions' ),
		'shortcode_video'         => esc_html__( 'Video',                  'jannah-extensions' ),
		'shortcode_video_url'     => esc_html__( 'Video URL',              'jannah-extensions' ),
		'shortcode_audio'         => esc_html__( 'Audio',                  'jannah-extensions' ),
		'shortcode_mp3'           => esc_html__( 'MP3 file URL',           'jannah-extensions' ),
		'shortcode_m4a'           => esc_html__( 'M4A file URL',           'jannah-extensions' ),
		'shortcode_ogg'           => esc_html__( 'OGG file URL',           'jannah-extensions' ),
		'shortcode_lightbox'      => esc_html__( 'Lightbox',               'jannah-extensions' ),
		'shortcode_lightbox_url'  => esc_html__( 'Full Image or YouTube / Vimeo Video URL', 'jannah-extensions' ),
		'shortcode_tooltip'       => esc_html__( 'Tooltip',                'jannah-extensions' ),
		'shortcode_direction'     => esc_html__( 'Direction',              'jannah-extensions' ),
		'shortcode_top'           => esc_html__( 'Top',                    'jannah-extensions' ),
		'shortcode_left'          => esc_html__( 'Left',                   'jannah-extensions' ),
		'shortcode_right'         => esc_html__( 'Right',                  'jannah-extensions' ),
		'shortcode_bottom'        => esc_html__( 'Bottom',                 'jannah-extensions' ),
		'shortcode_share'         => esc_html__( 'Share Buttons',          'jannah-extensions' ),
		'shortcode_facebook'      => esc_html__( 'Facebook Like Button',   'jannah-extensions' ),
		'shortcode_tweet'         => esc_html__( 'Tweet Button',           'jannah-extensions' ),
		'shortcode_pinterest'		  => esc_html__( 'Pinterest Button',       'jannah-extensions' ),
		'shortcode_follow'			  => esc_html__( 'Twitter Follow Button',  'jannah-extensions' ),
		'shortcode_username'		  => esc_html__( 'Twitter Username',       'jannah-extensions' ),
		'shortcode_login'			    => esc_html__( 'Login Form',             'jannah-extensions' ),
		'shortcode_tags'          => esc_html__( 'Tags Cloud',             'jannah-extensions' ),
		'shortcode_dropcap'			  => esc_html__( 'Dropcap',                'jannah-extensions' ),
		'shortcode_highlight'		  => esc_html__( 'Highlight Text',         'jannah-extensions' ),
		'shortcode_padding'			  => esc_html__( 'Padding',                'jannah-extensions' ),
		'shortcode_padding_top'   => esc_html__( 'Padding Top',            'jannah-extensions' ),
		'shortcode_padding_bottom'=> esc_html__( 'Padding Bottom',        'jannah-extensions' ),
		'shortcode_padding_right' => esc_html__( 'Padding right',          'jannah-extensions' ),
		'shortcode_padding_left'  => esc_html__( 'Padding Left',           'jannah-extensions' ),
		'shortcode_divider'       => esc_html__( 'Divider Line',           'jannah-extensions' ),
		'shortcode_solid'         => esc_html__( 'Solid',                  'jannah-extensions' ),
		'shortcode_dashed'        => esc_html__( 'Dashed',                 'jannah-extensions' ),
		'shortcode_normal'        => esc_html__( 'Normal',                 'jannah-extensions' ),
		'shortcode_double'        => esc_html__( 'Double',                 'jannah-extensions' ),
		'shortcode_dotted'        => esc_html__( 'Dotted',                 'jannah-extensions' ),
		'shortcode_margin_top'    => esc_html__( 'Margin Top',             'jannah-extensions' ),
		'shortcode_margin_bottom' => esc_html__( 'Margin Bottom',          'jannah-extensions' ),
		'shortcode_lists'         => esc_html__( 'Lists',                  'jannah-extensions' ),
		'shortcode_star'          => esc_html__( 'Star',                   'jannah-extensions' ),
		'shortcode_check'         => esc_html__( 'Check',                  'jannah-extensions' ),
		'shortcode_thumb_up'      => esc_html__( 'Thumb Up',               'jannah-extensions' ),
		'shortcode_thumb_down'    => esc_html__( 'Thumb Down',             'jannah-extensions' ),
		'shortcode_plus'          => esc_html__( 'Plus',                   'jannah-extensions' ),
		'shortcode_minus'         => esc_html__( 'Minus',                  'jannah-extensions' ),
		'shortcode_heart'         => esc_html__( 'Heart',                  'jannah-extensions' ),
		'shortcode_light_bulb'    => esc_html__( 'Light Bulb',             'jannah-extensions' ),
		'shortcode_cons'          => esc_html__( 'Cons',                   'jannah-extensions' ),
		'shortcode_ads'           => esc_html__( 'Ads',                    'jannah-extensions' ),
		'shortcode_ads1'          => esc_html__( 'Ads Shortcode 1',        'jannah-extensions' ),
		'shortcode_ads2'          => esc_html__( 'Ads Shortcode 2',        'jannah-extensions' ),
		'shortcode_ads3'          => esc_html__( 'Ads Shortcode 3',        'jannah-extensions' ),
		'shortcode_ads4'          => esc_html__( 'Ads Shortcode 4',        'jannah-extensions' ),
		'shortcode_ads5'          => esc_html__( 'Ads Shortcode 5',        'jannah-extensions' ),
		'shortcode_columns'       => esc_html__( 'Columns',                'jannah-extensions' ),
		'shortcode_add_content'   => esc_html__( 'Add content here',       'jannah-extensions' ),
		'shortcode_full_img'      => esc_html__( 'Full Width Image',       'jannah-extensions' ),
		'shortcode_index'         => esc_html__( 'Content Index',          'jannah-extensions' ),
		'shortcode_Restrict'      => esc_html__( 'Restrict Content',       'jannah-extensions' ),
		'shortcode_registered'    => esc_html__( 'For Registered Users only', 'jannah-extensions' ),
		'shortcode_guests'        => esc_html__( 'For Guests only',           'jannah-extensions' ),
	);

	wp_localize_script( 'jquery-ui-core', 'jannah_extensions_lang', $lang_text );
}


/*-----------------------------------------------------------------------------------*/
# Fix Shortcodes
/*-----------------------------------------------------------------------------------*/
add_filter( 'the_content', 'jannah_extensions_sc_fix_shortcodes' );
function jannah_extensions_sc_fix_shortcodes( $content ){

	$array_to_avoid = array(
		'<p>[dropcap'   => '<p_tag>[dropcap',
		'<p>[highlight' => '<p_tag>[highlight',
	);

	$content = strtr( $content, $array_to_avoid );

	$array = array(
		'[raw]'        => '',
		'[/raw]'       => '',
		'<p>[raw]'     => '',
		'[/raw]</p>'   => '',
		'[/raw]<br />' => '',
		'<p>['         => '[',
		']</p>'        => ']',
		']<br />'      => ']',
		']<br>'        => ']',
	);

	$content = strtr( $content, $array );

	return str_replace( '<p_tag>', '<p>', $content );
}


/*-----------------------------------------------------------------------------------*/
# Old Review Shortcode
/*-----------------------------------------------------------------------------------*/
if( function_exists( 'taqyeem_shortcode_review' ) ){
	add_shortcode( 'review', 'taqyeem_shortcode_review' );
}


/*-----------------------------------------------------------------------------------*/
# WP 3.6.0 | # For old theme versions Video shortcode
/*-----------------------------------------------------------------------------------*/
add_filter( 'the_content', 'jannah_extensions_sc_video_fix_shortcodes', 0);
function jannah_extensions_sc_video_fix_shortcodes( $content ){
	$videos  = '/(\[(video)\s?.*?\])(.+?)(\[(\/video)\])/';
	$content = preg_replace( $videos, '[embed]$3[/embed]', $content );
	return $content;
}


/*-----------------------------------------------------------------------------------*/
# Register the shortcode button
/*-----------------------------------------------------------------------------------*/
add_action( 'admin_init', 'jannah_extensions_sc_add_mce_button' );
function jannah_extensions_sc_add_mce_button() {
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	if ( 'true' == get_user_option( 'rich_editing' ) ) {
		add_filter( 'mce_external_plugins', 'jannah_extensions_sc_add_tinymce_plugin' );
		add_filter( 'mce_buttons',          'jannah_extensions_sc_register_mce_button' );
	}
}


/*-----------------------------------------------------------------------------------*/
# Add the button to the button array
/*-----------------------------------------------------------------------------------*/
function jannah_extensions_sc_register_mce_button( $buttons ) {
	array_push( $buttons, 'jannah_extensions_mce_button' );
	return $buttons;
}


/*-----------------------------------------------------------------------------------*/
# Declare script for new button
/*-----------------------------------------------------------------------------------*/
function jannah_extensions_sc_add_tinymce_plugin( $plugin_array ) {
	$plugin_array['jannah_extensions_mce_button'] = plugins_url( 'assets/js/mce.js', __FILE__ );
	return $plugin_array;
}





/*-----------------------------------------------------------------------------------*/
# [tie_tags] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tie_tags', 'jannah_extensions_sc_tags' );
function jannah_extensions_sc_tags( $atts, $content = null ) {

	$args = array(
		'smallest' => 8,
		'largest'  => 22,
		'unit'     => 'pt',
		'number'   => 0,
		'echo'     => false,
	);
	return '
		<div class="tags-shortcode">'.
			wp_tag_cloud( $args ) .'
		</div><!-- .tags-shortcode /-->
	';
}


/*-----------------------------------------------------------------------------------*/
# [tie_login] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tie_login', 'jannah_extensions_sc_login' );
function jannah_extensions_sc_login( $atts, $content = null ) {

	if( ! function_exists( 'tie_login_form' ) ){
		return;
	}

	ob_start();

	tie_login_form();

	return '
		<div class="login-widget container-wrapper login-shortcode">'.
			ob_get_clean() .'
		</div><!-- .login-shortcode /-->
	';
}


/*-----------------------------------------------------------------------------------*/
# [ads1] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'ads1', 'jannah_extensions_sc_ads1' );
function jannah_extensions_sc_ads1( $atts, $content = null ) {

	if( ! function_exists( 'tie_get_option') || ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) ){
		return;
	}

	return '
		<div class="stream-item stream-item-in-post stream-item-in-post-1">'.
			do_shortcode( apply_filters( 'TieLabs/custom_ad_code', tie_get_option( 'ads1_shortcode' ) ) ) .'
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [ads2] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'ads2', 'jannah_extensions_sc_ads2' );
function jannah_extensions_sc_ads2( $atts, $content = null ) {

	if( ! function_exists( 'tie_get_option') || ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) ){
		return;
	}

	return '
		<div class="stream-item stream-item-in-post stream-item-in-post-2">'.
			do_shortcode( apply_filters( 'TieLabs/custom_ad_code', tie_get_option( 'ads2_shortcode' ) ) ) .'
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [ads3] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'ads3', 'jannah_extensions_sc_ads3' );
function jannah_extensions_sc_ads3( $atts, $content = null ) {

	if( ! function_exists( 'tie_get_option') || ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) ){
		return;
	}

	return '
		<div class="stream-item stream-item-in-post stream-item-in-post-3">'.
			do_shortcode( apply_filters( 'TieLabs/custom_ad_code', tie_get_option( 'ads3_shortcode' ) ) ) .'
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [ads4] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'ads4', 'jannah_extensions_sc_ads4' );
function jannah_extensions_sc_ads4( $atts, $content = null ) {

	if( ! function_exists( 'tie_get_option') || ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) ){
		return;
	}

	return '
		<div class="stream-item stream-item-in-post stream-item-in-post-4">'.
			do_shortcode( apply_filters( 'TieLabs/custom_ad_code', tie_get_option( 'ads4_shortcode' ) ) ) .'
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [ads5] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'ads5', 'jannah_extensions_sc_ads5' );
function jannah_extensions_sc_ads5( $atts, $content = null ) {

	if( ! function_exists( 'tie_get_option') || ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) ){
		return;
	}

	return '
		<div class="stream-item stream-item-in-post stream-item-in-post-5">'.
			do_shortcode( apply_filters( 'TieLabs/custom_ad_code', tie_get_option( 'ads5_shortcode' ) ) ) .'
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [box] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'box', 'jannah_extensions_sc_box' );
function jannah_extensions_sc_box( $atts, $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'type'  => 'shadow',
		'align' => '',
		'class' => '',
		'width' => '',
	));

	extract( $atts );

	if( ! empty( $width ) ){
		$width = ' style="width:'.$width.'"';
	}

	return '
		<div class="box '.$type.' '.$class.' '.$align.'"'.$width.'>
			<div class="box-inner-block">
				<span class="fa tie-shortcode-boxicon"></span>'.
					do_shortcode( $content ) .'
			</div>
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [lightbox] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'lightbox', 'jannah_extensions_sc_lightbox' );
function jannah_extensions_sc_lightbox( $atts, $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'full'  => '',
		'title' => '',
	));

	extract( $atts );

	if( function_exists( 'tie_get_video_embed' )){
		$full = tie_get_video_embed( $full );
	}

	return '<a class="lightbox-enabled" href="'. esc_url( $full ) .'" data-caption="'. $title .'" title="'. $title .'">'. do_shortcode( $content ) .'</a>';
}


/*-----------------------------------------------------------------------------------*/
# [toggle] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'toggle', 'jannah_extensions_sc_toggle' );
function jannah_extensions_sc_toggle( $atts, $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'state' => 'open',
		'title' => '',
	));

	extract( $atts );

	$state = ( $state == 'open' ) ? 'tie-sc-open' : 'tie-sc-close';

	return '
		<div class="clearfix"></div>
		<div class="toggle '. $state .'">
			<h3 class="toggle-head">'. $title .' <span class="fa fa-angle-down" aria-hidden="true"></span></h3>
			<div class="toggle-content">'.
				do_shortcode( $content ) .'
			</div>
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [author] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'author', 'jannah_extensions_sc_author_info' );
function jannah_extensions_sc_author_info( $atts, $content = null ) {

	$title = esc_html__( 'About the author', 'jannah-extensions' );

	$atts = wp_parse_args( $atts, array(
		'image' => '',
	));

	extract( $atts );

	return '
		<div class="clearfix"></div>
		<div class="about-author about-author-box container-wrapper">
			<div class="author-avatar">
				<img src="'. esc_attr( $image ) .'" alt="">
			</div>
			<div class="author-info">
				<h4>'. $title .'</h4>'.
					do_shortcode( $content ) .'
			</div>
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [button] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'button', 'jannah_extensions_sc_button' );
function jannah_extensions_sc_button( $atts, $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'size'      => 'small',
		'color'     => 'primary',
		'link'      => '',
		'nofollow'  => '',
		'align'     => '',
		'icon'      => '',
		'target'    => '',
	));

	extract( $atts );

	$nofollow = ( $nofollow == 'true' ) ? ' rel="nofollow"' : '';
	$target   = ( $target == 'true' ) ? ' target="_blank"' : '';

	if( ! empty( $icon ) ){
		$icon = apply_filters( 'tie/extensions/shortcodes/button/icon', "fa $icon" );
		$icon = '<span class="'. $icon .'" aria-hidden="true"></span> ';
	}

	if( $color == 'primary' ){
		$color = 'button';
	}

	//$content = 'Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large Large';

	return '<a href="'. esc_url( $link ) .'"'. $target . $nofollow .' class="shortc-button '. $size .' '. $color .' '. $align .'">'. $icon . do_shortcode( $content ). '</a>';
}


/*-----------------------------------------------------------------------------------*/
# [flickr] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'flickr', 'jannah_extensions_sc_flickr' );
function jannah_extensions_sc_flickr( $atts, $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'number'  => 5,
		'orderby' => 'random',
		'id'      => '',
	));

	extract( $atts );

	return '
		<div class="flickr-wrapper">
			<script src="https://www.flickr.com/badge_code_v2.gne?count='. $number .'&amp;display='. $orderby .'&amp;size=s&amp;layout=x&amp;source=user&amp;user='. $id .'"></script>
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [feed] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'feed', 'jannah_extensions_sc_feed' );
function jannah_extensions_sc_feed( $atts, $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'number' => 5,
		'url'    => '',
	));

	extract( $atts );

	if( empty( $url )){
		return;
	}

	include_once( ABSPATH . WPINC . '/feed.php' );

	$rss = fetch_feed( $url );

	if ( is_wp_error( $rss ) ){
		return;
	}

	$maxitems  = $rss->get_item_quantity( $number );
	$rss_items = $rss->get_items( 0, $maxitems );

	$out = '<ul>';

	if ( ( isset( $maxitems ) && $maxitems == 0 ) || empty( $maxitems ) ) {
		$out .= '<li>'. esc_html__( 'No items.', 'jannah-extensions' ) .'</li>';
	}
	else{
		foreach ( $rss_items as $item ){
			$out .= '<li><a target="_blank" href="'. esc_url( $item->get_permalink() ) .'" title="'.  esc_html__( 'Posted', 'jannah-extensions' ) .' '. $item->get_date( 'j F Y | g:i a' ).'">'. esc_html( $item->get_title() ) .'</a></li>';
		}
	}

	$out .='</ul>';

	return $out;
}


/*-----------------------------------------------------------------------------------*/
# [googlemap] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'googlemap', 'jannah_extensions_sc_googlemap' );
function jannah_extensions_sc_googlemap( $atts, $content = null ) {

	if( ! empty( $atts['src'] ) && function_exists( 'tie_google_maps' ) ){

		if( ! function_exists( 'tie_google_maps' )){
			return $atts['src'];
		}

		return tie_google_maps( $atts['src'] );
	}
}


/*-----------------------------------------------------------------------------------*/
# [is_logged_in] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'is_logged_in', 'jannah_extensions_sc_is_logged_in' );
function jannah_extensions_sc_is_logged_in( $atts, $content = null ) {

	if( is_user_logged_in() ){
		return do_shortcode( $content );
	}
}


/*-----------------------------------------------------------------------------------*/
# [is_guest] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'is_guest', 'jannah_extensions_sc_is_guest' );
function jannah_extensions_sc_is_guest( $atts, $content = null ) {

	if( ! is_user_logged_in() ){
		return do_shortcode( $content );
	}
}


/*-----------------------------------------------------------------------------------*/
# [follow] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'follow', 'jannah_extensions_sc_follow' );
function jannah_extensions_sc_follow( $atts, $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'count' => 'false',
		'id'    => '',
		'size'  => '',
	));

	extract( $atts );

	$size = ( $size == 'large' ) ? 'data-size="large"' : '';

	return '
		<a href="https://twitter.com/'. $id .'" class="twitter-follow-button" data-show-count="'. $count .'" '. $size .'>Follow @'. $id .'</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	';
}


/*-----------------------------------------------------------------------------------*/
# [tooltip] Shortcode | The OLD shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tooltip', 'jannah_extensions_sc_tooltip' );
function jannah_extensions_sc_tooltip( $atts, $content = null ) {

	$tooltip_title = ! empty( $atts['text'] ) ? $atts['text'] : '';
	$atts['text']  = $content;

	return jannah_extensions_sc_tie_tooltip( $atts, $tooltip_title );
}


/*-----------------------------------------------------------------------------------*/
# [tie_tooltip] Shortcode | The NEW shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tie_tooltip', 'jannah_extensions_sc_tie_tooltip' );
function jannah_extensions_sc_tie_tooltip( $atts, $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'text'    => 'false',
		'gravity' => '',
	));

	extract( $atts );


	if( $gravity == 'w' ){
		$gravity = 'left';
	}
	elseif( $gravity == 'e' ){
		$gravity = 'right';
	}
	elseif( $gravity == 's' || $gravity == 'sw' || $gravity == 'se' ){
		$gravity = 'bottom';
	}
	else{
		$gravity = 'top';
	}

	return '<a data-toggle="tooltip" data-placement="'. $gravity .'" class="post-tooltip tooltip-'. $gravity .'" title="'. $text .'">'. do_shortcode( $content ) .'</a>';
}


/*-----------------------------------------------------------------------------------*/
# [highlight] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'highlight', 'jannah_extensions_sc_highlight' );
function jannah_extensions_sc_highlight( $atts, $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'color' => 'yellow',
	));

	extract( $atts );

	return '<span class="tie-highlight tie-highlight-'. $color .'">'. $content .'</span>';
}


/*-----------------------------------------------------------------------------------*/
# [tie_full_img] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tie_full_img', 'jannah_extensions_sc_full_width_img' );
function jannah_extensions_sc_full_width_img( $atts, $content = null ) {
	return '
		<div class="tie-full-width-img">
			'. do_shortcode( $content ) .'
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [dropcap] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'dropcap', 'jannah_extensions_sc_dropcap' );
function jannah_extensions_sc_dropcap( $atts, $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'type' => '',
	));

	extract( $atts );

	return '<span class="tie-dropcap '. $type .'">'. $content .'</span>';
}


/*-----------------------------------------------------------------------------------*/
# [tie_list] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tie_list', 'jannah_extensions_sc_lists' );
function jannah_extensions_sc_lists( $atts, $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'type' => 'checklist',
	));

	extract( $atts );

	return '
		<div class="'. $type .' tie-list-shortcode">'.
			do_shortcode( $content ) .'
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [checklist] Shortcode | ** Old Versions replaced with tie_list
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'checklist', 'jannah_extensions_sc_checklist' );
function jannah_extensions_sc_checklist( $atts, $content = null ) {
	return '
		<div class="checklist tie-list-shortcode">'.
			do_shortcode( $content ) .'
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [starlist] Shortcode | ** Old Versions replaced with tie_list
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'starlist', 'jannah_extensions_sc_starlist' );
function jannah_extensions_sc_starlist( $atts, $content = null ) {
	return '
		<div class="starlist tie-list-shortcode">'.
			do_shortcode( $content ) .'
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [facebook] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'facebook', 'jannah_extensions_sc_facebook' );
function jannah_extensions_sc_facebook( $atts, $content = null ) {
	$post_id = get_the_ID();
	return '<iframe src="https://www.facebook.com/plugins/like.php?href='. get_permalink( $post_id ) .'&amp;layout=box_count&amp;show_faces=false&amp;width=100&amp;action=like&amp;font&amp;colorscheme=light&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:50px; height:65px;" allowTransparency="true" async></iframe>';
}


/*-----------------------------------------------------------------------------------*/
# [digg] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'digg', 'jannah_extensions_sc_digg' );
function jannah_extensions_sc_digg( $atts, $content = null ) {
	return;
}


/*-----------------------------------------------------------------------------------*/
# [tweet] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tweet', 'jannah_extensions_sc_tweet' );
function jannah_extensions_sc_tweet( $atts, $content = null ) {
	$post_id  = get_the_ID();
	$username = '';

	if( function_exists( 'tie_get_option' )){
		$username = tie_get_option( 'share_twitter_username' );
	}

	return '<a href="'. esc_url( 'https://twitter.com/share' ) .'" class="twitter-share-button" data-url="'. get_permalink( $post_id ) .'" data-text="'. get_the_title( $post_id ) .'" data-via="'. $username .'" data-lang="en" data-count="vertical" >tweet</a><script async src="http://platform.twitter.com/widgets.js"></script>';
}


/*-----------------------------------------------------------------------------------*/
# [stumble] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'stumble', 'jannah_extensions_sc_stumble' );
function jannah_extensions_sc_stumble( $atts, $content = null ) {
	return;
}


/*-----------------------------------------------------------------------------------*/
# [pinterest] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'pinterest', 'jannah_extensions_sc_pinterest' );
function jannah_extensions_sc_pinterest( $atts, $content = null ) {
	$post_id = get_the_ID();

	return '
		<script src="//assets.pinterest.com/js/pinit.js"></script>
		<a href="http://pinterest.com/pin/create/button/?url='. get_permalink( $post_id ) .'&amp;media='. get_the_post_thumbnail( $post_id, 'large' ) .'" class="pin-it-button" count-layout="vertical">
			<img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" />
		</a>
	';
}


/*-----------------------------------------------------------------------------------*/
# [tie_slideshow] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tie_slideshow', 'jannah_extensions_sc_post_slideshow' );
function jannah_extensions_sc_post_slideshow( $atts, $content = null ) {

	$loader_icon = function_exists( 'tie_get_ajax_loader' ) ? tie_get_ajax_loader( false ) : '';

	do_action( 'tie_extensions_sc_before_post_slideshow' );

	return "
		<div class=\"post-content-slideshow-outer\">
			<div class=\"post-content-slideshow\">

			$loader_icon

				<div class=\"tie-slick-slider\">" .

					do_shortcode( $content ) ."

					<div class=\"slider-nav-wrapper\">
						<ul class=\"tie-slider-nav\"></ul>
					</div>
				</div><!-- tie-slick-slider -->
			</div><!-- post-content-slideshow -->
		</div><!-- post-content-slideshow-outer -->
	";
}


/*-----------------------------------------------------------------------------------*/
# [tie_slide] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tie_slide', 'jannah_extensions_sc_post_slide' );
function jannah_extensions_sc_post_slide( $atts, $content = null ) {
	return '
		<div class="slide post-content-slide">
			'.do_shortcode( $content ) .'
		</div><!-- post-content-slide -->
	';
}


/*-----------------------------------------------------------------------------------*/
# [tabs] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tabs', 'jannah_extensions_sc_tabs' );
function jannah_extensions_sc_tabs( $atts, $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'type' => '',
	));

	extract( $atts );

	$class_type = ( $type == 'vertical' ) ? 'tabs-vertical' : 'tabs-horizontal flex-tabs is-flex-tabs-shortcodes';

	return '
		<div class="tabs-shortcode tabs-wrapper container-wrapper '. $class_type .'">'.
			do_shortcode( $content ) .'
			<div class="clearfix"></div>
		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# [tab] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tab', 'jannah_extensions_sc_tab' );
function jannah_extensions_sc_tab( $atts, $content = null ) {
	STATIC $id = 1;

	$out ='
		<div class="tab-content" id="tab-content-'. $id .'">
			<div class="tab-content-wrap">'.
				do_shortcode( $content ) .'
			</div>
		</div>
	';

	$id++;

	return $out;
}


/*-----------------------------------------------------------------------------------*/
# [tabs_head] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tabs_head', 'jannah_extensions_sc_tabs_head' );
function jannah_extensions_sc_tabs_head( $atts, $content = null ) {
	return '
		<ul class="tabs">'.
			do_shortcode( $content ) .'
		</ul>
	';
}


/*-----------------------------------------------------------------------------------*/
# [tab_title] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tab_title', 'jannah_extensions_sc_tab_title' );
function jannah_extensions_sc_tab_title( $atts, $content = null ) {
	STATIC $id = 1;
	$out ='
		<li>
			<a href="#tab-content-' . $id . '">'.
				do_shortcode($content).'
			</a>
		</li>
	';

	$id++;

	return $out;
}


/*-----------------------------------------------------------------------------------*/
# [divider] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'divider', 'jannah_extensions_sc_divider' );
function jannah_extensions_sc_divider( $atts, $content = null ) {

	$atts = wp_parse_args( $atts, array(
		'style'  => 'normal',
		'top'    => 10,
		'bottom' => 10,
	));

	extract( $atts );

	return '
		<div class="clearfix"></div>
		<hr style="margin-top:'.$top.'px; margin-bottom:'.$bottom.'px;" class="divider divider-'.$style.'">
	';
}


/*-----------------------------------------------------------------------------------*/
# [tie_index] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'tie_index', 'jannah_extensions_sc_index' );
function jannah_extensions_sc_index( $atts, $content = null ) {

	if( ! function_exists( 'tie_get_option') || empty( $content )){
		return;
	}

	$index_id = sanitize_title( $content );
	$index_id = preg_replace( '/[^A-Za-z0-9\-]/', '', $index_id ); // Remove all special characters to fix an issue with non-latin languages

	return '
		<div id="'. $index_id .'" data-title="'. $content .'" class="index-title"></div>
	';
}



/*-----------------------------------------------------------------------------------*/
# [padding] Shortcode
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'padding', 'jannah_extensions_sc_padding' );
function jannah_extensions_sc_padding( $atts, $content = null ) {

	// Disable on Amp
	if( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) return $content;

	$atts = wp_parse_args( $atts, array(
		'top'    => '0',
		'bottom' => '0',
		'left'   => '0',
		'right'  => '0',
		'class'  => '',
	));

	extract( $atts );

	$class .= ! empty( $top  )   ? ' has-padding-top'    : '';
	$class .= ! empty( $bottom ) ? ' has-padding-bottom' : '';
	$class .= ! empty( $left  )  ? ' has-padding-left'   : '';
	$class .= ! empty( $right )  ? ' has-padding-right'  : '';

	return '
		<div class="tie-padding '.$class.'" style="padding-left:'.$left.'; padding-right:'.$right.'; padding-top:'.$top.'; padding-bottom:'.$bottom.';">

			<p>'. do_shortcode( $content ) .'</p>

		</div>
	';
}


/*-----------------------------------------------------------------------------------*/
# Columns Shortcodes
/*-----------------------------------------------------------------------------------*/
add_shortcode( 'one_third', 'jannah_extensions_one_third' );
function jannah_extensions_one_third( $atts, $content = null ) {
	return '
		<div class="one_third tie-columns">'.
			do_shortcode( $content ) .'
		</div>
	';
}

add_shortcode( 'one_third_last', 'jannah_extensions_one_third_last' );
function jannah_extensions_one_third_last( $atts, $content = null ) {
	return '
		<div class="one_third tie-columns last">'.
			do_shortcode( $content ) .'
		</div>
		<div class="clearfix"></div>
	';
}

add_shortcode( 'two_third', 'jannah_extensions_two_third' );
function jannah_extensions_two_third( $atts, $content = null ) {
	return '
		<div class="two_third tie-columns">'.
			do_shortcode( $content ) .'
		</div>
	';
}

add_shortcode( 'two_third_last', 'jannah_extensions_two_third_last' );
function jannah_extensions_two_third_last( $atts, $content = null ) {
	return '
		<div class="two_third tie-columns last">'.
			do_shortcode( $content ) .'
		</div>
		<div class="clearfix"></div>
	';
}

add_shortcode( 'one_half', 'jannah_extensions_one_half' );
function jannah_extensions_one_half( $atts, $content = null ) {
	return '
		<div class="one_half tie-columns">'.
			do_shortcode( $content ) .'
		</div>
	';
}

add_shortcode( 'one_half_last', 'jannah_extensions_one_half_last' );
function jannah_extensions_one_half_last( $atts, $content = null ) {
	return '
		<div class="one_half tie-columns last">'.
			do_shortcode( $content ) .'
		</div>
		<div class="clearfix"></div>
	';
}

add_shortcode( 'one_fourth', 'jannah_extensions_one_fourth' );
function jannah_extensions_one_fourth( $atts, $content = null ) {
	return '
		<div class="one_fourth tie-columns">'.
			do_shortcode( $content ) .'
		</div>
	';
}

add_shortcode( 'one_fourth_last', 'jannah_extensions_one_fourth_last' );
function jannah_extensions_one_fourth_last( $atts, $content = null ) {
	return '
		<div class="one_fourth tie-columns last">'.
			do_shortcode( $content ) .'
		</div>
		<div class="clearfix"></div>
	';
}

add_shortcode( 'three_fourth', 'jannah_extensions_three_fourth' );
function jannah_extensions_three_fourth( $atts, $content = null ) {
	return '
		<div class="three_fourth tie-columns">'.
			do_shortcode( $content ) .'
		</div>
	';
}

add_shortcode( 'three_fourth_last', 'jannah_extensions_three_fourth_last' );
function jannah_extensions_three_fourth_last( $atts, $content = null ) {
	return '
		<div class="three_fourth tie-columns last">'.
			do_shortcode( $content ) .'
		</div>
		<div class="clearfix"></div>
	';
}

add_shortcode( 'one_fifth', 'jannah_extensions_one_fifth' );
function jannah_extensions_one_fifth( $atts, $content = null ) {
	return '
		<div class="one_fifth tie-columns">'.
			do_shortcode( $content ) .'
		</div>
	';
}

add_shortcode( 'one_fifth_last', 'jannah_extensions_one_fifth_last' );
function jannah_extensions_one_fifth_last( $atts, $content = null ) {
	return '
		<div class="one_fifth tie-columns last">'.
			do_shortcode( $content ) .'
		</div>
		<div class="clearfix"></div>
	';
}

add_shortcode( 'two_fifth', 'jannah_extensions_two_fifth' );
function jannah_extensions_two_fifth( $atts, $content = null ) {
	return '
		<div class="two_fifth tie-columns">'.
			do_shortcode( $content ) .'
		</div>
	';
}

add_shortcode( 'two_fifth_last', 'jannah_extensions_two_fifth_last' );
function jannah_extensions_two_fifth_last( $atts, $content = null ) {
	return '
		<div class="two_fifth tie-columns last">'.
			do_shortcode( $content ) .'
		</div>
		<div class="clearfix"></div>
	';
}

add_shortcode( 'three_fifth', 'jannah_extensions_three_fifth' );
function jannah_extensions_three_fifth( $atts, $content = null ) {
	return '
		<div class="three_fifth tie-columns">'.
			do_shortcode( $content ) .'
		</div>
	';
}

add_shortcode( 'three_fifth_last', 'jannah_extensions_three_fifth_last' );
function jannah_extensions_three_fifth_last( $atts, $content = null ) {
	return '
		<div class="three_fifth tie-columns last">'.
			do_shortcode( $content ) .'
		</div>
		<div class="clearfix"></div>
	';
}

add_shortcode( 'four_fifth', 'jannah_extensions_four_fifth' );
function jannah_extensions_four_fifth( $atts, $content = null ) {
	return '
		<div class="four_fifth tie-columns">'.
			do_shortcode( $content ) .'
		</div>
	';
}

add_shortcode( 'four_fifth_last', 'jannah_extensions_four_fifth_last' );
function jannah_extensions_four_fifth_last( $atts, $content = null ) {
	return '
		<div class="four_fifth tie-columns last">'.
			do_shortcode( $content ) .'
		</div>
		<div class="clearfix"></div>
	';
}

add_shortcode( 'one_sixth', 'jannah_extensions_one_sixth' );
function jannah_extensions_one_sixth( $atts, $content = null ) {
	return '
		<div class="one_sixth tie-columns">'.
			do_shortcode( $content ) .'
		</div>
	';
}

add_shortcode( 'one_sixth_last', 'jannah_extensions_one_sixth_last' );
function jannah_extensions_one_sixth_last( $atts, $content = null ) {
	return '
		<div class="one_sixth tie-columns last">'.
			do_shortcode( $content ) .'
		</div>
		<div class="clearfix"></div>
	';
}

add_shortcode( 'five_sixth', 'jannah_extensions_five_sixth' );
function jannah_extensions_five_sixth( $atts, $content = null ) {
	return '
		<div class="five_sixth tie-columns">'.
			do_shortcode( $content ) .'
		</div>
	';
}

add_shortcode( 'five_sixth_last', 'jannah_extensions_five_sixth_last' );
function jannah_extensions_five_sixth_last( $atts, $content = null ) {
	return '
		<div class="five_sixth tie-columns last">'.
			do_shortcode( $content ) .'
		</div>
		<div class="clearfix"></div>
	';
}

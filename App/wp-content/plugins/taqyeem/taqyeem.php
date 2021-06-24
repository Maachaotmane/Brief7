<?php
/*
	Plugin Name: Taqyeem
	Plugin URI: http://codecanyon.net/item/taqyeem-wordpress-review-plugin/4558799?ref=tielabs
	Description: WordPress Review Plugin -  adding lovely ratings and reviews to your posts, pages, and custom post types.
	Author: TieLabs
	Version: 2.6.2
	Author URI: http://tielabs.com/
*/

require_once( 'taqyeem-panel.php' );
require_once( 'taqyeem-posts.php' );
require_once( 'taqyeem-widgets.php' );
require_once( 'verification.php' );
require_once( 'updater.php' );


define ('TIE_TAQYEEM',       'Taqyeem' );
define ('TIE_Plugin_ver',    '2.6.2' );
define ('TAQYEEM_PLUGIN_ID', '4558799' );

$taqyeem_default_data = array(
	'taqyeem_options'	=> array(
		'allowtorate'     => 'both',
		'rating_image'    => 'stars',
		'structured_data' => 'true'
	)
);


/*-----------------------------------------------------------------------------------*/
# Load Text Domain
/*-----------------------------------------------------------------------------------*/
add_action( 'plugins_loaded', 'taqyeem_init' );
function taqyeem_init() {
	load_plugin_textdomain( 'taq', false, dirname( plugin_basename( __FILE__ ) ).'/languages' );
}


/*-----------------------------------------------------------------------------------*/
# Store Defaults settings
/*-----------------------------------------------------------------------------------*/
if ( is_admin() && isset($_GET['activate'] ) && $pagenow == 'plugins.php' ) {
	global $taqyeem_default_data;
	if( ! get_option('taq_active') ){
		taqyeem_save_settings( $taqyeem_default_data );
		update_option( 'taq_active' , TIE_Plugin_ver );
	}
}


/*-----------------------------------------------------------------------------------*/
# Get plugin's Settings
/*-----------------------------------------------------------------------------------*/
function taqyeem_get_option( $name ) {
	$get_options = get_option( 'taqyeem_options' );

	if( ! empty( $get_options[ $name ] )){
		return $get_options[ $name ];
	}
	return false;
}


/*-----------------------------------------------------------------------------------*/
# Register and Enquee plugin's styles and scripts
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_enqueue_scripts', 'taqyeem_scripts_styles' );
function taqyeem_scripts_styles(){
	wp_enqueue_script( 'taqyeem-main',  plugins_url( 'js/tie.js', __FILE__ ), array( 'jquery' ), false, false );
	wp_enqueue_style ( 'taqyeem-style', plugins_url( 'style.css', __FILE__ ));
}


/*-----------------------------------------------------------------------------------*/
# Disable Updater and Verification for TieLabs themes
/*-----------------------------------------------------------------------------------*/
add_action( 'init', 'taqyeem_disable_updater_verification' );
function taqyeem_disable_updater_verification(){
	if( function_exists( 'tie_get_option' ) ){
		add_filter( 'Taqyeem/Updater/disable',      '__return_true' );
		add_filter( 'Taqyeem/Verification/disable', '__return_true' );
	}
}


/*-----------------------------------------------------------------------------------*/
# Get Reviews Box
/*-----------------------------------------------------------------------------------*/
function taqyeem_get_review( $position = 'review-top' ){

	if( ! is_singular() && taqyeem_get_option( 'taq_singular' ) ){
		return false;
	}

	$post = get_post();

	$get_meta = get_post_custom( $post->ID );

	if( ! empty( $get_meta['taq_review_criteria'][0] )){
		$get_criteria = unserialize( $get_meta['taq_review_criteria'][0] );
	}

	// Review Data
	$summary       = ! empty( $get_meta['taq_review_summary'][0] ) ? htmlspecialchars_decode( $get_meta['taq_review_summary'][0] ) : '';
	$short_summary = ! empty( $get_meta['taq_review_total'][0] ) ? $get_meta['taq_review_total'][0] : '';
	$style         = ! empty( $get_meta['taq_review_style'][0] ) ? $get_meta['taq_review_style'][0] : 'stars';
	$image_style   = taqyeem_get_option('rating_image')          ? taqyeem_get_option('rating_image') : 'stars';

	$total_score = $total_counter = $score = $ouput = 0;

	// Get users rate
	$users_rate = '';
	if( taqyeem_get_option('allowtorate') != 'none' ){
		$users_rate = taqyeem_get_user_rate();
	}

	// Review Style
	$review_class = array(
		'review-box',
		$position,
	);

	if( $style == 'percentage' ){
		$review_class[] = 'review-percentage';
	}
	elseif( $style == 'points' ){
		$review_class[] = 'review-percentage';
	}
	else{
		$review_class[] = 'review-stars';
	}

	$review_class = apply_filters( 'taqyeem_reviews_box_classes', $review_class );

	$ouput = '
		<div class="review_wrap">
			<div id="review-box" class="'. join( ' ', array_filter( $review_class ) )  .'">';

			if( ! empty( $get_meta['taq_review_title'][0] )){
				$head_calss =  apply_filters( 'taqyeem_reviews_head_classes', 'review-box-header' );
				$ouput .= '<h2 class="'. $head_calss .'">'. $get_meta['taq_review_title'][0] .'</h2>';
			}

			if( ! empty( $get_criteria ) && is_array( $get_criteria )){
				foreach( $get_criteria as $criteria ){
					if( $criteria['name'] && is_numeric( $criteria['score'] )){

						$criteria['score'] = max( 0, min( 100, $criteria['score'] ) );

						$score += $criteria['score'];
						$total_counter ++;

						if( $style == 'percentage' ){
							$ouput .= '
								<div class="review-item">
									<span><h5>'. $criteria['name'] .' - '. $criteria['score'] .'%</h5><span style="width:'. $criteria['score'] .'%" data-width="'. $criteria['score'] .'"></span></span>
								</div>
							';
						}
						elseif( $style == 'points' ){
							$point  =  $criteria['score']/10;
							$ouput .= '
								<div class="review-item">
									<span><h5>'. $criteria['name'] .' - '. $point.'</h5><span style="width:'. $criteria['score'] .'%" data-width="'. $criteria['score'] .'"></span></span>
								</div>
							';
						}
						else{
							$ouput .= '
								<div class="review-item">
									<h5>'. $criteria['name'] .'</h5>
									<span class="post-large-rate '.$image_style.'-large"><span style="width:'. $criteria['score'] .'%"></span></span>
								</div>
							';
						}
					}
				}
			}

			if( has_filter ('tie_taqyeem_before_summary' )){
				$ouput = apply_filters('tie_taqyeem_before_summary', $ouput, $get_meta );
			}

			if( ! empty( $score ) && ! empty( $total_counter )){
				$total_score =  $score / $total_counter;
			}

			$ouput .= '
				<div class="review-summary">';

			if( $style == 'percentage' ){
				$ouput .= '
					<div class="review-final-score">
						<h3>'. round($total_score) .'<span>%</span></h3>
						<h4>'. $short_summary .'</h4>
					</div>
				';
			}

			elseif( $style == 'points' ){
				$total_score = $total_score/10;
				$ouput .= '
					<div class="review-final-score">
						<h3>'. round($total_score,1).'</h3>
						<h4>'. $short_summary .' </h4>
					</div>
				';

			}
			else{
				$ouput .= '
					<div class="review-final-score">
						<span title="'. $short_summary .'" class="post-large-rate '.$image_style.'-large"><span style="width:'. $total_score .'%"></span></span>
						<h4>'. $short_summary .'</h4>
					</div>
				';
			}

			$ouput .= '
				<div class="review-short-summary">';

					if( has_filter('tie_taqyeem_before_summary_text') ) {
						$ouput = apply_filters('tie_taqyeem_before_summary_text', $ouput, $get_meta );
					}

					if( ! empty( $summary ) ){
						$ouput .= '<p>'. $summary .'</p>';
					}

					if( has_filter( 'tie_taqyeem_after_summary_text' ) ) {
						$ouput = apply_filters('tie_taqyeem_after_summary_text', $ouput, $get_meta );
					}

					$ouput .= '
				</div>
			</div>
			';

			if( has_filter('tie_taqyeem_before_user_rating') ) {
				$ouput = apply_filters('tie_taqyeem_before_user_rating', $ouput, $get_meta );
			}

			$ouput .= $users_rate;

			if( has_filter('tie_taqyeem_after_user_rating') ) {
				$ouput = apply_filters('tie_taqyeem_after_user_rating', $ouput, $get_meta );
			}

			$ouput .='
		</div>
	</div>';

	$ouput = apply_filters('tie_taqyeem_after_review_box', $ouput, $get_meta );

	return $ouput;
}


/*-----------------------------------------------------------------------------------*/
# Hook the rich snippet
/*-----------------------------------------------------------------------------------*/
add_filter( 'tie_taqyeem_after_review_box', 'taqyeem_review_rich_snippet' );
function taqyeem_review_rich_snippet( $ouput ){

	if( ! apply_filters( 'tie_taqyeem_rich_snippets', true ) || ! taqyeem_get_option( 'structured_data' ) ){
		return $ouput;
	}

	// Get the rich snippet
	$schema = taqyeem_review_get_rich_snippet();

	// Print the schema
	if( $schema ){
		$ouput .= '<script type="application/ld+json">'. json_encode( $schema ) .'</script>';
	}

	return $ouput;
}


/*-----------------------------------------------------------------------------------*/
# Get the rich snippet
/*-----------------------------------------------------------------------------------*/
function taqyeem_review_get_rich_snippet(){

	$post    = get_post();
	$post_id = $post->ID;

	$schema_type = get_post_meta( $post_id, 'taq_review_structured_data', true );
	$schema_type = ! empty( $schema_type ) ? $schema_type : taqyeem_get_option( 'default_structured_data' );
	$schema_type = ! empty( $schema_type ) ? $schema_type : 'product';

	// Get he total score and convert it to 0 ~ 5
	$total_score = (int) get_post_meta( $post_id, 'taq_review_score', true );

	if( ! empty( $total_score ) && $total_score > 0 ){
		$total_score = round( ($total_score*5)/100, 1 );
	}

	// Post data
	$description    = ! empty( $post->post_content ) ? strip_shortcodes( apply_filters('taqyeem_exclude_content', $post->post_content )) : '';
	$description    = wp_html_excerpt( $description, 200 );
	$puplished_date = ( get_the_time( 'c' ) ) ? get_the_time( 'c' ) : get_the_modified_date( 'c' );
	$modified_date  = ( get_the_modified_date( 'c' ) ) ? get_the_modified_date( 'c' ) : $puplished_date;

	// The Scemas Array
	$schema = array(
		'@context'       => 'http://schema.org',
		'@type'          => 'review',
		'dateCreated'    => $puplished_date,
		'datePublished'  => $puplished_date,
		'dateModified'   => $modified_date,
		'headline'       => get_the_title(),
		'name'           => get_the_title(),
		'url'            => get_permalink(),
		'description'    => $description,
		'copyrightYear'  => get_the_time( 'Y' ),

		'publisher'      => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo(),
		),

		'author' => array(
			'@type'  => 'Person',
			'name'   => get_the_author(),
			'sameAs' => get_author_posts_url( get_the_author_meta( 'ID' ) ),
		),

		'itemReviewed' => array(
			'@type' => $schema_type,
			'name'  => get_the_title(),
		),

		'reviewBody'    => $description,
		'reviewRating' => array(
			'@type'       => 'Rating',
			'worstRating' => 1,
			'bestRating'  => 5,
			'ratingValue' => $total_score,
			'description' => get_post_meta( get_the_ID(), 'taq_review_summary', true ),
		),
	);

	// Post image
	$image_id   = get_post_thumbnail_id();
	$image_data = wp_get_attachment_image_src( $image_id, 'full' );

	if( ! empty( $image_data ) ){
		$schema['image'] = array(
			'@type'  => 'ImageObject',
			'url'    => $image_data[0],
			'width'  => ( $image_data[1] > 696 ) ? $image_data[1] : 696,
			'height' => $image_data[2],
		);

		$schema['itemReviewed']['image'] = $schema['image']['url'];
	}


	// Product
	if( $schema_type == 'product' ){

		$review = $schema;
		unset( $review['itemReviewed'] );

		$product_description = get_post_meta( $post_id, 'taq_review_structured_data_product_description', true );
		$product_description = ! empty( $product_description ) ? $product_description : $description;

		$schema = array(
			'@context'    => 'http://schema.org',
			'@type'       => $schema_type,
			'name'        => get_the_title(),
			'description' => $product_description,

			'sku'  => get_post_meta( $post_id, 'taq_review_structured_data_product_sku', true ),
			'mpn'  => get_post_meta( $post_id, 'taq_review_structured_data_product_mpn', true ),
			'gtin' => get_post_meta( $post_id, 'taq_review_structured_data_product_gtin', true ),

			'brand' => array(
				'@type' => 'Thing',
				'name'  => get_post_meta( $post_id, 'taq_review_structured_data_product_brand', true ),
			),

			'offers' => array(
				'@type'           => 'Offer',
				'url'             => get_post_meta( $post_id, 'taq_review_structured_data_product_url', true ),
				'price'           => get_post_meta( $post_id, 'taq_review_structured_data_product_price', true ),
				'priceCurrency'   => get_post_meta( $post_id, 'taq_review_structured_data_product_currency', true ),
				'availability'    => get_post_meta( $post_id, 'taq_review_structured_data_product_availability', true ),
				'priceValidUntil' => get_post_meta( $post_id, 'taq_review_structured_data_product_price_date', true ),
			),

			'review' => $review,
		);

		// aggregateRating
		$rate  = get_post_meta( $post_id, 'tie_user_rate', true );
		$count = get_post_meta( $post_id, 'tie_users_num', true );

		if( ! empty( $rate ) && ! empty( $count ) ){

			$totla_users_score = round( $rate/$count, 2 );
			$totla_users_score = ( $totla_users_score > 5 ) ? 5 : $totla_users_score;

				$schema['aggregateRating'] = array(
					'@type' => 'AggregateRating',
					'ratingValue' => $totla_users_score,
					'reviewCount' => $count,
				);
		}

		// Image
		if( ! empty( $review['image'] ) ){
			$schema['image'] = $review['image'];
		}
	}

	// Books
	elseif( $schema_type == 'book' ){
		$schema['itemReviewed']['author'] = array(
			'@type'  => 'Person',
			'name'   => get_post_meta( $post_id, 'taq_review_structured_data_author', true ),
			'sameAs' => get_post_meta( $post_id, 'taq_review_structured_data_author_url', true ),
		);

		$schema['itemReviewed']['isbn'] = get_post_meta( $post_id, 'taq_review_structured_data_book_isbn', true );
	}

	// Event
	elseif( $schema_type == 'event' ){
		$schema['itemReviewed']['description'] = $description;
		$schema['itemReviewed']['location']    = array(
			'@type'   => 'Place',
			'name'    => get_post_meta( $post_id, 'taq_review_structured_data_event_location_name', true ),
			'address' => get_post_meta( $post_id, 'taq_review_structured_data_event_location_address', true ),
		);
		$schema['itemReviewed']['startDate']   = get_post_meta( $post_id, 'taq_review_structured_data_event_startdate', true );
		$schema['itemReviewed']['endDate']     = get_post_meta( $post_id, 'taq_review_structured_data_event_enddate', true );
	}

	// Movie
	elseif( $schema_type == 'movie' ){
		$schema['itemReviewed']['sameAs']      = get_post_meta( $post_id, 'taq_review_structured_data_movie_url', true );
		$schema['itemReviewed']['dateCreated'] = get_post_meta( $post_id, 'taq_review_structured_data_movie_date', true );
		$schema['itemReviewed']['director']    = get_post_meta( $post_id, 'taq_review_structured_data_movie_director', true );
	}

	// Course
	elseif( $schema_type == 'course' ){
		$schema['itemReviewed']['description'] = get_post_meta( $post_id, 'taq_review_structured_data_description', true );
		$schema['itemReviewed']['provider']    = get_post_meta( $post_id, 'taq_review_structured_data_course_provider', true );
	}

	// restaurant
	elseif( $schema_type == 'restaurant' ){
		$schema['itemReviewed']['address']       = get_post_meta( $post_id, 'taq_review_structured_data_restaurant_address', true );
		$schema['itemReviewed']['priceRange']    = get_post_meta( $post_id, 'taq_review_structured_data_restaurant_price', true );
		$schema['itemReviewed']['servesCuisine'] = get_post_meta( $post_id, 'taq_review_structured_data_restaurant_cuisine', true );
		$schema['itemReviewed']['telephone']     = get_post_meta( $post_id, 'taq_review_structured_data_restaurant_telephone', true );
	}

	// --
	return apply_filters( 'tie_taqyeem_rich_snippets_code', $schema );;
}


/*-----------------------------------------------------------------------------------*/
# Get Reviews Box
/*-----------------------------------------------------------------------------------*/
add_filter( 'the_content', 'taqyeem_insert_review' );
function taqyeem_insert_review( $content ){

	if( in_array('get_the_excerpt', $GLOBALS['wp_current_filter'])){
		return $content;
	}

	$post_id = get_the_ID();

	if( is_feed() ){
		return $content;
	}

	$get_meta = get_post_custom( $post_id );

	if( ! empty( $get_meta['taq_review_position'][0] )){
		$review_position = $get_meta['taq_review_position'][0];
	}

	$output = $output2 = '';

	if( ! empty( $review_position ) && $review_position == 'top'){
		$output  = taqyeem_get_review('review-top');
	}

	if( ! empty( $review_position ) && $review_position == 'bottom' ){
		$output2 = taqyeem_get_review('review-bottom');
	}

	return $output . $content . $output2;
}




/*-----------------------------------------------------------------------------------*/
# Users rate posts function
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_taqyeem_rate_post',        'taqyeem_rate_post' );
add_action( 'wp_ajax_nopriv_taqyeem_rate_post', 'taqyeem_rate_post' );
function taqyeem_rate_post(){

	if( taqyeem_get_option('allowtorate') == 'none' || ( is_user_logged_in() && taqyeem_get_option('allowtorate') == 'guests' ) || ( ! is_user_logged_in() && taqyeem_get_option( 'allowtorate' ) == 'users' )){
		return false;
	}

	# Get user rate data
	$post_id = $_REQUEST['post'];
	$rate    = abs( $_REQUEST['value'] );

	if( $rate > 5 ){
		$rate = 5;
	}

	# Get stored post data
	$rating = get_post_meta( $post_id, 'tie_user_rate', true );
	$count 	= get_post_meta( $post_id, 'tie_users_num', true );

	if( empty( $count ) || $count == '' ){
		$count = 0;
	}

	$count++;
	$total_rate = $rating + $rate;
	$total      = round( $total_rate/$count, 2 );

	# Registered user rate
	if ( is_user_logged_in() ) {

		$current_user = wp_get_current_user();
		$user_id      = $current_user->ID;
		$user_rated   = get_the_author_meta( 'tie_rated', $user_id );

		if( empty( $user_rated ) ){

			$user_rated[ $post_id ] = $rate;

			update_user_meta( $user_id, 'tie_rated',     $user_rated );
			update_post_meta( $post_id, 'tie_user_rate', $total_rate );
			update_post_meta( $post_id, 'tie_users_num', $count );

			echo $total;
		}

		else{
			if( ! array_key_exists( $post_id, $user_rated )){

				$user_rated[ $post_id ] = $rate;

				update_user_meta( $user_id, 'tie_rated',     $user_rated );
				update_post_meta( $post_id, 'tie_user_rate', $total_rate );
				update_post_meta( $post_id, 'tie_users_num', $count );

				echo $total;
			}
		}
	}

	# Guests rate
	else{
		$user_rated = $_COOKIE[ 'tie_rate_'.$post_id ];

		if( empty( $user_rated )){
			setcookie( 'tie_rate_'.$post_id , $rate , time()+31104000 , '/');
			update_post_meta( $post_id, 'tie_user_rate', $total_rate );
			update_post_meta( $post_id, 'tie_users_num', $count );
		}
	}

	die;
}


/*-----------------------------------------------------------------------------------*/
# Get user rate result
/*-----------------------------------------------------------------------------------*/
function taqyeem_get_user_rate(){

	$post_id = get_the_ID();
	$disable_rate = false ;

	if( taqyeem_get_option('allowtorate') == 'none' || ( is_user_logged_in() && taqyeem_get_option('allowtorate') == 'guests' ) || ( ! is_user_logged_in() && taqyeem_get_option( 'allowtorate' ) == 'users' )){
		$disable_rate = true ;
	}

	if( ! empty( $disable_rate )){
		$no_rate_text = __( 'No Ratings Yet !' , 'taq' );
		$rate_active  = false;
	}
	else{
		$no_rate_text = __( 'Be the first one !' , 'taq' );
		$rate_active  = ' taq-user-rate-active';
	}

	$image_style = taqyeem_get_option('rating_image') ? taqyeem_get_option('rating_image') : 'stars';

	$rate  = get_post_meta( $post_id, 'tie_user_rate', true );
	$count = get_post_meta( $post_id, 'tie_users_num', true );

	if( ! empty( $rate ) && !empty( $count )){

		$total = ( ($rate/$count)/5 )*100;
		$total = ( $total > 100 ) ? 100 : $total;

		$totla_users_score = round( $rate/$count, 2 );
		$totla_users_score = ( $totla_users_score > 5 ) ? 5 : $totla_users_score;
	}
	else{
		$totla_users_score = $total = $count = 0;
	}


	if( is_user_logged_in() ) {

		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		$user_rated = get_the_author_meta( 'tie_rated', $user_id );

		if( ! empty($user_rated) && is_array( $user_rated ) && array_key_exists( $post_id, $user_rated )){

			$user_rate = round( ( $user_rated[ $post_id ]*100)/5, 2);
			$user_rate = ( $user_rate > 100 ) ? 100 : $user_rate;

			return $output = '
				<div class="user-rate-wrap">

					<span class="user-rating-text">
						<strong>'.__( "Your Rating:" , "taq" ) .' </strong>
						<span class="taq-score">'. $user_rated[ $post_id ] .'</span>
						<small>( <span class="taq-count">'.$count.'</span> '.__( "votes" , "taq" ) .')</small>
					</span>

					<div data-rate="'. $user_rate .'" class="user-rate rated-done" title="">
						<span class="user-rate-image post-large-rate '.$image_style.'-large"><span style="width:'. $user_rate .'%"></span></span>
					</div>
					<div class="taq-clear"></div>

				</div>
			';
		}
	}

	elseif( ! empty( $_COOKIE[ 'tie_rate_'.$post_id ] )){
		$user_rate = $_COOKIE[ 'tie_rate_'.$post_id ];
		return $output = '
			<div class="user-rate-wrap">
				<span class="user-rating-text">
					<strong>'.__( "Your Rating:" , "taq" ) .'</strong>
					<span class="taq-score">'.$user_rate.'</span>
					<small>( <span class="taq-count">'.$count.'</span> '.__( "votes" , "taq" ) .')</small>
				</span>

				<div class="user-rate rated-done" title="">
					<span class="user-rate-image post-large-rate '.$image_style.'-large">
						<span style="width:'. (($user_rate*100)/5) .'%"></span>
					</span>
				</div>
				<div class="taq-clear"></div>

			</div>';
	}

	if( $total == 0 && $count == 0 ){
		return $output = '
			<div class="user-rate-wrap">
				<span class="user-rating-text">
					<strong>'.__( "User Rating:" , "taq" ) .' </strong>
					<span class="taq-score"></span>
					<small>'.$no_rate_text.'</small>
				</span>

				<div data-rate="'. $total .'" data-id="'.$post_id.'" class="user-rate'.$rate_active.'">
					<span class="user-rate-image post-large-rate '.$image_style.'-large">
						<span style="width:'. $total .'%"></span>
					</span>
				</div>

				<div class="taq-clear"></div>

			</div>';
	}

	else{
		return $output = '
			<div class="user-rate-wrap">
				<span class="user-rating-text">
					<strong>'.__( "User Rating:" , "taq" ) .' </strong>
					<span class="taq-score">'.$totla_users_score.'</span>
					<small>( <span class="taq-count">'.$count.'</span> '.__( "votes" , "taq" ) .')</small>
				</span>

				<div data-rate="'. $total .'" data-id="'.$post_id.'" class="user-rate'.$rate_active.'">
					<span class="user-rate-image post-large-rate '.$image_style.'-large">
						<span style="width:'. $total .'%"></span>
					</span>
				</div>
				<div class="taq-clear"></div>
			</div>
		';
	}
}


/*-----------------------------------------------------------------------------------*/
# Get Totla Reviews Score
/*-----------------------------------------------------------------------------------*/
function taqyeem_get_score( $post_id = false, $size = 'small', $echo = true ){

	$total_score = 0;
	$rate_size   = ( $size == 'large' ) ? 'large' : 'small';

	$post_id = ! empty( $post_id ) ? $post_id : get_the_ID();

	$image_style = taqyeem_get_option('rating_image') ? taqyeem_get_option('rating_image') : 'stars';

	$get_meta = get_post_custom( $post_id );

	if( !empty( $get_meta['taq_review_position'][0] ) ){
		$short_summary = ! empty( $get_meta['taq_review_total'][0] ) ? $get_meta['taq_review_total'][0] : '';

		if( !empty( $get_meta['taq_review_score'][0] ) ){
			$total_score = $get_meta['taq_review_score'][0];
		}

		$out = '
			<span title="'. $short_summary .'" class="post-single-rate post-'. $rate_size .'-rate '. $image_style .'-'. $rate_size .'">
				<span style="width: '. $total_score .'%"></span>
			</span>
		';

		if( ! $echo ){
			return $out;
		}

		echo $out;

	}
}


/*-----------------------------------------------------------------------------------*/
# Get Get Posts Reviews
/*-----------------------------------------------------------------------------------*/
function taqyeem_get_reviews( $num = 5, $order = 'latest', $thumbnail = false, $categories = 'all' ){

	if( has_filter( 'tie_taqyeem_widget_thumb_size' )){
		$thumbnail = apply_filters( 'tie_taqyeem_widget_thumb_size', $thumbnail );
	}

	if( $order == 'random'){
		$orderby = 'rand';
	}
	elseif( $order == 'best'){
		$orderby = 'meta_value';
	}
	else{
		$orderby = 'date';
	}

	$taq_args = array(
		'posts_per_page' => $num,
		'meta_key'       => 'taq_review_score',
		'orderby'        => $orderby,
		'post_status'    => 'publish',
		'meta_query'     => array(
			array(
				'key'     => 'taq_review_position',
				'value'   => array( 'top', 'bottom', 'custom' ),
				'compare' => 'IN',
			)
		)
	);

	if( $categories != 'all' ){
		$taq_args['cat'] = $categories;
	}

	$cat_query = new WP_Query( $taq_args );?>

	<ul class="reviews-posts">
		<?php

			if( $cat_query->have_posts() ):
				while ( $cat_query->have_posts() ): $cat_query->the_post();?>

					<li>
						<?php if ( has_post_thumbnail() && $thumbnail != false ) : ?>
							<div class="review-thumbnail">
								<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'taq' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
									<?php the_post_thumbnail( $thumbnail ); ?>
								</a>
							</div><!-- review-thumbnail /-->
						<?php endif; ?>

						<h3><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'taq' ), the_title_attribute( 'echo=0' ) ); ?>" ><?php the_title(); ?></a></h3>
						<?php taqyeem_get_score(); ?>
					</li>
					<?php
				endwhile;

			else: ?>
				<li><?php _e('No Posts' , 'taq') ?></li>
				<?php
			endif;

			wp_reset_postdata();
		?>
	</ul>
<?php
}


/*-----------------------------------------------------------------------------------*/
# Get Get Post types Reviews
/*-----------------------------------------------------------------------------------*/
function taqyeem_get_types_reviews( $num = 5 , $order = 'latest' , $thumbnail = false , $types = 'any' ){

	if( has_filter( 'tie_taqyeem_widget_thumb_size' )){
		$thumbnail = apply_filters( 'tie_taqyeem_widget_thumb_size', $thumbnail );
	}

	if( $order == 'rand' ){
		$orderby = 'rand';
	}
	elseif( $order == 'best' ){
		$orderby = 'meta_value';
	}
	else{
		$orderby = 'date';
	}

	$taq_args = array(
		'posts_per_page' => $num,
		'meta_key'       => 'taq_review_score',
		'orderby'        => $orderby,
		'post_type'      => $types,
		'post_status'    => 'publish',
		'meta_query'     => array(
			array(
				'key'     => 'taq_review_position',
				'value'   => array( 'top', 'bottom', 'custom' ),
				'compare' => 'IN',
			)
		)
	);

	$cat_query = new WP_Query( $taq_args ); ?>
	<ul class="reviews-posts">
		<?php
			if( $cat_query->have_posts() ):
				while ( $cat_query->have_posts() ): $cat_query->the_post(); ?>
					<li>
						<?php if ( has_post_thumbnail() && $thumbnail != false ): ?>
							<div class="review-thumbnail">
								<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'taq' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
									<?php the_post_thumbnail( $thumbnail ); ?>
								</a>
							</div><!-- review-thumbnail /-->
						<?php endif; ?>

						<h3><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'taq' ), the_title_attribute( 'echo=0' ) ); ?>" ><?php the_title(); ?></a></h3>
						<?php taqyeem_get_score(); ?>
					</li>
					<?php
				endwhile;
			else: ?>
				<li><?php _e('No Posts' , 'taq') ?></li>
				<?php
			endif;

			wp_reset_postdata();
		?>
	</ul>
	<?php
}


/*-----------------------------------------------------------------------------------*/
# Shortcode to disapy the review box
/*-----------------------------------------------------------------------------------*/
add_shortcode('taq_review', 'taqyeem_shortcode_review');
function taqyeem_shortcode_review( $atts, $content = null ) {
	$output = taqyeem_get_review( 'review-bottom' );
	return $output;
}


/*-----------------------------------------------------------------------------------*/
# Enqueue Fonts From Google Webfonts
/*-----------------------------------------------------------------------------------*/
function taqyeem_enqueue_font ( $got_font) {
	if ($got_font) {

		$char_set = '&subset=latin';

		if( taqyeem_get_option('typography_latin_extended') || taqyeem_get_option('typography_cyrillic') ||
		taqyeem_get_option('typography_cyrillic_extended') || taqyeem_get_option('typography_greek') ||
		taqyeem_get_option('typography_greek_extended') ){

			if( taqyeem_get_option('typography_latin_extended') ){
				$char_set .= ',latin-ext';
			}
			if( taqyeem_get_option('typography_cyrillic') ){
				$char_set .= ',cyrillic';
			}
			if( taqyeem_get_option('typography_cyrillic_extended') ){
				$char_set .= ',cyrillic-ext';
			}
			if( taqyeem_get_option('typography_greek') ){
				$char_set .= ',greek';
			}
			if( taqyeem_get_option('typography_greek_extended') ){
				$char_set .= ',greek-ext';
			}
		}

		$font_pieces 	= explode(":", $got_font);
		$font_name 		= $font_pieces[0];
		$font_name 		= str_replace (" ","+", $font_pieces[0] );

		$font_variants 	= $font_pieces[1];
		$font_variants 	= str_replace ("|",",", $font_pieces[1] );

		$protocol = is_ssl() ? 'https' : 'http';
		wp_enqueue_style( $font_name , $protocol.'://fonts.googleapis.com/css?family='.$font_name . ':' . $font_variants.$char_set );
	}
}


/*-----------------------------------------------------------------------------------*/
# Get The Font Name
/*-----------------------------------------------------------------------------------*/
function taqyeem_get_font ( $got_font ) {
	if ($got_font) {
		$font_pieces 	= explode(":", $got_font);
		$font_name 		= $font_pieces[0];
		return $font_name;
	}
}


/*-----------------------------------------------------------------------------------*/
# Typography Elements Array
/*-----------------------------------------------------------------------------------*/
$taqyeem_typography = array(
	"#review-box h2.review-box-header"													=>		"review_typography_title",
	"#review-box .review-item h5,	#review-box.review-percentage .review-item h5"		=>		"review_typography_items",
	"#review-box .review-short-summary, #review-box .review-short-summary p"			=>		"review_typography_summery",
	"#review-box .review-final-score h3"												=>		"review_typography_total",
	"#review-box .review-final-score h4"												=>		"review_typography_final",
	".user-rate-wrap, #review-box strong"												=>		"review_user_rate"
);


/*-----------------------------------------------------------------------------------*/
# Get Custom Typography
/*-----------------------------------------------------------------------------------*/
add_action('wp_enqueue_scripts', 'taqyeem_typography');
function taqyeem_typography(){

	if( ! apply_filters( 'taqyeem_custom_styles', true ) ){
		return;
	}

	global $taqyeem_typography;
	foreach( $taqyeem_typography as $selector => $value){
		$option = taqyeem_get_option( $value );

		if( ! empty( $option['font'] )){
			taqyeem_enqueue_font( $option['font'] );
		}
	}
}


/*-----------------------------------------------------------------------------------*/
# Taqyeem Wp Head
/*-----------------------------------------------------------------------------------*/
add_action('wp_head', 'taqyeem_wp_head');
function taqyeem_wp_head() {
	global $taqyeem_typography;
	?>
<script type='text/javascript'>
/* <![CDATA[ */
var taqyeem = {"ajaxurl":"<?php echo admin_url('admin-ajax.php'); ?>" , "your_rating":"<?php _e( 'Your Rating:' , 'taq' ) ?>"};
/* ]]> */
</script>

<?php
	if( ! apply_filters( 'taqyeem_custom_styles', true ) ){
		return;
	}
?>
<style type="text/css" media="screen">
<?php if( taqyeem_get_option( 'review_bg' ) ): ?>
.review-final-score {border-color: <?php echo taqyeem_get_option( 'review_bg' );?>;}
.review-box  {background-color:<?php echo taqyeem_get_option( 'review_bg' );?> ;}
<?php endif; ?>
<?php if( taqyeem_get_option( 'review_main_color' ) ): ?>
#review-box h2.review-box-header , .user-rate-wrap  {background-color:<?php echo taqyeem_get_option( 'review_main_color' );?> ;}
<?php endif; ?>
<?php if( taqyeem_get_option( 'review_items_color' ) ): ?>
.review-stars .review-item , .review-percentage .review-item span, .review-summary  {background-color:<?php echo taqyeem_get_option( 'review_items_color' );?> ;}
<?php endif; ?>
<?php if( taqyeem_get_option( 'review_secondery_color' ) ): ?>
.review-percentage .review-item span span,.review-final-score {background-color:<?php echo taqyeem_get_option( 'review_secondery_color' );?> ;}
<?php endif; ?>
<?php if( taqyeem_get_option( 'review_links_color' ) || taqyeem_get_option( 'review_links_decoration' )  ): ?>
.review-summary a {
	<?php if( taqyeem_get_option( 'review_links_color' ) ) echo 'color: '.taqyeem_get_option( 'review_links_color' ).';'; ?>
	<?php if( taqyeem_get_option( 'review_links_decoration' ) ) echo 'text-decoration: '.taqyeem_get_option( 'review_links_decoration' ).';'; ?>
}
<?php endif; ?>
<?php if( taqyeem_get_option( 'review_links_color_hover' ) || taqyeem_get_option( 'review_links_decoration_hover' )  ): ?>
.review-summary a:hover {
	<?php if( taqyeem_get_option( 'review_links_color_hover' ) ) echo 'color: '.taqyeem_get_option( 'review_links_color_hover' ).';'; ?>
	<?php if( taqyeem_get_option( 'review_links_decoration_hover' ) ) echo 'text-decoration: '.taqyeem_get_option( 'review_links_decoration_hover' ).';'; ?>
}
<?php endif; ?>
<?php do_action( 'tie_taqyeem_styling_css' ); ?>
<?php
foreach( $taqyeem_typography as $selector => $value){
$option = taqyeem_get_option( $value );
if( ! empty( $option['font'] ) || ! empty( $option['color'] ) || ! empty( $option['size'] ) || ! empty( $option['weight'] ) || ! empty( $option['style'] ) ):
echo "\n".$selector."{\n"; ?>
<?php if( ! empty( $option['font'] ) )
	echo "	font-family: '". taqyeem_get_font( $option['font']  )."';\n"?>
<?php if( ! empty( $option['color'] ) )
	echo "	color :". $option['color'].";\n"?>
<?php if( ! empty( $option['size'] ) )
	echo "	font-size : ".$option['size']."px;\n"?>
<?php if( ! empty( $option['weight'] ) )
	echo "	font-weight: ".$option['weight'].";\n"?>
<?php if( ! empty( $option['style'] ) )
	echo "	font-style: ". $option['style'].";\n"?>
}

<?php endif;
} ?>
<?php echo htmlspecialchars_decode( taqyeem_get_option('css') ) , "\n";?>
<?php if( taqyeem_get_option('css_tablets') ) : ?>
@media only screen and (max-width: 985px) and (min-width: 768px){
<?php echo htmlspecialchars_decode( taqyeem_get_option('css_tablets') ) , "\n";?>
}
<?php endif; ?>
<?php if( taqyeem_get_option('css_wide_phones') ) : ?>
@media only screen and (max-width: 767px) and (min-width: 480px){
<?php echo htmlspecialchars_decode( taqyeem_get_option('css_wide_phones') ) , "\n";?>
}
<?php endif; ?>
<?php if( taqyeem_get_option('css_phones') ) : ?>
@media only screen and (max-width: 479px) and (min-width: 320px){
<?php echo htmlspecialchars_decode( taqyeem_get_option('css_phones') ) , "\n";?>
}
<?php endif; ?>
</style>
<?php
}

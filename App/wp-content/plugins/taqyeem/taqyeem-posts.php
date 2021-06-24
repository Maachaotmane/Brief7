<?php

/*-----------------------------------------------------------------------------------*/
# Register Reviews Posts Options Meta Box
/*-----------------------------------------------------------------------------------*/
add_action( 'admin_init', 'taqyeem_post_init' );
function taqyeem_post_init(){

	$post_types = get_post_types( array( '_builtin' => false, 'public' => true ), 'names');
	$post_types['post'] = 'post';
	$post_types['page'] = 'page';

	foreach ( $post_types as $post_type ) {
		add_meta_box( 'taqyeem_post_options', TIE_TAQYEEM .' - '. __( 'Review Options' , 'taq' ), 'taqyeem_post_options', $post_type, 'normal', 'high' );
	}
}



/*-----------------------------------------------------------------------------------*/
# Reviews Posts Options
/*-----------------------------------------------------------------------------------*/
function taqyeem_post_options(){

	global $post;
	$get_meta = get_post_custom( $post->ID );

	if( isset( $get_meta['taq_review_criteria'][0] ) ){
		$taq_review_criteria = unserialize( $get_meta['taq_review_criteria'][0] );
	}
	?>

	<div class="taqyeem-item" style="border:0 none; box-shadow: none;">

		<input type="hidden" name="taqyeem_hidden_flag" value="true" />

		<script type="text/javascript">
			jQuery(function() {
				jQuery( "#taqyeem-reviews-list" ).sortable({placeholder: "taqyeem-state-highlight"});
			});
		</script>

		<?php

		taqyeem_options_items(
			array(
				'name'    => __('Review Box Position','taq'),
				'id'      => 'taq_review_position',
				'type'    => 'select',
				'options' => array(
					''       => __( 'Disable',            'taq'),
					'top'    => __( 'Top of the post',    'taq'),
					'bottom' => __( 'Bottom of the post', 'taq'),
					'custom' => __( 'Custom position',    'taq'),
				)));

		?>

		<p id="taqyeem_custom_position_hint" class="taqyeem_message_hint">
			<?php printf( __( 'Use <strong>[taq_review]</strong> shortcode to place the review box in any place within post content or use <strong> %s </strong> Widget .', 'taq' ),  TIE_TAQYEEM .' - Review Box' );	?>
		</p>


		<div id="taq-reviews-options">
			<?php

			do_action( 'tie_taqyeem_before_review_options' );

			taqyeem_options_items(
				array(
					'name'    => __( 'Review Style', 'taq' ),
					'id'      => 'taq_review_style',
					'type'    => 'radio',
					'options' => array(
						'stars'      => __( 'Image',      'taq' ),
						'percentage' => __( 'Percentage', 'taq' ),
						'points'     => __( 'Points',     'taq' ),
					)));

			taqyeem_options_items(
				array(
					'name' => __( 'Review Box Title', 'taq' ),
					'id'   => 'taq_review_title',
					'type' => 'text',
				));

			taqyeem_options_items(
				array(
					'name' => __( 'Text appears under the total score', 'taq' ),
					'id'   => 'taq_review_total',
					'type' => 'text',
				));

			do_action( 'tie_taqyeem_before_review_summary_option' );

			taqyeem_options_items(
				array(
					'name' => __( 'Review Summary', 'taq' ),
					'id'   => 'taq_review_summary',
					'type' => 'textarea',
				));

			taqyeem_options_items(
				array(
					'name'    => __('Structured Data Type','taq'),
					'id'      => 'taq_review_structured_data',
					'type'    => 'select',
					'options' => array(
						''               => __( 'Default',        'taq'),
						'product'        => __( 'Product',        'taq'),
						'book'           => __( 'Book',           'taq'),
						'movie'          => __( 'Movie',          'taq'),
						'game'           => __( 'Game',           'taq'),
						'event'          => __( 'Event',          'taq'),
						'course'         => __( 'Course',         'taq'),
						'organization'   => __( 'Organization',   'taq'),
						'musicrecording' => __( 'MusicRecording', 'taq'),
						'musicplaylist'  => __( 'MusicPlaylist',  'taq'),
						'episode'        => __( 'Episode',        'taq'),
						'restaurant'     => __( 'Restaurant',     'taq'),
					)));

			// Product
			taqyeem_options_items(
				array(
					'name'  => __( 'Product Description', 'taq' ),
					'id'    => 'taq_review_structured_data_product_description',
					'class' => 'taq_structured_data_options taq_structured_data_product_options',
					'type'  => 'textarea',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Product URL', 'taq' ),
					'id'    => 'taq_review_structured_data_product_url',
					'class' => 'taq_structured_data_options taq_structured_data_product_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Brand', 'taq' ),
					'id'    => 'taq_review_structured_data_product_brand',
					'class' => 'taq_structured_data_options taq_structured_data_product_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'SKU', 'taq' ),
					'id'    => 'taq_review_structured_data_product_sku',
					'class' => 'taq_structured_data_options taq_structured_data_product_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'MPN Number', 'taq' ),
					'id'    => 'taq_review_structured_data_product_mpn',
					'class' => 'taq_structured_data_options taq_structured_data_product_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'GTIN Number', 'taq' ),
					'id'    => 'taq_review_structured_data_product_gtin',
					'class' => 'taq_structured_data_options taq_structured_data_product_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Product Availability', 'taq' ),
					'id'    => 'taq_review_structured_data_product_availability',
					'class' => 'taq_structured_data_options taq_structured_data_product_options',
					'type'    => 'radio',
					'options' => array(
						'InStock'    => __( 'InStock',    'taq' ),
						'OutOfStock' => __( 'OutOfStock', 'taq' ),
				)));

			taqyeem_options_items(
				array(
					'name'  => __( 'Product Price', 'taq' ),
					'id'    => 'taq_review_structured_data_product_price',
					'class' => 'taq_structured_data_options taq_structured_data_product_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Product Price Currency ( Three-letter ISO 4217 format )', 'taq' ),
					'id'    => 'taq_review_structured_data_product_currency',
					'class' => 'taq_structured_data_options taq_structured_data_product_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Product Valid Until ( YYYY-MM-DD )', 'taq' ),
					'id'    => 'taq_review_structured_data_product_price_date',
					'class' => 'taq_structured_data_options taq_structured_data_product_options',
					'type'  => 'text',
				));

			// Event
			taqyeem_options_items(
				array(
					'name'  => __( 'Location Name', 'taq' ),
					'id'    => 'taq_review_structured_data_event_location_name',
					'class' => 'taq_structured_data_options taq_structured_data_event_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Location Address', 'taq' ),
					'id'    => 'taq_review_structured_data_event_location_address',
					'class' => 'taq_structured_data_options taq_structured_data_event_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Event Description', 'taq' ),
					'id'    => 'taq_review_structured_data_event_description',
					'class' => 'taq_structured_data_options taq_structured_data_event_options',
					'type'  => 'textarea',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Start date ( YYYY-MM-DD )', 'taq' ),
					'id'    => 'taq_review_structured_data_event_startdate',
					'class' => 'taq_structured_data_options taq_structured_data_event_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'End date ( YYYY-MM-DD )', 'taq' ),
					'id'    => 'taq_review_structured_data_event_enddate',
					'class' => 'taq_structured_data_options taq_structured_data_event_options',
					'type'  => 'text',
				));

			// Book
			taqyeem_options_items(
				array(
					'name'  => __( 'Author', 'taq' ),
					'id'    => 'taq_review_structured_data_author',
					'class' => 'taq_structured_data_options taq_structured_data_book_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Author URL', 'taq' ),
					'id'    => 'taq_review_structured_data_author_url',
					'class' => 'taq_structured_data_options taq_structured_data_book_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'ISBN', 'taq' ),
					'id'    => 'taq_review_structured_data_book_isbn',
					'class' => 'taq_structured_data_options taq_structured_data_book_options',
					'type'  => 'text',
				));

			// Movie
			taqyeem_options_items(
				array(
					'name'  => __( 'Movie URL', 'taq' ),
					'id'    => 'taq_review_structured_data_movie_url',
					'class' => 'taq_structured_data_options taq_structured_data_movie_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Movie Date', 'taq' ),
					'id'    => 'taq_review_structured_data_movie_date',
					'class' => 'taq_structured_data_options taq_structured_data_movie_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Director', 'taq' ),
					'id'    => 'taq_review_structured_data_movie_director',
					'class' => 'taq_structured_data_options taq_structured_data_movie_options',
					'type'  => 'text',
				));

			// Course
			taqyeem_options_items(
				array(
					'name'  => __( 'Description', 'taq' ),
					'id'    => 'taq_review_structured_data_description',
					'class' => 'taq_structured_data_options taq_structured_data_course_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Provider', 'taq' ),
					'id'    => 'taq_review_structured_data_course_provider',
					'class' => 'taq_structured_data_options taq_structured_data_course_options',
					'type'  => 'text',
				));

			// Restaurant
			taqyeem_options_items(
				array(
					'name'  => __( 'Address', 'taq' ),
					'id'    => 'taq_review_structured_data_restaurant_address',
					'class' => 'taq_structured_data_options taq_structured_data_restaurant_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Price', 'taq' ),
					'id'    => 'taq_review_structured_data_restaurant_price',
					'class' => 'taq_structured_data_options taq_structured_data_restaurant_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Cuisine', 'taq' ),
					'id'    => 'taq_review_structured_data_restaurant_cuisine',
					'class' => 'taq_structured_data_options taq_structured_data_restaurant_options',
					'type'  => 'text',
				));

			taqyeem_options_items(
				array(
					'name'  => __( 'Telephone', 'taq' ),
					'id'    => 'taq_review_structured_data_restaurant_telephone',
					'class' => 'taq_structured_data_options taq_structured_data_restaurant_options',
					'type'  => 'text',
				));

			do_action( 'tie_taqyeem_after_review_options' );

			?>

			<input id="add_review_criteria" type="button" class="button" value="<?php  _e( 'Add New Review Criteria' , 'taq' ) ?>">

			<?php do_action( 'tie_taqyeem_before_review_criteria' ); ?>

			<div class="clear"></div>

			<ul id="taqyeem-reviews-list">
				<?php $i = 0;

				if( ! empty( $taq_review_criteria ) && is_array( $taq_review_criteria )){
					foreach( $taq_review_criteria as $taq_review ){
						$i++; ?>

						<li class="taqyeem-option-item taqyeem-review-item">
							<span class="label"><?php  _e( 'Review Criteria' , 'taq' ) ?></span>
							<input name="taq_review_criteria[<?php echo $i ?>][name]" type="text" value="<?php if( !empty($taq_review['name'] ) ) echo $taq_review['name'] ?>" />
							<div class="clear"></div>
							<span class="label"><?php  _e( 'Criteria Score' , 'taq' ) ?></span>
							<div id="criteria<?php echo $i ?>-slider"></div>
							<input type="text" id="criteria<?php echo $i ?>" value="<?php if( !empty($taq_review['score']) ) echo $taq_review['score']; else echo 0; ?>" name="taq_review_criteria[<?php echo $i ?>][score]" style="width:40px; opacity: 0.7;" />
							<a class="del-review" title="<?php  _e( 'Delete' , 'taq' ) ?>"><?php  _e( 'Delete' , 'taq' ) ?></a>
							<script>
							  jQuery(document).ready(function(){
									jQuery( '#criteria<?php echo $i ?>-slider' ).slider({
										range: 'min',
										min: 0,
										max: 100,
										value: <?php if( !empty($taq_review['score']) ) echo $taq_review['score']; else echo 0; ?>,
										slide: function(event, ui) {
											jQuery('#criteria<?php echo $i ?>').attr('value', ui.value );
										}
									});
								});
							</script>
						</li>
						<?php
					}
				}
				?>
			</ul>
			<script>var taqNextReview = <?php echo $i+1 ?> ;</script>
		</div>
	</div>
  <?php
}



/*-----------------------------------------------------------------------------------*/
# Save Review
/*-----------------------------------------------------------------------------------*/
add_action('save_post', 'taqyeem_save_post');
function taqyeem_save_post( $post_id ){

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
		return $post_id;
	}

	if ( isset($_POST['taqyeem_hidden_flag']) ){

		$custom_meta_fields = array(
			'taq_review_position' => array(
				'taq_review_title',
				'taq_review_style',
				'taq_review_summary',
				'taq_review_total',
				'taq_review_criteria',

				'taq_review_structured_data',
				'taq_review_structured_data_author',
				'taq_review_structured_data_author_url',

				'taq_review_structured_data_product_brand',
				'taq_review_structured_data_product_sku',
				'taq_review_structured_data_product_mpn',
				'taq_review_structured_data_product_gtin',
				'taq_review_structured_data_product_description',
				'taq_review_structured_data_product_availability',
				'taq_review_structured_data_product_price',
				'taq_review_structured_data_product_currency',
				'taq_review_structured_data_product_price_date',
				'taq_review_structured_data_product_url',

				'taq_review_structured_data_event_location_name',
				'taq_review_structured_data_event_location_address',
				'taq_review_structured_data_event_description',
				'taq_review_structured_data_event_startdate',
				'taq_review_structured_data_event_enddate',

				'taq_review_structured_data_book_isbn',

				'taq_review_structured_data_movie_url',
				'taq_review_structured_data_movie_date',
				'taq_review_structured_data_movie_director',

				'taq_review_structured_data_description',
				'taq_review_structured_data_course_provider',

				'taq_review_structured_data_restaurant_address',
				'taq_review_structured_data_restaurant_price',
				'taq_review_structured_data_restaurant_cuisine',
				'taq_review_structured_data_restaurant_telephone',
			),
		);

		$custom_meta_fields = apply_filters('tie_taqyeem_save_review_fields', $custom_meta_fields );

		foreach( $custom_meta_fields as $key => $custom_meta_field ){

			# Dependency Options fields ----------
			if( is_array( $custom_meta_field ) ){

				if( ! empty( $_POST[ $key ] )){

					update_post_meta( $post_id, $key, $_POST[ $key ] );

					foreach ( $custom_meta_field as $single_field ){
						if( ! empty( $_POST[ $single_field ] )){

							$option_value = $_POST[ $single_field ];

							if( $single_field != 'taq_review_criteria' ){
								$option_value = htmlspecialchars(stripslashes( $option_value ));
							}

							update_post_meta( $post_id, $single_field, $option_value );
						}
						else{
							delete_post_meta( $post_id, $single_field );
						}
					}
				}
				else{
					delete_post_meta( $post_id, $key );
				}
			}

			# Single Options fields ----------
			else{
				if( ! empty( $_POST[ $custom_meta_field ] )){
					update_post_meta( $post_id, $custom_meta_field, $_POST[ $custom_meta_field ] );
				}
				else{
					delete_post_meta( $post_id, $custom_meta_field );
				}
			}
		}

		# Save the total score ----------
		$get_meta = get_post_custom( $post_id );

		$total_counter = $score = 0;
		if( isset( $get_meta['taq_review_criteria'][0] )){

			$criterias = unserialize( $get_meta['taq_review_criteria'][0] );

			foreach( $criterias as $criteria){
				if( $criteria['name'] && $criteria['score'] && is_numeric( $criteria['score'] )){

					$criteria['score'] = max( 0, min( 100, $criteria['score'] ) );
					$score += $criteria['score'];
					$total_counter ++;
				}
			}

			if( !empty( $score ) && !empty( $total_counter ) ){
				$total_score =  $score / $total_counter;
			}

			update_post_meta( $post_id, 'taq_review_score', $total_score );
		}

	}
}



/*-----------------------------------------------------------------------------------*/
# The Plugin Options
/*-----------------------------------------------------------------------------------*/
function taqyeem_options_items( $value ){
	global $post;

	$class = 'taqyeem-option-item';
	if( ! empty( $value['class'] ) ){
		$class .= ' ' . $value['class'];
	}

	?>

	<div class="<?php echo $class ?>" id="<?php echo $value['id'] ?>-item">
		<span class="label"><?php echo $value['name']  ?></span>

		<?php
			$id = $value['id'];
			$get_meta = get_post_custom($post->ID);

			$current_value = isset( $get_meta[ $id ][0] ) ? $get_meta[ $id ][0] : '';

			switch ( $value['type'] ){

				case 'text': ?>
					<input  name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="text" value="<?php echo $current_value ?>" />
					<?php
					break;

				case 'select': ?>
					<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
						<?php foreach ( $value['options'] as $key => $option ) { ?>
						<option value="<?php echo $key ?>" <?php selected( $current_value, $key ) ?>><?php _e( $option , 'taq' ) ?></option>
						<?php } ?>
					</select>
					<?php
					break;

				case 'checkbox': ?>
					<input <?php checked( $current_value, 'true' ) ?> class="on-of" type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" />
					<?php
					break;

				case 'radio': ?>
					<div class="radio-contnet">
						<?php
							$i = 0;
							foreach ($value['options'] as $key => $option) { ?>
								<label style="display:block; margin-bottom:8px;"><input name="<?php echo $value['id']; ?>" type="radio" value="<?php echo $key ?>" <?php if ( ( !empty($current_value) && $current_value == $key ) ||  ( empty($current_value) && $i == 0 ) ) { echo ' checked="checked"' ; } ?>> <?php echo $option; ?></label>
								<?php
								$i++;
							}
						?>
					</div>
					<?php
					break;

				case 'textarea': ?>
					<textarea style="width:430px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="textarea" cols="100%" rows="3" tabindex="4"><?php if( !empty( $current_value ) ) echo $current_value  ?></textarea>
					<?php
					break;

				case 'color': ?>

					<div id="<?php echo $value['id']; ?>colorSelector" class="color-pic"><div style="background-color:<?php echo taqyeem_get_option($value['id']) ; ?>"></div></div>
						<input style="width:80px; margin-right:5px;"  name="taqyeem_options[<?php echo $value['id']; ?>]" id="<?php echo $value['id']; ?>" type="text" value="<?php if( !empty( $current_value ) ) echo $current_value  ?>" />

						<script>
							jQuery('#<?php echo $value['id']; ?>colorSelector').ColorPicker({
								color: '<?php if( !empty( $current_value ) ) echo $current_value  ?>',
								onShow: function (colpkr) {
									jQuery(colpkr).fadeIn(500);
									return false;
								},
								onHide: function (colpkr) {
									jQuery(colpkr).fadeOut(500);
									return false;
								},
								onChange: function (hsb, hex, rgb) {
									jQuery('#<?php echo $value['id']; ?>colorSelector div').css('backgroundColor', '#' + hex);
									jQuery('#<?php echo $value['id']; ?>').val('#'+hex);
								}
							});
						</script>
					<?php
					break;
			}
		?>
	</div>
<?php
}

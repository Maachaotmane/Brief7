<?php

function jannah_switcher_ajax_process_post( $id, $theme = false ) {

	$message = array();



	# Post Format ----------
	$format = get_post_format( $id );

	if( $format == 'video' || $format == 'audio' || $format == 'gallery' ){

		$format = ( $format == 'gallery' ) ? 'slider' : $format;

		$format_updated = update_post_meta( $id, 'tie_post_head', $format );

		if( $format_updated ){
			$message[] = __( 'Post format updated.', 'jannah_switcher' );
		}
	}



	# Video URL ----------
	if( $post_video = get_post_meta( $id, 'cb_video_embed_code_post', true ) ){

		// Check if it is an embed code or not
		if ( strpos( $post_video, 'http') === 0 ) {
			$video_updated = update_post_meta( $id, 'tie_video_url', $post_video );
		}
		else{
			$video_updated = update_post_meta( $id, 'tie_embed_code', $post_video );
		}

		if( $video_updated ){
			$message[] = __( 'Video data updated', 'jannah_switcher' );
		}
	}



	# Audio URL ----------
	if( $post_audio = get_post_meta( $id, 'cb_soundcloud_embed_code_post', true ) ){

		$audio_updated = update_post_meta( $id, 'tie_audio_embed', $post_audio );

		if( $audio_updated ){
			$message[] = __( 'Audio data updated', 'jannah_switcher' );
		}
	}



	# Gallery Images ----------
	if( $stored_images = get_post_meta( $id, 'cb_gallery_content', false ) ){

		if( is_array( $stored_images ) ){

			$gallery = array();

			foreach( $stored_images as $imgid ){
				$gallery[] = array( 'id' => $imgid );
			}

			$gallery_updated = update_post_meta( $id, 'tie_post_gallery', $gallery );

			if( $gallery_updated ){
				$message[] = __( 'Gallery images updated.', 'jannah_switcher' );
			}
		}
	}



	# Background Color ----------
	if( $bg_color = get_post_meta( $id, 'cb_bg_color_post', true ) ){

		$bg_color_updated = update_post_meta( $id, 'background_color', $bg_color );

		if( $bg_color_updated ){

			$message[] = __( 'Background Color updated.', 'jannah_switcher' );
		}
	}



	# Background Image ----------
	if( $bg_img = get_post_meta( $id, 'cb_bg_image_post', true ) ){

		$image = array();

		update_post_meta( $id, 'background_type', 'image' );

		# ----------
		$bg_img = wp_get_attachment_image_src( $bg_img, 'full' );

		$image['img'] = $bg_img[0];

		if( $bg_settings = get_post_meta( $id, 'cb_bg_image_post_setting', true ) ){

			if( $bg_settings == 1 ){ // Full-Width Stretch
				$image['attachment'] = 'cover';
			}

			elseif( $bg_settings == 3 ){ // No-Repeat
				$image['repeat'] = 'no-repeat';
			}
		}

		$bg_img_updated = update_post_meta( $id, 'background_image', $image );

		if( $bg_img_updated ){

			$message[] = __( 'Background Image updated.', 'jannah_switcher' );
		}
	}



	# Number of Views ----------
	$views_count = get_post_meta( $id, 'cb_visit_counter', true );

	if( ! empty( $views_count ) ){
		$views_updated = update_post_meta( $id, 'tie_views', (int) $views_count );

		if( $views_updated ){

			$message[] = __( 'Post views Number Updated', 'jannah_switcher' );
		}
	}



	# Post Template ----------
	if( $post_template = get_post_meta( $id, 'cb_featured_image_style', true ) ){


		if( $post_template == 'standard' || $post_template == 'standard-uncrop' ){

			$st_title_style = get_post_meta( $id, 'cb_featured_image_st_title_style', true );

			if( $st_title_style == 'cb-fis-tl-st-default' ){

				$post_style = 1;
			}

			elseif( $st_title_style == 'cb-fis-tl-st-above' ){

				$post_style = 2;
			}


			if( $post_template == 'standard-uncrop' && empty( $format ) && empty( $format_updated ) ){

				$format_updated = update_post_meta( $id, 'tie_post_head', 'thumb' );

				if( $format_updated ){
					$message[] = __( 'Post format updated.', 'jannah_switcher' );
				}
			}
		}

		else{

			$title_style = get_post_meta( $id, 'cb_featured_image_title_style', true );

			if( $post_template == 'full-width' ){

				if( $title_style == 'cb-fis-tl-default' ){

					$post_style = 5;
				}
				elseif( $title_style == 'cb-fis-tl-overlay' ){

					$post_style = 4;
				}
			}

			elseif( $post_template == 'full-background' ){

				$post_style = 8;
			}

			elseif( $post_template == 'parallax' ){

				$post_style = 4;
			}
		}

		if( ! empty( $post_style ) ){

			$post_style_updated = update_post_meta( $id, 'tie_post_layout', $post_style );

			if( $post_style_updated ){
				$message[] = __( 'Post Style Updated', 'jannah_switcher' );
			}
		}
	}



	# Sidebar Position ----------
	if( $sidebar = get_post_meta( $id, 'cb_full_width_post', true ) ){

		if( $sidebar == 'sidebar_left' ){
			$sidebar = 'left';
		}
		elseif( $sidebar == 'sidebar' ){
			$sidebar = 'right';
		}
		elseif( $sidebar == 'nosidebar' ){
			$sidebar = 'full';
		}
		elseif( $sidebar == 'nosidebar-narrow' ){
			$sidebar = 'one-column';
		}

		$sidebar_updated = update_post_meta( $id, 'tie_sidebar_pos', $sidebar );
		if( $sidebar_updated ){
			$message[] = __( 'Sidebar Updated.', 'jannah_switcher' );
		}
	}



	# Has a review ----------
	if( get_post_meta( $id, 'cb_review_checkbox', true ) != 0 ){

		$position = ( get_post_meta( $id, 'cb_review_checkbox', true ) == 'top' ) ? 'top' : 'bottom';

		$reviews_on = update_post_meta( $id, 'taq_review_position', $position );

		if( $reviews_on ){
			$message[] = __( 'Review position updated', 'jannah_switcher' );
		}


		# Review Criteria ----------
		$reviews_criteria = array();
		$total_score = $total_counter = 0;

		# What is the Fuck, Why they store the rating in this way!! ----------
		for ( $i=1; $i <7 ; $i++ ){

			$criteria = array();

			# Score ----------
			$rate_score = get_post_meta( $id, 'cb_cs'. $i, true );

			if( ! empty( $rate_score ) ){

				# Label ----------
				$criteria['name']  = get_post_meta( $id, 'cb_ct'. $i, true );
				$criteria['score'] =  max( 0, min( 100, $rate_score ) );

				$total_score   += $criteria['score'];
				$total_counter ++;

				$reviews_criteria[] = $criteria;
			}
		}

		$reviews_criteria_updated = update_post_meta( $id, 'taq_review_criteria', $reviews_criteria );

		if( $reviews_criteria_updated ){
			$message[] = __( 'Review Criteria updated', 'jannah_switcher' );
		}


		# Update the total review score ----------
		if( ! empty( $total_score ) && ! empty( $total_counter ) ){
			$reviews_total_updated = update_post_meta( $id, 'taq_review_score', ( $total_score / $total_counter ) );
			if( $reviews_total_updated ){
				$message[] = __( 'Total review Score updated.', 'jannah_switcher' );
			}
		}

	}



	# Review Style ----------
	if( $review_style = get_post_meta( $id, 'cb_score_display_type', true ) ){

		$reviews_style_updated = update_post_meta( $id, 'taq_review_style', $review_style );

		if( $reviews_style_updated ){
			$message[] = __( 'Review style updated.', 'jannah_switcher' );
		}
	}



	# Review Summery ----------
	$review_summary = get_post_meta( $id, 'cb_summary', true );


	# What is the Fuck, Why they store the rating in this way!! ----------
	$pros_array = array();
	$cons_array = array();

	for ( $i=1; $i <4 ; $i++ ){

		# Positive ----------
		if( $pro = get_post_meta( $id, 'cb_pro_'. $i, true ) ){

			if( ! empty( $pro ) ){
				$pros_array[] = $pro;
			}
		}

		# Negative ----------
		if( $con = get_post_meta( $id, 'cb_con_'. $i, true ) ){

			if( ! empty( $con ) ){
				$cons_array[] = $con;
			}
		}
	}


	# Positive Array ----------
	$pros_text = '';
	if( ! empty( $pros_array ) ){

		$pros_title = get_post_meta( $id, 'cb_pros_title', true );

		if( ! empty( $pros_title) ){
			$pros_text .= '<p><strong>'. $pros_title .'</strong></p>';
		}

		$pros_text .= '[tie_list type="thumbup"]<ul class="cons-list"><li>'. implode( '</li><li>', $pros_array ) .'</li></ul>[/tie_list]';
	}

	# Negative Array ----------
	$cons_text = '';
	if( ! empty( $cons_array ) ){

		$cons_title = get_post_meta( $id, 'cb_cons_title', true );

		if( ! empty( $cons_title) ){
			$cons_text .= '<p><strong>'. $cons_title .'</strong></p>';
		}

		$cons_text .= '[tie_list type="thumbdown"]<ul class="pros-list"><li>'. implode( '</li><li>', $cons_array ) .'</li></ul>[/tie_list]';
	}


	$review_summary  = empty( $review_summary ) ? '' : '<p>'. $review_summary .'</p>';
	$review_summary .= $pros_text . $cons_text;

	if( ! empty( $review_summary  ) ){
		$review_summary_updated = update_post_meta( $id, 'taq_review_summary', $review_summary );

		if( $review_summary_updated ){
			$message[] = __( 'Review Summary updated', 'jannah_switcher' );
		}
	}



	# Review Total ----------
	if( $review_score_title = get_post_meta( $id, 'cb_rating_short_summary_in', true ) ){

		$review_total_updated = update_post_meta( $id, 'taq_review_total', $review_score_title );

		if( $review_total_updated ){

			$message[] = sprintf(__('%1$s Converted to %2$s', 'jannah_switcher'), 'cb_rating_short_summary_in', 'taq_review_total' );
		}
	}



	if( ! empty( $message ) ){
		$message[] =	sprintf(__( 'Successfully switched in %s seconds', 'jannah_switcher' ), timer_stop() );
		return $message;
	}


	return array( __( 'Nothing to Switch.', 'jannah_switcher' ) );


	exit;
}

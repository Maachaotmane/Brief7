<?php

function jannah_switcher_ajax_process_post( $id, $theme = false ) {

  $message = array();



  # Source & Via ----------
  $source_via = array(
  	'post_source' => 'tie_source',
  	'post_via'    => 'tie_via',
  );

  foreach( $source_via as $old_option => $new_option ){

		if( $stored_source_array = get_post_meta( $id, $old_option, true ) ){

			# To hold the new values ----------
			$source = array();

			# Walk through the array and fetch the data ----------
			if( is_array( $stored_source_array ) ){

				foreach ( $stored_source_array as $stored_source ){

					if( ! empty( $stored_source['title'] ) ){
						$source_url = ! empty( $stored_source['post_source_url'] ) ? $stored_source['post_source_url'] : '';

						$source[] = array(
							'text' => $stored_source['title'],
							'url'  => $source_url,
						);
					}
				}
			}

			if( ! empty( $source ) ){
				# Update the post data with the stored value ---------
				$source_updated = update_post_meta( $id, $new_option, $source );

				# Store the success message ----------
				if( $source_updated ){
					$message[] = sprintf(__('%1$s Converted to %2$s', 'jannah_switcher'), $old_option, $new_option );
				}
			}
		}
	}



	# Primary Category ---------- || GoodLife
  if( $primary_cat = get_post_meta( $id, 'post-primary-category', true ) ){

		$primary_cat_updated = update_post_meta( $id, 'tie_primary_category', $primary_cat );

		if( $primary_cat_updated ){
			$message[] = __( 'Primary Category Updated', 'jannah_switcher' );
		}
	}



	# Post Format ----------
	$format = get_post_format( $id );

	if( $format == 'video' || $format == 'image' || $format == 'gallery' ){

		$format = ( $format == 'gallery' ) ? 'slider' : $format;
		$format = ( $format == 'image' )   ? 'thumb'  : $format;

		$format_updated = update_post_meta( $id, 'tie_post_head', $format );

		if( $format_updated ){
			$message[] = __( 'Post format updated.', 'jannah_switcher' );
		}
	}



	# Video URL ----------
  if( $post_video = get_post_meta( $id, 'post_video', true ) ){

		$video_updated = update_post_meta( $id, 'tie_video_url', $post_video );

		if( $video_updated ){
			$message[] = __( 'Video data updated', 'jannah_switcher' );
		}
	}



	# Gallery Images ----------
	$gallery_meta_key = ( $theme == 'goodlife' ) ? 'pp_gallery_slider' : 'post-gallery-photos';

  if( $stored_images = get_post_meta( $id, $gallery_meta_key, true ) ){

  	$stored_images = explode( ',', $stored_images );

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



	# Post Style ----------
	if( $theme == 'goodlife' ){

		# Standard ----------
		if( empty( $format ) ){

			if( $standard_post_style = get_post_meta( $id, 'standard-post-detail-style', true ) ){

				$style = false;

		 		if( $standard_post_style == 'style2' ){
		  		$style = 4;
		  	}
		  	elseif( $standard_post_style == 'style3' ){
		  		$style = 5;
		  	}
		  	elseif( $standard_post_style == 'style4' ){
		  		$style = 4;

				  $sidebar_updated = update_post_meta( $id, 'tie_sidebar_pos', 'full' );

					if( $sidebar_updated ){
						$message[] = __( 'Sidebar Position updated.', 'jannah_switcher' );
					}
		  	}
		  	elseif( $standard_post_style == 'style5' ){
		  		$style = 8;
		  	}

		  	# update the post style ----------
	  		$post_style_updated = update_post_meta( $id, 'tie_post_layout', $style );

				if( $post_style_updated ){
					$message[] = __( 'Post Style Updated', 'jannah_switcher' );
				}

				# if the style is not 1 -> Hide the featured image and use it as a cover bg ----------
				if( $standard_post_style != 'style1' ){
					update_post_meta( $id, 'tie_post_featured',    'no' );
					update_post_meta( $id, 'tie_featured_use_fea', 'true' );
				}

			}
		}

		# Gallery ----------
		if( ! empty( $format ) && $format == 'slider' ){

			if( $gallery_post_style = get_post_meta( $id, 'gallery-post-detail-style', true ) ){

				$style = false;

		  	if( $gallery_post_style == 'style3' ){
		  		$style = 6;
		  	}

		  	# update the post style ----------
	  		$post_style_updated = update_post_meta( $id, 'tie_post_layout', $style );

				if( $post_style_updated ){
					$message[] = __( 'Post Style Updated', 'jannah_switcher' );
				}
			}
		}

		# Video ----------
		if( ! empty( $format ) && $format == 'video' ){

			if( $video_post_style = get_post_meta( $id, 'gallery-post-detail-style', true ) ){

				$style = false;

		  	if( $video_post_style == 'style1' ){
		  		$style = 6;
		  	}

		  	# update the post style ----------
	  		$post_style_updated = update_post_meta( $id, 'tie_post_layout', $style );

				if( $post_style_updated ){
					$message[] = __( 'Post Style Updated', 'jannah_switcher' );
				}
			}
		}
	}

		# Post Style ---------- thevoux
  elseif( get_post_meta( $id, 'article_style_override', true ) == 'on' ){

	  if( $post_style = get_post_meta( $id, 'post-style', true ) ){

	  	if( $post_style == 'style4' ){

			  $sidebar_updated = update_post_meta( $id, 'tie_sidebar_pos', 'full' );

				if( $sidebar_updated ){
					$message[] = __( 'Sidebar Position updated.', 'jannah_switcher' );
				}
	  	}

	  	elseif( $post_style == 'style2' || $post_style == 'style3' ){

				$post_style_updated = update_post_meta( $id, 'tie_post_layout', 4 );

				if( $post_style_updated ){
					$message[] = __( 'Post Style Updated', 'jannah_switcher' );
				}

				# -------
				if( $post_image = get_post_meta( $id, 'post-top-image', true ) ){
					$post_image_updated = update_post_meta( $id, 'tie_featured_custom_bg', $post_image );

					if( $post_image_updated ){
						$message[] = __( 'Post Cover Image Updated', 'jannah_switcher' );
					}
				}
	  	}
		}
	}



	# Has a review ----------
  if( $is_review = get_post_meta( $id, 'is_review', true ) ){

  	if( $is_review == 'yes' || $is_review == 'on' ){

	  	# Review Position ----------
	  	if( $theme == 'goodlife' ){

			  $review_position = get_post_meta( $id, 'post-review-style', true );
			  $review_position = ( $review_position == 'style3' ) ? 'bottom' : 'top';
			}
			else{
				$review_position = 'bottom';
			}

			$review_position_updated = update_post_meta( $id, 'taq_review_position', $review_position );

	  	if( $review_position_updated ){
				$message[] = __( 'Review Position updated.', 'jannah_switcher' );
			}


			#-----
			$reviews_style_updated = update_post_meta( $id, 'taq_review_style', 'points' );
	  	if( $reviews_style_updated ){
				$message[] = __( 'Review style updated.', 'jannah_switcher' );
			}
		}

		// Fix a bug caused by version 1.0.1
		elseif( ! get_post_meta( $id, 'taq_review_criteria' ) ){

			delete_post_meta( $id, 'taq_review_position' );
		}
	}



	# Review Title ----------
  if( $review_title = get_post_meta( $id, 'post_ratings_title', true ) ){

  	$review_title_updated = update_post_meta( $id, 'taq_review_title', $review_title );

  	if( $review_title_updated ){
			$message[] = __( 'Review Title updated.', 'jannah_switcher' );
		}
	}


	# Reviews ----------
	$reviews_criteria = array();

	# Review Criteria ----------
	if( $stored_criteria = get_post_meta( $id, 'post_ratings_percentage', true ) ){

		if( is_array( $stored_criteria ) ){

			$total_score = $total_counter = 0;

			foreach ( $stored_criteria as $single_criteria ) {

				$criteria = array();

				if( ! empty( $single_criteria['title'] ) ){

					$score = ! empty( $single_criteria['feature_score'] ) ? $single_criteria['feature_score'] : 0;

					$criteria['name']  = $single_criteria['title'];
					$criteria['score'] = $score * 10;

					$reviews_criteria[] = $criteria;

					$total_score   += $criteria['score'];
					$total_counter ++;
				}
			}
		}
	}


	# Check if we get criteria? if no check the manual average field ----------
	if( empty( $reviews_criteria ) && get_post_meta( $id, 'post_ratings_average', true ) ){

		$the_average = get_post_meta( $id, 'post_ratings_average', true ) * 10;

		$reviews_criteria[] = array(
			'name'  => 'Rating',
			'score' => $the_average,
		);

		$total_score   = $the_average;
		$total_counter = 1;
	}

	# Store the reviews ----------
	if( ! empty( $reviews_criteria ) ){

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



	# Review Comment ----------
	if( $ratings_comments = get_post_meta( $id, 'post_ratings_comments', true ) ){

		if( is_array( $ratings_comments ) ){

			$pros_array = array();
			$cons_array = array();

			foreach ( $ratings_comments as $comment ) {

				if( ! empty( $comment['title'] ) ){

					if( $comment['feature_comment_type'] == 'positive' ){
						$pros_array[] = $comment['title'];
					}
					elseif( $comment['feature_comment_type'] == 'negative' ){
						$cons_array[] = $comment['title'];
					}
				}
			}

			# Positive Array ----------
			$pros_text = '';
			if( ! empty( $pros_array ) ){
				$pros_text .= '<p><strong>The Good</strong></p>';
				$pros_text .= '[tie_list type="thumbup"]<ul class="cons-list"><li>'. implode( '</li><li>', $pros_array ) .'</li></ul>[/tie_list]';
			}

			# Negative Array ----------
			$cons_text = '';
			if( ! empty( $cons_array ) ){
				$cons_text .= '<p><strong>The Bad</strong></p>';
				$cons_text .= '[tie_list type="thumbdown"]<ul class="pros-list"><li>'. implode( '</li><li>', $cons_array ) .'</li></ul>[/tie_list]';
			}


			# Review Summary ----------
			$review_summary = $pros_text . $cons_text;

			if( ! empty( $review_summary  ) ){
				$review_summary_updated = update_post_meta( $id, 'taq_review_summary', $review_summary );

		  	if( $review_summary_updated ){
					$message[] = __( 'Review Summary updated', 'jannah_switcher' );
				}
			}
		}
	}




	if( ! empty( $message ) ){

		$message[] =	sprintf(__( 'Successfully switched in %s seconds', 'jannah_switcher' ), timer_stop() );
		return $message;
	}


	return array( __( 'Nothing to Switch.', 'jannah_switcher' ) );


	exit;
}

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
  if( $post_video = get_post_meta( $id, 'add_video_url', true ) ){

  	# Self Hosted Video ----------
  	preg_match( '#^(http|https)://.+\.(mp4|m4v|webm|ogv|wmv|flv)$#i', $post_video, $matches );
		if ( ! empty( $matches[0] ) ) {
  		$video_updated = update_post_meta( $id, 'tie_video_self', $matches[0] );
		}
		# ---------
		else{
			$video_updated = update_post_meta( $id, 'tie_video_url', $post_video );
		}

		if( $video_updated ){
			$message[] = __( 'Video URL updated', 'jannah_switcher' );
		}
	}


	# Audio URL ----------
  if( $post_audio = get_post_meta( $id, 'add_audio_url', true ) ){

		# Slef Hosted Audio ----------
		preg_match( '#^(http|https)://.+\.(mp3|m4a|ogg|wav|wma)$#i', $post_audio, $matches );
		if ( ! empty( $matches[0] ) ){
  		$audio_updated = update_post_meta( $id, 'tie_audio_mp3', $matches[0] );
  	}
  	# ---------
  	else{
			$audio_updated = update_post_meta( $id, 'tie_audio_soundcloud', $post_audio );
		}

		if( $audio_updated ){
			$message[] = __( 'Audio URL updated', 'jannah_switcher' );
		}
	}


	# Gallery Images ----------
  if( $stored_images = get_post_meta( $id, 'post_upload_gallery', true ) ){

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


	# Full Width Sidebar ----------
	/*
  if( ! get_post_meta( $id, 'post_sidebar', true ) ){
		$sidebar_updated = update_post_meta( $id, 'tie_sidebar_pos', 'full' );

		if( $sidebar_updated ){
			$message[] = __( 'Sidebar Settings updated', 'jannah_switcher' );
		}
	}
	*/


	# Has a review ----------
  if( get_post_meta( $id, 'enable_rating', true ) ){
		$reviews_on = update_post_meta( $id, 'taq_review_position', 'bottom' );

		if( $reviews_on ){
			$message[] = __( 'Review position updated', 'jannah_switcher' );
		}
	}


	# Review Summery ----------
	if( $review_summary = get_post_meta( $id, 'rating_note', true ) ){
		$review_summary_updated = update_post_meta( $id, 'taq_review_summary', $review_summary );

  	if( $review_summary_updated ){
			$message[] = __( 'Review Summary updated', 'jannah_switcher' );
		}
	}


	# Review Criteria ----------
  if( $criteria_number = get_post_meta( $id, 'rating_module', true ) ){

  	# At least contains 1 -------------
  	if( $criteria_number > 0 ){

			$reviews_criteria = array();
			$total_score = $total_counter = 0;

			# What is the Fuck, Why they store the rating in this way!! ----------
			for ( $i=0; $i < $criteria_number; $i++ ){

				$criteria = array();

				# Label ----------
				$criteria['name'] = get_post_meta( $id, 'rating_module_'. $i .'_score_label', true );

				# Score ----------
				$rate_score = get_post_meta( $id, 'rating_module_'. $i .'_score_number', true );

				if( ! empty( $rate_score ) ){

					$rate_score = $rate_score * 10;
					$criteria['score'] =  max( 0, min( 100, $rate_score ) );

					$total_score   += $criteria['score'];
					$total_counter ++;
				}

				$reviews_criteria[] = $criteria;
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
	}


	if( ! empty( $message ) ){
		$message[] =	sprintf(__( 'Successfully switched in %s seconds', 'jannah_switcher' ), timer_stop() );
		return $message;
	}


	return array( __( 'Nothing to Switch.', 'jannah_switcher' ) );


	exit;
}

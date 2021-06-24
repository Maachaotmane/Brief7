<?php

function jannah_switcher_ajax_process_post( $id, $theme = false ) {

  $message = array();



  # Number of Views ----------
  if( $views_count = get_post_meta( $id, 'post_views_count', true ) ){
		$views_updated = update_post_meta( $id, 'tie_views', (int) $views_count );

		if( $views_updated ){

			$message[] = __( 'post_views_count converted tie_views', 'jannah_switcher' );
		}
	}



  # Post Options ----------
	$post_options = get_post_meta( $id, 'mom_posts_extra', true );

	# Post Format ----------
	$format = get_post_format( $id );



	# Post Image ----------
	if( $format == 'image' ){

		$image_format_updated = update_post_meta( $id, 'tie_post_head', 'thumb' );

		if( $image_format_updated ){

			$message[] = __( 'Image format updated.', 'jannah_switcher' );
		}
	}



	# Post Audio ----------
	if( $format == 'audio' ){

		$audio_format_updated = update_post_meta( $id, 'tie_post_head', 'audio' );

		if( $audio_format_updated ){

			$message[] = __( 'Audio format updated.', 'jannah_switcher' );
		}
	}

	# Audio URL ----------
  if( ! empty( $post_options['audio_type'] ) ){

  	if( $post_options['audio_type'] == 'soundcloud' ){

  		if( ! empty( $post_options['audio_sc'] ) ){

  			if( strpos( $post_options['audio_sc'], '<iframe' ) !== false ){

					preg_match('/src="([^"]+)"/', $post_options['audio_sc'], $match);
					$url = str_replace( 'https://w.soundcloud.com/player/?url=', '', $match[1] );
					$url = explode( '&', $url );
					$url = $url[0];
				}
				else{

					$url = $post_options['audio_sc'];
				}

				$soundcloud_format_updated = update_post_meta( $id, 'tie_audio_soundcloud', $url );

				if( $soundcloud_format_updated ){

					$message[] = __( 'SoundCloud updated.', 'jannah_switcher' );
				}
  		}

  	}
  	else{

  		if( ! empty( $post_options['audio_mp3'] ) ){
  			update_post_meta( $id, 'tie_audio_mp3', $post_options['audio_mp3'] );
  		}

  		if( ! empty( $post_options['audio_m4a'] ) ){
  			update_post_meta( $id, 'tie_audio_m4a', $post_options['audio_m4a'] );
  		}

  		if( ! empty( $post_options['audio_ogg'] ) ){
  			update_post_meta( $id, 'tie_audio_oga', $post_options['audio_ogg'] );
  		}

  		$message[] = __( 'Self Hosted Audio URLs updated.', 'jannah_switcher' );
  	}
  }



  # Post Gallery ----------
	if( $format == 'gallery' ){

		$slider_format_updated = update_post_meta( $id, 'tie_post_head', 'slider' );

		if( $slider_format_updated ){

			$message[] = __( 'Slider format updated.', 'jannah_switcher' );
		}
	}

	# Gallery Images ----------
  if( ! empty( $post_options['slides'] ) && is_array( $post_options['slides'] ) ){

  	$gallery = array();

  	foreach( $post_options['slides'] as $slide ){
  		$gallery[] = array( 'id' => $slide['imgid'] );
  	}

		$gallery_updated = update_post_meta( $id, 'tie_post_gallery', $gallery );

		if( $gallery_updated ){

			$message[] = __( 'Gallery images updated.', 'jannah_switcher' );
		}
  }





	# Post Video ----------
	if( $format == 'video' ){

		$video_format_updated = update_post_meta( $id, 'tie_post_head', 'video' );

		if( $video_format_updated ){

			$message[] = __( 'Video format updated.', 'jannah_switcher' );
		}
	}

	# Video URL ----------
  if( ! empty( $post_options['video_type'] ) ){

  	if( $post_options['video_type'] == 'html5' ){

  		if( ! empty( $post_options['html5_mp4'] ) ){

  			$self_video = $post_options['html5_mp4'];
  		}
  		elseif( ! empty( $post_options['html5_m4v'] ) ){

  			$self_video = $post_options['html5_m4v'];
  		}
  		elseif( ! empty( $post_options['html5_webm'] ) ){

  			$self_video = $post_options['html5_webm'];
  		}
  		elseif( ! empty( $post_options['html5_ogv'] ) ){

  			$self_video = $post_options['html5_ogv'];
  		}
  		elseif( ! empty( $post_options['html5_wmv'] ) ){

  			$self_video = $post_options['html5_wmv'];
  		}
  		elseif( ! empty( $post_options['html5_flv'] ) ){

  			$self_video = $post_options['html5_flv'];
  		}

  		$video_updated = update_post_meta( $id, 'tie_video_self', $self_video );
  	}
  	else{

  		if( ! empty( $post_options['video_id'] ) ){

  			switch ( $post_options['video_type'] ) {
  				case 'youtube':
  					$video_prefix = 'https://www.youtube.com/watch?v=';
  					break;

  				case 'vimeo':
  					$video_prefix = 'https://vimeo.com/';
  					break;

  				case 'dailymotion':
  				case 'daily':
  					$video_prefix = 'http://www.dailymotion.com/video/';
  					break;
  			}

  			if( $post_options['video_type'] != 'facebook' ){
					$video_updated = update_post_meta( $id, 'tie_video_url', $video_prefix . $post_options['video_id'] );
				}
				else{

					$facebook_embed_code = '<iframe src="http://www.facebook.com/video/embed?video_id='. $post_options['video_id'] .'" width="780" height="343" frameborder="0"></iframe>';
					$video_updated = update_post_meta( $id, 'tie_embed_code', $facebook_embed_code );
				}
			}
  	}


		if( $video_updated ){

			$message[] = __( 'Video Updated', 'jannah_switcher' );
		}
	}



 	# Background Color ----------
 	if( $bg_color = get_post_meta( $id, 'mom_custom_bg', true ) ){

 		$bg_color_updated = update_post_meta( $id, 'background_color', $bg_color );

		if( $bg_color_updated ){

			$message[] = __( 'Background Color updated.', 'jannah_switcher' );
		}
 	}



 	# Background Image ----------
 	if( $bg_img = get_post_meta( $id, 'mom_custom_bg_img', true ) ){

 		update_post_meta( $id, 'background_type', 'image' );

 		$bg_img_updated = update_post_meta( $id, 'background_image', array( 'img' => $bg_img ) );

		if( $bg_img_updated ){

			$message[] = __( 'Background Image updated.', 'jannah_switcher' );
		}
 	}



 	# Custom Logo ----------
 	if( $custom_logo = get_post_meta( $id, 'mom_custom_logo', true ) ){

 		update_post_meta( $id, 'custom_logo', 'true' );

 		$logo_updated = update_post_meta( $id, 'logo', $custom_logo );

		if( $logo_updated ){

			$message[] = __( 'Custom Logo updated.', 'jannah_switcher' );
		}
 	}



 	# Post Highlights ----------
 	if( $post_highlights = get_post_meta( $id, 'mom_post_highlights', true ) ){

 		$post_highlights = explode( PHP_EOL, $post_highlights );

 		$post_highlights_updated = update_post_meta( $id, 'tie_highlights_text', $post_highlights );

		if( $post_highlights_updated ){

			$message[] = __( 'Story Highlights Updated.', 'jannah_switcher' );
		}
 	}



 	# Source ----------
 	if( $post_source = get_post_meta( $id, 'mom_post_source', true ) ){

 		# Check if contains an URL ----------
		if( strpos( $post_source , '<a' ) !== false ){

		  $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";

		  if( preg_match_all( "/$regexp/siU", $post_source, $matches, PREG_SET_ORDER )){
	  		$source = array(
	  			array(
						'text' => $matches[0][3],
						'url'  => $matches[0][2],
					)
				);
		  }
		}
		else{
		 	$source = array(
		 		array(
					'text' => $post_source,
				)
			);
		}

		$source_updated = update_post_meta( $id, 'tie_source', $source );

		if( $source_updated ){
			$message[] = __( 'Post Source Updated', 'jannah_switcher' );
		}
 	}



 	# The Sidebar Position ----------
 	if( $sidebar_position = get_post_meta( $id, 'mom_page_layout', true ) ){

		if( $sidebar_position == 'right-sidebar' || $sidebar_position == 'both-sidebars-right' ){
			$sidebar_position = 'right';
		}
		elseif( $sidebar_position == 'left-sidebar' || $sidebar_position == 'both-sidebars-left' ){
			$sidebar_position = 'left';
		}
		elseif( $sidebar_position == 'fullwidth' ){
			$sidebar_position = 'full';
		}
		else{
			$sidebar_position = false;
		}

		$sidebar_updated = update_post_meta( $id, 'tie_sidebar_pos', $sidebar_position );

		if( $sidebar_updated ){

			$message[] = __( 'Sidebar Position updated.', 'jannah_switcher' );
		}
	}



	# The Custom Sidebar ----------
 	if( $custom_sidebar = get_post_meta( $id, 'mom_right_sidebar', true ) ){

		$custom_sidebar_updated = update_post_meta( $id, 'tie_sidebar_post', $custom_sidebar );

		if( $custom_sidebar_updated ){

			$message[] = __( 'Custom Sidebar updated.', 'jannah_switcher' );
		}


		$theme_options = get_option( 'tie_jannah_options' );

		if( ! empty( $theme_options['sidebars'] ) && is_array( $theme_options['sidebars'] ) ){

			if ( ! in_array( $old_option_value, $theme_options['sidebars'] ) ){

				$theme_options['sidebars'][] = $custom_sidebar;
				$sidebar_updated = true;
			}
		}
		else{
			$theme_options['sidebars'] = array( $custom_sidebar );
			$sidebar_updated = true;
		}

		if( isset( $sidebar_updated ) ){
			$custom_sidebars = update_option( 'tie_jannah_options', $theme_options );

				if( $custom_sidebars ){

					$message[] = __( 'Custom Sidebars List Updated.', 'jannah_switcher' );
				}
		}
	}


	# Post Reviews ----------
	if( $review_styles = get_post_meta( $id, '_mom_review_styles', true ) ){

		if( empty( $review_styles ) || ! is_array( $review_styles ) ){

			$style = 'stars';
		}
		else{

			if( $review_styles[0] == 'style-circles' ){

				$style = 'percentage';
			}
			elseif( $review_styles[0] == 'style-bars' ){

				$style = 'points';
			}
			else{

				$style = 'stars';
			}
		}
	}

	$reviews_style_updated = update_post_meta( $id, 'taq_review_style', $style );

	if( $reviews_style_updated ){

		$message[] = sprintf(__('%1$s Converted to %2$s', 'jannah_switcher'), '_mom_review_styles', 'taq_review_style' );
	}


	# Review Box Title ----------
	if( $review_box_title = get_post_meta( $id, '_mom_review_box_title', true ) ){

		$reviews_title_updated = update_post_meta( $id, 'taq_review_title', $review_box_title );

  	if( $reviews_title_updated ){

			$message[] = sprintf(__('%1$s Converted to %2$s', 'jannah_switcher'), '_mom_review_box_title', 'taq_review_title' );
		}
	}


	# Review Summery ----------
	if( $review_summary = get_post_meta( $id, '_mom_review_summary', true ) ){

		$review_summary_updated = update_post_meta( $id, 'taq_review_summary', $review_summary );

  	if( $review_summary_updated ){

			$message[] = sprintf(__('%1$s Converted to %2$s', 'jannah_switcher'), '_mom_review_summary', 'taq_review_summary' );
		}
	}


	# Review Total ----------
	if( $review_score_title = get_post_meta( $id, '_mom_review_score_title', true ) ){

		$review_total_updated = update_post_meta( $id, 'taq_review_total', $review_score_title );

  	if( $review_total_updated ){

			$message[] = sprintf(__('%1$s Converted to %2$s', 'jannah_switcher'), '_mom_review_score_title', 'taq_review_total' );
		}
	}




	if( $review_criterias = get_post_meta( $id, '_mom_review-criterias', true ) ){

		$reviews_criteria = array();
		$total_score = $total_counter = 0;

		if( is_array( $review_criterias ) ){

			foreach( $review_criterias as $value ) {

				$criteria = array();

				$criteria['name'] = ! empty( $value['cr_name'] ) ? $value['cr_name'] : '';

				if( ! empty( $value['cr_score'] ) ){

					$criteria['score'] =  max( 0, min( 100, $value['cr_score'] ) );

					$total_score   += $criteria['score'];
					$total_counter ++;
				}

				$reviews_criteria[] = $criteria;
			}
		}


		update_post_meta( $id, 'taq_review_position', 'bottom' );

		$reviews_criteria_updated = update_post_meta( $id, 'taq_review_criteria', $reviews_criteria );

		if( $reviews_criteria_updated ){

			$message[] = sprintf(__('%1$s Converted to %2$s', 'jannah_switcher'), 'review-criterias', 'taq_review_criteria' );
		}



		if( ! empty( $total_score ) && ! empty( $total_counter ) ){

			$reviews_total_updated = update_post_meta( $id, 'taq_review_score', ( $total_score / $total_counter ) );

			if( $reviews_total_updated ){

				$message[] = __( 'Total review Score updated.', 'jannah_switcher' );
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

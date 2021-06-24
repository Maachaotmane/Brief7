<?php

function jannah_switcher_ajax_process_post( $id, $theme = false ) {

  $message = array();

  # Source ----------
	$source = array();
  $stored_source = array(
		'_bs_source_name'   => '_bs_source_url',
		'_bs_source_name_2' => '_bs_source_url_2',
		'_bs_source_name_3' => '_bs_source_url_3'
	);

	foreach( $stored_source as $stored_name => $stored_url ) {
		# Check and get the option value ----------
  	if( $stored_name_value = get_post_meta( $id, $stored_name, true ) ){
			$source[] = array(
				'text' => $stored_name_value,
				'url'  => get_post_meta( $id, $stored_url, true ),
			);
  	}
	}

	if( ! empty( $source ) ){

		# Update the post data with the stored value ---------
		$source_updated = update_post_meta( $id, 'tie_source', $source );

		# Store the success message ----------
		if( $source_updated ){
			$message[] = __( 'Post Sources data Updated', 'jannah_switcher' );
		}
	}



  # Via ----------
	$via = array();
  $stored_via = array(
		'_bs_via_name'   => '_bs_via_url',
		'_bs_via_name_2' => '_bs_via_url_2',
		'_bs_via_name_3' => '_bs_via_url_3'
	);

	foreach( $stored_via as $stored_name => $stored_url ) {
		# Check and get the option value ----------
  	if( $stored_name_value = get_post_meta( $id, $stored_name, true ) ){
			$via[] = array(
				'text' => $stored_name_value,
				'url'  => get_post_meta( $id, $stored_url, true ),
			);
  	}
	}

	if( ! empty( $via ) ){

		# Update the post data with the stored value ---------
		$via_updated = update_post_meta( $id, 'tie_via', $via );

		# Store the success message ----------
		if( $via_updated ){
			$message[] = __( 'Post Via data Updated', 'jannah_switcher' );
		}
	}




  # Single Options ----------
  $post_options_keys = array(
  	'bs_subtitle'            => 'tie_post_sub_title',
  	'better-views-count'     => 'tie_views',
		'post_template'          => 'tie_post_layout',
		'page_layout'            => 'tie_sidebar_pos',
		'_bs_primary_category'   => 'tie_primary_category',
		'bg_color'               => 'background_color',
		'bg_image'               => 'background_image',
		'_bs_review_heading'     => 'taq_review_title',
		'_bs_review_verdict'     => 'taq_review_total',
		'_bs_review_rating_type' => 'taq_review_style',
	);

  # Walk throug the keys ----------
  foreach( $post_options_keys as $old_option => $new_option ){

  	# Check and get the option value ----------
  	if( $old_option_value = get_post_meta( $id, $old_option, true ) ){

  		# Background Image ----------
			if( $old_option == 'bg_image' && ! empty( $old_option_value['img'] )){

				update_post_meta( $id, 'background_type', 'image' );

				$image_array = array(
					'img' => $old_option_value['img']
				);

				if( ! empty( $old_option_value['type'] ) ){

					$type = $old_option_value['type'];

					switch ( $type ){

						case 'fit-cover':
						case 'parallax':
						case 'cover':

							$image_array['attachment'] = 'cover';
							break;

							case 'repeat':
							case 'no-repeat':
							case 'repeat-x':
							case 'repeat-y':

							$image_array['repeat'] = $type;
							break;

						default:
							# code...
							break;
					}
				}


				$old_option_value = array(  );
			}

			# Post Template ----------
			if( $old_option == 'post_template' ){

				switch ( $old_option_value ) {

					case 'style-1':
						$old_option_value = 1;
						break;

					case 'style-2':
					case 'style-6':
					case 'style-7':
						$old_option_value = 5;
						break;

					case 'style-3':
						$old_option_value = 4;
						$update_featured_bg = true;
						break;

					case 'style-4':
					case 'style-5':
						$old_option_value = 8;
						$update_featured_bg = true;
						break;

					case 'style-8':
					case 'style-9':
					case 'style-10':
					case 'style-11':
						$old_option_value = 2;
						break;

					case 'style-12':
					case 'style-13':
						$old_option_value = 7;
						break;

					default:
						$old_option_value = false;
						break;
				}
			}

			if( ! empty( $update_featured_bg ) ){
				$featured_bg_updated = update_post_meta( $id, 'tie_featured_use_fea', 'yes' );

				# Store the success message ----------
				if( $featured_bg_updated ){
					$message[] = __( 'Featured Image background Updated.', 'jannah_switcher' );
				}
			}


			# Page Template ----------
			if( $old_option == 'page_layout' ){

				switch ( $old_option_value ) {

					case '1-col':
					case '3-col-0':
						$old_option_value = 'full';
						break;

					case '2-col-right':
					case '3-col-1':
					case '3-col-2':
					case '3-col-4':
						$old_option_value = 'right';
						break;

					case '2-col-left':
					case '3-col-3':
					case '3-col-5':
					case '3-col-6':
						$old_option_value = 'left';
						break;

					default:
						$old_option_value = false;
						break;
				}
			}

  		# Update the theme option with the stored value ---------
			$option_updated = update_post_meta( $id, $new_option, $old_option_value );

			# Store the success message ----------
			if( $option_updated ){
				$message[] = sprintf(__('%1$s Converted to %2$s', 'jannah_switcher'), $old_option, $new_option );
			}
  	}
  } // Foreach


  # Embed input field ----------
  $format = get_post_format( $id );

  if( $embed_code = get_post_meta( $id, '_featured_embed_code', true ) ){

  	# Self Hosted Video ----------
  	preg_match( '#^(http|https)://.+\.(mp4|m4v|webm|ogv|wmv|flv)$#i', $embed_code, $matches );

		if ( ! empty( $matches[0] ) ) {

			# Update the video Url ----------
  		$video_updated = update_post_meta( $id, 'tie_video_self', $matches[0] );
		}
		else{


			# Slef Hosted Audio ----------
			preg_match( '#^(http|https)://.+\.(mp3|m4a|ogg|wav|wma)$#i', $embed_code, $matches );

			if ( ! empty( $matches[0] ) ) {

				# Update the Audio Url ----------
	  		$audio_updated = update_post_meta( $id, 'tie_audio_mp3', $matches[0] );
				if( $audio_updated ){
  				$message[] = __( 'Self Hosted Audio URLs updated.', 'jannah_switcher' );
				}

				# Update the video format ----------
				$audio_format_updated = update_post_meta( $id, 'tie_post_head', 'audio' );
				if( $audio_format_updated ){
					$message[] = __( 'Audio Post format updated.', 'jannah_switcher' );
				}

			}
			else{


				# SoundCloud ----------
				if( strpos( $embed_code, 'soundcloud.com' ) !== false ){

					# if iframe Exists so it is an embed code ----------
			  	if( strpos( $embed_code, '<iframe' ) !== false ){
						preg_match('/src="([^"]+)"/', $embed_code, $match);
						$url = str_replace( 'https://w.soundcloud.com/player/?url=', '', $match[1] );
						$url = explode( '&', $url );
						$url = $url[0];
					}

					# Or it is a link for a SoundCloud file ----------
					else{
						$url = $embed_code;
					}


					# Update the video format ----------
					$audio_format_updated = update_post_meta( $id, 'tie_post_head', 'audio' );
					if( $audio_format_updated ){
						$message[] = __( 'Audio Post format updated.', 'jannah_switcher' );
					}

					# Update Sound Cloud ----------
					$soundcloud_format_updated = update_post_meta( $id, 'tie_audio_soundcloud', $url );
					if( $soundcloud_format_updated ){
						$message[] = __( 'SoundCloud updated.', 'jannah_switcher' );
					}

				}
				else{


					# Check if is an iframe is still exists ----------
					if( strpos( $embed_code, '<iframe' ) !== false ){
						$video_updated = update_post_meta( $id, 'tie_embed_code', $embed_code );

					}

					# This is an URl ----------
					else{
						$video_updated = update_post_meta( $id, 'tie_video_url', $embed_code );
					}

				}
			}
		}


		if( $video_updated ){

			# Update the video format ----------
			$video_format_updated = update_post_meta( $id, 'tie_post_head', 'video' );
			if( $video_format_updated ){
				$message[] = __( 'Video format updated.', 'jannah_switcher' );
			}

			$message[] = __( 'Video Resources Updated', 'jannah_switcher' );
		}
  } // Embed Code



	# Reviews ----------
	if( get_post_meta( $id, '_bs_review_enabled', true ) ){

		# Position ----------
		$position = ( get_post_meta( $id, '_bs_review_pos', true ) == 'top' ) ? 'top' : 'bottom';
		update_post_meta( $id, 'taq_review_position', $position );
	}

	# Review Summery ----------
	$review_summary  = get_post_meta( $id, '_bs_review_verdict_summary', true );
	$review_summary .= ' '.get_post_meta( $id, '_bs_review_extra_desc', true );

	if( ! empty( $review_summary ) && $review_summary != ' ' ){
		$review_summary_updated = update_post_meta( $id, 'taq_review_summary', $review_summary );
		if( $review_summary_updated ){
			$message[] = __( 'Review Summary Updated.', 'jannah_switcher') ;
		}
	}

	# Review Criteria ----------
	if( $review_criteria = get_post_meta( $id, '_bs_review_criteria', true ) ){

		$reviews_criteria = array();
		$total_score = $total_counter = 0;

		if( is_array( $review_criteria ) ){

			foreach( $review_criteria as $value ){

				$criteria = array();

				$criteria['name']  = ! empty( $value['label'] ) ? $value['label'] : '';

				if( ! empty( $value['rate'] ) ){

					$the_rate = $value['rate'] * 10;

					$criteria['score'] =  max( 0, min( 100, $the_rate ) );

					$total_score   += $criteria['score'];
					$total_counter ++;
				}

				$reviews_criteria[] = $criteria;
			}
		}


		$reviews_criteria_updated = update_post_meta( $id, 'taq_review_criteria', $reviews_criteria );

		if( $reviews_criteria_updated ){
			$message[] = sprintf(__('%1$s Converted to %2$s', 'jannah_switcher'), '_bs_review_criteria', 'taq_review_criteria' );
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

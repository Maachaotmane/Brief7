<?php
function jannah_switcher_ajax_process_post( $post_id, $theme = false ) {

  $message = array();

	$current_post_data = get_post_meta( $post_id );

	if( ! empty( $current_post_data ) && is_array( $current_post_data ) ){

		extract( $current_post_data );
	}


	# Videos Embed Code ----------
	if( ! empty( $tie_embed_code[0] ) ){

		$embed_code = htmlspecialchars_decode( $tie_embed_code[0] );
		$embed_updated = update_post_meta( $post_id, 'tie_embed_code', $embed_code );

		if( $embed_updated ){
			delete_post_meta( $post_id, 'tie_embed_code' );
			$message[] = __( 'Video Embeded code updated.', 'jannah_switcher' );
		}
	}



	# tie_banner_above Code ----------
	if( ! empty( $tie_banner_above[0] ) ){

		$banner_above = htmlspecialchars_decode( $tie_banner_above[0] );
		$banner_above_updated = update_post_meta( $post_id, 'tie_get_banner_above', $banner_above );

		if( $banner_above_updated ){
			delete_post_meta( $post_id, 'tie_banner_above' );
			$message[] = __( 'Banner Above the Post code updated.', 'jannah_switcher' );
		}
	}



	# tie_banner_below Code ----------
	if( ! empty( $tie_banner_below[0] ) ){

		$banner_below = htmlspecialchars_decode( $tie_banner_below[0] );
		$bbanner_below_updated = update_post_meta( $post_id, 'tie_get_banner_below', $banner_below );

		if( $bbanner_below_updated ){
			delete_post_meta( $post_id, 'tie_banner_below' );
			$message[] = __( 'Banner Below the Post code updated.', 'jannah_switcher' );
		}
	}



	# SoundCloud ----------
	if( ! empty( $tie_post_head[0] ) && $tie_post_head[0] == 'soundcloud' ){

		$update_soundcloud = update_post_meta( $post_id, 'tie_post_head', 'audio' );

		if( $update_soundcloud ){

			$message[] = __( 'Post Format Changed from soundcloud to audio', 'jannah_switcher' );
		}
	}


	# LightBox ----------
	if( ! empty( $tie_post_head[0] ) && $tie_post_head[0] == 'lightbox' ){

		$update_lightbox = update_post_meta( $post_id, 'tie_post_head', 'thumb' );
		update_post_meta( $post_id, 'tie_image_lightbox', 'yes' );

		if( $update_lightbox ){

			$message[] = __( 'Post Format Changed from lightbox to thumb', 'jannah_switcher' );
		}
	}


	# Background ----------
	if( ! empty( $post_background[0] ) ){

		$background = maybe_unserialize( $post_background[0] );

		# Background Color ----------
		if( ! empty( $background['color'] ) && empty( $background_color ) ){

			$background_color = update_post_meta( $post_id, 'background_color', $background['color'] );

			if( $background_color ){

				$message[] = __( 'Background Color updated.', 'jannah_switcher' );
			}
		}


		# Background Image ----------
		if( ! empty( $background['img'] ) && empty( $background_image ) ){
			update_post_meta( $post_id, 'background_type', 'image');
			delete_post_meta( $post_id, 'post_background' );
			$background_image = update_post_meta( $post_id, 'background_image', $background );

			if( $background_image ){

				$message[] = __( 'Background Image updated.', 'jannah_switcher' );
			}
		}
	}



	# There is no title feature in the theme so we check if one of other fields exists to execute the code one time
	if( ! empty( $tie_review_position[0] ) && empty( $taq_review_title[0] ) ){
		$update_new_title = update_post_meta( $post_id, 'taq_review_title',  esc_html__( 'Review Overview', 'jannah' ) );

		if( $update_new_title ){

			$message[] = __( 'Review Title Updated.', 'jannah_switcher' );
		}
	}

	if( ! empty( $tie_review_position[0] ) && empty( $taq_review_position[0] ) ){
		if( $tie_review_position[0] == 'both' ){
			$update_new_position = update_post_meta($post_id, 'taq_review_position', 'top' );
		}
		else{
			$update_new_position = update_post_meta($post_id, 'taq_review_position', $tie_review_position[0] );
		}

		if( $update_new_position ){

			delete_post_meta($post_id, 'tie_review_position');
			$message[] = __( 'Review Position Updated.', 'jannah_switcher' );
		}
	}

	if( ! empty( $tie_review_style[0] ) && empty( $taq_review_style[0] ) ){
		$update_new_style  = update_post_meta($post_id, 'taq_review_style', $tie_review_style[0] );

		if( $update_new_style ){

			delete_post_meta($post_id, 'tie_review_style');
			$message[] = __( 'Review Style Updated.', 'jannah_switcher' );
		}
	}

	if( ! empty( $tie_review_summary[0] ) && empty( $taq_review_summary[0] ) ){
		$update_new_summary = update_post_meta($post_id, 'taq_review_summary', $tie_review_summary[0] );

		if( $update_new_summary ){

			delete_post_meta( $post_id, 'tie_review_summary');

			$message[] = __( 'Review Summary Updated.', 'jannah_switcher' );
		}
	}

	if( ! empty( $tie_review_total[0] ) && empty( $taq_review_total[0] ) ){
		$update_new_total = update_post_meta($post_id, 'taq_review_total', $tie_review_total[0] );

		if( $update_new_total ){

			delete_post_meta($post_id, 'tie_review_total');

			$message[] = __( 'Review Total rate Updated.', 'jannah_switcher' );
		}
	}

	if( ! empty( $tie_review_criteria[0] ) && empty( $taq_review_criteria[0] ) ){
		$update_new_criteria = update_post_meta($post_id, 'taq_review_criteria', maybe_unserialize( $tie_review_criteria[0] ) );

		if( $update_new_criteria ) {

			delete_post_meta( $post_id, 'tie_review_criteria' );

			$message[] = __( 'Review Criteria rate Updated.', 'jannah_switcher' );
		}
	}

	if( ! empty( $tie_review_score[0] ) && empty( $taq_review_score[0] ) ){
		$update_new_score = update_post_meta($post_id, 'taq_review_score', $tie_review_score[0] );

		if( $update_new_score ){

			delete_post_meta($post_id, 'tie_review_score');

			$message[] = __( 'Review Score Updated.', 'jannah_switcher' );
		}
	}





	if( ! empty( $message ) ){

		$message[] =	sprintf(__( 'Successfully switched in %s seconds', 'jannah_switcher' ), timer_stop() );
		return $message;
	}


	return array( __( 'Nothing to Switch.', 'jannah_switcher' ) );


	exit;
}

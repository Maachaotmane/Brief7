<?php

function jannah_switcher_ajax_process_post( $id, $theme = false ) {

  $message = array();


  # --------------
  if( $theme == 'braxton' || $theme == 'maxmag' ){

		if( $post_video = get_post_meta( $id, 'mvp-video-embed', true ) ){
			$video_updated = update_post_meta( $id, 'tie_embed_code', $post_video );
			if( $video_updated ){
				$message[] = __( 'Video data updated', 'jannah_switcher' );
			}
		}

		$format_updated = update_post_meta( $id, 'tie_post_head', 'video' );
		if( $format_updated ){
			$message[] = __( 'Post format updated.', 'jannah_switcher' );
		}

  }

  # --------------
	else{

		# Post Format ----------
		$format = get_post_format( $id );

		if( $format == 'video' || $format == 'audio' || $format == 'gallery' ){

			# Gallery Images ----------
		  if( $format == 'gallery' ){

		  	$format = 'slider';

		  	# -----
		  	$stored_images = get_attached_media( 'image', $id );

		  	if( ! empty( $stored_images ) ){

					$gallery = array();

			  	foreach( $stored_images as $image ){
			  		$gallery[] = array( 'id' => $image->ID );
			  	}

					$gallery_updated = update_post_meta( $id, 'tie_post_gallery', $gallery );

					if( $gallery_updated ){
						$message[] = __( 'Gallery images updated.', 'jannah_switcher' );
					}
		  	}
			}

			# Audio URL ----------
			elseif( $format == 'audio' ){

			  if( $post_audio = get_post_meta( $id, 'mvp-video-embed', true ) ){

					$audio_updated = update_post_meta( $id, 'tie_audio_embed', $post_audio );

					if( $audio_updated ){
						$message[] = __( 'Audio data updated', 'jannah_switcher' );
					}
				}
			}

			# Video URL ----------
			elseif( $format == 'video' ){

			  if( $post_video = get_post_meta( $id, 'mvp-video-embed', true ) ){

					$video_updated = update_post_meta( $id, 'tie_embed_code', $post_video );

					if( $video_updated ){
						$message[] = __( 'Video data updated', 'jannah_switcher' );
					}
				}
			}

		}

		$format_updated = update_post_meta( $id, 'tie_post_head', $format );

		if( $format_updated ){
			$message[] = __( 'Post format updated.', 'jannah_switcher' );
		}


	} // Else;



  # Number of Views ----------
  $views_count = get_post_meta( $id, 'post_views_count', true );

  if( ! empty( $views_count ) ){
		$views_updated = update_post_meta( $id, 'tie_views', (int) $views_count );

		if( $views_updated ){

			$message[] = __( 'post_views_count converted tie_views', 'jannah_switcher' );
		}
	}



	# Post Template ----------
	if( $theme == 'flex-mag' ){

	  if( $post_template = get_post_meta( $id, 'mvp_post_template', true ) ){


	  	if( $post_template == 'temp2' || $post_template == 'temp4' ){
	  		$post_style = 7;
	  		$sidebar    = 'full';
	  	}
	  	elseif( $post_template == 'temp5' ){
	  		$post_style = 4;
	  	}
	  	elseif( $post_template == 'temp6' ){
	  		$post_style = 4;
	  		$sidebar    = 'full';
	  	}


	  	#---
			if( ! empty( $post_style ) ){

				$post_style_updated = update_post_meta( $id, 'tie_post_layout', $post_style );

				if( $post_style_updated ){
					$message[] = __( 'Post Style Updated', 'jannah_switcher' );
				}
			}

			# ---
			if( ! empty( $sidebar ) ){

				$sidebar_updated = update_post_meta( $id, 'tie_sidebar_pos', $sidebar );

				if( $sidebar_updated ){
					$message[] = __( 'Sidebar Updated.', 'jannah_switcher' );
				}
			}

			# ---
			$featured_bg_updated = update_post_meta( $id, 'tie_featured_use_fea', 'yes' );

			if( $featured_bg_updated ){
				$message[] = __( 'Featured Image background Updated.', 'jannah_switcher' );
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

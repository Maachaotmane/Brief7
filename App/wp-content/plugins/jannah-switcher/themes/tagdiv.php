<?php

function jannah_switcher_ajax_process_post( $id, $theme = false ) {

  $message = array();

  # Number of Views ----------
  $views_count = get_post_meta( $id, 'post_views_count', true );

  if( ! empty( $views_count ) ){
		$views_updated = update_post_meta( $id, 'tie_views', (int) $views_count );

		if( $views_updated ){

			$message[] = __( 'post_views_count converted tie_views', 'jannah_switcher' );
		}
	}


	# Post Video ----------
	$format = get_post_format( $id );

	if( $format == 'video' ){
		$video_format_updated = update_post_meta( $id, 'tie_post_head', 'video' );

		if( $video_format_updated ){

			$message[] = __( 'Video format updated.', 'jannah_switcher' );
		}
	}

	# Video URL ----------
  $post_video = get_post_meta( $id, 'td_post_video', true );

  if( ! empty( $post_video['td_video'] ) ){
		$video_updated = update_post_meta( $id, 'tie_video_url', $post_video['td_video'] );

		if( $video_updated ){

			$message[] = __( 'td_video converted to tie_video_url', 'jannah_switcher' );
		}
	}


	# Post Options ----------
  $post_options_keys = array(
		'td_post_template'    => 'tie_post_layout',
		'td_sidebar_position' => 'tie_sidebar_pos',

		'td_primary_cat'      => 'tie_primary_category',
		'td_sidebar'          => 'tie_sidebar_post',
		'td_subtitle'         => 'tie_post_sub_title',
		'td_source'           => 'tie_source',
		'td_via'              => 'tie_via',
		'review'              => 'taq_review_summary',
	);

  $post_options = get_post_meta( $id, 'td_post_theme_settings', true );

  if( ! empty( $post_options ) && is_array( $post_options ) ){

  	# Single Value options ----------
  	foreach( $post_options_keys as $old_option => $new_option ){

  		# If the options is exists update the TieLabs Option ----------
  		if( ! empty( $post_options[ $old_option ] )){

  			# The old option value ----------
  			$old_option_value = $post_options[ $old_option ];


  			# The Post template ----------
  			if( $old_option == 'td_post_template' ){

  				if( $old_option_value == 'single_template_2' ){

						$old_option_value = 3;
  				}
  				elseif( $old_option_value == 'single_template_3' ){

						$old_option_value = ( $format == 'video' ) ? 6 : 4;
  				}
  				elseif( $old_option_value == 'single_template_7' ){

  					$old_option_value = 4;
  				}
  				elseif( $old_option_value == 'single_template_8' ){

  					$old_option_value = 8;


  					update_post_meta( $id, 'tie_post_featured', 'no' );
  					$featured_bg_updated = update_post_meta( $id, 'tie_featured_use_fea', 'yes' );

						# Store the success message ----------
						if( $featured_bg_updated ){
							$message[] = __( 'Featured Image background Updated.', 'jannah_switcher' );
						}

  				}
  				elseif( $old_option_value == 'single_template_9' || $old_option_value == 'single_template_10' ){

  					$old_option_value = 6;
  				}
  				elseif( $old_option_value == 'single_template_12' || $old_option_value == 'single_template_13' ){

  					$old_option_value = 7;
  				}
  				else{
  					$old_option_value = '';
  				}
  			}


  			# Source ----------
  			elseif( $old_option == 'td_source' ){

  				$source_url = ! empty( $post_options['td_source_url'] ) ? $post_options['td_source_url'] : '';

  				$old_option_value = array(
  					array(
  						'text' => $post_options['td_source'],
  						'url'  => $source_url,
  					),
  				);
  			}


  			# Via ----------
  			elseif( $old_option == 'td_via' ){

  				$via_url = ! empty( $post_options['td_via_url'] ) ? $post_options['td_via_url'] : '';

  				$old_option_value = array(
  					array(
  						'text' => $post_options['td_via'],
  						'url'  => $via_url,
  					),
  				);
  			}


  			# The Sidebar Position ----------
  			elseif( $old_option == 'td_sidebar_position' ){

  				if( $old_option_value == 'sidebar_left' ){
						$old_option_value = 'left';
  				}
  				elseif( $old_option_value == 'sidebar_right' ){
						$old_option_value = 'right';
  				}
  				elseif( $old_option_value == 'no_sidebar' ){
  					$old_option_value = 'full';
  				}
  			}


  			# The Custom Sidebar ----------
  			elseif( $old_option == 'td_sidebar' ){

  				$theme_options = get_option( 'tie_jannah_options' );

  				if( ! empty( $theme_options['sidebars'] ) && is_array( $theme_options['sidebars'] ) ){

  					if ( ! in_array( $old_option_value, $theme_options['sidebars'] ) ){
							$theme_options['sidebars'][] = $old_option_value;
							$sidebar_updated = true;
						}
					}
					else{
						$theme_options['sidebars'] = array( $old_option_value );
						$sidebar_updated = true;
					}

					if( isset( $sidebar_updated ) ){
						$custom_sidebars = update_option( 'tie_jannah_options', $theme_options );

							if( $custom_sidebars ){

								$message[] = __( 'Custom Sidebars List Updated.', 'jannah_switcher' );
							}
					}
				}



  			if( ! empty( $old_option_value ) ){

  				$option_updated = update_post_meta( $id, $new_option, $old_option_value );

					if( $option_updated ){

						$message[] = sprintf(__('%1$s Converted to %2$s', 'jannah_switcher'), $old_option, $new_option );
					}

  			}
  		}
  	}


  	# Arrays options ----------
  	if( ! empty( $post_options['has_review'] ) ){

			update_post_meta( $id, 'taq_review_position', 'bottom' );

			if( $post_options['has_review'] == 'rate_stars' ){

				$old_option_value  = 'stars';
				$key_option_name   = 'p_review_stars';
				$correction_factor = 20;
			}
			elseif( $post_options['has_review'] == 'rate_percent' ){

				$old_option_value  = 'percentage';
				$key_option_name   = 'p_review_percents';
				$correction_factor = 1;
			}
			elseif( $post_options['has_review'] == 'rate_point' ){

				$old_option_value  = 'points';
				$key_option_name   = 'p_review_points';
				$correction_factor = 10;
			}

  		$reviews_style_updated = update_post_meta( $id, 'taq_review_style', $old_option_value );

    	if( $reviews_style_updated ){

				$message[] = sprintf(__('%1$s Converted to %2$s', 'jannah_switcher'), 'has_review', 'taq_review_style' );
			}
  	}


  	if( ! empty( $post_options[ $key_option_name ] ) && is_array( $post_options[ $key_option_name ] ) ){

			$reviews_criteria = array();
			$total_score = $total_counter = 0;

			foreach( $post_options[ $key_option_name ] as $value ){
				$criteria = array();

				$criteria['name']  = ! empty( $value['desc'] ) ? $value['desc'] : '';

				if( ! empty( $value['rate'] ) ){

					$the_rate = $value['rate'] * $correction_factor;

					$criteria['score'] =  max( 0, min( 100, $the_rate ) );

					$total_score   += $criteria['score'];
					$total_counter ++;
				}

				$reviews_criteria[] = $criteria;
			}


  		$reviews_criteria_updated = update_post_meta( $id, 'taq_review_criteria', $reviews_criteria );

			if( $reviews_criteria_updated ){

				$message[] = sprintf(__('%1$s Converted to %2$s', 'jannah_switcher'), $key_option_name, 'taq_review_criteria' );
			}



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

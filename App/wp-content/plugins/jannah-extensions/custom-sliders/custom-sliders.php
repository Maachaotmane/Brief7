<?php
/**
 * TieLabs Custom Slider
 *
 * @package Jannah
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/*-----------------------------------------------------------------------------------*/
# Register the custom slider post type
/*-----------------------------------------------------------------------------------*/
add_action( 'init', 'jannah_extensions_slider_register' );
function jannah_extensions_slider_register() {

	$args = array(
		'labels' => array(
			'name'          => esc_html__( 'Custom Sliders', 'jannah-extensions' ),
			'singular_name' => esc_html__( 'Slider',         'jannah-extensions' ),
			'add_new_item'  => esc_html__( 'Add New Slider', 'jannah-extensions' ),
		),
		'public'              => false,
		'show_ui'             => true,
		'menu_icon'           => 'dashicons-format-gallery',
		'can_export'          => true,
		'exclude_from_search' => true,
		'capability_type'     => 'post',
		'hierarchical'        => false,
		'menu_position'       => 10,
		'rewrite'             => array( 'slug' => 'slider' ),
		'supports'            => array( 'title' ),
	);

	register_post_type( 'tie_slider' , $args );
}


/*-----------------------------------------------------------------------------------*/
# custom slider meta box
/*-----------------------------------------------------------------------------------*/
add_action( 'edit_form_after_title', 'jannah_extensions_slider_add_slides' );
function jannah_extensions_slider_add_slides() {

	$post_id      = get_the_id();
	$current_page = get_current_screen();

	if( get_post_type( $post_id ) != 'tie_slider' || $current_page->post_type != 'tie_slider' ){
		return;
	}

	$custom  = get_post_custom( $post_id );

	if( ! empty( $custom['custom_slider'][0] )){
		$slider = maybe_unserialize( $custom['custom_slider'][0] );
	}

	wp_enqueue_media();
	?>

  <script>
	  jQuery(document).ready(function() {

			//Delet Block ----------
			jQuery(document).on('click', '.tie-delete-slide' , function(){
				jQuery(this).closest( 'li' ).addClass('removed').fadeOut(function() {
					jQuery(this).remove();
				});
			});

			jQuery(function() {
				jQuery( "#tie-slider-items ul" ).sortable({placeholder: "tie-state-highlight"});
			});

			/* Uploading files */
			var tie_uploader;
			jQuery(document).on( 'click', '#upload_add_slide' , function(event){

				event.preventDefault();
				tie_uploader = wp.media.frames.tie_uploader = wp.media({
					title: '<?php esc_html_e( 'Insert Images', 'jannah-extensions' ) ?>',
					library: {
						type: 'image'
					},
					button: {
						text: '<?php esc_html_e( 'Select', 'jannah-extensions' ) ?>',
					},
					multiple: true
				});

				tie_uploader.on( 'select', function() {
					var selection = tie_uploader.state().get('selection');

					selection.map( function( attachment ) {
						attachment = attachment.toJSON();
						jQuery( '#tie-slider-items ul' ).append( '\
							<li id="listItem_'+ nextCell +'" class="ui-state-default">\
								<div class="slider-content">\
									<div class="slider-img">\
										<img src="'+attachment.url+'" alt="">\
									</div>\
									<div class="slider-option-item">\
										<label for="custom_slider['+ nextCell +'][title]">\
											<span class="tie-slider-label"><?php esc_html_e( 'Slide Title:', 'jannah-extensions' ) ?></span>\
											<input id="custom_slider['+ nextCell +'][title]" name="custom_slider['+ nextCell +'][title]" value="" type="text" />\
										</label>\
									</div>\
									<div class="slider-option-item">\
										<label for="custom_slider['+ nextCell +'][link]">\
											<span class="tie-slider-label"><?php esc_html_e( 'Slide Link:', 'jannah-extensions' ) ?></span>\
											<input placeholder="http://" id="custom_slider['+ nextCell +'][link]" name="custom_slider['+ nextCell +'][link]" value="" type="text" />\
										</label>\
									</div>\
									<div class="slider-option-item">\
										<label for="custom_slider['+ nextCell +'][caption]">\
											<span class="tie-slider-label"><?php esc_html_e( 'Slide Caption:', 'jannah-extensions' ) ?></span>\
											<textarea name="custom_slider['+ nextCell +'][caption]" id="custom_slider['+ nextCell +'][caption]"></textarea>\
										</label>\
									</div>\
									<input id="custom_slider['+ nextCell +'][id]" name="custom_slider['+ nextCell +'][id]" value="'+attachment.id+'" type="hidden" />\
									<a class="tie-delete-slide dashicons dashicons-trash"></a>\
								</div>\
							</li>\
						');
						nextCell ++ ;
					});
				});

				tie_uploader.open();
			});

		});
	</script>

	<div id="tie-slider-items">
		<ul>
			<?php

				$i = 0;
				if( ! empty( $slider ) && is_array( $slider) ){
					foreach( $slider as $slide ){
						$i++;
						?>

						<li id="listItem_<?php echo $i ?>" class="ui-state-default">

							<div class="slider-content">

								<div class="slider-img">
									<?php echo wp_get_attachment_image( $slide['id'] , 'thumbnail' );  ?>
								</div>

								<div class="slider-option-item">
									<label for="custom_slider[<?php echo $i ?>][title]">
										<span class="tie-slider-label"><?php esc_html_e( 'Slide Title:', 'jannah-extensions' ) ?> </span>
										<input id="custom_slider[<?php echo $i ?>][title]" name="custom_slider[<?php echo $i ?>][title]" value="<?php echo esc_attr( $slide['title']) ?>" type="text" />
									</label>
								</div>

								<div class="slider-option-item">
									<label for="custom_slider[<?php echo $i ?>][link]">
										<span class="tie-slider-label"><?php esc_html_e( 'Slide Link:', 'jannah-extensions' ) ?></span>
										<input placeholder="http://" id="custom_slider[<?php echo $i ?>][link]" name="custom_slider[<?php echo $i ?>][link]" value="<?php  echo esc_attr( $slide['link'] ) ?>" type="text" />
									</label>
								</div>

								<div class="slider-option-item">
									<label for="custom_slider[<?php echo $i ?>][caption]">
										<span class="tie-slider-label"><?php esc_html_e( 'Slide Caption:', 'jannah-extensions' ) ?></span>
										<textarea name="custom_slider[<?php echo $i ?>][caption]" id="custom_slider[<?php echo $i ?>][caption]"><?php echo esc_attr($slide['caption']) ; ?></textarea>
									</label>
								</div>

								<input id="custom_slider[<?php echo $i ?>][id]" name="custom_slider[<?php echo $i ?>][id]" value="<?php  echo $slide['id']  ?>" type="hidden" />
								<a class="tie-delete-slide dashicons dashicons-trash"></a>
							</div>
						</li>
						<?php
					}
				}

			?>
		</ul>

		<input id="upload_add_slide" type="button" class="tie-primary-button button button-primary button-large" value="<?php esc_html_e( 'Add New Slide', 'jannah-extensions' ) ?>">
	</div>

	<script> var nextCell = <?php echo $i+1 ?>;</script>
  <?php
}


/*-----------------------------------------------------------------------------------*/
# Save the custom slider
/*-----------------------------------------------------------------------------------*/
add_action( 'save_post', 'jannah_extensions_save_slider' );
function jannah_extensions_save_slider( $post_id ){

	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
		return $post_id;
	}

	if( ! empty( $_POST['custom_slider'] )){
		update_post_meta( $post_id, 'custom_slider', $_POST['custom_slider']);
	}
	else{
		delete_post_meta( $post_id, 'custom_slider' );
	}
}


/*-----------------------------------------------------------------------------------*/
# Slider page columns
/*-----------------------------------------------------------------------------------*/
add_filter( 'manage_edit-tie_slider_columns', 'jannah_extensions_slider_edit_columns' );
function jannah_extensions_slider_edit_columns($columns){

	$columns = array(
		'cb'     => '<input type="checkbox" />',
		'title'  => esc_html__( 'Title',            'jannah-extensions' ),
		'slides' => esc_html__( 'Number of slides', 'jannah-extensions' ),
		'id'     => esc_html__( 'ID',               'jannah-extensions' ),
    'date'   => esc_html__( 'Date',             'jannah-extensions' ),
  );

  return $columns;
}


/*-----------------------------------------------------------------------------------*/
# Slider page columns content
/*-----------------------------------------------------------------------------------*/
add_action( 'manage_tie_slider_posts_custom_column', 'jannah_extensions_slider_custom_columns' );
function jannah_extensions_slider_custom_columns($column){
	$post_id = get_the_id();

	switch ( $column ) {
		case 'slides':
			$custom_slider_args = array(
				'post_type'     => 'tie_slider',
				'p'             => $post_id,
				'no_found_rows' => 1,
			);

			$custom_slider = new WP_Query( $custom_slider_args );

			while ( $custom_slider->have_posts() ){
				$custom_slider->the_post();
				$number = 0;
				$custom = get_post_meta( $post_id, 'custom_slider', true );

				if( ! empty( $custom )){
					$number = count( $custom );
				}

				echo $number;
			}

			wp_reset_postdata();
			break;

		case 'id':
			echo $post_id;
			break;
	}
}

?>

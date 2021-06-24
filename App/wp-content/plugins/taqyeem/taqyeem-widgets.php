<?php

/*-----------------------------------------------------------------------------------*/
# Post Reviews Widget
/*-----------------------------------------------------------------------------------*/

/**
 * Register the widget.
 */
add_action( 'widgets_init', 'taqyeem_review_register_widgets' );
function taqyeem_review_register_widgets() {
	register_widget( 'TAQYEEM_REVIEW_WIDGET' );
}

/**
 * Widget API: TAQYEEM_REVIEW_WIDGET class
 */
class TAQYEEM_REVIEW_WIDGET extends WP_Widget {

	public function __construct() {
		$widget_ops  = array( 'classname' => 'taqyeem-review-widget'  );
		$control_ops = array( 'id_base' => 'taqyeem-review-widget' );
		parent::__construct( 'taqyeem-review-widget',TIE_TAQYEEM .' - Review Box', $widget_ops, $control_ops );
	}

	/**
	 * Outputs the content for the widget instance.
	 */
	public function widget( $args, $instance ) {

		extract( $args );

		$post_id  = get_the_ID();
		$get_meta = get_post_custom( $post_id );

		if ( is_singular() && ! empty( $get_meta['taq_review_position'][0] )){

			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			echo $before_widget;

			if ( !empty($instance['title']) ){
				echo $before_title . $title . $after_title;
			}

			echo taqyeem_get_review( 'review-bottom' );

			echo $after_widget;
		}
	}

	/**
	 * Handles updating settings for widget instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		return $instance;
	}

	/**
	 * Outputs the settings form for the widget.
	 */
	public function form( $instance ) {
		$defaults = array( 'title' =>__( 'Review Overview', 'taq' ) );
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title    = isset( $instance['title'] ) ? esc_attr( $instance['title']) : ''; ?>

		<p><em style="color:red;"><?php _e( 'This Widget appears in single post only .' , 'taq') ?></em></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title : ' , 'taq') ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title ?>" class="widefat" type="text" />
		</p>

	<?php
	}
}



/*-----------------------------------------------------------------------------------*/
# Best, latest & random Review Widget
/*-----------------------------------------------------------------------------------*/

/**
 * Register the widget.
 */
add_action( 'widgets_init', 'taqyeem_get_reviews_register_widgets' );
function taqyeem_get_reviews_register_widgets() {
	register_widget( 'TAQYEEM_GET_REVIEWS' );
}

/**
 * Widget API: TAQYEEM_GET_REVIEWS class
 */
class TAQYEEM_GET_REVIEWS extends WP_Widget {

	public function __construct() {
		$widget_ops 	= array( 'classname' => 'reviews-posts-widget' );
		$control_ops 	= array( 'id_base' => 'reviews-posts-widget' );
		parent::__construct( 'reviews-posts-widget',TIE_TAQYEEM .' - Reviews Posts', $widget_ops, $control_ops );
	}

	/**
	 * Outputs the content for the widget instance.
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$num_posts     = ! empty( $instance['num_posts'] ) ? $instance['num_posts'] : 5;
		$cats_id       = ! empty( $instance['cats_id'] ) ? $instance['cats_id'] : '';
		$thumb_size    = ! empty( $instance['thumb_size'] ) ? array( $instance['thumb_size'], $instance['thumb_size'] ): array( 50, 50 );
		$reviews_order = ! empty( $instance['reviews_order'] ) ? $instance['reviews_order'] : 'latest';


		if( empty( $instance['thumb'] ) ){
			$thumb_size = false;
		}

		echo $before_widget;

		if ( !empty($instance['title']) ){
			echo $before_title . $title . $after_title;
		}

		taqyeem_get_reviews( $num_posts, $reviews_order, $thumb_size, $cats_id );

		echo $after_widget;
	}

	/**
	 * Handles updating settings for widget instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                  = $old_instance;
		$instance['title']         = sanitize_text_field( $new_instance['title'] );
		$instance['num_posts']     = $new_instance['num_posts'];
		$instance['cats_id']       = implode(',' , $new_instance['cats_id'] );
		$instance['thumb']         = $new_instance['thumb'];
		$instance['thumb_size']    = $new_instance['thumb_size'];
		$instance['reviews_order'] = $new_instance['reviews_order'];
		return $instance;
	}

	/**
	 * Outputs the settings form for the widget.
	 */
	public function form( $instance ) {
		$defaults = array(
			'title'         =>__( 'Best Reviews' , 'taq'),
			'num_posts'     => '5',
			'reviews_order' => 'best',
			'cats_id'       => '1',
			'thumb'         => 'true',
			'thumb_size'    => 50,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title         = isset( $instance['title'] ) ? esc_attr( $instance['title']) : '';
		$num_posts     = isset( $instance['num_posts'] ) ? esc_attr( $instance['num_posts']) : 5;
		$reviews_order = isset( $instance['reviews_order'] ) ? $instance['reviews_order'] : 'latest';
		$thumb         = isset( $instance['thumb'] ) ? 'true' : '';
		$thumb_size    = isset( $instance['thumb_size'] ) ? $instance['thumb_size'] : 50;
		$cats_id       = array();

		if( ! empty( $instance['cats_id'] )){
			$cats_id = explode ( ',', $instance['cats_id'] ) ;
		}

		$categories_obj = get_categories();
		$categories     = array();

		foreach ($categories_obj as $pn_cat ){
			$categories[$pn_cat->cat_ID] = $pn_cat->cat_name;
		}
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title : ' , 'taq') ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat" type="text" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'num_posts' ); ?>"><?php _e( 'Number of posts to show :' , 'taq') ?></label>
			<input id="<?php echo $this->get_field_id( 'num_posts' ); ?>" name="<?php echo $this->get_field_name( 'num_posts' ); ?>" value="<?php echo $num_posts; ?>" type="text" size="3" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'cats_id' ); ?>"><?php _e( 'Categories :' , 'taq') ?></label>
			<select multiple="multiple" id="<?php echo $this->get_field_id( 'cats_id' ); ?>[]" name="<?php echo $this->get_field_name( 'cats_id' ); ?>[]">
				<?php foreach ($categories as $key => $option) { ?>
				<option value="<?php echo $key ?>" <?php if ( in_array( $key , $cats_id ) ) { echo ' selected="selected"' ; } ?>><?php echo $option; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'reviews_order' ); ?>"><?php _e( 'Posts Order :' , 'taq') ?></label>
			<select id="<?php echo $this->get_field_id( 'reviews_order' ); ?>" name="<?php echo $this->get_field_name( 'reviews_order' ); ?>" >
				<option value="latest" <?php selected( $reviews_order, 'latest' ); ?>><?php _e( 'Recent Reviews' , 'taq') ?></option>
				<option value="random" <?php selected( $reviews_order, 'random' ); ?>><?php _e( 'Random Reviews' , 'taq') ?></option>
				<option value="best"   <?php selected( $reviews_order,  'best'  ); ?>><?php _e( 'Best Reviews' , 'taq') ?></option>
			</select>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'thumb' ); ?>" name="<?php echo $this->get_field_name( 'thumb' ); ?>" value="true" <?php checked( $thumb, 'true' ); ?> type="checkbox" />
			<label for="<?php echo $this->get_field_id( 'thumb' ); ?>"> <?php _e( 'Display Thumbinals' , 'taq') ?></label>
		</p>

		<?php

		if( ! has_filter( 'tie_taqyeem_widget_thumb_size' )){ ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'thumb_size' ); ?>"><?php _e( 'Thumbinals Size :' , 'taq') ?></label>
				<input id="<?php echo $this->get_field_id( 'thumb_size' ); ?>" name="<?php echo $this->get_field_name( 'thumb_size' ); ?>" value="<?php echo $thumb_size; ?>" type="text" size="3" />
			</p>
			<?php
		}
	}
}



/*-----------------------------------------------------------------------------------*/
# Post Types Review Widget
/*-----------------------------------------------------------------------------------*/

/**
 * Register the widget.
 */
add_action( 'widgets_init', 'taqyeem_types_get_reviews_register_widgets' );
function taqyeem_types_get_reviews_register_widgets() {
	register_widget( 'TAQYEEM_TYPES_GET_REVIEWS' );
}

/**
 * Widget API: TAQYEEM_TYPES_GET_REVIEWS class
 */
class TAQYEEM_TYPES_GET_REVIEWS extends WP_Widget {

	public function __construct() {
		$widget_ops 	= array( 'classname' => 'reviews-posts-widget' );
		$control_ops 	= array( 'width' => 250, 'height' => 350, 'id_base' => 'reviews-types-widget' );
		parent::__construct( 'reviews-types-widget',TIE_TAQYEEM .' - Reviews Post Types', $widget_ops, $control_ops );
	}

	/**
	 * Outputs the content for the widget instance.
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$num_posts     = ! empty( $instance['num_posts'] ) ? $instance['num_posts'] : 5;
		$types         = ! empty( $instance['types'] ) ? $instance['types'] : '';
		$thumb_size    = ! empty( $instance['thumb_size'] ) ? array( $instance['thumb_size'], $instance['thumb_size'] ): array( 50, 50 );
		$reviews_order = ! empty( $instance['reviews_order'] ) ? $instance['reviews_order'] : 'latest';


		if( empty( $instance['thumb'] ) ){
			$thumb_size = false;
		}

		echo $before_widget;

		if ( !empty($instance['title']) ){
			echo $before_title . $title . $after_title;
		}

		taqyeem_get_types_reviews( $num_posts, $reviews_order, $thumb_size, $types );

		echo $after_widget;
	}

	/**
	 * Handles updating settings for widget instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                  = $old_instance;
		$instance['title']         = sanitize_text_field( $new_instance['title'] );
		$instance['num_posts']     = $new_instance['num_posts'];
		$instance['types']         = $new_instance['types'];
		$instance['thumb']         = $new_instance['thumb'];
		$instance['thumb_size']    = $new_instance['thumb_size'];
		$instance['reviews_order'] = $new_instance['reviews_order'];
		return $instance;
	}

	/**
	 * Outputs the settings form for the widget.
	 */
	public function form( $instance ) {
		$defaults = array(
			'title'         =>__( 'Best Reviews' , 'taq'),
			'num_posts'     => '5',
			'reviews_order' => 'best',
			'thumb'         => 'true',
			'thumb_size'    => 50,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title         = isset( $instance['title'] ) ? esc_attr( $instance['title']) : '';
		$num_posts     = isset( $instance['num_posts'] ) ? esc_attr( $instance['num_posts']) : 5;
		$reviews_order = isset( $instance['reviews_order'] ) ? $instance['reviews_order'] : 'latest';
		$types         = isset( $instance['types'] ) ? $instance['types'] : '';
		$thumb         = isset( $instance['thumb'] ) ? 'true' : '';
		$thumb_size    = isset( $instance['thumb_size'] ) ? $instance['thumb_size'] : 50;

		$post_types = array();
		$post_types = get_post_types( array('_builtin' => false ) ,'names');
		$post_types['post'] = 'post';
		$post_types['page'] = 'page';

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title : ' , 'taq') ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat" type="text" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'num_posts' ); ?>"><?php _e( 'Number of posts to show :' , 'taq') ?></label>
			<input id="<?php echo $this->get_field_id( 'num_posts' ); ?>" name="<?php echo $this->get_field_name( 'num_posts' ); ?>" value="<?php echo $num_posts; ?>" type="text" size="3" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'types' ); ?>"><?php _e( 'Post Types :' , 'taq') ?></label>
			<select multiple="multiple" id="<?php echo $this->get_field_id( 'types' ); ?>[]" name="<?php echo $this->get_field_name( 'types' ); ?>[]">
				<?php foreach ($post_types as $key => $option) { ?>
				<option value="<?php echo $key ?>" <?php if ( !empty( $types ) && in_array( $key , $types ) ) { echo ' selected="selected"' ; } ?>><?php echo $option; ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'reviews_order' ); ?>"><?php _e( 'Posts Order :' , 'taq') ?></label>
			<select id="<?php echo $this->get_field_id( 'reviews_order' ); ?>" name="<?php echo $this->get_field_name( 'reviews_order' ); ?>" >
				<option value="latest" <?php selected( $reviews_order, 'latest' ); ?>><?php _e( 'Recent Reviews' , 'taq') ?></option>
				<option value="random" <?php selected( $reviews_order, 'random' ); ?>><?php _e( 'Random Reviews' , 'taq') ?></option>
				<option value="best"   <?php selected( $reviews_order,  'best'  ); ?>><?php _e( 'Best Reviews' , 'taq') ?></option>
			</select>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'thumb' ); ?>" name="<?php echo $this->get_field_name( 'thumb' ); ?>" value="true" <?php checked( $thumb, 'true' ); ?> type="checkbox" />
			<label for="<?php echo $this->get_field_id( 'thumb' ); ?>"> <?php _e( 'Display Thumbinals' , 'taq') ?></label>
		</p>

		<?php

		if( ! has_filter( 'tie_taqyeem_widget_thumb_size' )){ ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'thumb_size' ); ?>"><?php _e( 'Thumbinals Size :' , 'taq') ?></label>
				<input id="<?php echo $this->get_field_id( 'thumb_size' ); ?>" name="<?php echo $this->get_field_name( 'thumb_size' ); ?>" value="<?php echo $thumb_size; ?>" type="text" size="3" />
			</p>
			<?php
		}
	}
}

?>
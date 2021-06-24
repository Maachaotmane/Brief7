<?php
/**
 * The template used for displaying promotion headline
 *
 * @package BusinessFocus
 */

$enable_section = get_theme_mod( 'photofocus_promo_head_visibility', 'disabled' );

if ( ! photofocus_check_section( $enable_section ) ) {
	// Bail if hero content is not enabled
	return;
}

$photofocus_type = get_theme_mod( 'photofocus_promo_head_type', 'page' );

if ( 'page' === $photofocus_type && $photofocus_id = get_theme_mod( 'photofocus_promotion_headline' ) ) {
	$args['page_id'] = absint( $photofocus_id );
}

// If $args is empty return false
if ( empty( $args ) ) {
	return;
}

// Create a new WP_Query using the argument previously created
$promotion_headline_query = new WP_Query( $args );
if ( $promotion_headline_query->have_posts() ) :
	while ( $promotion_headline_query->have_posts() ) :
		$promotion_headline_query->the_post();

		$sub_title = get_theme_mod( 'photofocus_promo_head_sub_title' );
		?>
		<div id="promotion-section" class="section promotion-section content-align-center text-align-center content-frame content-color-white promotion-headline-one">
			<div class="wrapper section-content-wrapper">
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="hentry-inner">
						<?php photofocus_post_thumbnail( '','html-with-bg' ); // photofocus_post_thumbnail( $image_size, $photofocus_type = 'html', $echo = true, $no_thumb = false ). ?>

						<div class="content-wrapper">
							<div class="entry-container">
								<div class="entry-container-frame">
									<header class="entry-header section-title-wrapper">
										<?php if ( $sub_title ) : ?>
											<div class="section-subtitle">
												<?php
												$sub_title = apply_filters( 'the_content', $sub_title );
												echo wp_kses_post( str_replace( ']]>', ']]&gt;', $sub_title ) );
												?>
											</div><!-- .section-description-wrapper -->
										<?php endif; ?>

										<?php the_title( '<h2 class="entry-title section-title">', '</h2>' ); ?>
									</header><!-- .entry-header -->

									<?php
										$image = get_theme_mod( 'photofocus_promo_head_logo_image' );
										if ( $image ) : ?>
											<div class="post-thumbnail">
												<img src="<?php echo esc_url( $image ); ?>">
											</div><!-- .post-thumbnail-->
										<?php endif; ?>

									<div class="entry-content">
										<?php
										the_content();

										wp_link_pages( array(
											'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'ecommercefocus' ) . '</span>',
											'after'       => '</div>',
											'link_before' => '<span class="page-number">',
											'link_after'  => '</span>',
											'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'ecommercefocus' ) . ' </span>%',
											'separator'   => '<span class="screen-reader-text">, </span>',
										) );
										?>
									</div><!-- .entry-content -->
								</div><!-- .entry-container-frame -->
							</div><!-- .entry-container -->
						</div><!-- .content-wrapper -->
					</div><!-- .hentry-inner -->
				</article><!-- #post-## -->
			</div><!-- .wrapper -->
		</div><!-- .section -->
	<?php
	endwhile;
	wp_reset_postdata();
endif;

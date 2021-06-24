<?php
/**
 * The template for displaying Woo Products Showcase
 *
 * @package Audioman
 */

if ( ! class_exists( 'WooCommerce' ) ) {
    // Bail if WooCommerce is not installed
    return;
}

$enable_content = get_theme_mod( 'photofocus_woo_recent_products_option', 'disabled' );

if ( ! photofocus_check_section( $enable_content ) ) {
	// Bail if featured content is disabled.
	return;
}

$number         = get_theme_mod( 'photofocus_woo_recent_products_number', 4 );
$columns        = 4;

$shortcode = '[products';

if ( $number ) {
	$shortcode .= ' limit="' . esc_attr( $number ) . '"';
}

if ( $columns ) {
	$shortcode .= ' columns="' . absint( $columns ) . '"';
}

$shortcode .= ']';

$title     = get_theme_mod( 'photofocus_woo_recent_products_headline', esc_html__( 'Products Showcase', 'ecommercefocus' ) );
$sub_title = get_theme_mod( 'photofocus_woo_recent_products_subheadline', esc_html__( 'This season\'s top sold products', 'ecommercefocus' ) );

?>

<div id="product-content-section" class="product-content-section section">
	<div class="wrapper">
		<?php if ( $title || $sub_title ) : ?>
			<div class="section-heading-wrapper product-section-headline">
				<?php if ( $sub_title ) : ?>
					<div class="section-subtitle">
						<p class="section-subtitle">
							<?php echo wp_kses_post( $sub_title ); ?>
						</p>
					</div><!-- .taxonomy-description-wrapper -->
				<?php endif; ?>

				<?php if ( '' != $title ) : ?>
					<div class="section-title-wrapper">
						<h2 class="section-title"><?php echo wp_kses_post( $title ); ?></h2>
					</div><!-- .section-title-wrapper -->
				<?php endif; ?>

			</div><!-- .section-heading-wrapper -->
		<?php endif; ?>

		<div class="section-content-wrapper product-content-wrapper">
			<?php echo do_shortcode( $shortcode ); ?>
		</div><!-- .section-content-wrapper -->

		<?php
			$target = get_theme_mod( 'photofocus_woo_recent_products_target' ) ? '_blank': '_self';
			$link   = get_theme_mod( 'photofocus_woo_recent_products_link', get_permalink( wc_get_page_id( 'shop' ) ) );
			$text   = get_theme_mod( 'photofocus_woo_recent_products_text' );

			if ( $text ) :
		?>
			<p class="view-more">
				<a class="button" target="<?php echo $target; ?>" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $text ); ?></a>
			</p>
		<?php endif; ?>
	</div><!-- .wrapper -->
</div><!-- .sectionr -->

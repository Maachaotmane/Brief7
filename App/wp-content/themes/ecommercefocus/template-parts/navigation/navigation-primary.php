<?php
/**
 * Primary Menu Template
 *
 * @package PhotoFocus Pro
 */

?>
<div id="site-header-menu" class="site-header-menu">
	<div id="primary-menu-wrapper" class="menu-wrapper">
		<div class="menu-toggle-wrapper">
			<button id="menu-toggle"  class="menu-toggle" aria-controls="top-menu" aria-expanded="false"><?php echo photofocus_get_svg( array( 'icon' => 'bars' ) ); echo photofocus_get_svg( array( 'icon' => 'close' ) ); ?><span class="menu-label"><?php echo esc_html_e( 'Menu', 'ecommercefocus' ); ?></span></button>
		</div><!-- .menu-toggle-wrapper -->

		<?php
		if ( function_exists( 'photofocus_cart_link' ) && class_exists( 'WooCommerce' ) ) {
			photofocus_cart_link();
		}
		?>


		<?php
		if ( function_exists( 'photofocus_myaccount_icon_link' ) && class_exists( 'WooCommerce' ) ) {
			photofocus_myaccount_icon_link();
		}
		?>

		<div class="menu-inside-wrapper">
			<?php if ( has_nav_menu( 'primary-menu' ) ) : ?>
				<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'ecommercefocus' ); ?>">
					<?php
						wp_nav_menu( array(
								'container'      => '',
								'theme_location' => 'primary-menu',
								'menu_id'        => 'primary-menu',
								'menu_class'     => 'menu nav-menu',
							)
						);
					?>
				</nav><!-- .main-navigation -->
			<?php else : ?>
				<nav id="site-navigation" class="main-navigation default-page-menu" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'ecommercefocus' ); ?>">
					<?php wp_page_menu(
						array(
							'menu_class' => 'primary-menu-container',
							'before'     => '<ul id="menu-primary-items" class="menu nav-menu">',
							'after'      => '</ul>',
						)
					); ?>
				</nav><!-- .main-navigation -->
			<?php endif; ?>

			<div class="mobile-social-search">
				<div class="search-container">
					<?php get_search_form(); ?>
				</div>

				<?php if ( has_nav_menu( 'social-menu' ) ) : ?>
					<div id="header-menu-social" class="menu-social"><?php get_template_part('template-parts/navigation/navigation', 'social'); ?></div>
				<?php endif; ?>

			</div><!-- .mobile-social-search -->
		</div><!-- .menu-inside-wrapper -->
	</div><!-- #primary-menu-wrapper.menu-wrapper -->

	<?php if( class_exists( 'WooCommerce' ) ) {
		photofocus_header_cart(); 
	} ?>

	<?php if ( has_nav_menu( 'social-menu' ) ) : ?>
		<div id="social-menu-wrapper" class="menu-wrapper">
			<div class="menu-toggle-wrapper">
				<button id="share-toggle"  class="menu-toggle toggle-top share-toggle" aria-controls="top-menu" aria-expanded="false"><?php echo photofocus_get_svg( array( 'icon' => 'share' ) ); echo photofocus_get_svg( array( 'icon' => 'close' ) ); ?><span class="share-label screen-reader-text"><?php echo esc_html_e( 'Social Share', 'ecommercefocus' ); ?></span></button>

			</div><!-- .menu-toggle-wrapper -->

			<div class="menu-inside-wrapper">
				<?php get_template_part( 'template-parts/navigation/navigation', 'social' ); ?>
			</div><!-- .menu-inside-wrapper -->
		</div><!-- .menu-wrapper -->
	<?php endif; ?>

	<div id="primary-search-wrapper" class="menu-wrapper">
		<div class="menu-toggle-wrapper">
			<button id="search-toggle" class="menu-toggle search-toggle"><?php echo photofocus_get_svg( array( 'icon' => 'search' ) ); echo photofocus_get_svg( array( 'icon' => 'close' ) ); ?><span class="screen-reader-text"><?php esc_html_e( 'Search', 'ecommercefocus' ); ?></span></button>
		</div><!-- .menu-toggle-wrapper -->

		<div class="menu-inside-wrapper">
			<div class="search-container">
				<?php get_Search_form(); ?>
			</div>
		</div><!-- .menu-inside-wrapper -->
	</div><!-- #social-search-wrapper.menu-wrapper -->
</div><!-- .site-header-menu -->

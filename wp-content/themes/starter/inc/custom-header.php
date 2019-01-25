<?php
/**
 * Sample implementation of the Custom Header feature
 *
 * You can add an optional custom header image to header.php like so ...
 *
	<?php the_header_image_tag(); ?>
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package Starter
 */

if ( ! function_exists( 'starter_featured_image' ) ) :
	/**
	 * Template for Featured Header Image from theme options
	 *
	 * To override this in a child theme
	 * simply create your own starter_featured_image(), and that function will be used instead.
	 *
	 * @since Catch Base 1.0
	 */
	function starter_featured_image() {
		if ( is_post_type_archive( 'jetpack-testimonial' ) ) :
			$jetpack_options = get_theme_mod( 'jetpack_testimonials' );
			if ( isset( $jetpack_options['featured-image'] ) && '' !== $jetpack_options['featured-image'] ) : ?>
				<div class="post-thumbnail archive-header-image">
					<?php echo wp_get_attachment_image( (int) $jetpack_options['featured-image'], 'starter-slider' ); ?>
				</div>
			<?php endif;

		elseif ( is_post_type_archive( 'jetpack-portfolio' ) ) :

			$jetpack_portfolio_featured_image = get_option( 'jetpack_portfolio_featured_image' );
			if ( '' !== $jetpack_portfolio_featured_image ) : ?>
				<div class="post-thumbnail archive-header-image">
					<?php echo wp_get_attachment_image( (int) $jetpack_portfolio_featured_image, 'starter-slider' ); ?>
				</div>
			<?php endif;

		elseif ( has_custom_header() ) : ?>
			<div class="custom-header-media">
				<?php the_custom_header_markup(); ?>
			</div>

			<?php get_template_part( 'template-parts/header/header-media', 'text' ); ?>
		<?php
		endif;

	} // starter_featured_image
endif;

if ( ! function_exists( 'starter_featured_page_post_image' ) ) :
	/**
	 * Template for Featured Header Image from Post and Page
	 *
	 * To override this in a child theme
	 * simply create your own starter_featured_imaage_pagepost(), and that function will be used instead.
	 *
	 * @since Starter 1.0
	 */
	function starter_featured_page_post_image() {
		if ( class_exists( 'woocommerce' ) && is_shop() ) {
			if ( ! has_post_thumbnail( absint( get_option( 'woocommerce_shop_page_id' ) ) ) ) {
				starter_featured_image();
				return;
			}
		} elseif ( ! has_post_thumbnail() ) {
			starter_featured_image();
			return;
		}
		?>
		<div class="post-thumbnail singular-header-image">
			<?php
			if ( class_exists( 'woocommerce' ) && is_shop() ) {
				echo get_the_post_thumbnail( absint( get_option( 'woocommerce_shop_page_id' ) ) );
			} else {
				the_post_thumbnail('starter-slider');
			}
			?>
		</div><!-- .post-thumbnail -->
		<?php
	} // starter_featured_page_post_image
endif;


if ( ! function_exists( 'starter_featured_overall_image' ) ) :
	/**
	 * Template for Featured Header Image from theme options
	 *
	 * To override this in a child theme
	 * simply create your own starter_featured_pagepost_image(), and that function will be used instead.
	 *
	 * @since Starter 1.0
	 */
	function starter_featured_overall_image() {
		global $post, $wp_query;
		$enable = get_theme_mod( 'starter_header_media_option', 'homepage' );

		// Get Page ID outside Loop
		$page_id = absint( $wp_query->get_queried_object_id() );

		$page_for_posts = absint( get_option( 'page_for_posts' ) );

		// Check Enable/Disable header image in Page/Post Meta box
		if ( is_page() || is_single() ) {
			//Individual Page/Post Image Setting
			$individual_featured_image = get_post_meta( $post->ID, 'starter-header-image', true );

			if ( 'disable' === $individual_featured_image || ( 'default' === $individual_featured_image && 'disable' === $enable ) ) {
				echo '<!-- Page/Post Disable Header Image -->';
				return;
			}
			elseif ( 'enable' == $individual_featured_image && 'disable' === $enable ) {
				starter_featured_page_post_image();
			}
		}

		// Check Homepage
		if ( 'homepage' === $enable ) {
			if ( is_front_page() || ( is_home() && intval( $page_for_posts ) !== intval( $page_id ) ) ) {
				starter_featured_image();
			}
		} if ( 'entire-site' === $enable ) {
			starter_featured_image();
		}
		// Check Entire Site (Post/Page)
		elseif ( 'entire-site-page-post' === $enable ) {
			if ( is_page() || is_single() || ( is_home() && $page_for_posts === $page_id ) ) {
				starter_featured_page_post_image();
			}
			else {
				starter_featured_image();
			}
		}
	} // starter_featured_overall_image
endif;

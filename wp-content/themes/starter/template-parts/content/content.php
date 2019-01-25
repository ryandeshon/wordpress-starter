<?php
/**
 * The template part for displaying content
 *
 * @package Starter
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="post-wrapper">
		<?php
			starter_post_thumbnail( 'starter-square' );
		?>

		<div class="entry-container">
			<header class="entry-header">
				<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
					<span class="sticky-post"><?php esc_html_e( 'Featured', 'starter' ); ?></span>
				<?php endif; ?>

				<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

				<?php echo starter_entry_header(); ?>
			</header><!-- .entry-header -->
				<div class="entry-summary">
					<?php the_excerpt(); ?>
				</div><!-- .entry-summary -->
			<div class="entry-footer">
				<div class="entry-meta">
					<?php echo starter_entry_category(); ?>
				</div><!-- .entry-meta -->
			</div><!-- .entry-footer -->
		</div><!-- .entry-container -->
	</div><!-- .hentry-inner -->
</article><!-- #post-## -->

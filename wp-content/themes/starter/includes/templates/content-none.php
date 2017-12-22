<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @package Starter
 */

?>

<section class="no-results not-found">
	<header>
		<h2><?php esc_html_e( 'Nothing Found', 'Starter' ); ?></h2>
	</header>

	<div>

		<?php if ( is_search() ) { ?>
			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'Starter' ); ?></p>
			<?php get_search_form(); ?>

		<?php } else { ?>
			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'Starter' ); ?></p>
			<?php get_search_form(); ?>
			
		<?php } ?>
	</div>
</section>
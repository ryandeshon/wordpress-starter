<?php
/**
 * This is an example of a template partial, used to display posts
 *
 * @see get_template_part()
 * @package Starter
 */

?>

<article>
	<header>
		<a href="<?php the_permalink(); ?>">
			<?php the_title(); ?>
		</a>
	</header>

	<div>
		<?php the_content(); ?>
	</div>
</article>
<?php
/**
 * The main template file.
 *
 * @package Starter
 */

get_header(); ?>

<?php if ( have_posts() ) {
		while ( have_posts() ) { the_post();

			get_template_part( 'includes/templates/content', get_post_format() );

		}

		global $wp_query; 
		base_pagination(get_query_var('paged'), $wp_query->max_num_pages); 

	} else {

		get_template_part( 'includes/templates/content', 'none' );

	}
?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
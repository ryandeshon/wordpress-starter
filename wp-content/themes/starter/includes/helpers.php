<?php
/**
* A few helpers used by templates and WP functions
* 
* @since 1.0
*/

/**
* Used to extend the pagination class a bit, for use with API
* 
* @since 1.0
*
* @see paginate_links()
*
* @param int $current 		current page
* @param int $total 		total number of pages
* @param string $format 	url parameter format
*
*/
function base_pagination( $current, $total, $format = '/page/%#%' ) {

    $paginate_links = paginate_links( array(
        'format' => $format,
        'current' => max( 1, $current ),
        'total' => $total,
        'mid_size' => 5
    ) );

    if ( $paginate_links ) {
        echo '<div class="pagination">';
        echo $paginate_links;
        echo '</div>';
    }
}

/**
* Custom excerpt, may or may not be useful
* 
* @since 1.0
*
* @param string $content 	filtered content for the excerpt
*
*/
function filter_excerpt( $content ) {
	global $post;

	$text = get_the_content( '' );
	$text = apply_filters( 'the_content', $text);
	$tags_to_strip = array('table', 'img');
	foreach ($tags_to_strip as $tag)
	{
	   $text = preg_replace("/<\\/?" . $tag . "(.|\\s)*?>/", $replace_with, $text);

	}

	$video = get_post_meta( get_the_ID(), 'video', true );
	if($video) {
		$text = $video . $text;
	}
	
	$excerpt_length = apply_filters('excerpt_length', 150);


		    $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		    if ( count($words) > $excerpt_length ) {
		        array_pop($words);
		        $text = implode(' ', $words);
		        $text = force_balance_tags( $text );
		    } else {
		        $text = implode(' ', $words);
		    }



	return $text;
}
remove_all_filters( 'get_the_excerpt' );
remove_all_filters( 'the_excerpt' );
add_filter( 'the_excerpt', 'filter_excerpt' );

/**
 * SVG Helper Function
 *
 * @param  string $name SVG name.
 *
 * @return string       HTML block
 */
function show_icon( $name ) {
	$output = '<svg viewBox="0 0 100 100" class="icon icon-' . $name . '">
              <use
                xlink:href="' . get_stylesheet_directory_uri() . '/assets/svg/sprite.defs.svg#' . $name . '">
              </use></svg>';
	return $output;
}
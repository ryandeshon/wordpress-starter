<?php
/**
 * Display Breadcrumb
 *
 * @package Starter
 */

if ( ! function_exists( 'starter_breadcrumb' ) ) :
	function starter_breadcrumb() {
		/* === OPTIONS === */
		$text['home']     = esc_html__( 'Home', 'starter' ); // text for the 'Home' link
		/* translators: 1: before text/html, 2: after text/html. */
		$text['category'] = esc_html__( '%1$s Archive for %2$s', 'starter' ); // text for a category page
		/* translators: 1: before text/html, 2: after text/html. */
		$text['search']   = esc_html__( '%1$sSearch results for: %2$s', 'starter' ); // text for a search results page
		/* translators: 1: before text/html, 2: after text/html. */
		$text['tag']      = esc_html__( '%1$sPosts tagged %2$s', 'starter' ); // text for a tag page
		/* translators: 1: before text/html, 2: after text/html. */
		$text['author']   = esc_html__( '%1$sView all posts by %2$s', 'starter' ); // text for an author page
		$text['404']      = esc_html__( 'Error 404', 'starter' ); // text for the 404 page

		$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
		$before      = '<span class="breadcrumb-current">'; // tag before the current crumb
		$after       = '</span>'; // tag after the current crumb
		/* === END OF OPTIONS === */

		global $post, $paged, $page;
		$homeLink   = home_url( '/' );
		$linkBefore = '<span class="breadcrumb" typeof="v:Breadcrumb">';
		$linkAfter  = '</span>';
		$linkAttr   = ' rel="v:url" property="v:title"';
		$link       = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;

			echo '<div class="breadcrumb-area custom">
				<nav class="entry-breadcrumbs">';

			if ( ! is_home() ) {
			echo sprintf( $link, esc_url( $homeLink ), $text['home'] ); // WPCS: XSS OK.
			}
			if ( is_category() ) {
				$thisCat = get_category( get_query_var( 'cat' ), false );

				if ( $thisCat->parent != 0 ) {
					$cats = get_category_parents( $thisCat->parent, true, false );
					$cats = str_replace( '<a', $linkBefore . '<a' . $linkAttr, $cats );
					$cats = str_replace( '</a>', '</a>' . $linkAfter, $cats );
					echo $cats; // WPCS: XSS OK.
				}

				echo $before . sprintf( $text['category'], '<span class="archive-text">', '</span>' . single_cat_title( '', false ) ) . $after; // WPCS: XSS OK.

			} elseif ( is_search() ) {
				echo $before . sprintf( $text['search'], '<span class="search-text">', '</span>' . get_search_query() ) . $after; // WPCS: XSS OK.

			} elseif ( is_day() ) {
				echo sprintf( $link, esc_url( get_year_link( get_the_time( __( 'Y', 'starter' ) ) ) ), esc_html( get_the_time( __( 'Y', 'starter' ) ) ) ) ; // WPCS: XSS OK.

				echo sprintf( $link, esc_url( get_month_link( get_the_time( __( 'Y', 'starter' ) ), get_the_time( __( 'n', 'starter' ) ) ) ), esc_html( get_the_time( __( 'F', 'starter' ) ) ) ); // WPCS: XSS OK.

				echo $before . esc_html( get_the_time( __( 'd', 'starter' ) ) ) . $after; // WPCS: XSS OK.

			} elseif ( is_month() ) {
				echo sprintf( $link, esc_url( get_year_link( get_the_time( __( 'Y', 'starter' ) ) ) ), esc_html( get_the_time( __( 'Y', 'starter' ) ) ) ) ; // WPCS: XSS OK.

				echo $before . esc_html( get_the_time( __( 'F', 'starter' ) ) ) . $after; // WPCS: XSS OK.


			} elseif ( is_year() ) {
				echo $before . esc_html( get_the_time( __( 'Y', 'starter' ) ) ) . $after; // WPCS: XSS OK.


			} elseif ( is_single() && !is_attachment() ) {
				if ( get_post_type() != 'post' ) {
					$post_type = get_post_type_object( get_post_type() );
					$post_link = get_post_type_archive_link( $post_type->name );

					printf( $link, esc_url( $post_link ), esc_html( $post_type->labels->singular_name ) ); // WPCS: XSS OK.

					echo $before . esc_html( get_the_title() ) . $after; // WPCS: XSS OK.
				}
				else {
					$cat  = get_the_category();
					$cat  = $cat[0];
					$cats = get_category_parents( $cat, true, '' );
					$cats = preg_replace( "#^(.+)$#", "$1", $cats );
					$cats = str_replace( '<a', $linkBefore . '<a' . $linkAttr, $cats );
					$cats = str_replace( '</a>', '</a>' . $linkAfter, $cats );
					echo $cats;  // WPCS: XSS OK.

					echo $before . esc_html( get_the_title() ) . $after;  // WPCS: XSS OK.
				}
			} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
				$post_type = get_post_type_object( get_post_type() );
				echo isset( $post_type->labels->singular_name ) ? $before . esc_html( $post_type->labels->singular_name ) . $after : '';  // WPCS: XSS OK.
			} elseif ( is_attachment() ) {
				$parent = get_post( $post->post_parent );
				$cat    = get_the_category( $parent->ID );

				if ( isset( $cat[0] ) ) {
					$cat = $cat[0];
				}

				if ( $cat ) {
					$cats = get_category_parents( $cat, true );
					$cats = str_replace( '<a', $linkBefore . '<a' . $linkAttr, $cats );
					$cats = str_replace( '</a>', '</a>' . $linkAfter, $cats );
					echo $cats; // WPCS: XSS OK.
				}

				printf( $link, esc_url( get_permalink( $parent ) ), esc_html( $parent->post_title ) ); // WPCS: XSS OK.

				echo $before . esc_html( get_the_title() ) . $after; // WPCS: XSS OK.
			} elseif ( is_page() && ! $post->post_parent ) {
				echo $before . esc_html( get_the_title() ) . $after; // WPCS: XSS OK.
			} elseif ( is_page() && $post->post_parent ) {
				$parent_id   = $post->post_parent;
				$breadcrumbs = array();

				while( $parent_id ) {
					$page_child    = get_post( $parent_id );
					$breadcrumbs[] = sprintf( $link, esc_url( get_permalink( $page_child->ID ) ), esc_html( get_the_title( $page_child->ID ) ) );
					$parent_id     = $page_child->post_parent;
				}

				$breadcrumbs = array_reverse( $breadcrumbs );

				for( $i = 0; $i < count( $breadcrumbs ); $i++ ) {
					echo $breadcrumbs[$i];  // WPCS: XSS OK.
				}

				echo $before . esc_html( get_the_title() ) . $after; // WPCS: XSS OK.
			} elseif ( is_tag() ) {
				echo $before . sprintf( $text['tag'], '<span class="tag-text">', '</span>' . single_tag_title( '', false ) ) . $after;  // WPCS: XSS OK.

			} elseif ( is_author() ) {
				global $author;
				$userdata = get_userdata( $author );
				echo $before . sprintf( $text['author'], '<span class="author-text">', '</span>' . $userdata->display_name ) . $after;  // WPCS: XSS OK.

			} elseif ( is_404() ) {
				echo $before . $text['404'] . $after;  // WPCS: XSS OK.

			}

			if ( get_query_var( 'paged' ) ) {
				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
					echo ' (';
				}

				/* translators: %s: current page number. */
				echo sprintf( esc_html__( 'Page %s', 'starter' ), absint( max( $paged, $page ) ) );

				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
					echo ')';
				}
			}

			echo '</nav><!-- .entry-breadcrumbs -->
			</div><!-- .breadcrumb-area -->';

	}
endif;

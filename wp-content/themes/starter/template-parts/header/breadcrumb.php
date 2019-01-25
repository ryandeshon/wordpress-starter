<?php
/**
 * Display Breadcrumb
 *
 * @package Starter
 */
?>

<?php
$enable_breadcrumb = get_theme_mod( 'starter_breadcrumb_option', 1 );

if ( $enable_breadcrumb ) :
        starter_breadcrumb();
endif;

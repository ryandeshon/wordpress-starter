<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and starts the body
 *
 * @package Starter
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'Starter' ); ?></a>

	<header role="banner">

	</header>

	<div id="content">
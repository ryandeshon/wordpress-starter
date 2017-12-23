<?php
/**
 * Starter Theme functions and definitions.
 *
 * @link https://codex.wordpress.org/Functions_File_Explained
 *
 * @package Starter
 */

if ( ! function_exists( 'STARTER_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function STARTER_setup() {

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 */
	add_theme_support( 'post-thumbnails' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );
}
endif; // STARTER_setup
add_action( 'after_setup_theme', 'STARTER_setup' );


/**
 * Enqueue scripts and styles.
 */
function STARTER_scripts() {
	wp_enqueue_style( 'wbm-style', get_stylesheet_uri() );

	wp_register_script( 'app', get_template_directory_uri() . '/assets/js/scripts.min.js', array('jquery'), '', true );
	wp_localize_script('app', 'WP_VARS', array( 'template_dir' => get_template_directory_uri() ) );

	wp_enqueue_script( 'app');
	if (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
	  wp_register_script('livereload', 'http://localhost:35729/livereload.js?snipver=1', null, false, true);
	  wp_enqueue_script('livereload');
	}
}
add_action( 'wp_enqueue_scripts', 'STARTER_scripts' );


/**
 * Custom Template class, for passing locally scoped variables
 */
require get_template_directory() . '/includes/lib/template/template.php';

/**
 * Basic helpers to get started
 */
require get_template_directory() . '/includes/helpers.php';

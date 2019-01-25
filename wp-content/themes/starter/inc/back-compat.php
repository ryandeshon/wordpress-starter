<?php
/**
 * Starter back compat functionality
 *
 * Prevents Starter from running on WordPress versions prior to 4.4,
 * since this theme is not meant to be backward compatible beyond that and
 * relies on many newer functions and markup changes introduced in 4.4.
 *
 * @package Starter
 */

/**
 * Prevent switching to Starter on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 * @since Starter 0.1
 */
function starter_switch_theme() {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );

	unset( $_GET['activated'] );

	add_action( 'admin_notices', 'starter_upgrade_notice' );
}
add_action( 'after_switch_theme', 'starter_switch_theme' );

/**
 * Adds a message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * Starter on WordPress versions prior to 4.4.
 *
 * @since Starter 0.1
 *
 * @global string $wp_version WordPress version.
 */
function starter_upgrade_notice() {
	/* translators: %s: current WordPress version. */
	$message = sprintf( __( 'Starter requires at least WordPress version 4.4. You are running version %s. Please upgrade and try again.', 'starter' ), $GLOBALS['wp_version'] );
	printf( '<div class="error"><p>%s</p></div>', $message );// WPCS: XSS ok.
}

/**
 * Prevents the Customizer from being loaded on WordPress versions prior to 4.4.
 *
 * @since Starter 0.1
 *
 * @global string $wp_version WordPress version.
 */
function starter_customize() {
	/* translators: %s: current WordPress version. */
	$message = sprintf( __( 'Starter requires at least WordPress version 4.4. You are running version %s. Please upgrade and try again.', 'starter' ), $GLOBALS['wp_version'] );

	wp_die( $message, '', array( // WPCS: XSS ok.
		'back_link' => true,
	) ); // WPCS: XSS ok.
}
add_action( 'load-customize.php', 'starter_customize' );

/**
 * Prevents the Theme Preview from being loaded on WordPress versions prior to 4.4.
 *
 * @since Starter 0.1
 *
 * @global string $wp_version WordPress version.
 */
function starter_preview() {
	if ( isset( $_GET['preview'] ) ) {
		/* translators: %s: current WordPress version. */
		wp_die( sprintf( __( 'Starter requires at least WordPress version 4.4. You are running version %s. Please upgrade and try again.', 'starter' ), $GLOBALS['wp_version'] ) );// WPCS: XSS ok.
	}
}
add_action( 'template_redirect', 'starter_preview' );

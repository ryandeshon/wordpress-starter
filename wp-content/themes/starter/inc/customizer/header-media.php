<?php
/**
 * Header Media Options
 *
 * @package Starter
 */

function starter_header_media_options( $wp_customize ) {
	$wp_customize->get_section( 'header_image' )->description = esc_html__( 'If you add video, it will only show up on Homepage/FrontPage. Other Pages will use Header/Post/Page Image depending on your selection of option. Header Image will be used as a fallback while the video loads ', 'starter' );

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_header_media_option',
			'default'           => 'homepage',
			'sanitize_callback' => 'starter_sanitize_select',
			'choices'           => array(
				'homepage'               => esc_html__( 'Homepage / Frontpage', 'starter' ),
				'entire-site'            => esc_html__( 'Entire Site', 'starter' ),
				'entire-site-page-post'  => esc_html__( 'Entire Site, Page/Post Featured Image', 'starter' ),
				'disable'                => esc_html__( 'Disabled', 'starter' ),
			),
			'label'             => esc_html__( 'Enable on ', 'starter' ),
			'section'           => 'header_image',
			'type'              => 'select',
			'priority'          => 1,
		)
	);

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_header_media_title',
			'default'           => esc_html__( 'Welcome to Starter', 'starter' ),
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Header Media Title', 'starter' ),
			'section'           => 'header_image',
			'type'              => 'text',
		)
	);

    starter_register_option( $wp_customize, array(
			'name'              => 'starter_header_media_text',
			'default'           => esc_html__( 'Make things as simple as possible but no simpler.', 'starter' ),
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Header Media Text', 'starter' ),
			'section'           => 'header_image',
			'type'              => 'textarea',
		)
	);

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_header_media_url',
			'default'           => '#',
			'sanitize_callback' => 'esc_url_raw',
			'label'             => esc_html__( 'Header Media Url', 'starter' ),
			'section'           => 'header_image',
		)
	);

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_header_media_url_text',
			'default'           => esc_html__( 'Continue Reading', 'starter' ),
			'sanitize_callback' => 'sanitize_text_field',
			'label'             => esc_html__( 'Header Media Url Text', 'starter' ),
			'section'           => 'header_image',
		)
	);

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_header_url_target',
			'sanitize_callback' => 'starter_sanitize_checkbox',
			'label'             => esc_html__( 'Check to Open Link in New Window/Tab', 'starter' ),
			'section'           => 'header_image',
			'type'              => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'starter_header_media_options' );


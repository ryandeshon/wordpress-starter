<?php
/**
 * Theme Options
 *
 * @package Starter
 */

/**
 * Add theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function starter_theme_options( $wp_customize ) {
	$wp_customize->add_panel( 'starter_theme_options', array(
		'title'    => esc_html__( 'Theme Options', 'starter' ),
		'priority' => 130,
	) );

	// Breadcrumb Option.
	$wp_customize->add_section( 'starter_breadcrumb_options', array(
		'description'   => esc_html__( 'Breadcrumbs are a great way of letting your visitors find out where they are on your site with just a glance.', 'starter' ),
		'panel'         => 'starter_theme_options',
		'title'         => esc_html__( 'Breadcrumb', 'starter' ),
	) );

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_breadcrumb_option',
			'default'           => 1,
			'sanitize_callback' => 'starter_sanitize_checkbox',
			'label'             => esc_html__( 'Check to enable Breadcrumb', 'starter' ),
			'section'           => 'starter_breadcrumb_options',
			'type'              => 'checkbox',
		)
	);

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_latest_posts_title',
			'default'           => esc_html__( 'News', 'starter' ),
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Latest Posts Title', 'starter' ),
			'section'           => 'starter_theme_options',
		)
	);

	// Layout Options
	$wp_customize->add_section( 'starter_layout_options', array(
		'title' => esc_html__( 'Layout Options', 'starter' ),
		'panel' => 'starter_theme_options',
		)
	);

	/* Default Layout */
	starter_register_option( $wp_customize, array(
			'name'              => 'starter_default_layout',
			'default'           => 'right-sidebar',
			'sanitize_callback' => 'starter_sanitize_select',
			'label'             => esc_html__( 'Default Layout', 'starter' ),
			'section'           => 'starter_layout_options',
			'type'              => 'radio',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'starter' ),
				'no-sidebar'            => esc_html__( 'No Sidebar', 'starter' ),
			),
		)
	);

	/* Homepage/Archive Layout */
	starter_register_option( $wp_customize, array(
			'name'              => 'starter_homepage_archive_layout',
			'default'           => 'right-sidebar',
			'sanitize_callback' => 'starter_sanitize_select',
			'label'             => esc_html__( 'Homepage/Archive Layout', 'starter' ),
			'section'           => 'starter_layout_options',
			'type'              => 'radio',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'starter' ),
				'no-sidebar'            => esc_html__( 'No Sidebar', 'starter' ),
			),
		)
	);

	// Excerpt Options.
	$wp_customize->add_section( 'starter_excerpt_options', array(
		'panel'     => 'starter_theme_options',
		'title'     => esc_html__( 'Excerpt Options', 'starter' ),
	) );

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_excerpt_length',
			'default'           => '20',
			'sanitize_callback' => 'absint',
			'description' => esc_html__( 'Excerpt length. Default is 20 words', 'starter' ),
			'input_attrs' => array(
				'min'   => 10,
				'max'   => 200,
				'step'  => 5,
				'style' => 'width: 60px;',
			),
			'label'    => esc_html__( 'Excerpt Length (words)', 'starter' ),
			'section'  => 'starter_excerpt_options',
			'type'     => 'number',
		)
	);

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_excerpt_more_text',
			'default'           => esc_html__( 'Continue reading', 'starter' ),
			'sanitize_callback' => 'sanitize_text_field',
			'label'             => esc_html__( 'Read More Text', 'starter' ),
			'section'           => 'starter_excerpt_options',
			'type'              => 'text',
		)
	);

	// Homepage / Frontpage Options.
	$wp_customize->add_section( 'starter_homepage_options', array(
		'description' => esc_html__( 'Only posts that belong to the categories selected here will be displayed on the front page', 'starter' ),
		'panel'       => 'starter_theme_options',
		'title'       => esc_html__( 'Homepage / Frontpage Options', 'starter' ),
	) );

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_front_page_category',
			'sanitize_callback' => 'starter_sanitize_category_list',
			'custom_control'    => 'Starter_Multi_Categories_Control',
			'label'             => esc_html__( 'Categories', 'starter' ),
			'section'           => 'starter_homepage_options',
			'type'              => 'dropdown-categories',
		)
	);

	// Pagination Options.
	$pagination_type = get_theme_mod( 'starter_pagination_type', 'default' );

	$nav_desc = '';

	$nav_desc = sprintf(
		wp_kses(
			__( 'For infinite scrolling, use %1$sCatch Infinite Scroll Plugin%2$s with Infinite Scroll module Enabled.', 'starter' ),
			array(
				'a' => array(
					'href' => array(),
					'target' => array(),
				),
				'br'=> array()
			)
		),
		'<a target="_blank" href="https://wordpress.org/plugins/catch-infinite-scroll/">',
		'</a>'
	);

	$wp_customize->add_section( 'starter_pagination_options', array(
		'description' => $nav_desc,
		'panel'       => 'starter_theme_options',
		'title'       => esc_html__( 'Pagination Options', 'starter' ),
	) );

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_pagination_type',
			'default'           => 'default',
			'sanitize_callback' => 'starter_sanitize_select',
			'choices'           => starter_get_pagination_types(),
			'label'             => esc_html__( 'Pagination type', 'starter' ),
			'section'           => 'starter_pagination_options',
			'type'              => 'select',
		)
	);

	/* Scrollup Options */
	$wp_customize->add_section( 'starter_scrollup', array(
		'panel'    => 'starter_theme_options',
		'title'    => esc_html__( 'Scrollup Options', 'starter' ),
	) );

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_disable_scrollup',
			'sanitize_callback' => 'starter_sanitize_checkbox',
			'label'             => esc_html__( 'Disable Scroll Up', 'starter' ),
			'section'           => 'starter_scrollup',
			'type'              => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'starter_theme_options' );

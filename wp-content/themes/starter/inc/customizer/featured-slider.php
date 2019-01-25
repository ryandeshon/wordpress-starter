<?php
/**
 * Featured Slider Options
 *
 * @package Starter
 */

/**
 * Add hero content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function starter_slider_options( $wp_customize ) {
	$wp_customize->add_section( 'starter_featured_slider', array(
			'panel' => 'starter_theme_options',
			'title' => esc_html__( 'Featured Slider', 'starter' ),
		)
	);

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_slider_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'starter_sanitize_select',
			'choices'           => starter_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'starter' ),
			'section'           => 'starter_featured_slider',
			'type'              => 'select',
		)
	);

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_slider_number',
			'default'           => '4',
			'sanitize_callback' => 'starter_sanitize_number_range',

			'active_callback'   => 'starter_is_slider_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Slides is changed (Max no of slides is 20)', 'starter' ),
			'input_attrs'       => array(
				'style' => 'width: 45px;',
				'min'   => 0,
				'max'   => 20,
				'step'  => 1,
			),
			'label'             => esc_html__( 'No of Slides', 'starter' ),
			'section'           => 'starter_featured_slider',
			'type'              => 'number',
		)
	);


	$slider_number = get_theme_mod( 'starter_slider_number', 4 );

	for ( $i = 1; $i <= $slider_number ; $i++ ) {
		// Page Sliders
		starter_register_option( $wp_customize, array(
				'name'              =>'starter_slider_page_' . $i,
				'sanitize_callback' => 'starter_sanitize_post',
				'active_callback'   => 'starter_is_slider_active',
				'label'             => esc_html__( 'Page', 'starter' ) . ' # ' . $i,
				'section'           => 'starter_featured_slider',
				'type'              => 'dropdown-pages',
			)
		);
	} // End for().
}
add_action( 'customize_register', 'starter_slider_options' );

/**
 * Returns an array of featured content show registered
 *
 * @since Starter 0.1
 */
function starter_content_show() {
	$options = array(
		'excerpt'      => esc_html__( 'Show Excerpt', 'starter' ),
		'full-content' => esc_html__( 'Full Content', 'starter' ),
		'hide-content' => esc_html__( 'Hide Content', 'starter' ),
	);
	return apply_filters( 'starter_content_show', $options );
}


/**
 * Returns an array of featured content show registered
 *
 * @since Starter 0.1
 */
function starter_meta_show() {
	$options = array(
		'show-meta'      => esc_html__( 'Show Meta', 'starter' ),
		'hide-meta' => esc_html__( 'Hide Meta', 'starter' ),
	);
	return apply_filters( 'starter_content_show', $options );
}

/** Active Callback Functions */

if( ! function_exists( 'starter_is_slider_active' ) ) :
	/**
	* Return true if slider is active
	*
	* @since Starter 0.1
	*/
	function starter_is_slider_active( $control ) {
		global $wp_query;

		$page_id = $wp_query->get_queried_object_id();

		// Front page display in Reading Settings
		$page_for_posts = get_option('page_for_posts');

		$enable = $control->manager->get_setting( 'starter_slider_option' )->value();

		//return true only if previwed page on customizer matches the type of slider option selected
		return ( 'entire-site' == $enable || ( ( is_front_page() || ( is_home() && $page_for_posts != $page_id ) ) && 'homepage' == $enable )
			);
	}
endif;

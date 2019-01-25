<?php
/**
 * Featured Content options
 *
 * @package Starter
 */

/**
 * Add featured content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function starter_featured_content_options( $wp_customize ) {
	// Add note to ECT Featured Content Section
    starter_register_option( $wp_customize, array(
            'name'              => 'starter_featured_content_jetpack_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Starter_Note_Control',
            'label'             => sprintf( esc_html__( 'For all Featured Content Options for Starter Theme, go %1$shere%2$s', 'starter' ),
                '<a href="javascript:wp.customize.section( \'starter_featured_content\' ).focus();">',
                 '</a>'
            ),
           'section'            => 'featured_content',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

    $wp_customize->add_section( 'starter_featured_content', array(
			'title' => esc_html__( 'Featured Content', 'starter' ),
			'panel' => 'starter_theme_options',
		)
	);

    starter_register_option( $wp_customize, array(
            'name'              => 'starter_featured_content_note_1',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Starter_Note_Control',
            'active_callback'   => 'starter_is_ect_featured_content_inactive',
            'label'             => sprintf( esc_html__( 'For Featured Content, install %1$sEssential Content Types%2$s Plugin with Featured Content Type Enabled', 'starter' ),
                '<a target="_blank" href="https://wordpress.org/plugins/essential-content-types/">',
                '</a>'
            ),
            'section'           => 'starter_featured_content',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

	// Add color scheme setting and control.
	starter_register_option( $wp_customize, array(
			'name'              => 'starter_featured_content_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'starter_sanitize_select',
			'active_callback'   => 'starter_is_ect_featured_content_active',
			'choices'           => starter_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'starter' ),
			'section'           => 'starter_featured_content',
			'type'              => 'select',
		)
	);

    starter_register_option( $wp_customize, array(
            'name'              => 'starter_featured_content_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Starter_Note_Control',
            'active_callback'  => 'starter_is_featured_content_active',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
			'label'             => sprintf( esc_html__( 'For CPT heading and sub-heading, go %1$shere%2$s', 'starter' ),
                 '<a href="javascript:wp.customize.control( \'featured_content_title\' ).focus();">',
                 '</a>'
            ),
            'section'           => 'starter_featured_content',
            'type'              => 'description',
        )
    );

	starter_register_option( $wp_customize, array(
			'name'              => 'starter_featured_content_number',
			'default'           => 3,
			'sanitize_callback' => 'starter_sanitize_number_range',
			'active_callback'  => 'starter_is_featured_content_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Featured Content is changed (Max no of Featured Content is 20)', 'starter' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of Featured Content', 'starter' ),
			'section'           => 'starter_featured_content',
			'type'              => 'number',
		)
	);

	$number = get_theme_mod( 'starter_featured_content_number', 3 );

	//loop for featured post content
	for ( $i = 1; $i <= $number ; $i++ ) {
		starter_register_option( $wp_customize, array(
				'name'              => 'starter_featured_content_cpt_' . $i,
				'sanitize_callback' => 'starter_sanitize_post',
				'active_callback'   => 'starter_is_featured_content_active',
				'label'             => esc_html__( 'Featured Content', 'starter' ) . ' ' . $i ,
				'section'           => 'starter_featured_content',
				'type'              => 'select',
                'choices'           => starter_generate_post_array( 'featured-content' ),
			)
		);
	} // End for().
}
add_action( 'customize_register', 'starter_featured_content_options', 10 );

/** Active Callback Functions **/
if ( ! function_exists( 'starter_is_featured_content_active' ) ) :
	/**
	* Return true if featured content is active
	*
	* @since Starter 0.1
	*/
	function starter_is_featured_content_active( $control ) {
		$enable = $control->manager->get_setting( 'starter_featured_content_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( starter_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'starter_is_ect_featured_content_active' ) ) :
    /**
    * Return true if featured_content is active
    *
    * @since Starter 0.1
    */
    function starter_is_ect_featured_content_active( $control ) {
        return ( class_exists( 'Essential_Content_Featured_Content' ) || class_exists( 'Essential_Content_Pro_Featured_Content' ) );
    }
endif;

if ( ! function_exists( 'starter_is_ect_featured_content_inactive' ) ) :
    /**
    * Return true if featured_content is active
    *
    * @since Starter 0.1
    */
    function starter_is_ect_featured_content_inactive( $control ) {
        return ! ( class_exists( 'Essential_Content_Featured_Content' ) || class_exists( 'Essential_Content_Pro_Featured_Content' ) );
    }
endif;

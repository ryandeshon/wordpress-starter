<?php
/**
 * Add Portfolio Settings in Customizer
 *
 * @package Starter
 */

/**
 * Add portfolio options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function starter_portfolio_options( $wp_customize ) {
    // Add note to Jetpack Portfolio Section
    starter_register_option( $wp_customize, array(
            'name'              => 'starter_jetpack_portfolio_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Starter_Note_Control',
            'label'             => sprintf( esc_html__( 'For Portfolio Options for Starter Theme, go %1$shere%2$s', 'starter' ),
                 '<a href="javascript:wp.customize.section( \'starter_portfolio\' ).focus();">',
                 '</a>'
            ),
            'section'           => 'jetpack_portfolio',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

	$wp_customize->add_section( 'starter_portfolio', array(
            'panel'    => 'starter_theme_options',
            'title'    => esc_html__( 'Portfolio', 'starter' ),
        )
    );

    starter_register_option( $wp_customize, array(
            'name'              => 'starter_portfolio_note_1',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Starter_Note_Control',
            'active_callback'   => 'starter_is_ect_portfolio_inactive',
            'label'             => sprintf( esc_html__( 'For Portfolio, install %1$sEssential Content Types%2$s Plugin with Portfolio Content Type Enabled', 'starter' ),
                '<a target="_blank" href="https://wordpress.org/plugins/essential-content-types/">',
                '</a>'
            ),
            'section'           => 'starter_portfolio',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

    starter_register_option( $wp_customize, array(
			'name'              => 'starter_portfolio_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'starter_sanitize_select',
            'active_callback'   => 'starter_is_ect_portfolio_active',
			'choices'           => starter_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'starter' ),
			'section'           => 'starter_portfolio',
			'type'              => 'select',
		)
	);

    starter_register_option( $wp_customize, array(
            'name'              => 'starter_portfolio_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Starter_Note_Control',
            'active_callback'   => 'starter_is_portfolio_active',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
			'label'             => sprintf( esc_html__( 'For CPT heading and sub-heading, go %1$shere%2$s', 'starter' ),
                 '<a href="javascript:wp.customize.control( \'jetpack_portfolio_title\' ).focus();">',
                 '</a>'
            ),
            'section'           => 'starter_portfolio',
            'type'              => 'description',
        )
    );

    starter_register_option( $wp_customize, array(
            'name'              => 'starter_portfolio_number',
            'default'           => '6',
            'sanitize_callback' => 'starter_sanitize_number_range',
            'active_callback'   => 'starter_is_portfolio_active',
            'label'             => esc_html__( 'Number of items to show', 'starter' ),
            'section'           => 'starter_portfolio',
            'type'              => 'number',
            'input_attrs'       => array(
                'style'             => 'width: 100px;',
                'min'               => 0,
            ),
        )
    );

    $number = get_theme_mod( 'starter_portfolio_number', 6 );

    for ( $i = 1; $i <= $number ; $i++ ) {
        //for CPT
        starter_register_option( $wp_customize, array(
                'name'              => 'starter_portfolio_cpt_' . $i,
                'sanitize_callback' => 'starter_sanitize_post',
                'active_callback'   => 'starter_is_portfolio_active',
                'label'             => esc_html__( 'Portfolio', 'starter' ) . ' ' . $i ,
                'section'           => 'starter_portfolio',
                'type'              => 'select',
                'choices'           => starter_generate_post_array( 'jetpack-portfolio' ),
            )
        );
    } // End for().
}
add_action( 'customize_register', 'starter_portfolio_options' );

/** Active Callback Functions **/
if ( ! function_exists( 'starter_is_portfolio_active' ) ) :
    /**
    * Return true if portfolio content is active
    *
    * @since Starter 0.1
    */
    function starter_is_portfolio_active( $control ) {
        $enable = $control->manager->get_setting( 'starter_portfolio_option' )->value();

        //return true only if previewed page on customizer matches the type of content option selected
        return ( starter_check_section( $enable ) && starter_is_ect_portfolio_active( $control ) );
    }
endif;

if ( ! function_exists( 'starter_is_ect_portfolio_active' ) ) :
    /**
    * Return true if portfolio is active
    *
    * @since Starter 0.1
    */
    function starter_is_ect_portfolio_active( $control ) {
        return ( class_exists( 'Essential_Content_Jetpack_Portfolio' ) || class_exists( 'JetPack' ) || class_exists( 'Essential_Content_Pro_Jetpack_Portfolio' ) );
    }
endif;

if ( ! function_exists( 'starter_is_ect_portfolio_inactive' ) ) :
    /**
    * Return true if portfolio is inactive
    *
    * @since Starter 0.1
    */
    function starter_is_ect_portfolio_inactive( $control ) {
        return ! ( class_exists( 'Essential_Content_Jetpack_Portfolio' ) || class_exists( 'JetPack' ) || class_exists( 'Essential_Content_Pro_Jetpack_Portfolio' ) );
    }
endif;
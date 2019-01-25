<?php
/**
 * Add Testimonial Settings in Customizer
 *
 * @package Starter
*/

/**
 * Add testimonial options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function starter_testimonial_options( $wp_customize ) {
    // Add note to Jetpack Testimonial Section
    starter_register_option( $wp_customize, array(
            'name'              => 'starter_jetpack_testimonial_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Starter_Note_Control',
            'label'             => sprintf( esc_html__( 'For Testimonial Options for Starter Theme, go %1$shere%2$s', 'starter' ),
                '<a href="javascript:wp.customize.section( \'starter_testimonials\' ).focus();">',
                 '</a>'
            ),
           'section'            => 'jetpack_testimonials',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

    $wp_customize->add_section( 'starter_testimonials', array(
            'panel'    => 'starter_theme_options',
            'title'    => esc_html__( 'Testimonials', 'starter' ),
        )
    );

    starter_register_option( $wp_customize, array(
            'name'              => 'starter_testimonial_note_1',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'starter_Note_Control',
            'active_callback'   => 'starter_is_ect_testimonial_inactive',
            'label'             => sprintf( esc_html__( 'For Testimonials, install %1$sEssential Content Types%2$s Plugin with Testimonial Content Type Enabled', 'starter' ),
                '<a target="_blank" href="https://wordpress.org/plugins/essential-content-types/">',
                '</a>'
            ),
            'section'           => 'starter_testimonials',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

    starter_register_option( $wp_customize, array(
            'name'              => 'starter_testimonial_option',
            'default'           => 'disabled',
            'sanitize_callback' => 'starter_sanitize_select',
            'active_callback'   => 'starter_is_ect_testimonial_active',
            'choices'           => starter_section_visibility_options(),
            'label'             => esc_html__( 'Enable on', 'starter' ),
            'section'           => 'starter_testimonials',
            'type'              => 'select',
            'priority'          => 1,
        )
    );

    starter_register_option( $wp_customize, array(
            'name'              => 'starter_testimonial_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Starter_Note_Control',
            'active_callback'   => 'starter_is_testimonial_active',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
			'label'             => sprintf( esc_html__( 'For CPT heading and sub-heading, go %1$shere%2$s', 'starter' ),
                '<a href="javascript:wp.customize.section( \'jetpack_testimonials\' ).focus();">',
                '</a>'
            ),
            'section'           => 'starter_testimonials',
            'type'              => 'description',
        )
    );

    starter_register_option( $wp_customize, array(
            'name'              => 'starter_testimonial_number',
            'default'           => '3',
            'sanitize_callback' => 'starter_sanitize_number_range',
            'active_callback'   => 'starter_is_testimonial_active',
            'label'             => esc_html__( 'Number of items to show', 'starter' ),
            'section'           => 'starter_testimonials',
            'type'              => 'number',
            'input_attrs'       => array(
                'style'             => 'width: 100px;',
                'min'               => 0,
            ),
        )
    );

    $number = get_theme_mod( 'starter_testimonial_number', 3 );

    for ( $i = 1; $i <= $number ; $i++ ) {
        //for CPT
        starter_register_option( $wp_customize, array(
                'name'              => 'starter_testimonial_cpt_' . $i,
                'sanitize_callback' => 'starter_sanitize_post',
                'active_callback'   => 'starter_is_testimonial_active',
                'label'             => esc_html__( 'Testimoial', 'starter' ) . ' ' . $i ,
                'section'           => 'starter_testimonials',
                'type'              => 'select',
                'choices'           => starter_generate_post_array( 'jetpack-testimonial' ),
            )
        );
    } // End for().
}
add_action( 'customize_register', 'starter_testimonial_options' );

/** Active Callback Functions **/
if ( ! function_exists( 'starter_is_testimonial_active' ) ) :
    /**
    * Return true if portfolio content is active
    *
    * @since Starter 0.1
    */
    function starter_is_testimonial_active( $control ) {
        $enable = $control->manager->get_setting( 'starter_testimonial_option' )->value();

        //return true only if previewed page on customizer matches the type of content option selected
        return ( starter_check_section( $enable ) && starter_is_ect_testimonial_active( $control ) );
    }
endif;

if ( ! function_exists( 'starter_is_ect_testimonial_active' ) ) :
    /**
    * Return true if portfolio is active
    *
    * @since Starter 0.1
    */
    function starter_is_ect_testimonial_active( $control ) {
        return ( class_exists( 'Essential_Content_Jetpack_Portfolio' ) || class_exists( 'JetPack' ) || class_exists( 'Essential_Content_Pro_Jetpack_Portfolio' ) );
    }
endif;

if ( ! function_exists( 'starter_is_ect_testimonial_inactive' ) ) :
    /**
    * Return true if portfolio is inactive
    *
    * @since Starter 0.1
    */
    function starter_is_ect_testimonial_inactive( $control ) {
        return ! ( class_exists( 'Essential_Content_Jetpack_Portfolio' ) || class_exists( 'JetPack' ) || class_exists( 'Essential_Content_Pro_Jetpack_Portfolio' ) );
    }
endif;
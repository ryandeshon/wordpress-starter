<?php
/**
 * Customizer functionality
 *
 * @package Starter
 */

/**
 * Sets up the WordPress core custom header and custom background features.
 *
 * @since Starter 0.1
 *
 * @see starter_header_style()
 */
function starter_custom_header_and_background() {
	/**
	 * Filter the arguments used when adding 'custom-background' support in Persona.
	 *
	 * @since Starter 0.1
	 *
	 * @param array $args {
	 *     An array of custom-background support arguments.
	 *
	 *     @type string $default-color Default color of the background.
	 * }
	 */
	add_theme_support( 'custom-background', apply_filters( 'starter_custom_background_args', array(
		'default-color' => '#111111',
	) ) );

	/**
	 * Filter the arguments used when adding 'custom-header' support in Persona.
	 *
	 * @since Starter 0.1
	 *
	 * @param array $args {
	 *     An array of custom-header support arguments.
	 *
	 *     @type string $default-text-color Default color of the header text.
	 *     @type int      $width            Width in pixels of the custom header image. Default 1200.
	 *     @type int      $height           Height in pixels of the custom header image. Default 280.
	 *     @type bool     $flex-height      Whether to allow flexible-height header images. Default true.
	 *     @type callable $wp-head-callback Callback function used to style the header image and text
	 *                                      displayed on the blog.
	 * }
	 */
	add_theme_support( 'custom-header', apply_filters( 'starter_custom_header_args', array(
		'default-image'      	 => get_parent_theme_file_uri( '/assets/images/header-image.jpg' ),
		'default-text-color'     => '#000000',
		'width'                  => 1920,
		'height'                 => 822,
		'flex-height'            => true,
		'flex-height'            => true,
		'wp-head-callback'       => 'starter_header_style',
		'video'                  => true,
	) ) );

	register_default_headers( array(
		'default-image' => array(
			'url'           => '%s/assets/images/header-image.jpg',
			'thumbnail_url' => '%s/assets/images/header-image-275x155.jpg',
			'description'   => esc_html__( 'Default Header Image', 'starter' ),
		),
	) );
}
add_action( 'after_setup_theme', 'starter_custom_header_and_background' );


/**
 * Customize video play/pause button in the custom header.
 *
 * @param array $settings header video settings.
 */
function starter_video_controls( $settings ) {
	$settings['l10n']['play'] = '<span class="screen-reader-text">' . esc_html__( 'Play background video', 'starter' ) . '</span>' . starter_get_svg( array(
		'icon' => 'play',
	) );
	$settings['l10n']['pause'] = '<span class="screen-reader-text">' . esc_html__( 'Pause background video', 'starter' ) . '</span>' . starter_get_svg( array(
		'icon' => 'pause',
	) );
	return $settings;
}
add_filter( 'header_video_settings', 'starter_video_controls' );

if ( ! function_exists( 'starter_header_style' ) ) :
	/**
	 * Styles the header text displayed on the site.
	 *
	 * Create your own starter_header_style() function to override in a child theme.
	 *
	 * @since Starter Pro 1.0
	 *
	 * @see starter_custom_header_and_background().
	 */
	function starter_header_style() {
	// If the header text has been hidden.
	?>
	<style type="text/css" id="starter-header-css">
		<?php
		// If the header text option is untouched, let's bail.
		if ( display_header_text() ) {
			$header_text_color = get_header_textcolor();
			?>
			.site-title a,
			.site-description {
				color: #<?php echo esc_attr( $header_text_color ); ?>;
			}
		<?php
		} else {
			?>
			.site-branding {
				margin: 0 auto 0 0;
			}

			.site-identity {
				clip: rect(1px, 1px, 1px, 1px);
				position: absolute;
			}
		<?php
		}
		?>
	</style>
	<?php
}
endif; // starter_header_style

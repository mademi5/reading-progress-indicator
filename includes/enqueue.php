<?php
/**
 * Enqueue front-end and admin assets.
 *
 * @package ReadingProgressIndicator
 */

defined( 'ABSPATH' ) || exit;

/**
 * Determine whether the progress bar should display on the current page.
 *
 * @return bool True if the bar should render.
 */
function rpi_should_display() {
	$settings = rpi_get_settings();

	if ( empty( $settings['enabled'] ) ) {
		return false;
	}

	$display_on = (array) $settings['display_on'];

	if ( in_array( 'all', $display_on, true ) ) {
		return true;
	}

	if ( is_singular() ) {
		$post_type = get_post_type();
		return in_array( $post_type, $display_on, true );
	}

	return false;
}

/**
 * Enqueue progress bar scripts and styles on the front end.
 *
 * @return void
 */
function rpi_enqueue_front_assets() {
	if ( ! rpi_should_display() ) {
		return;
	}

	$settings = rpi_get_settings();

	wp_enqueue_style(
		'rpi-progress-bar',
		RPI_PLUGIN_URL . 'assets/progress-bar.css',
		array(),
		RPI_VERSION
	);

	wp_enqueue_script(
		'rpi-progress-bar',
		RPI_PLUGIN_URL . 'assets/progress-bar.js',
		array(),
		RPI_VERSION,
		true
	);

	wp_localize_script( 'rpi-progress-bar', 'rpiSettings', array(
		'position'        => $settings['position'],
		'style'           => $settings['style'],
		'color'           => $settings['color'],
		'gradientEnd'     => $settings['gradient_end_color'],
		'thickness'       => $settings['thickness'],
		'borderRadius'    => $settings['border_radius'],
		'opacity'         => $settings['opacity'],
		'animation'       => $settings['animation'],
		'showPercentage'  => $settings['show_percentage'],
		'showCongrats'    => $settings['show_congrats'],
	) );

	$is_horizontal = in_array( $settings['position'], array( 'top', 'bottom' ), true );
	$size_prop     = $is_horizontal ? 'height' : 'width';

	$css_vars = sprintf(
		':root{--rpi-color:%s;--rpi-gradient-end:%s;--rpi-%s:%dpx;--rpi-radius:%dpx;--rpi-opacity:%s;}',
		esc_attr( $settings['color'] ),
		esc_attr( $settings['gradient_end_color'] ),
		$size_prop,
		intval( $settings['thickness'] ),
		intval( $settings['border_radius'] ),
		floatval( $settings['opacity'] )
	);

	wp_add_inline_style( 'rpi-progress-bar', $css_vars );
}
add_action( 'wp_enqueue_scripts', 'rpi_enqueue_front_assets' );

/**
 * Output the progress bar HTML in the footer.
 *
 * @return void
 */
function rpi_render_bar_markup() {
	if ( ! rpi_should_display() ) {
		return;
	}

	$settings = rpi_get_settings();
	echo '<div id="rpi-progress-wrap" class="rpi-position-' . esc_attr( $settings['position'] ) . '" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">';
	echo '<div id="rpi-progress-bar"></div>';
	echo '</div>';

	if ( ! empty( $settings['show_percentage'] ) ) {
		echo '<div id="rpi-percentage-badge" class="rpi-badge-' . esc_attr( $settings['position'] ) . '" aria-hidden="true">0%</div>';
	}
}
add_action( 'wp_footer', 'rpi_render_bar_markup' );

/**
 * Enqueue admin-only assets on the plugin settings page.
 *
 * @param string $hook Current admin page hook suffix.
 * @return void
 */
function rpi_enqueue_admin_assets( $hook ) {
	if ( 'settings_page_reading-progress' !== $hook ) {
		return;
	}

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );

	wp_enqueue_style(
		'rpi-admin',
		RPI_PLUGIN_URL . 'admin/admin.css',
		array( 'wp-color-picker' ),
		RPI_VERSION
	);

	wp_enqueue_style(
		'rpi-progress-bar',
		RPI_PLUGIN_URL . 'assets/progress-bar.css',
		array(),
		RPI_VERSION
	);

	wp_enqueue_script(
		'rpi-progress-bar',
		RPI_PLUGIN_URL . 'assets/progress-bar.js',
		array(),
		RPI_VERSION,
		true
	);

	wp_localize_script( 'rpi-progress-bar', 'rpiSettings', array(
		'position'        => rpi_get_settings()['position'],
		'style'           => rpi_get_settings()['style'],
		'color'           => rpi_get_settings()['color'],
		'gradientEnd'     => rpi_get_settings()['gradient_end_color'],
		'thickness'       => rpi_get_settings()['thickness'],
		'borderRadius'    => rpi_get_settings()['border_radius'],
		'opacity'         => rpi_get_settings()['opacity'],
		'animation'       => rpi_get_settings()['animation'],
		'showPercentage'  => rpi_get_settings()['show_percentage'],
		'showCongrats'    => rpi_get_settings()['show_congrats'],
		'isPreview'       => true,
	) );
}
add_action( 'admin_enqueue_scripts', 'rpi_enqueue_admin_assets' );
